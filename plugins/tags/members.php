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

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_tags_members' );

/**
 * Short description for 'plgTagsMembers'
 * 
 * Long description (if any) ...
 */
class plgTagsMembers extends JPlugin
{

	/**
	 * Description for '_total'
	 * 
	 * @var integer
	 */
	private $_total = null;

	/**
	 * Short description for 'plgTagsMembers'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgTagsMembers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'tags', 'members' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	/**
	 * Short description for 'onTagAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function onTagAreas()
	{
		$areas = array(
			'members' => JText::_('PLG_TAGS_MEMBERS')
		);
		return $areas;
	}

	/**
	 * Short description for 'onTagView'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $tags Parameter description (if any) ...
	 * @param      mixed $limit Parameter description (if any) ...
	 * @param      integer $limitstart Parameter description (if any) ...
	 * @param      string $sort Parameter description (if any) ...
	 * @param      unknown $areas Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function onTagView( $tags, $limit=0, $limitstart=0, $sort='', $areas=null )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onTagAreas() ) && !array_intersect( $areas, array_keys( $this->onTagAreas() ) )) {
				return array();
			}
		}

		// Do we have a member ID?
		if (empty($tags)) {
			return array();
		}

		$database =& JFactory::getDBO();

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->id;
		}
		$ids = implode(',',$ids);

		// Build the query
		$f_count = "SELECT COUNT(f.uidNumber) FROM (SELECT a.uidNumber, COUNT(DISTINCT t.tagid) AS uniques ";

		$f_fields = "SELECT a.uidNumber AS id, a.name AS title, a.username as alias, NULL AS itext, b.bio AS ftext, a.emailConfirmed AS state, a.registerDate AS created, a.uidNumber AS created_by, NULL AS modified, a.registerDate AS publish_up, a.picture AS publish_down, CONCAT( 'index.php?option=com_members&id=', a.uidNumber ) AS href, 'members' AS section, COUNT(DISTINCT t.tagid) AS uniques, a.params, NULL AS rcount, NULL AS data1, NULL AS data2, NULL AS data3 ";

		/*$f_from = " FROM #__xprofiles AS a, #__tags_object AS t, #__tags AS tg 
					WHERE a.public=1 AND a.uidNumber=t.objectid AND t.tbl='xprofiles' AND tg.id=t.tagid AND (tg.tag='".$tag."' OR tg.raw_tag='".$tag."' OR tg.alias='".$tag."')";*/
		$f_from = " FROM #__xprofiles AS a LEFT JOIN #__xprofiles_bio AS b ON a.uidNumber=b.uidNumber, #__tags_object AS t
					WHERE a.public=1 
					AND a.uidNumber=t.objectid 
					AND t.tbl='xprofiles' 
					AND t.tagid IN ($ids)";
		$f_from .= " GROUP BY a.uidNumber HAVING uniques=".count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title': $order_by .= 'title ASC, publish_up';  break;
			case 'id':    $order_by .= "id DESC";                break;
			case 'date':
			default:      $order_by .= 'publish_up DESC, title'; break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		// Execute the query
		if (!$limit) {
			$database->setQuery( $f_count . $f_from .") AS f" );
			$this->_total = $database->loadResult();
			return $this->_total;
		} else {
			if (count($areas) > 1) {
				ximport('Hubzero_Document');
				Hubzero_Document::addComponentStylesheet('com_members');

				return $f_fields.$f_from;
			}

			if ($this->_total != null) {
				if ($this->_total == 0) {
					return array();
				}
			}

			$database->setQuery( $f_fields . $f_from .  $order_by );
			$rows = $database->loadObjectList();

			// Did we get any results?
			if ($rows) {
				// Loop through the results and set each item's HREF
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_members&id='.$row->id);
				}
			}

			// Return the results
			return $rows;
		}
	}

	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------


	/**
	 * Short description for 'documents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function documents()
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_members');
	}

	/*public function before()
	{
		// ...
	}*/

	/**
	 * Short description for 'out'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $row Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function out( $row )
	{
		$config =& JComponentHelper::getParams( 'com_members' );

		$row->picture = $row->publish_down;

		if ($row->picture) {
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			if ($row->id < 0) {
				$id = abs($row->id);
				$thumb .= DS.'n'.plgXSearchMembers::niceidformat($id).DS.$row->picture;
			} else {
				$thumb .= DS.plgTagsMembers::niceidformat($row->id).DS.$row->picture;
			}
		} else {
			$thumb = $config->get('defaultpic');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
		}

		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);

		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_($row->href);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}

		$html  = "\t".'<li class="member">'."\n";
		if (is_file(JPATH_ROOT.$thumb)) {
			$html .= "\t\t".'<p class="photo"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
		}
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->ftext) {
			$html .= "\t\t".Hubzero_View_Helper_Html::shortenText(Hubzero_View_Helper_Html::purifyText(stripslashes($row->ftext)), 200)."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		return $html;
	}

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	/*public function after()
	{
		// ...
	}*/
}
