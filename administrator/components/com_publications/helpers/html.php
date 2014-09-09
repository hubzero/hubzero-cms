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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'ResourcesHtml'
 *
 * Long description (if any) ...
 */
class PublicationsAdminHtml
{

	/**
	 * Short description for 'error'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'warning'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'alert'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	/**
	 * Short description for 'statusKey'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public static function statusKey()
	{
		?>
		<br />
			<ul class="key">
				<li class="draft"><?php echo JText::_('COM_PUBLICATIONS_VERSION_DRAFT'); ?></li>
				<li class="ready"><?php echo JText::_('COM_PUBLICATIONS_VERSION_READY'); ?></li>
				<li class="new"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PENDING'); ?></li>
				<li class="preserving"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PRESERVING'); ?></li>
				<li class="wip"><?php echo JText::_('COM_PUBLICATIONS_VERSION_WIP'); ?></li>
				<li class="published"><?php echo JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED'); ?></li>
				<li class="unpublished"><?php echo JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED'); ?></li>
			</ul>
		<?php
	}

	/**
	 * Short description for 'parseTag'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0)
		{
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		}
		else
		{
			$match = '';
		}
		return $match;
	}

	/**
	 * Short description for 'build_path'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $date Parameter description (if any) ...
	 * @param      unknown $pid Parameter description (if any) ...
	 * @param      unknown $vid Parameter description (if any) ...
	 * @param      string $base Parameter description (if any) ...
	 * @param      unknown $filedir Parameter description (if any) ...
	 * @param      unknown $root Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */

	public static function buildPath(
		$pid = NULL,
		$vid = NULL,
		$base = '',
		$filedir = '',
		$root = 0
	)
	{
		if ($vid === NULL or $pid === NULL )
		{
			return false;
		}
		if (!$base)
		{
			$pubconfig = JComponentHelper::getParams( 'com_publications' );
			$base = $pubconfig->get('webpath');
		}

		$base = DS . trim($base, DS);
		$pub_dir =  \Hubzero\Utility\String::pad( $pid );
		$version_dir =  \Hubzero\Utility\String::pad( $vid );
		$path = $base . DS . $pub_dir . DS . $version_dir;
		$path = $filedir ? $path . DS . $filedir : $path;
		$path = $root ? JPATH_ROOT . $path : $path;

		return $path;
	}

