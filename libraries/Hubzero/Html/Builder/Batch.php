<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html\Builder;

/**
 * Extended Utility class for batch processing widgets.
 */
class Batch
{
	/**
	 * Display a batch widget for the access level selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function access()
	{
		// Create the batch selector to change an access level on a selection list.
		$lines = array(
			'<label id="batch-access-lbl" for="batch-access" class="hasTip" title="' . Lang::txt('JLIB_HTML_BATCH_ACCESS_LABEL') . '::' . Lang::txt('JLIB_HTML_BATCH_ACCESS_LABEL_DESC') . '">',
			Lang::txt('JLIB_HTML_BATCH_ACCESS_LABEL'),
			'</label>',
			Access::assetgrouplist(
				'batch[assetgroup_id]', '',
				'class="inputbox"',
				array(
					'title' => Lang::txt('JLIB_HTML_BATCH_NOCHANGE'),
					'id'    => 'batch-access'
				)
			)
		);

		return implode("\n", $lines);
	}

	/**
	 * Displays a batch widget for moving or copying items.
	 *
	 * @param   string  $extension  The extension that owns the category.
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function item($extension)
	{
		// Create the copy/move options.
		$options = array(
			Select::option('c', Lang::txt('JLIB_HTML_BATCH_COPY')),
			Select::option('m', Lang::txt('JLIB_HTML_BATCH_MOVE'))
		);

		// Create the batch selector to change select the category by which to move or copy.
		$lines = array(
			'<label id="batch-choose-action-lbl" for="batch-choose-action">',
			Lang::txt('JLIB_HTML_BATCH_MENU_LABEL'),
			'</label>',
			'<fieldset id="batch-choose-action" class="combo">',
			'<select name="batch[category_id]" class="inputbox" id="batch-category-id">',
			'<option value="">' . Lang::txt('JSELECT') . '</option>',
			Select::options(\JHtml::_('category.options', $extension)),
			'</select>',
			Select::radiolist($options, 'batch[move_copy]', '', 'value', 'text', 'm'),
			'</fieldset>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Display a batch widget for the language selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 */
	public static function language()
	{
		// Create the batch selector to change the language on a selection list.
		$lines = array(
			'<label id="batch-language-lbl" for="batch-language" class="hasTip" title="' . Lang::txt('JLIB_HTML_BATCH_LANGUAGE_LABEL') . '::' . Lang::txt('JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC') . '">',
			Lang::txt('JLIB_HTML_BATCH_LANGUAGE_LABEL'),
			'</label>',
			'<select name="batch[language_id]" class="inputbox" id="batch-language-id">',
			'<option value="">' . Lang::txt('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE') . '</option>',
			Select::options(\JHtml::_('contentlanguage.existing', true, true), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Display a batch widget for the user selector.
	 *
	 * @param   boolean  $noUser  Choose to display a "no user" option
	 * @return  string   The necessary HTML for the widget.
	 */
	public static function user($noUser = true)
	{
		$optionNo = '';
		if ($noUser)
		{
			$optionNo = '<option value="0">' . Lang::txt('JLIB_HTML_BATCH_USER_NOUSER') . '</option>';
		}

		// Create the batch selector to select a user on a selection list.
		$lines = array(
			'<label id="batch-user-lbl" for="batch-user" class="hasTip" title="' . Lang::txt('JLIB_HTML_BATCH_USER_LABEL') . '::' . Lang::txt('JLIB_HTML_BATCH_USER_LABEL_DESC') . '">',
			Lang::txt('JLIB_HTML_BATCH_USER_LABEL'),
			'</label>',
			'<select name="batch[user_id]" class="inputbox" id="batch-user-id">',
			'<option value="">' . Lang::txt('JLIB_HTML_BATCH_USER_NOCHANGE') . '</option>',
			$optionNo,
			Select::options(\JHtml::_('user.userlist'), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}
}
