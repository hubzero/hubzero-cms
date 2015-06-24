<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.language.help');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides a select list of help sites.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6.0
 */
class JFormFieldHelpsite extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6.0
	 */
	public $type = 'Helpsite';

	/**
	 * Method to get the help site field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6.0
	 */
	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		$opts = array();
		if (file_exists(PATH_CORE . '/help/helpsites.xml'))
		{
			$opts = JHelp::createSiteList(PATH_CORE . '/help/helpsites.xml', $this->value);
		}
		else
		{
			$opts[] = JHtml::_(
				'select.option',
				'English (GB) - HUBzero help',
				'http://hubzero.org/documentation/'
			);
		}

		$options = array_merge(parent::getOptions(), $opts);

		return $options;
	}
}
