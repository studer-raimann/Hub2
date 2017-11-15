<?php

namespace SRAG\Plugins\Hub2\Metadata\Implementation;

/**
 * Class CustomMetadata
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class CustomMetadata extends AbstractImplementation implements IMetadataImplementation {

	/**
	 * @inheritDoc
	 */
	public function write() {
		$id = $this->getMetadata()->getIdentifier();
		$values = new \ilAdvancedMDValues(1, $this->getIliasId(), null, "-");

		$ilADTGroup = $values->getADTGroup();
		$value = $this->getMetadata()->getValue();
		$ilADT = $ilADTGroup->getElement($id);
		switch (true) {
			case ($ilADT instanceof \ilADTText):
				$ilADT->setText($value);
				break;
			case ($ilADT instanceof \ilADTDate):
				$ilADT->setDate(new \ilDate(time(), 3));
				break;
		}
	}


	/**
	 * @inheritDoc
	 */
	public function read() {
		// TODO: Implement read() method.
	}
}
