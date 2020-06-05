<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Models\Adapters;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for a forum post link for the site-wide forum
 */
class Site extends Base
{
	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_forum',
	);

	/**
	 * Scope title
	 *
	 * @var string
	 */
	protected $_name = 'site';

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string $type   The type of link to return
	 * @param   mixed  $params Optional string or associative array of params to append
	 * @return  string
	 */
	public function build($type='', $params=null)
	{
		$segments = $this->_segments;

		if ($this->get('section'))
		{
			$segments['section'] = $this->get('section');
		}
		if ($this->get('category'))
		{
			$segments['category'] = $this->get('category');
		}
		if ($this->get('thread'))
		{
			$segments['thread'] = $this->get('thread');
		}

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base . '?' . (string) $this->_build($this->_segments);
			break;

			case 'edit':
				if ($this->get('thread'))
				{
					$segments['thread'] = $this->get('post');
				}
				$segments['task'] = 'edit';
			break;

			case 'delete':
				if ($this->get('thread'))
				{
					$segments['thread'] = $this->get('post');
				}
				$segments['task'] = 'delete';
			break;

			case 'new':
			case 'newthread':
				if ($this->get('thread'))
				{
					unset($segments['thread']);
				}
				$segments['task'] = 'new';
			break;

			case 'download':
				$segments['post'] = $this->get('post');
				$segments['file'] = '';
			break;

			case 'reply':
				$segments['reply'] = $this->get('post');
			break;

			case 'anchor':
				if ($this->get('post'))
				{
					$anchor = '#c' . $this->get('post');
				}
			break;

			case 'abuse':
				return 'index.php?option=com_support&task=reportabuse&category=forum&id=' . $this->get('post') . '&parent=' . $this->get('parent');
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
}
