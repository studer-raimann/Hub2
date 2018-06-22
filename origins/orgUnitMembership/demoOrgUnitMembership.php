<?php

namespace SRAG\Plugins\Hub2\Origin;

use Exception;
use ilCSVReader;
use SRAG\Plugins\Hub2\Exception\BuildObjectsFailedException;
use SRAG\Plugins\Hub2\Exception\ConnectionFailedException;
use SRAG\Plugins\Hub2\Exception\ParseDataFailedException;
use SRAG\Plugins\Hub2\Object\HookObject;
use SRAG\Plugins\Hub2\Object\DTO\IDataTransferObject;
use SRAG\Plugins\Hub2\Object\OrgUnitMembership\IOrgUnitMembershipDTO;
use SRAG\Plugins\Hub2\Origin\Config\IOriginConfig;
use stdClass;

/**
 * Class demoOrgUnitMembership
 *
 * @package SRAG\Plugins\Hub2\Origin
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class demoOrgUnitMembership extends AbstractOriginImplementation {

	/**
	 * Connect to the service providing the sync data.
	 * Throw a ConnectionFailedException to abort the sync if a connection is not possible.
	 *
	 * @throws ConnectionFailedException
	 * @return bool
	 */
	public function connect(): bool {
		$csv_file = $this->config()->getFilePath();

		if ($this->config()->getConnectionType() != IOriginConfig::CONNECTION_TYPE_FILE || !file_exists($csv_file)) {
			// CSV file not found!
			throw new ConnectionFailedException("The csv file $csv_file does not exists!");
		}

		return true;
	}


	/**
	 * Parse and prepare (sanitize/validate) the data to fill the DTO objects.
	 * Return the number of data. Note that this number is used to check if the amount of delivered
	 * data is sufficent to continue the sync, depending on the configuration of the origin.
	 *
	 * Throw a ParseDataFailedException to abort the sync if your data cannot be parsed.
	 *
	 * @throws ParseDataFailedException
	 * @return int
	 */
	public function parseData(): int {// Parse csv file
		$csv_file = $this->config()->getFilePath();

		$csv = new ilCSVReader();
		$csv->setSeparator(",");
		$csv->open($csv_file);
		$rows = $csv->getDataArrayFromCSVFile();
		$csv->close();

		// Map columns
		$columns_map = [
			"OrgUnitId" => "org_unit_id",
			"UserId" => "user_id",
			"Position" => "position"
		];
		$columns = array_map(function ($column) use (&$columns_map) {
			if (isset($columns_map[$column])) {
				return $columns_map[$column];
			} else {
				// Optimal column
				return "";
			}
		}, array_shift($rows));
		foreach ($columns_map as $key => $value) {
			if (!in_array($value, $columns)) {
				// Column missing!
				throw new ParseDataFailedException("Column <b>$key ($value)</b> does not exists in <b>{$csv_file}</b>!");
			}
		}

		// Get data
		foreach ($rows as $rowId => $row) {
			if ($row === [ 0 => "" ]) {
				continue; // Skip empty rows
			}

			$data = new stdClass();

			foreach ($row as $cellI => $cell) {
				if (!isset($columns[$cellI])) {
					// Column missing!
					throw new ParseDataFailedException("<b>Row $rowId, column $cellI</b> does not exists in <b>{$csv_file}</b>!");
				}

				if ($columns[$cellI] != "") { // Skip optimal columns
					$data->{$columns[$cellI]} = $cell;
				}
			}

			$this->data[] = $data;
		}

		return count($this->data);
	}


	/**
	 * Build the hub DTO objects from the parsed data.
	 * An instance of such objects MUST be obtained over the DTOObjectFactory. The factory
	 * is available via $this->factory().
	 *
	 * Example for an origin syncing users:
	 *
	 * $user = $this->factory()->user($data->extId) {   }
	 * $user->setFirstname($data->firstname)
	 *  ->setLastname($data->lastname)
	 *  ->setGender(UserDTO::GENDER_FEMALE) {   }
	 *
	 * Throw a BuildObjectsFailedException to abort the sync at this stage.
	 *
	 * @throws BuildObjectsFailedException
	 * @return IDataTransferObject[]
	 */
	public function buildObjects(): array {
		$org_units = [];

		foreach ($this->data as $data) {
			$org_unit = $this->factory()->orgUnitMembership(intval($data->org_unit_id), intval($data->user_id), intval($data->position));

			$org_unit->setOrgUnitIdType(IOrgUnitMembershipDTO::ORG_UNIT_ID_TYPE_EXTERNAL_EXT_ID);

			$org_units[] = $org_unit;
		}

		return $org_units;
	}


	// HOOKS
	// ------------------------------------------------------------------------------------------------------------

	/**
	 * Called if any exception occurs during processing the ILIAS objects. This hook can be used to
	 * influence the further processing of the current origin sync or the global sync:
	 *
	 * - Throw an AbortOriginSyncException to stop the current sync of this origin.
	 *   Any other following origins in the processing chain are still getting executed normally.
	 * - Throw an AbortOriginSyncOfCurrentTypeException to abort the current sync of the origin AND
	 *   all also skip following syncs from origins of the same object type, e.g. User, Course etc.
	 * - Throw an AbortSyncException to stop the global sync. The sync of any other following
	 * origins in the processing chain is NOT getting executed.
	 *
	 * Note that if you do not throw any of the exceptions above, the sync will continue.
	 *
	 * @param Exception $e
	 */
	public function handleException(Exception $e) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeCreateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterCreateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeUpdateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterUpdateILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function beforeDeleteILIASObject(HookObject $object) { }


	/**
	 * @param HookObject $object
	 */
	public function afterDeleteILIASObject(HookObject $object) { }


	/**
	 * Executed before the synchronization of the origin is executed.
	 */
	public function beforeSync() { }


	/**
	 * Executed after the synchronization of the origin has been executed.
	 */
	public function afterSync() { }
}