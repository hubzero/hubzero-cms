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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Publications Plugin class for related content
 */
class plgPublicationsRelated extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'publications', 'related' );
		$this->_params = new JParameter( $this->_plugin->params );

		$this->loadLanguage();
	}


	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @return     array
	 */
	public function &onPublicationSubAreas( $publication )
	{
		$areas = array(
			'related' => JText::_('PLG_PUBLICATION_RELATED')
		);
		return $areas;
	}

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param      object  $publication 	Current publication
	 * @param      string  $option    		Name of the component
	 * @param      integer $miniview  		View style
	 * @return     array
	 */
	public function onPublicationSub( $publication, $option, $miniview=0 )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		$database = JFactory::getDBO();

		// Build the query that checks topic pages
		$sql1 = "SELECT v.id, v.pageid, MAX(v.version) AS version, w.title, w.pagename AS alias,
				v.pagetext AS abstract, NULL AS type, NULL AS published, NULL AS published_up,
				w.scope, w.rating, w.times_rated, w.ranking, 'wiki' AS class, 'Topic' AS section, w.`group_cn`
				FROM #__wiki_page AS w, #__wiki_version AS v
				WHERE w.id=v.pageid AND v.approved=1 AND (v.pagetext LIKE '%[[Resource(".$publication->id.")]]%'
				OR v.pagetext LIKE '%[[Resource(".$publication->id.",%' OR v.pagetext LIKE '%[/Resource/".$publication->id." %'";

		$sql1 .= ($publication->alias) ? " OR v.pagetext LIKE '%[[Resource(".$publication->alias."%') " : ") ";
		$juser = JFactory::getUser();

		if (!$juser->get('guest'))
		{
			if ($juser->authorize('com_publications', 'manage') || $juser->authorize('com_groups', 'manage'))
			{
				$sql1 .= '';
			}
			else
			{
				$ugs = \Hubzero\User\Helper::getGroups( $juser->get('id'), 'members' );
				$groups = array();
				if ($ugs && count($ugs) > 0)
				{
					foreach ($ugs as $ug)
					{
						$groups[] = $ug->cn;
					}
				}
				$g = "'".implode("','",$groups)."'";

				$sql1 .= "AND (w.access!=1 OR (w.access=1 AND (w.group_cn IN ($g) OR w.created_by='".$juser->get('id')."'))) ";
			}
		}
		else
		{
			$sql1 .= "AND w.access!=1 ";
		}
		$sql1 .= "AND w.`group_cn` IS NULL "; // only get topic pages
		$sql1 .= "GROUP BY pageid ORDER BY ranking DESC, title LIMIT 10";

		// Initiate a helper class
		$helper = new PublicationHelper($database, $publication->version_id, $publication->id);
		$helper->getTags();

		// Get version authors
		$authors = isset($publication->_authors) ? $publication->_authors : array();

		// Build the query that get publications related by tag
		$sql2 = "SELECT DISTINCT r.publication_id as id, NULL AS pageid, r.id AS version,
				r.title, C.alias, r.abstract, C.category, r.state as published,
				r.published_up, NULL AS scope, C.rating, C.times_rated, C.ranking,
				rt.alias AS class, rt.name AS section, NULL AS `group` "
			 . "\n FROM #__publications as C, #__publication_categories AS rt, #__publication_versions AS r "
			 . "\n JOIN #__tags_object AS a ON r.publication_id=a.objectid AND a.tbl='publications'"
			 . "\n JOIN #__publication_authors AS PA ON PA.publication_version_id=r.id "
			 . "\n WHERE C.id=r.publication_id ";
		if ($helper->tags)
		{
			$tquery = '';
			foreach ($helper->tags as $tagg)
			{
				$tquery .= "'".$tagg['id']."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$sql2 .= " AND ( a.tagid IN (".$tquery.")";
			$sql2 .= (count($authors) > 0) ? " OR " : "";
		}
		if (count($authors) > 0)
		{
			$aquery = '';
			foreach ($authors as $author)
			{
				$aquery .= "'".$author->user_id."',";
			}
			$aquery = substr($aquery,0,strlen($aquery) - 1);
			$sql2 .= ($helper->tags) ? "" : " AND ( ";
			$sql2 .= " PA.user_id IN (".$aquery.")";
		}
		$sql2 .= ($helper->tags || count($authors) > 0 ) ? ")" : "";

		$sql2 .= " AND r.publication_id !=".$publication->id;
		$sql2.= " AND C.category = rt.id AND C.category!=8 ";
		$sql2 .= "AND r.access=0 ";
		$sql2 .= "AND r.state=1 ";
		$sql2 .= "GROUP BY r.publication_id ORDER BY r.ranking LIMIT 10";

		// Build the final query
		$query = "SELECT k.* FROM (($sql1) UNION ($sql2)) AS k ORDER BY ranking DESC LIMIT 10";

		// Execute the query
		$database->setQuery( $query );
		$related = $database->loadObjectList();

		// Instantiate a view
		if ($miniview)
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'publications',
					'element'=>'related',
					'name'=>'browse',
					'layout' => 'mini'
				)
			);
		}
		else
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'publications',
					'element'=>'related',
					'name'=>'browse'
				)
			);
		}

		// Pass the view some info
		$view->option 		= $option;
		$view->publication 	= $publication;
		$view->related 		= $related;
		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		// Return the an array of content
		return $arr;
	}
}
