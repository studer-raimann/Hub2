<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\Plugins\Hub2\Config\ArConfig;
use srag\Plugins\Hub2\Config\ArConfigOld;
use srag\Plugins\Hub2\Jobs\Log\DeleteOldLogsJob;
use srag\Plugins\Hub2\Jobs\RunSync;
use srag\Plugins\Hub2\Log\Log;
use srag\Plugins\Hub2\Menu\Menu;
use srag\Plugins\Hub2\Object\Category\ARCategory;
use srag\Plugins\Hub2\Object\CompetenceManagement\ARCompetenceManagement;
use srag\Plugins\Hub2\Object\Course\ARCourse;
use srag\Plugins\Hub2\Object\CourseMembership\ARCourseMembership;
use srag\Plugins\Hub2\Object\Group\ARGroup;
use srag\Plugins\Hub2\Object\GroupMembership\ARGroupMembership;
use srag\Plugins\Hub2\Object\OrgUnit\AROrgUnit;
use srag\Plugins\Hub2\Object\OrgUnitMembership\AROrgUnitMembership;
use srag\Plugins\Hub2\Object\Session\ARSession;
use srag\Plugins\Hub2\Object\SessionMembership\ARSessionMembership;
use srag\Plugins\Hub2\Object\User\ARUser;
use srag\Plugins\Hub2\Origin\User\ARUserOrigin;
use srag\Plugins\Hub2\Utils\Hub2Trait;
use srag\RemovePluginDataConfirm\Hub2\PluginUninstallTrait;

/**
 * Class ilHub2Plugin
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class ilHub2Plugin extends ilCronHookPlugin
{

    use PluginUninstallTrait;
    use Hub2Trait;
    const PLUGIN_ID = 'hub2';
    const PLUGIN_NAME = 'Hub2';
    const PLUGIN_CLASS_NAME = self::class;
    const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = hub2RemoveDataConfirm::class;
    /**
     * @var self
     */
    protected static $instance;


    /**
     * @return string
     */
    public function getPluginName() : string
    {
        return self::PLUGIN_NAME;
    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->provider_collection->setMainBarProvider(new Menu(self::dic()->dic(), $this));
    }

    /**
     * @return ilCronJob[]
     */
    public function getCronJobInstances() : array
    {
        return [new RunSync(), new DeleteOldLogsJob()];
    }


    /**
     * @param string $a_job_id
     *
     * @return ilCronJob
     */
    public function getCronJobInstance(/*string*/
        $a_job_id
    )/*: ?ilCronJob*/
    {
        switch ($a_job_id) {
            case RunSync::CRON_JOB_ID:
                return new RunSync();

            case DeleteOldLogsJob::CRON_JOB_ID:
                return new DeleteOldLogsJob();

            default:
                return null;
        }
    }

    /**
     * @inheritdoc
     */
    protected function deleteData()/*: void*/
    {
        self::dic()->database()->dropTable(ARUserOrigin::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARUser::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARCourse::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARCourseMembership::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARCategory::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARSession::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARGroup::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARGroupMembership::TABLE_NAME, false);
        self::dic()->database()->dropTable(ARSessionMembership::TABLE_NAME, false);
        self::dic()->database()->dropTable(ArConfig::TABLE_NAME, false);
        self::dic()->database()->dropTable(ArConfigOld::TABLE_NAME, false);
        self::dic()->database()->dropTable(AROrgUnit::TABLE_NAME, false);
        self::dic()->database()->dropTable(AROrgUnitMembership::TABLE_NAME, false);
        self::dic()->database()->dropTable(Log::TABLE_NAME, false);
        self::dic()->database()->dropAutoIncrementTable(Log::TABLE_NAME);
        self::dic()->database()->dropTable(ARCompetenceManagement::TABLE_NAME, false);

        ilUtil::delDir(ILIAS_DATA_DIR . "/hub/");
    }
}
