<?php

namespace srag\Plugins\Hub2\Origin\Config\User;

use srag\Plugins\Hub2\Origin\Config\IOriginConfig;

/**
 * Interface IUserOriginConfig
 *
 * @package srag\Plugins\Hub2\Origin\Config\User
 */
interface IUserOriginConfig extends IOriginConfig
{

    //	const SYNC_FIELD_NONE = 1;
    //	const SYNC_FIELD_EMAIL = 2;
    //	const SYNC_FIELD_EXT_ID = 3;

    const LOGIN_FIELD = 'ilias_login_field';
    const LOGIN_FIELD_SHORTENED_FIRST_LASTNAME = 1; // John Doe => j.doe
    const LOGIN_FIELD_EMAIL = 2;
    const LOGIN_FIELD_EXT_ACCOUNT = 3;
    const LOGIN_FIELD_EXT_ID = 4;
    const LOGIN_FIELD_FIRSTNAME_LASTNAME = 5; // John Doe => john.doe
    const LOGIN_FIELD_HUB_LOGIN = 6; // Login is picked from the login property on the UserDTO object
    const LOGIN_KEEP_CASE = 'login_keep_case';
    /**
     * @return int
     */
    //	public function getSyncField():int;

    /**
     * @return int
     */
    public function getILIASLoginField() : int;

    /**
     * @return bool
     */
    public function isKeepCase() : bool;


    /**
     * @return array
     */
    public static function getAvailableLoginFields() : array;
}
