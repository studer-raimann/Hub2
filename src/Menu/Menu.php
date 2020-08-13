<?php

namespace srag\Plugins\Hub2\Menu;

use hub2MainGUI;
use ilAdministrationGUI;
use ilHub2ConfigGUI;
use ilHub2Plugin;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuPluginProvider;
use ilObjComponentSettingsGUI;
use srag\DIC\Hub2\DICTrait;
use srag\Plugins\Hub2\Utils\Hub2Trait;

/**
 * Class Menu
 * @package srag\Plugins\Hub2\Menu
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticMainMenuPluginProvider
{

    use DICTrait;
    use Hub2Trait;

    const PLUGIN_CLASS_NAME = ilHub2Plugin::class;

    /**
     * @inheritdoc
     */
    public function getStaticTopItems() : array
    {
        return [
            $this->mainmenu->topParentItem($this->if->identifier(ilHub2Plugin::PLUGIN_ID . "_top"))->withTitle(ilHub2Plugin::PLUGIN_NAME)
                ->withAvailableCallable(function () : bool {
                    return $this->plugin->isActive();
                })->withVisibilityCallable(function () : bool {
                    return self::dic()->rbacreview()->isAssigned(self::dic()->user()->getId(), 2); // Default admin role
                })
        ];
    }

    /**
     * @inheritdoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];
        
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "ref_id", SYSTEM_FOLDER_ID ?? 31);
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "ctype", IL_COMP_SERVICE);
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "cname", "Cron");
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "slot_id", "crnhk");
        self::dic()->ctrl()->setParameterByClass(ilHub2ConfigGUI::class, "pname", ilHub2Plugin::PLUGIN_NAME);

        return [
            $this->mainmenu->link($this->if->identifier(ilHub2Plugin::PLUGIN_ID . "_configuration"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(ilHub2Plugin::PLUGIN_NAME)
                ->withAction(self::dic()->ctrl()->getLinkTargetByClass([
                    ilAdministrationGUI::class,
                    ilObjComponentSettingsGUI::class,
                    ilHub2ConfigGUI::class
                ], hub2MainGUI::CMD_INDEX))
                ->withAvailableCallable(function () : bool {
                    return $this->plugin->isActive();
                })->withVisibilityCallable(function () : bool {
                    return self::dic()->rbacreview()->isAssigned(self::dic()->user()->getId(), 2); // Default admin role
                })
        ];
    }
}
