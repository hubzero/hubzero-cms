<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models\Adapters;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for site-wide wiki
 */
class Site extends Base
{
	/**
	 * URL segments
	 *
	 * @var  array
	 */
	protected $_segments = array(
		'option' => 'com_wiki',
	);

	/**
	 * Constructor
	 *
	 * @param   string   $pagename
	 * @param   string   $path
	 * @param   integer  $scope_id
	 * @return  void
	 */
	public function __construct($pagename=null, $path=null, $scope_id=null)
	{
		$pagename = ($path ? $path . '/' : '') . $pagename;

		$this->_segments['pagename'] = $pagename;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		$segments = $this->_segments;

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				unset($segments['pagename']);
				return $this->_base . '?' . (string) $this->_build($segments);
			break;

			case 'pdf':
			case 'new':
			case 'rename':
			case 'edit':
			case 'delete':
			case 'history':
			case 'compare':
			case 'approve':
			case 'comments':
			case 'deleterevision':
			case 'addcomment':
			case 'setcurrentrevision':
				$segments['task'] = $type;
			break;

			case 'permalink':
			default:

			break;
		}

		if (is_string($params))
		{
			$params = str_replace('&amp;', '&', $params);

			if (substr($params, 0, 1) == '#')
			{
				$anchor = $params;
			}
			else
			{
				if (substr($params, 0, 1) == '?')
				{
					$params = substr($params, 1);
				}
				parse_str($params, $parsed);
				$params = $parsed;
			}
		}

		$segments = array_merge($segments, (array) $params);

		return $this->_base . '?' . (string) $this->_build($segments) . (string) $anchor;
	}

	/**
	 * Get an array of routing inputs
	 *
	 * @param   string  $task
	 * @return  array
	 */
	public function routing($task='save')
	{
		return array(
			'option' => $this->_segments['option'],
			'task'   => $task
		);
	}
}
