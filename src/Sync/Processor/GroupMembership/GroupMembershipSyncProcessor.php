<?php

namespace srag\Plugins\Hub2\Sync\Processor\GroupMembership;

use ilObject2;
use ilObjGroup;
use srag\Plugins\Hub2\Exception\HubException;
use srag\Plugins\Hub2\Object\DTO\IDataTransferObject;
use srag\Plugins\Hub2\Object\GroupMembership\GroupMembershipDTO;
use srag\Plugins\Hub2\Object\ObjectFactory;
use srag\Plugins\Hub2\Origin\Config\Group\GroupOriginConfig;
use srag\Plugins\Hub2\Origin\IOrigin;
use srag\Plugins\Hub2\Origin\IOriginImplementation;
use srag\Plugins\Hub2\Origin\OriginRepository;
use srag\Plugins\Hub2\Origin\Properties\Group\GroupProperties;
use srag\Plugins\Hub2\Sync\IObjectStatusTransition;
use srag\Plugins\Hub2\Sync\Processor\FakeIliasMembershipObject;
use srag\Plugins\Hub2\Sync\Processor\ObjectSyncProcessor;

/**
 * Class GroupMembershipSyncProcessor
 *
 * @package srag\Plugins\Hub2\Sync\Processor\GroupMembership
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class GroupMembershipSyncProcessor extends ObjectSyncProcessor implements IGroupMembershipSyncProcessor {

	/**
	 * @var GroupProperties
	 */
	protected $props;
	/**
	 * @var GroupOriginConfig
	 */
	protected $config;


	/**
	 * @param IOrigin                 $origin
	 * @param IOriginImplementation   $implementation
	 * @param IObjectStatusTransition $transition
	 */
	public function __construct(IOrigin $origin, IOriginImplementation $implementation, IObjectStatusTransition $transition) {
		parent::__construct($origin, $implementation, $transition);
		$this->props = $origin->properties();
		$this->config = $origin->config();
	}


	/**
	 * @inheritdoc
	 */
	protected function handleCreate(IDataTransferObject $dto) {
		/**
		 * @var GroupMembershipDTO $dto
		 */
		$ilias_group_ref_id = $this->buildParentRefId($dto);

		$group = $this->findILIASGroup($ilias_group_ref_id);
		if (!$group) {
			return NULL;
		}
		$user_id = $dto->getUserId();
		$group->getMembersObject()->add($user_id, $this->mapRole($dto));

		return new FakeIliasMembershipObject($ilias_group_ref_id, $user_id);
	}


	/**
	 * @inheritdoc
	 */
	protected function handleUpdate(IDataTransferObject $dto, $ilias_id) {
		/**
		 * @var GroupMembershipDTO $dto
		 */
		$obj = FakeIliasMembershipObject::loadInstanceWithConcatenatedId($ilias_id);

		$ilias_group_ref_id = $dto->getIliasGroupRefId();
		$user_id = $dto->getUserId();
		if (!$this->props->updateDTOProperty('role')) {
			return new FakeIliasMembershipObject($ilias_group_ref_id, $user_id);
		}

		$group = $this->findILIASGroup($ilias_group_ref_id);
		if (!$group) {
			return NULL;
		}

		$group->getMembersObject()->updateRoleAssignments($user_id, [ $this->getILIASRole($dto, $group) ]);

		$obj->setUserIdIlias($dto->getUserId());
		$obj->setContainerIdIlias($group->getRefId());
		$obj->initId();

		return $obj;
	}


	/**
	 * @inheritdoc
	 */
	protected function handleDelete($ilias_id) {
		$obj = FakeIliasMembershipObject::loadInstanceWithConcatenatedId($ilias_id);

		$group = $this->findILIASGroup($obj->getContainerIdIlias());
		$group->getMembersObject()->delete($obj->getUserIdIlias());

		return $obj;
	}


	/**
	 * @param int $iliasId
	 *
	 * @return ilObjGroup|null
	 */
	protected function findILIASGroup($iliasId) {
		if (!ilObject2::_exists($iliasId, true)) {
			return NULL;
		}

		return new ilObjGroup($iliasId);
	}


	/**
	 * @param GroupMembershipDTO $dto
	 *
	 * @return int
	 * @throws HubException
	 */
	protected function buildParentRefId(GroupMembershipDTO $dto) {
		if ($dto->getGroupIdType() == GroupMembershipDTO::PARENT_ID_TYPE_REF_ID) {
			if (self::dic()->tree()->isInTree($dto->getGroupId())) {
				return (int)$dto->getGroupId();
			}
			throw new HubException("Could not find the ref-ID of the parent group in the tree: '{$dto->getGroupId()}'");
		}
		if ($dto->getGroupIdType() == GroupMembershipDTO::PARENT_ID_TYPE_EXTERNAL_EXT_ID) {
			// The stored parent-ID is an external-ID from a group.
			// We must search the parent ref-ID from a group object synced by a linked origin.
			// --> Get an instance of the linked origin and lookup the group by the given external ID.
			$linkedOriginId = $this->config->getLinkedOriginId();
			if (!$linkedOriginId) {
				throw new HubException("Unable to lookup external parent ref-ID because there is no origin linked");
			}
			$originRepository = new OriginRepository();
			$origin = array_pop(array_filter($originRepository->groups(), function ($origin) use ($linkedOriginId) {
				/** @var IOrigin $origin */
				return (int)$origin->getId() == $linkedOriginId;
			}));
			if ($origin === NULL) {
				$msg = "The linked origin syncing group was not found, please check that the correct origin is linked";
				throw new HubException($msg);
			}
			$objectFactory = new ObjectFactory($origin);
			$group = $objectFactory->group($dto->getGroupId());
			if (!$group->getILIASId()) {
				throw new HubException("The linked group does not (yet) exist in ILIAS");
			}
			if (!self::dic()->tree()->isInTree($group->getILIASId())) {
				throw new HubException("Could not find the ref-ID of the parent group in the tree: '{$group->getILIASId()}'");
			}

			return (int)$group->getILIASId();
		}

		return 0;
	}


	/**
	 * @param GroupMembershipDTO $object
	 *
	 * @return int
	 */
	protected function mapRole(GroupMembershipDTO $object) {
		switch ($object->getRole()) {
			case GroupMembershipDTO::ROLE_ADMIN:
				return IL_GRP_ADMIN;
			case GroupMembershipDTO::ROLE_MEMBER:
				return IL_GRP_MEMBER;
			default:
				return IL_CRS_MEMBER;
		}
	}


	/**
	 * @param GroupMembershipDTO $object
	 * @param ilObjGroup         $group
	 *
	 * @return int
	 */
	protected function getILIASRole(GroupMembershipDTO $object, ilObjGroup $group) {
		switch ($object->getRole()) {
			case GroupMembershipDTO::ROLE_ADMIN:
				return $group->getDefaultAdminRole();
			case GroupMembershipDTO::ROLE_MEMBER:
				return $group->getDefaultMemberRole();
			default:
				return $group->getDefaultMemberRole();
		}
	}
}
