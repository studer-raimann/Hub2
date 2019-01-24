<?php

namespace srag\Plugins\Hub2\Notification;

use ilHub2Plugin;
use srag\DIC\Hub2\DICTrait;
use srag\Plugins\Hub2\Utils\Hub2Trait;

/**
 * Class OriginNotifications
 *
 * @author  Stefan Wanzenried <sw@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package srag\Plugins\Hub2\Notification
 *
 * @deprecated
 */
class OriginNotifications {

	use DICTrait;
	use Hub2Trait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const PLUGIN_CLASS_NAME = ilHub2Plugin::class;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const CONTEXT_COMMON = 'common';
	/**
	 * Holds all messages of all contexts
	 *
	 * @var array
	 *
	 * @deprecated
	 */
	protected $messages = [];


	/**
	 * Add a new message
	 *
	 * @param string $message
	 * @param string $context
	 *
	 * @return $this
	 *
	 * @deprecated
	 */
	public function addMessage($message, $context = '') {
		$context = ($context) ? $context : self::CONTEXT_COMMON;
		if (!is_array($this->messages[$context])) {
			$this->messages[$context] = [];
		}
		$this->messages[$context][] = $message;

		return $this;
	}


	/**
	 * Get all messages of all contexts. If you provide a $context, only messages of the given
	 * context are returned.
	 *
	 * @param string $context
	 *
	 * @return array
	 *
	 * @deprecated
	 */
	public function getMessages($context = '') {
		return ($context
			&& isset($this->messages[$context])) ? $this->messages[$context] : $this->messages;
	}
}
