<?php

namespace srag\Plugins\Hub2\UI\User;

use ilCheckboxInputGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilTextareaInputGUI;
use ilTextInputGUI;
use srag\Plugins\Hub2\Origin\Config\User\IUserOriginConfig;
use srag\Plugins\Hub2\Origin\Config\User\UserOriginConfig;
use srag\Plugins\Hub2\Origin\Properties\User\UserProperties;
use srag\Plugins\Hub2\Origin\User\ARUserOrigin;
use srag\Plugins\Hub2\UI\OriginConfig\OriginConfigFormGUI;

/**
 * Class UserOriginConfigFormGUI
 *
 * @package srag\Plugins\Hub2\UI\User
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class UserOriginConfigFormGUI extends OriginConfigFormGUI
{

    /**
     * @var ARUserOrigin
     */
    protected $origin;


    /**
     * @inheritdoc
     */
    protected function addSyncConfig()
    {
        parent::addSyncConfig();

        $syncfield = new ilSelectInputGUI(self::plugin()->translate('usr_config_login_field'), $this->conf(IUserOriginConfig::LOGIN_FIELD));
        $options = [];
        foreach (UserOriginConfig::getAvailableLoginFields() as $id) {
            $options[$id] = self::plugin()->translate('usr_config_login_field_' . $id);
        }
        $syncfield->setOptions($options);
        $syncfield->setInfo(self::plugin()->translate('usr_config_login_field_info'));
        $syncfield->setRequired(true);
        $syncfield->setValue($this->origin->config()->getILIASLoginField());
        $this->addItem($syncfield);

        $keep_case = new ilCheckboxInputGUI(self::plugin()->translate('usr_config_login_keep_case'), $this->conf(IUserOriginConfig::LOGIN_KEEP_CASE));
        $keep_case->setInfo(self::plugin()->translate('usr_config_login_keep_case_info'));
        $keep_case->setChecked($this->origin->config()->isKeepCase());
        $this->addItem($keep_case);
    }


    /**
     * @inheritdoc
     */
    protected function addPropertiesNew()
    {
        parent::addPropertiesNew();

        $activate = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_activate_account'), $this->prop(UserProperties::ACTIVATE_ACCOUNT));
        $activate->setChecked($this->origin->properties()->get(UserProperties::ACTIVATE_ACCOUNT));
        $this->addItem($activate);
        //
        $cb = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_create_password'), $this->prop(UserProperties::CREATE_PASSWORD));
        $cb->setChecked($this->origin->properties()->get(UserProperties::CREATE_PASSWORD));
        $this->addItem($cb);
        $send_password = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_send_password'), $this->prop(UserProperties::SEND_PASSWORD));
        $send_password->setChecked($this->origin->properties()->get(UserProperties::SEND_PASSWORD));
        //		$syncfield = new ilSelectInputGUI(self::plugin()->translate('usr_prop_send_password_field'), $this->prop(UserOriginProperties::SEND_PASSWORD_FIELD));
        //		$opt = array('email'            => 'email',
        //		             'external_account' => 'external_account',
        //		             'email_password'   => 'email_password',);
        //		$syncfield->setOptions($opt);
        //		$syncfield->setValue(
        //			$this->origin->properties()
        //				->get(UserOriginProperties::SEND_PASSWORD_FIELD)
        //		);
        //		$activate->addSubItem($syncfield);

        $subject = new ilTextInputGUI(self::plugin()
            ->translate('usr_prop_password_mail_subject'), $this->prop(UserProperties::PASSWORD_MAIL_SUBJECT));
        $subject->setValue($this->origin->properties()->get(UserProperties::PASSWORD_MAIL_SUBJECT));
        $send_password->addSubItem($subject);
        $mail_body = new ilTextareaInputGUI(self::plugin()
            ->translate('usr_prop_password_mail_body'), $this->prop(UserProperties::PASSWORD_MAIL_BODY));
        $mail_body->setInfo(self::plugin()->translate('usr_prop_password_mail_placeholders') . ': [LOGIN], [PASSWORD]');
        $mail_body->setCols(80);
        $mail_body->setRows(15);
        $mail_body->setValue($this->origin->properties()->get(UserProperties::PASSWORD_MAIL_BODY));
        $send_password->addSubItem($mail_body);
        $mail_date_format = new ilTextInputGUI(self::plugin()
            ->translate('usr_prop_password_mail_date_format'), $this->prop(UserProperties::PASSWORD_MAIL_DATE_FORMAT));
        $mail_date_format->setInfo('<a target=\'_blank\' href=\'http://php.net/manual/de/function.date.php\'>' . htmlspecialchars(self::plugin()
                ->translate('usr_prop_password_mail_date_format_info')) . '</a>');
        $mail_date_format->setValue($this->origin->properties()->get(UserProperties::PASSWORD_MAIL_DATE_FORMAT));
        $send_password->addSubItem($mail_date_format);
        $this->addItem($send_password);
    }


    /**
     * @inheritdoc
     */
    protected function addPropertiesUpdate()
    {
        parent::addPropertiesUpdate();

        $activate = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_update_password'), $this->prop(UserProperties::UPDATE_PASSWORD));
        $activate->setInfo(self::plugin()->translate('usr_prop_update_password_info'));
        $activate->setChecked($this->origin->properties()->get(UserProperties::UPDATE_PASSWORD));
        $this->addItem($activate);

        $activate = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_reactivate_account'), $this->prop(UserProperties::REACTIVATE_ACCOUNT));
        $activate->setInfo(self::plugin()->translate('usr_prop_reactivate_account_info'));
        $activate->setChecked($this->origin->properties()->get(UserProperties::REACTIVATE_ACCOUNT));
        $this->addItem($activate);

        $activate = new ilCheckboxInputGUI(self::plugin()->translate('usr_prop_resend_password'), $this->prop(UserProperties::RE_SEND_PASSWORD));
        $activate->setInfo(self::plugin()->translate('usr_prop_resend_password_info'));
        $activate->setChecked($this->origin->properties()->get(UserProperties::RE_SEND_PASSWORD));
        $this->addItem($activate);
    }


    /**
     * @inheritdoc
     */
    protected function addPropertiesDelete()
    {
        parent::addPropertiesDelete();

        $delete = new ilRadioGroupInputGUI(self::plugin()->translate('usr_prop_delete_mode'), $this->prop(UserProperties::DELETE));
        $opt = new ilRadioOption(self::plugin()->translate('usr_prop_delete_mode_none'), UserProperties::DELETE_MODE_NONE);
        $delete->addOption($opt);
        $opt = new ilRadioOption(self::plugin()->translate('usr_prop_delete_mode_inactive'), UserProperties::DELETE_MODE_INACTIVE);
        $delete->addOption($opt);
        $opt = new ilRadioOption(self::plugin()->translate('usr_prop_delete_mode_delete'), UserProperties::DELETE_MODE_DELETE);
        $delete->addOption($opt);
        $delete->setValue($this->origin->properties()->get(UserProperties::DELETE));
        $this->addItem($delete);
    }
}
