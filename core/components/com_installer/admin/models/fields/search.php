<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Form Field Search class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.6
 */
class JFormFieldSearch extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Search';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$html = '';
		$html.= '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value) . '" title="' . Lang::txt('JSEARCH_FILTER') . '" onchange="this.form.submit();" />';
		$html.= '<button type="submit" class="btn">' . Lang::txt('JSEARCH_FILTER_SUBMIT') . '</button>';
		$html.= '<button type="button" class="btn" onclick="$(\'#' . $this->id . '\').val(\'\');this.form.submit();">' . Lang::txt('JSEARCH_FILTER_CLEAR') . '</button>';
		return $html;
	}
}
