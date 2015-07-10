<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Supports a list of installed application languages
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @see         JFormFieldContentLanguage for a select list of content languages.
 * @since       11.1
 */
class JFormFieldLanguage extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Language';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Initialize some field attributes.
		$client = (string) $this->element['client'];
		if ($client != 'site' && $client != 'administrator')
		{
			$client = 'site';
		}

		$path = PATH_APP . DS . 'bootstrap' . DS . $client;
		if (!is_dir($path))
		{
			$path = PATH_CORE . DS . 'bootstrap' . DS . $client;
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(
			parent::getOptions(),
			JLanguageHelper::createLanguageList($this->value, $path, true, true)
		);

		return $options;
	}
}
