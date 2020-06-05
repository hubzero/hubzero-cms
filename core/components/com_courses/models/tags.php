<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Components\Tags\Models\Cloud;

require_once \Component::path('com_tags') . DS . 'models' . DS . 'cloud.php';

/**
 * Helper class for handling course tags
 */
class Tags extends Cloud
{
	/**
	 * Object type, used for linking objects (such as resources) to tags
	 *
	 * @var  string
	 */
	protected $_scope = 'courses';

	/**
	 * Turn a string of tags to an array
	 *
	 * @param   string $tag Tag string
	 * @param   string $remove
	 * @return  mixed
	 */
	public function parseTags($tag, $remove='')
	{
		if (is_array($tag))
		{
			$bunch = $tag;
		}
		else
		{
			$bunch = $this->_parse($tag);
		}

		$tags = array();
		if ($remove)
		{
			foreach ($bunch as $t)
			{
				if ($remove == $t)
				{
					continue;
				}
				$tags[] = $t;
			}
		}
		else
		{
			return $bunch;
		}

		return $tags;
	}

	/**
	 * Render a tag cloud
	 *
	 * @param   string  $rtrn    Format to render
	 * @param   array   $filters Filters to apply
	 * @param   boolean $clear   Clear cached data?
	 * @return  string
	 */
	public function render($rtrn='html', $filters=array(), $clear=false)
	{
		switch (strtolower($rtrn))
		{
			case 'string':
				if (!isset($this->_cache['tags_string']) || $clear)
				{
					$tags = array();
					foreach ($this->tags('list', $filters, $clear) as $tag)
					{
						$tags[] = $tag->get('raw_tag');
					}
					$this->_cache['tags_string'] = implode(', ', $tags);
				}
				return $this->_cache['tags_string'];
			break;

			case 'array':
				return $this->tags('list', $filters, $clear);
			break;

			case 'cloud':
			case 'html':
			default:
				if (!isset($this->_cache['tags_cloud']) || $clear)
				{
					$view = new \Hubzero\Component\View(array(
						'base_path' => dirname(__DIR__) . '/site',
						'name'      => 'courses',
						'layout'    => '_tags'
					));
					if (isset($filters['filters']))
					{
						$view->base    = $filters['base'];
						$view->filters = $filters['filters'];
					}
					$view->config = \Component::params('com_tags');
					$view->tags   = $this->tags('list', $filters, $clear);

					$this->_cache['tags_cloud'] = $view->loadTemplate();
				}
				return $this->_cache['tags_cloud'];
			break;
		}
	}
}
