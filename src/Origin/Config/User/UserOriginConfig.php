<?php

namespace srag\Plugins\Hub2\Origin\Config\User;

use srag\Plugins\Hub2\Origin\Config\OriginConfig;

/**
 * Class UserOriginConfig
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package srag\Plugins\Hub2\Origin\Config\User
 */
class UserOriginConfig extends OriginConfig implements IUserOriginConfig
{

    /**
     * @var array
     */
    protected $user_data
        = [
            //		'sync_field' => IUserOriginConfig::SYNC_FIELD_NONE,
            self::LOGIN_FIELD => IUserOriginConfig::LOGIN_FIELD_SHORTENED_FIRST_LASTNAME,
            self::LOGIN_KEEP_CASE => false,
        ];


    public function __construct(array $data)
    {
        parent::__construct(array_merge($this->user_data, $data));
    }

    //	/**
    //	 * @inheritdoc
    //	 */
    //	public function getSyncField():int {
    //		return intval($this->get(IUserOriginConfig::SYNC_FIELD_NONE));
    //	}

    /**
     * @inheritdoc
     */
    public function getILIASLoginField() : int
    {
        return intval($this->get(self::LOGIN_FIELD));
    }

    /**
     * @return bool
     */
    public function isKeepCase() : bool
    {
        return (bool) $this->get(self::LOGIN_KEEP_CASE);
    }

    /**
     * @inheritdoc
     */
    public static function getAvailableLoginFields() : array
    {
        return [
            self::LOGIN_FIELD_SHORTENED_FIRST_LASTNAME,
            self::LOGIN_FIELD_EMAIL,
            self::LOGIN_FIELD_EXT_ACCOUNT,
            self::LOGIN_FIELD_EXT_ID,
            self::LOGIN_FIELD_FIRSTNAME_LASTNAME,
            self::LOGIN_FIELD_HUB_LOGIN,
        ];
    }
}
