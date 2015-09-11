<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * Form Field Search class.
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
