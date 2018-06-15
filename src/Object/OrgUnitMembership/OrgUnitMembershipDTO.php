<?php

namespace SRAG\Plugins\Hub2\Object\OrgUnitMembership;

use SRAG\Plugins\Hub2\Object\DTO\DataTransferObject;
use SRAG\Plugins\Hub2\Sync\Processor\FakeIliasMembershipObject;

/**
 * Class OrgUnitMembershipDTO
 *
 * @package SRAG\Plugins\Hub2\Object\OrgUnitMembership
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class OrgUnitMembershipDTO extends DataTransferObject implements IOrgUnitMembershipDTO {

	/**
	 * @var int
	 */
	protected $org_unit_id;
	/**
	 * @var int
	 */
	protected $org_unit_id_type = self::ORG_UNIT_ID_TYPE_REF_ID;
	/**
	 * @var int
	 */
	protected $user_id;
	/**
	 * @var int
	 */
	protected $position;


	/**
	 * @param int $org_unit_id
	 * @param int $user_id
	 */
	public function __construct(int $org_unit_id, int $user_id) {
		parent::__construct($org_unit_id . FakeIliasMembershipObject::GLUE . $user_id);
		$this->org_unit_id = $org_unit_id;
		$this->user_id = $user_id;
	}


	/**
	 * @inheritdoc
	 */
	public function getOrgUnitId(): int {
		return $this->org_unit_id;
	}


	/**
	 * @inheritdoc
	 */
	public function setOrgUnitId(int $org_unit_id): IOrgUnitMembershipDTO {
		$this->org_unit_id = $org_unit_id;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function getOrgUnitIdType(): int {
		return $this->org_unit_id_type;
	}


	/**
	 * @inheritdoc
	 */
	public function setOrgUnitIdType(int $org_unit_id_type): IOrgUnitMembershipDTO {
		$this->org_unit_id_type = $org_unit_id_type;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function getUserId(): int {
		return $this->user_id;
	}


	/**
	 * @inheritdoc
	 */
	public function setUserId(int $user_id): IOrgUnitMembershipDTO {
		$this->user_id = $user_id;

		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function getPosition(): int {
		return $this->position;
	}


	/**
	 * @inheritdoc
	 */
	public function setPosition(int $position): IOrgUnitMembershipDTO {
		$this->position = $position;

		return $this;
	}
}
