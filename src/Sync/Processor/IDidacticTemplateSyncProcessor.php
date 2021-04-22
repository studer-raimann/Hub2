<?php

namespace srag\Plugins\Hub2\Sync;

use srag\Plugins\Hub2\Object\DTO\IDidacticTemplateAwareDataTransferObject;
use ilObject;

/**
 * Interface IDidacticTemplateSyncProcessor
 * @package srag\Plugins\Hub2\Sync
 * @author Thibeau Fuhrer <thf@studer-raimann.ch>
 */
interface IDidacticTemplateSyncProcessor
{

    /**
     * @param IDidacticTemplateAwareDataTransferObject $dto
     * @param ilObject                                 $ilias_object
     */
    public function handleDidacticTemplate(IDidacticTemplateAwareDataTransferObject $dto, ilObject $ilias_object);
}