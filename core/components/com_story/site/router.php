<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing for com_story
 *
 * A story's address carries the day it ran:
 *
 *     /story/2026/09/13/a-slug
 *
 * which is what this format is known for, and which makes the archive fall
 * out of the same parse rather than needing routes of its own:
 *
 *     /story/2026/09        an archive month
 *     /story/section/news   a section front page
 *     /story/topic/linux    a topic
 */
class Router extends Base
{
	/**
	 * Build the route for the component
	 *
	 * @param   array  &$query
	 * @return  array
	 */
	public function build(&$query)
	{
		$segments = array();

		// A discussion is addressed by id rather than by slug: two stories on
		// one day can carry the same conversation title, and the id is what
		// the comment anchors are already written against.
		if (!empty($query['view']) && $query['view'] == 'comments')
		{
			$segments[] = 'comments';
			unset($query['view']);

			if (!empty($query['discussion']))
			{
				$segments[] = $query['discussion'];
				unset($query['discussion']);
			}

			if (!empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}

			return $segments;
		}

		if (!empty($query['view']) && in_array($query['view'], array('sections', 'topics')))
		{
			$segments[] = ($query['view'] == 'sections') ? 'section' : 'topic';
			unset($query['view']);
		}

		if (!empty($query['section']))
		{
			$segments[] = $query['section'];
			unset($query['section']);
		}

		if (!empty($query['topic']))
		{
			$segments[] = $query['topic'];
			unset($query['topic']);
		}

		if (!empty($query['year']))
		{
			$segments[] = $query['year'];
			unset($query['year']);

			if (!empty($query['month']))
			{
				$segments[] = $query['month'];
				unset($query['month']);

				if (!empty($query['day']))
				{
					$segments[] = $query['day'];
					unset($query['day']);
				}
			}
		}

		if (!empty($query['story']))
		{
			$segments[] = $query['story'];
			unset($query['story']);
		}

		if (!empty($query['task']))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL
	 *
	 * @param   array  &$segments
	 * @return  array
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (empty($segments))
		{
			return $vars;
		}

		// A leading word names a view; a leading number starts a date.
		if ($segments[0] === 'section')
		{
			array_shift($segments);
			$vars['view'] = 'sections';

			if (!empty($segments[0]))
			{
				$vars['section'] = array_shift($segments);
			}

			return $vars;
		}

		if ($segments[0] === 'comments')
		{
			array_shift($segments);
			$vars['view'] = 'comments';

			if (!empty($segments[0]) && preg_match('/^\d+$/', $segments[0]))
			{
				$vars['discussion'] = (int) array_shift($segments);
			}

			if (!empty($segments[0]))
			{
				$vars['task'] = array_shift($segments);
			}

			return $vars;
		}

		if ($segments[0] === 'topic')
		{
			array_shift($segments);
			$vars['view'] = 'topics';

			if (!empty($segments[0]))
			{
				$vars['topic'] = array_shift($segments);
			}

			return $vars;
		}

		if (preg_match('/^\d{4}$/', $segments[0]))
		{
			$vars['year'] = (int) array_shift($segments);

			if (!empty($segments[0]) && preg_match('/^\d{1,2}$/', $segments[0]))
			{
				$vars['month'] = (int) array_shift($segments);
			}

			if (!empty($segments[0]) && preg_match('/^\d{1,2}$/', $segments[0]))
			{
				$vars['day'] = (int) array_shift($segments);
			}

			// Anything left after a full date is the story itself; a partial
			// date with nothing after it is an archive.
			if (!empty($segments[0]))
			{
				$vars['story'] = array_shift($segments);
				$vars['view']  = 'stories';
				$vars['task']  = 'article';
			}
			else
			{
				$vars['view'] = 'archive';
			}

			return $vars;
		}

		$vars['task'] = array_shift($segments);

		return $vars;
	}
}
