<?php namespace SRAG\Hub2\Object\User;

use SRAG\Hub2\Object\ARObject;

/**
 * Class ARUser
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @package SRAG\ILIAS\Plugins\Hub2\Object
 */
class ARUser extends ARObject implements IUser {

	/**
	 * @inheritdoc
	 */
	public static function returnDbTableName() {
		return 'sr_hub2_user';
	}
}