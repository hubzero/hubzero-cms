<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;
use Components\Resources\Models\Entry;

/**
 * Wiki macro class that will insert a linked title to a resource
 */
class Resource extends Macro
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
		$txt['wiki'] = 'This macro will insert a linked title to a resource. It can be passed wither an ID or alias.';
		$txt['html'] = '<p>This macro will insert a linked title to a resource. It can be passed wither an ID or alias.</p>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		$this->linkLog = array();

		$et = $this->args;

		if (!$et)
		{
			return '';
		}

		$p = explode(',', $et);
		$resource = array_shift($p);

		$nolink = false;
		$scrnshts = false;
		$num = 1;
		$p = explode(' ', end($p));
		foreach ($p as $a)
		{
			$a = trim($a);

			if (substr($a, 0, 11) == 'screenshots')
			{
				$bits = explode('=', $a);
				$num = intval(end($bits));
				$scrnshts = true;
			}
			elseif ($a == 'nolink')
			{
				$nolink = true;
			}
		}

		require_once \Component::path('com_resources') . '/models/entry.php';

		// Is it numeric?
		if (is_numeric($resource))
		{
			// Yes, then get resource by ID
			$r = Entry::one((int)$resource);
		}
		else
		{
			$r = Entry::oneByAlias(trim($resource));
		}

		// Did we get a result from the database?
		if ($r && $r->get('id'))
		{
			if ($scrnshts)
			{
				return $this->screenshots($r->get('alias'), $num);
			}

			// Build and return the link
			$link = $r->link();

			$this->linkLog[] = array(
				'link'     => '[[Resource(' . $et . ')]]',
				'url'      => $link,
				'page_id'  => $this->pageid,
				'scope'    => 'resource',
				'scope_id' => $r[0]
			);

			if ($nolink)
			{
				return stripslashes($r->get('title'));
			}
			else
			{
				return '<a href="' . \Route::url($link) . '">' . stripslashes($r->get('title')) . '</a>';
			}
		}
		else
		{
			// Return error message
			return '(Resource(' . $et . ') failed)';
		}
	}

	/**
	 * Get a list of screenshots
	 *
	 * @param   string   $alias  Resource alias
	 * @param   integer  $num    Number of screenshots to show
	 * @return  string
	 */
	public function screenshots($alias, $num=1)
	{
		$config = \Component::params('com_resources');
		$path = DS . trim($config->get('toolpath', '/site/tools'), DS);

		$alias = strtolower($alias);
		$d = @dir(PATH_APP . $path . DS . $alias);
		$images = array();

		if ($d)
		{
			while (false !== ($entry = $d->read()))
			{
				if (is_file(PATH_APP . $path . DS . $alias . DS . $entry)
				 && substr($entry, 0, 1) != '.'
				 && strtolower($entry) !== 'index.html')
				{
					if (preg_match("#bmp|gif|jpg|png|swf#i", $entry))
					{
						$images[] = $entry;
					}
				}
			}
			$d->close();
		}
		sort($images);

		$html = '';

		if (count($images) > 0)
		{
			$k = 0;
			for ($i = 0, $n = count($images); $i < $n; $i++)
			{
				$tn = $this->thumbnail($images[$i]);
				$type = explode('.', $images[$i]);

				if (is_file(PATH_APP . $path . DS . $alias . DS . $tn) && $k < $num)
				{
					$k++;

					$html .= '<a rel="lightbox" href="' . $path . DS . $alias . DS . $images[$i].'" title="Screenshot #' . $k . '">';
					$html .= '<img src="' . $path . DS . $alias . DS . $tn . '" alt="Screenshot #' . $k . '" /></a>';
				}
			}
		}

		return $html;
	}

	/**
	 * Generate a thumbnail name from a picture name
	 *
	 * @param   string  $pic  Picture name
	 * @return  string
	 */
	public function thumbnail($pic)
	{
		$pic = explode('.', $pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.', $pic);
		return $tn;
	}
}
