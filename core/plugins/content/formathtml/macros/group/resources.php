<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . '/group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Resources extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var  bool
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
		$txt['html']  = '<p>Displays group resources.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Resources()]]</code></li>
								<li><code>[[Group.Resources(3)]]</code> - Displays the 3 latest group resources</li>
								<li><code>[[Group.Resources(type=teachingmaterials, 5)]]</code> - Displays the 5 latest group teachingmaterials resources.</li>
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

		// Get details
		$type  = $this->_getType($args, 'all');
		$limit = $this->_getLimit($args, 5);
		$class = $this->_getClass($args);

		require_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

		// Get resources
		$groupResources = $this->_getResources($type, $limit);

		$html = '<div class="resources ' . $class . '">';

		foreach ($groupResources as $resource)
		{
			$resourceLink     = \Route::url('index.php?option=com_resources&id=' . $resource->get('id'));
			$resourceTypeLink = \Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=resources&area=' . $resource->type->get('alias'));

			$html .= '<a href="' . $resourceLink . '"><strong>' . $resource->get('title') . '</strong></a>';
			$html .= '<p class="category"> in: <a href="' . $resourceTypeLink . '">' . $resource->type->get('type') . '</a></p>';
			$html .= '<p>' . \Hubzero\Utility\Str::truncate($resource->get('introtext')) . '</p>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get resources for a resource type
	 *
	 * @param   string   $type
	 * @param   integer  $limit
	 * @return  array
	 */
	private function _getResources($type = 'all', $limit = 5)
	{
		// Build query
		$filters = array();
		$filters['now'] = date('Y-m-d H:i:s', time() + 0 * 60 * 60);
		$filters['sortby'] = 'date';
		$filters['group'] = $this->group->get('cn');
		$filters['access'] = 'all';
		$filters['authorized'] = '';
		$filters['select'] = 'records';
		$filters['limit'] = $limit;
		$filters['limitstart'] = 0;

		// Get categories
		$categories = \Components\Resources\Models\Type::getMajorTypes();

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		foreach ($categories as $category)
		{
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $category->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = array();
			$cats[$normalized]['id'] = $category->id;
		}

		// Do we have a type?
		if (in_array($type, array_keys($cats)))
		{
			$filters['type'] = $cats[$type]['id'];
		}

		// Get results
		$query = \Components\Resources\Models\Entry::all()
			->whereEquals('group_owner', $this->group->get('cn'))
			->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED);

		if (isset($filters['type']) && $filters['type'])
		{
			$query->whereEquals('type', $filters['type']);
		}

		$rows = $query
			->limit($filters['limit'])
			->start($filters['limitstart'])
			->order('created', 'desc')
			->rows();

		return $rows;
	}

	/**
	 * Get item limit
	 *
	 * @param   array    $args     Macro Arguments
	 * @param   integer  $default  Default return value
	 * @return  mixed
	 */
	private function _getLimit(&$args, $default = 5)
	{
		foreach ($args as $k => $arg)
		{
			if (is_numeric($arg) && $arg > 0 && $arg < 50)
			{
				$limit = $arg;
				unset($args[$k]);
				return $limit;
			}
		}

		// if we didnt find one return default
		return $default;
	}

	/**
	 * Get class
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getClass(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/class=([\w-]*)/', $arg, $matches))
			{
				$class = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);
				return $class;
			}
		}
	}

	/**
	 * Get type
	 *
	 * @param   array  $args  Macro Arguments
	 * @return  mixed
	 */
	private function _getType(&$args)
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/type=([\w]*)/', $arg, $matches))
			{
				$type = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);
				return $type;
			}
		}
	}
}
