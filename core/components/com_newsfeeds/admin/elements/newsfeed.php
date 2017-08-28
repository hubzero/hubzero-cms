<?php
/**
 * @package		Joomla.Administrator
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Renders a newsfeed selection element
 *
 * @package		Joomla.Administrator
 * @subpackage	com_newsfeeds
 * @deprecated	Parameter is deprecated and will be removed in a future version. Use JForm instead.
 * @since		1.5
 */

class JElementNewsfeed extends JElement
{
	/**
	 * Element name
	 *
	 * @var		string
	 */
	protected	$_name = 'Newsfeed';

	/**
	 * Fetch elements
	 *
	 * @param  string   $name
	 * @param  unknown  $value
	 * @param  object   &$node
	 * @param  string   $control_name
	 * @param  object
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = App::get('db');

		$query = 'SELECT a.id, c.title, a.name'
		. ' FROM #__newsfeeds AS a'
		. ' INNER JOIN #__categories AS c ON a.catid = c.id'
		. ' WHERE a.published = 1'
		. ' AND c.published = 1'
		. ' ORDER BY a.catid, a.name'
		;
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$n = count($options);
		for ($i = 0; $i < $n; $i++)
		{
			$options[$i]->text = $options[$i]->title . '-' . $options[$i]->name;
		}

		array_unshift($options, Html::select('option', '0', '- '.Lang::txt('COM_NEWSFEEDS_SELECT_FEED').' -', 'id', 'text'));

		return Html::select('genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'id', 'text', $value, $control_name.$name);
	}
}
