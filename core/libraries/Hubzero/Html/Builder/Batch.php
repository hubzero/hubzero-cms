<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Builder;

use Lang;

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
			Select::options(Category::options($extension)),
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
			Select::options(ContentLanguage::existing(true, true), 'value', 'text'),
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

		// Get the database object and a new query object.
		$db = \App::get('db');

		// Build the query.
		$query = $db->getQuery()
			->select('a.id', 'value')
			->select('a.name', 'text')
			->from('#__users', 'a')
			->whereEquals('a.block', '0')
			->order('a.name', 'asc');

		// Set the query and load the options.
		$db->setQuery($query->toString());
		$items = $db->loadObjectList();

		// Create the batch selector to select a user on a selection list.
		$lines = array(
			'<label id="batch-user-lbl" for="batch-user" class="hasTip" title="' . Lang::txt('JLIB_HTML_BATCH_USER_LABEL') . '::' . Lang::txt('JLIB_HTML_BATCH_USER_LABEL_DESC') . '">',
			Lang::txt('JLIB_HTML_BATCH_USER_LABEL'),
			'</label>',
			'<select name="batch[user_id]" class="inputbox" id="batch-user-id">',
			'<option value="">' . Lang::txt('JLIB_HTML_BATCH_USER_NOCHANGE') . '</option>',
			$optionNo,
			Select::options($items, 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}
}