	/**
	 * Short description for 'writeRating'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $rating Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function writeRating( $rating )
	{
		switch ($rating)
		{
			case 0.5: $class = ' half';      break;
			case 1:   $class = ' one';       break;
			case 1.5: $class = ' onehalf';   break;
			case 2:   $class = ' two';       break;
			case 2.5: $class = ' twohalf';   break;
			case 3:   $class = ' three';     break;
			case 3.5: $class = ' threehalf'; break;
			case 4:   $class = ' four';      break;
			case 4.5: $class = ' fourhalf';  break;
			case 5:   $class = ' five';      break;
			case 0:
			default:  $class = ' none';      break;
		}

		return '<p class="avgrating' . $class . '"><span>Rating: ' . $rating.' out of 5 stars</span></p>';
	}

	//----------------------------------------------------------
	// Form <select> builders
	//----------------------------------------------------------

	/**
	 * Short description for 'selectAccess'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $as Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectAccess($as, $value)
	{
		$as = explode(',',$as);
		$html  = '<select name="access">'."\n";
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			$html .= "\t".'<option value="'.$i.'"';
			if ($value == $i)
			{
				$html .= ' selected="selected"';
			}
			$html .= '>'.trim($as[$i]).'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * License selection
	 *
	 * @param      array $licenses Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectLicense($licenses, $selected)
	{
		$value = $selected ? $selected->id : 1;
		$html  = '<select name="license_type" id="license_type">'."\n";
		foreach ($licenses as $license)
		{
			$html .= "\t".'<option value="' . $license->id . '"';
			if ($value == $license->id)
			{
				$html .= ' selected="selected"';
			}
			$html .= '>' . trim($license->name) . '</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Project selection
	 *
	 * @param      array  $projects
	 * @return     string Return description (if any) ...
	 */
	public static function selectProjects($projects)
	{
		$html  = '<select name="projectid" id="projectid">'."\n";
		$html .= "\t".'<option value="0" selected="selected">' . JText::_('COM_PUBLICATIONS_SELECT_PROJECT') . '</option>';
		foreach ($projects as $project)
		{
			$html .= "\t".'<option value="' . $project->id . '"';
			$html .= '>' . trim($project->title) . ' (' . $project->alias . ')</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectGroup'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $groups Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectGroup($groups, $value, $groupOwner)
	{
		$html  = '<select name="group_owner"';
		if (!$groups || $groupOwner) {
			$html .= ' disabled="disabled"';
		}
		$html .= ' style="max-width: 15em;">'."\n";
		$html .= ' <option value="">'.JText::_('Select group ...').'</option>'."\n";
		if ($groups)
		{
			foreach ($groups as $group)
			{
				$html .= ' <option value="' . $group->gidNumber . '"';
				if ($value == $group->gidNumber)
				{
					$html .= ' selected="selected"';
				}
				$html .= '>'.$group->description .'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectSection'
	 *
	 * Long description (if any) ...
	 *
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      integer $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectSection($name, $array, $value, $class='', $id)
	{
		$html  = '<select name="'.$name.'" id="'.$name.'" onchange="return listItemTask(\'cb'. $id .'\',\'regroup\')"';
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		$html .= ' <option value="0"';
		$html .= ($id == $value || $value == 0) ? ' selected="selected"' : '';
		$html .= '>'.JText::_('[ none ]').'</option>'."\n";
		foreach ($array as $anode)
		{
			$selected = ($anode->id == $value || $anode->name == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$anode->id.'"'.$selected.'>'.$anode->name.'</option>'."\n";
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectCategory'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $arr Parameter description (if any) ...
	 * @param      string $name Parameter description (if any) ...
	 * @param      mixed $value Parameter description (if any) ...
	 * @param      string $shownone Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @param      string $js Parameter description (if any) ...
	 * @param      string $skip Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectCategory($arr, $name, $value='', $shownone='', $class='', $js='', $skip='')
	{
		$html  = '<select name="'.$name.'" id="'.$name.'"'.$js;
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		if ($shownone != '')
		{
			$html .= "\t".'<option value=""';
			$html .= ($value == 0 || $value == '') ? ' selected="selected"' : '';
			$html .= '>'.$shownone.'</option>'."\n";
		}
		if ($skip)
		{
			$skips = explode(',',$skip);
		}
		else
		{
			$skips = array();
		}
		foreach ($arr as $anode)
		{
			if (!in_array($anode->id, $skips))
			{
				$selected = ($value && ($anode->id == $value || $anode->name == $value))
					  ? ' selected="selected"'
					  : '';
				$html .= "\t".'<option value="'.$anode->id.'"'.$selected.'>'.$anode->name.'</option>'."\n";
			}
		}
		$html .= '</select>'."\n";
		return $html;
	}

	/**
	 * Short description for 'selectAuthorsNoEdit'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $authnames Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function selectAuthorsNoEdit($authnames, $option)
	{
		$authIDs = array();
		$html = '';
		$i = 1;
		if ($authnames != NULL)
		{
			$html = '<ul id="author-list">'."\n";
			foreach ($authnames as $authname)
			{
				$authIDs[] = $authname->id;
				$name = $authname->name;

				$org = ($authname->organization)
					? htmlentities($authname->organization,ENT_COMPAT,'UTF-8') : '';
				$credit = ($authname->credit)
					? htmlentities($authname->credit,ENT_COMPAT,'UTF-8') : '';
				$userid = $authname->user_id ? $authname->user_id : 'unregistered';

				$html .= "\t".'<li id="author_'.$authname->id.'">'
					. $i . '. ' . $name . ' (' . $userid . ')';
				$html .= $org ? ' - <span class="org">' . $org . '</span>' : '';
				$html .= ' <a class="editauthor" href="index.php?option=' . $option . '&controller=items&task=editauthor&author=' . $authname->id .'" >' . JText::_('COM_PUBLICATIONS_EDIT') . '</a> ';
				$html .= ' <a class="editauthor" href="index.php?option=' . $option . '&controller=items&task=deleteauthor&aid=' . $authname->id .'"  > ' . JText::_('COM_PUBLICATIONS_DELETE') . '</a> ';
				if ($credit)
				{
					$html .= '<br />' . JText::_('COM_PUBLICATIONS_CREDIT') . ': ' . $credit;
				}
				$html .= '</li>' . "\n";
				$i++;
			}
			$html.= '</ul>';
		}
		else
		{
			$html.= '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_AUTHORS') . '</p>';
		}
		return $html;
	}

	/**
	 * Select content
	 *
	 * @param      object $pub
	 * @param      string $option
	 * @return     string HTML
	 */
	public static function selectContent($pub, $option, $useBlocks = false, $database)
	{
		if (!$pub->_attachments)
		{
			return '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
		}
		$html 	= '';
		$prime  = $pub->_attachments[1];
		$second = $pub->_attachments[2];

		if ($useBlocks && isset($pub->_curationModel))
		{
			$prime    = $pub->_curationModel->getElements(1);
			$second   = $pub->_curationModel->getElements(2);
			$gallery  = $pub->_curationModel->getElements(3);

			// Get attachment type model
			$attModel = new PublicationsModelAttachments($database);

			// Draw list of primary elements
			$html .= '<h5>' . JText::_('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
			$list  = $attModel->listItems(
				$prime,
				$pub,
				true
			);
			$html .= $list ? $list : '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

			// Draw list of secondary elements
			$html .= '<h5>' . JText::_('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
			$list  = $attModel->listItems(
				$second,
				$pub,
				'administrator'
			);
			$html .= $list ? $list : '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';

			// Draw list of gallery elements
			$html .= '<h5>' . JText::_('COM_PUBLICATIONS_GALLERY') . '</h5>';
			$list  = $attModel->listItems(
				$gallery,
				$pub,
				'administrator'
			);
			$html .= $list ? $list : '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
		}
		else
		{
			$html .= '<h5>' . JText::_('COM_PUBLICATIONS_PRIMARY_CONTENT') . '</h5>';
			if ($prime)
			{
				$html .= '<ul class="content-list">';
				foreach ($prime as $att)
				{
					$type = $att->type;
					if ($att->type == 'file')
					{
						$ext  = explode('.', $att->path);
						$type = strtoupper(end($ext));
					}
					$title = $att->title ? $att->title : $att->path;
					$html .= '<li>('.$type.') ';
					$html .= $att->title ? $att->title : $att->path;
					$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
					$html .= '</li>'."\n";
				}
				$html .= '</ul>';
			}
			else
			{
				$html .= '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
			}
			$html .= '<h5>' . JText::_('COM_PUBLICATIONS_SUPPORTING_CONTENT') . '</h5>';
			if ($second)
			{
				$html .= '<ul class="content-list">';
				foreach ($second as $att)
				{
					$type = $att->type;
					if ($att->type == 'file')
					{
						$ext  = explode('.', $att->path);
						$type = strtoupper(end($ext));
					}
					$title = $att->title ? $att->title : $att->path;
					$html .= '<li>('.$type.') ';
					$html .= $att->title ? $att->title : $att->path;
					$html .= $att->title != $att->path ? '<br /><span class="ctitle">' . $att->path.'</span>' : '';
					$html .= '</li>'."\n";
				}
				$html .= '</ul>';
			}
			else
			{
				$html .= '<p class="notice">' . JText::_('COM_PUBLICATIONS_NO_CONTENT') . '</p>';
			}
		}

		return $html;
	}

	/**
	 * Short description for 'dateToPath'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $date Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function dateToPath( $date )
	{
		if ($date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ))
		{
			$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		$dir_year  = date('Y', $date);
		$dir_month = date('m', $date);
		return $dir_year.DS.$dir_month;
	}

	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------

	/**
	 * Short description for 'dir_name'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $dir Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, '/'));
		if ($lastSlash == strlen($dir)-1)
		{
			return substr($dir, 0, $lastSlash);
		}
		else
		{
			return dirname($dir);
		}
	}

	/**
	 * Short description for 'parse_size'
	 *
	 * Long description (if any) ...
	 *
	 * @param      mixed $size Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function parse_size($size)
	{
		if ($size < 1024)
		{
			return $size.' bytes';
		}
		elseif ($size >= 1024 && $size < 1024*1024)
		{
			return sprintf('%01.2f',$size/1024.0).' <abbr title="kilobytes">Kb</abbr>';
		}
		else
		{
			return sprintf('%01.2f',$size/(1024.0*1024)).' <abbr title="megabytes">Mb</abbr>';
		}
	}

	/**
	 * Short description for 'num_files'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $dir Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir))
		{
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.')
				{
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}

	/**
	 * Short description for 'PublicationHelper::getPubStateProperty'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $row Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function getPubStateProperty($row, $get = 'class')
	{
		$status 	= '';
		$class 		= '';
		$task       = '';
		$now 		= JFactory::getDate()->toSql();

		switch ($row->state)
		{
			case 0:
				$class  = 'unpublished';
				$status = JText::_('COM_PUBLICATIONS_VERSION_UNPUBLISHED');
				$task 	= 'publish';
				break;

			case 1:
				$class   = 'published';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED');
				$task 	 = 'unpublish';

				if ($now <= $row->published_up)
				{
					$class   = 'pending';
					$status  = JText::_('COM_PUBLICATIONS_VERSION_COMING');
				}
				elseif ($now <= $row->published_down || $row->published_down == "0000-00-00 00:00:00")
				{
					$class 	 = 'published';
					$status  = JText::_('COM_PUBLICATIONS_VERSION_PUBLISHED');
				}
				elseif ($row->published_down && $row->published_down != "0000-00-00 00:00:00" && $now > $row->published_down)
				{
					$status  = JText::_('COM_PUBLICATIONS_VERSION_EXPIRED');
					$class   = 'expired';
				}
				break;

			case 2:
				$class   = 'deleted';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_DELETED');
				break;

			case 3:
			default:
				$class   = 'draft';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_DRAFT');
				break;

			case 4:
				$class   = 'ready';
				$status  = JText::_('COM_PUBLICATIONS_VERSION_READY');
				break;

			case 5:
				$class  = 'new';
				$status = JText::_('COM_PUBLICATIONS_VERSION_PENDING');
				$task = 'publish';
				break;

			case 6:
				$class  = 'archived';
				$status = JText::_('COM_PUBLICATIONS_VERSION_DARKARCHIVE');
				break;

			case 10:
				$class	= 'preserving';
				$status = JText::_('COM_PUBLICATIONS_VERSION_PRESERVING');
				break;

			case 7:
				$class	= 'wip';
				$status = JText::_('COM_PUBLICATIONS_VERSION_WIP');
				break;
		}

		switch ($get)
		{
			case 'class':
				return $class;
				break;
			case 'status':
				return $status;
				break;
			case 'task':
				return $task;
				break;
		}
	}

	/**
	 * Remove paragraph tags and break tags
	 *
	 * @param      string $pee Text to unparagraph
	 * @return     string
	 */
	public static function _txtUnpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Get project thumbnail
	 *
	 * @param      int 		$pid
	 * @param      int 		$versionid
	 * @param      array 	$config
	 * @return     string HTML
	 */
	public static function getThumbSrc( $pid, $versionid, $config, $cat = '' )
	{
		// Get publication directory path
		$webpath = $config->get('webpath', 'site/publications');
		$path 	 = PublicationsAdminHtml::buildPath($pid, $versionid, $webpath);

		if (file_exists( JPATH_ROOT . $path . DS . 'thumb.png' ))
		{
			return $path . DS . 'thumb.png';
		}
		else
		{
			// Get default picture
			$default = $cat == 'tools'
					? $config->get('toolpic', '/components/com_publications/assets/img/tool_thumb.gif')
					: $config->get('defaultpic', '/components/com_publications/assets/img/resource_thumb.gif');

			return file_exists( JPATH_ROOT . $default) ? $default : NULL;
		}
	}

	/**
	 * Create a thumbnail name
	 *
	 * @param      string $image Image name
	 * @param      string $tn    Thumbnail prefix
	 * @param      string $ext
	 * @return     string
	 */
	public static function createThumbName( $image=null, $tn='_thumb', $ext = '' )
	{
		if (!$image)
		{
			$this->setError( JText::_('COM_PUBLICATIONS_NO_IMAGE') );
			return false;
		}

		$image = explode('.',$image);
		$n = count($image);

		if ($n > 1)
		{
			$image[$n-2] .= $tn;
			$end = array_pop($image);
			if ($ext)
			{
				$image[] = $ext;
			}
			else
			{
				$image[] = $end;
			}

			$thumb = implode('.',$image);
		}
		else
		{
			// No extension
			$thumb = $image[0];
			$thumb .= $tn;
			if ($ext)
			{
				$thumb .= '.'.$ext;
			}
		}
		return $thumb;
	}
}

