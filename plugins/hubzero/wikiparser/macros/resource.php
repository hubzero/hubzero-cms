<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourceMacro'
 * 
 * Long description (if any) ...
 */
class ResourceMacro extends WikiMacro
{

	/**
	 * Short description for 'description'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = 'This macro will insert a linked title to a resource. It can be passed wither an ID or alias.';
		$txt['html'] = '<p>This macro will insert a linked title to a resource. It can be passed wither an ID or alias.</p>';
		return $txt['html'];
	}

	/**
	 * Short description for 'render'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     mixed Return description (if any) ...
	 */
	public function render()
	{
		$et = $this->args;

		if (!$et) {
			return '';
		}

		$p = preg_split('#,#', $et);
		$resource = array_shift($p);

		$nolink = false;
		$scrnshts = false;
		$num = 1;
		$p = explode(' ',end($p));
		foreach ($p as $a)
		{
			$a = trim($a);

			if (substr($a,0,11) == 'screenshots') {
				$bits = explode('=', $a);
				$num = intval(end($bits));
				$scrnshts = true;
			} elseif ($a == 'nolink') {
				$nolink = true;
			}
		}

		// Is it numeric?
		if (is_numeric($resource)) {
			// Yes, then get resource by ID
			$id = intval($resource);
			$sql = "SELECT id, title, alias FROM #__resources WHERE id=".$id;
		} else {
			// No, get resource by alias
			$sql = "SELECT id, title, alias FROM #__resources WHERE alias='".trim($resource)."'";
		}

		// Perform query
		$this->_db->setQuery( $sql );
		$r = $this->_db->loadRow();

		// Did we get a result from the database?
		if ($r) {
			if ($scrnshts && $r[2]) {
				return $this->screenshots( $r[2], $num );
			}

			// Build and return the link
			if ($r[2]) {
				$link = 'index.php?option=com_resources&amp;alias='.$r[2];
			} else {
				$link = 'index.php?option=com_resources&amp;id='.$id;
			}

			if ($nolink) {
				return stripslashes($r[1]);
			} else {
				//return '['.JRoute::_( $link ).' '.stripslashes($r[1]).']';
				return '<a href="'.JRoute::_( $link ).'">'.stripslashes($r[1]).'</a>';
			}
		} else {
			// Return error message
			return '(Resource('.$et.') failed)';
		}
	}

	/**
	 * Short description for 'screenshots'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $alias Parameter description (if any) ...
	 * @param      integer $num Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function screenshots( $alias, $num=1 )
	{
		$config =& JComponentHelper::getParams( 'com_resources' );
		$path = $config->get('toolpath');

		$alias = strtolower($alias);
		$d = @dir(JPATH_ROOT.$path.DS.$alias);
		$images = array();

		if ($d) {
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT.$path.DS.$alias.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (preg_match("#bmp|gif|jpg|png|swf#i", $img_file )) {
						$images[] = $img_file;
					}
				}
			}
			$d->close();
		}
		sort($images);

		$html = '';

		if (count($images) > 0) {
			$k = 0;
			for ($i=0, $n=count($images); $i < $n; $i++)
			{
				$tn = $this->thumbnail($images[$i]);
				$type = explode('.',$images[$i]);

				if (is_file(JPATH_ROOT.$path.DS.$alias.DS.$tn) && $k < $num) {
					$k++;

					$html .= '<a rel="lightbox" href="'.$path.DS.$alias.DS.$images[$i].'" title="Screenshot #'.$k.'">';
					$html .= '<img src="'.$path.DS.$alias.DS.$tn.'" alt="Screenshot #'.$k.'" /></a>'.n;
				}
			}
		}

		return $html;
	}

	/**
	 * Short description for 'thumbnail'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $pic Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function thumbnail($pic)
	{
		$pic = explode('.',$pic);
		$n = count($pic);
		$pic[$n-2] .= '-tn';
		$end = array_pop($pic);
		$pic[] = 'gif';
		$tn = implode('.',$pic);
		return $tn;
	}
}

