<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group Announcements Macro
 */
class Announcements extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group announcements.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Announcements()]]</code></li>
								<li><code>[[Group.Announcements(3)]]</code> - Displays the next 3 group Announcements</li>
							</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Check if we can render
		if (!parent::canRender())
		{
			return \Lang::txt('[This macro is designed for Groups only]');
		}

		// Get args
		$args = $this->getArgs();

		$filters = array(
			'limit' => (isset($args[0]) && is_numeric($args[0])) ? $args[0] : 3
		);

		$query = \Hubzero\Item\Announcement::all()
			->whereEquals('scope', 'group')
			->whereEquals('scope_id', $this->group->get('gidNumber'))
			->whereEquals('state', \Hubzero\Item\Announcement::STATE_PUBLISHED)
			->where('publish_up', 'IS', null, 'and', 1)
				->orWhere('publish_up', '<=', \Date::toSql(), 1)
				->resetDepth()
			->where('publish_down', 'IS', null, 'and', 1)
				->orWhere('publish_down', '>=', \Date::toSql(), 1)
			->order('created', 'desc')
			->start(0);

		if (isset($filters['limit']))
		{
			$query->limit((int)$filters['limit']);
		}

		$rows = $query->rows();

		// Create the html container
		$html  = '<div class="group-announcements">';

		if ($rows->count() > 0)
		{
			foreach ($rows as $row)
			{
				if (file_exists(\Plugin::path('groups', 'announcements') . '/views/browse/tmpl/item.php'))
				{
					$view = new \Hubzero\Plugin\View(array(
						'folder'  => 'groups',
						'element' => 'announcements',
						'name'    => 'browse',
						'layout'  => 'item'
					));
					$view->set('group', $this->group);
					$view->set('authorized', false);
					$view->set('announcement', $row);
					$view->set('showClose', false);

					$html .= $view->loadTemplate();
				}
				else
				{
					// Build link
					$link = \Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=announcements&id=' . $row->id);

					// Create list
					$html .= '<div class="announcement-container">';
					$html .= '	<div class="announcement">';
					$html .= '		<a class=" title" href="' . $link . '">' . $row->content . '</a>';
					$html .= '		<span class="date">' . $row->published('date') . '</span>';
					$html .= '		<span class="time">' . $row->published('time') . '</span>';
					$html .= '	</div>';
					$html .= '</div>';
				}
			}
		}
		else
		{
			$html .= '<p>Currently there are no announcements. View the <a href="' . \Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=announcements') . '">full archive here</a>.</p>';
		}

		// Close the container
		$html .= '</div>';

		// Return rendered events
		return $html;
	}
}
