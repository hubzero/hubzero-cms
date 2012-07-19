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

//----------------------------------------------------------
// Information retrieval for items/info linked to a resource
//----------------------------------------------------------

/**
 * Short description for 'ResourcesHelper'
 * 
 * Long description (if any) ...
 */
class ResourcesHelper extends JObject
{

	/**
	 * Description for '_id'
	 * 
	 * @var mixed
	 */
	private $_id = 0;

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Description for '_data'
	 * 
	 * @var array
	 */
	private $_data = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $id, &$db )
	{
		$this->_id = $id;
		$this->_db =& $db;

		$this->contributors = null;
		$this->children = null;
		$this->firstchild = null;
		$this->parents = null;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//----------------------------------------------------------
	// Contributors
	//----------------------------------------------------------

	/**
	 * Short description for 'getUnlinkedContributors'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $incSubmitter Parameter description (if any) ...
	 * @return     void
	 */
	public function getUnlinkedContributors($incSubmitter=false)
	{
		if (!isset($this->_contributors)) {
			$this->getCons();
		}
		$contributors = $this->_contributors;

		$html = '';
		if ($contributors != '') {
			$names = array();
			foreach ($contributors as $contributor)
			{
				if ($incSubmitter == false && $contributor->role == 'submitter') {
					continue;
				}
				if ($contributor->lastname || $contributor->firstname) {
					$name = stripslashes($contributor->firstname) .' ';
					if ($contributor->middlename != NULL) {
						$name .= stripslashes($contributor->middlename) .' ';
					}
					$name .= stripslashes($contributor->lastname);
				} else {
					$name = $contributor->name;
				}
				$name = str_replace( '"', '&quot;', $name );
				$names[] = $name;
			}
			if (count($names) > 0) {
				$html = implode( '; ', $names );
			}
		}
		$this->ul_contributors = $html;
	}

	/**
	 * Short description for 'getToolAuthors'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $toolname Parameter description (if any) ...
	 * @param      string $revision Parameter description (if any) ...
	 * @return     void
	 */
	public function getToolAuthors($toolname, $revision)
	{
		if (false) // @FIXME  quick hack to deal with influx of data in jos_tool_groups
		{
		$sql = "SELECT n.uidNumber AS id, t.name AS name, n.name AS xname,  n.organization AS xorg, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, t.organization AS org, t.*, NULL as role"
			 . "\n FROM #__tool_authors AS t, #__xprofiles AS n "
			 . "\n WHERE n.uidNumber=t.uid AND t.toolname='".$toolname."'"
			 . "\n AND t.revision='".$revision."'"
			 . "\n ORDER BY t.ordering";
		}
		else
		{
		$sql = "SELECT n.uidNumber AS id, t.name AS name, n.name AS xname, n.organization AS xorg, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, t.organization AS org, t.*, NULL as role"
			 . "\n FROM #__tool_authors AS t, #__xprofiles AS n, #__tool_version AS v "
			 . "\n WHERE n.uidNumber=t.uid AND t.toolname='".$toolname."' AND v.id=t.version_id and v.state<>3"
			 . "\n AND t.revision='".$revision."'"
			 . "\n ORDER BY t.ordering";
		}
		$this->_db->setQuery( $sql );
		$cons = $this->_db->loadObjectList();
		if ($cons) {
			foreach ($cons as $k => $c)
			{
				if (!$cons[$k]->name) {
					$cons[$k]->name = $cons[$k]->xname;
				}
				if (trim($cons[$k]->org) == '') {
					$cons[$k]->org = $cons[$k]->xorg;
				}
			}
		}
		$this->_contributors = $cons;
	}

	/**
	 * Short description for 'getCons'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function getCons()
	{
		/*$sql = "SELECT n.uidNumber AS id, 
				a.name AS name, 
				n.name AS xname,
				n.givenName AS firstname, 
				n.middleName AS middlename, 
				n.surname AS lastname, 
				a.organization AS org, 
				n.organization AS xorg, 
				a.role
				FROM #__xprofiles AS n, 
				#__author_assoc AS a 
				WHERE n.uidNumber=a.authorid 
				AND a.subtable='resources' 
				AND a.subid=".$this->_id." 
				ORDER BY ordering, surname, givenName, middleName";
		*/
		$sql = "SELECT a.authorid, a.name, a.organization AS org, a.role, n.uidNumber AS id, n.givenName AS firstname, n.middleName AS middlename, n.surname AS lastname, n.organization AS xorg
				FROM #__author_assoc AS a 
				LEFT JOIN #__xprofiles AS n ON n.uidNumber=a.authorid
				WHERE a.subtable='resources' 
				AND a.subid=" . $this->_id . " 
				ORDER BY ordering, surname, givenName, middleName";

		$this->_db->setQuery($sql);
		$cons = $this->_db->loadObjectList();
		/*if ($cons) 
		{
			foreach ($cons as $k => $c)
			{
				if (!$cons[$k]->name) 
				{
					$cons[$k]->name = $cons[$k]->xname;
				}
				if (trim($cons[$k]->org) == '') 
				{
					$cons[$k]->org = $cons[$k]->xorg;
				}
			}
		}*/
		$this->_contributors = $cons;
	}

	/**
	 * Short description for 'getContributors'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $showorgs Parameter description (if any) ...
	 * @param      integer $newstyle Parameter description (if any) ...
	 * @return     void
	 */
	public function getContributors($showorgs=false, $newstyle=0)
	{
		if (!isset($this->_contributors) && !$this->_contributors) {
			$this->getCons();
		}
		$contributors = $this->_contributors;

		if ($contributors != '') {
			$html = '';
			$names = array();
			$orgs = array();
			$i = 1;
			$k = 0;
			$orgsln = '';
			$names_s = array();
			$orgsln_s = '';

			foreach ($contributors as $contributor)
			{
				if (strtolower($contributor->role) == 'submitter') {
					continue;
				}
				
				// Build the user's name and link to their profile
				if ($contributor->name) {
					$name = $contributor->name;
				} else if ($contributor->lastname || $contributor->firstname) {
					$name = stripslashes($contributor->firstname) .' ';
					if ($contributor->middlename != NULL) {
						$name .= stripslashes($contributor->middlename) .' ';
					}
					$name .= stripslashes($contributor->lastname);
				} else {
					$name = $contributor->xname;
				}
				if (!$contributor->org) {
					$contributor->org = $contributor->xorg;
				}

				$name = str_replace( '"', '&quot;', $name );
				if ($contributor->id)
				{
					$link  = '<a href="'.JRoute::_('index.php?option=com_members&amp;id='.$contributor->id).'" rel="contributor" title="View the profile of '.$name.'">'.$name.'</a>';
				}
				else 
				{
					$link  = $name;
				}
				$link .= ($contributor->role) ? ' ('.$contributor->role.')' : '';

				if ($newstyle) {
					if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs)) {
						$orgs[$i-1] = trim($contributor->org);
						$orgsln 	.= $i. '. ' .trim($contributor->org).' ';
						$orgsln_s 	.= trim($contributor->org).' ';
						$k = $i;
						$i++;
						
						$link_s = $link;
						$link .= '<sup>'. $k .'</sup>';
						$names_s[] = $link_s;
					} else {
						//$k = array_search(trim($contributor->org), $orgs) + 1;
						$link_s = $link;
						$link .= '';
						$names_s[] = $link_s;
					}
				} else {
					$orgs[trim($contributor->org)][] = $link;
				}

				$names[] = $link;
			}

			if ($showorgs && !$newstyle) {
				foreach ($orgs as $org=>$links)
				{
					$orgs[$org] = implode( ', ', $links ).'<br />'.$org;
				}
				$html .= implode( '<br /><br />', $orgs );
			} else if ($newstyle) {
				if (count($names) > 0) {
					$html = '<p>'.ucfirst(JText::_('By')).' ';
					$html .= count($orgs) > 1  ? implode( ', ', $names ) : implode( ', ', $names_s )  ;
					$html .= '</p>';
				}
				if ($showorgs && count($orgs) > 0) {
					$html .= '<p class="orgs">';
					$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
					$html .= '</p>';
				}
			} else {
				if (count($names) > 0) {
					$html = implode( ', ', $names );
				}
			}
		} else {
			$html = '';
		}
		$this->contributors = $html;
	}
	
	/**
	 * Short description for 'getSubmitters'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $showorgs Parameter description (if any) ...
	 * @param      integer $newstyle Parameter description (if any) ...
	 * @return     void
	 */
	public function getSubmitters($showorgs=false, $newstyle=0, $badges=0)
	{
		if (!isset($this->_contributors) && !$this->_contributors) {
			$this->getCons();
		}
		$contributors = $this->_contributors;

		if ($contributors != '') {
			$html = '';
			$names = array();
			$orgs = array();
			$i = 1;
			$k = 0;
			$orgsln = '';
			$names_s = array();
			$orgsln_s = '';

			foreach ($contributors as $contributor)
			{
				if (strtolower($contributor->role) != 'submitter') {
					continue;
				}
				
				// Build the user's name and link to their profile
				if ($contributor->name) {
					$name = $contributor->name;
				} else if ($contributor->lastname || $contributor->firstname) {
					$name = stripslashes($contributor->firstname) .' ';
					if ($contributor->middlename != NULL) {
						$name .= stripslashes($contributor->middlename) .' ';
					}
					$name .= stripslashes($contributor->lastname);
				} else {
					$name = $contributor->xname;
				}
				if (!$contributor->org) {
					$contributor->org = $contributor->xorg;
				}

				$name = str_replace( '"', '&quot;', $name );
				if ($contributor->id)
				{
					$link  = '<a href="'.JRoute::_('index.php?option=com_members&amp;id='.$contributor->id).'" rel="contributor" title="View the profile of '.$name.'">'.$name.'</a>';
				}
				else 
				{
					$link  = $name;
				}
				//$link .= ($contributor->role) ? ' <span class="user-badges"><span>'.$contributor->role.'</span></span>' : '';

				if ($newstyle) {
					if ($badges) {
						$xuser =& JUser::getInstance($contributor->id);
						if (is_object($xuser) && $xuser->get('name')) {
							$types = array(23 => 'manager', 24 => 'administrator', 25 => 'super administrator', 21 => 'publisher', 20 => 'editor');
							if (isset($types[$xuser->gid])) {
								$link .= ' <ul class="badges"><li>' . str_replace(' ', '-', $types[$xuser->gid]) . '</li></ul>';
							}
						}
					}
					
					if (trim($contributor->org) != '' && !in_array(trim($contributor->org), $orgs)) {
						$orgs[$i-1] = trim($contributor->org);
						$orgsln 	.= $i. '. ' .trim($contributor->org).' ';
						$orgsln_s 	.= trim($contributor->org).' ';
						$k = $i;
						$i++;
					} else {
						$k = array_search(trim($contributor->org), $orgs) + 1;
					}
					$link_s = $link;
					$link .= '<sup>'. $k .'</sup>';
					$names_s[] = $link_s;

				} else {
					$orgs[trim($contributor->org)][] = $link;
				}

				$names[] = $link;
			}

			if ($showorgs && !$newstyle) {
				foreach ($orgs as $org=>$links)
				{
					$orgs[$org] = implode( ', ', $links ).'<br />'.$org;
				}
				$html .= implode( '<br /><br />', $orgs );
			} else if ($newstyle) {
				if (count($names) > 0) {
					$html  = '<p>';
					$html .= count($orgs) > 1  ? implode( ', ', $names ) : implode( ', ', $names_s )  ;
					$html .= '</p>';
				}
				if ($showorgs && count($orgs) > 0) {
					$html .= '<p class="orgs">';
					$html .= count($orgs) > 1 ? $orgsln : $orgsln_s;
					$html .= '</p>';
				}
			} else {
				if (count($names) > 0) {
					$html = implode( ', ', $names );
				}
			}
		} else {
			$html = '';
		}
		$this->contributors = $html;
	}

	/**
	 * Short description for 'getContributorIDs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function getContributorIDs()
	{
		$cons = array();

		if (isset($this->_data['_contributors'])) {
			$contributors = $this->_contributors;
		} else {
			$sql = "SELECT n.uidNumber AS id"
				 . "\n FROM #__xprofiles AS n"
				 . "\n JOIN #__author_assoc AS a ON n.uidNumber=a.authorid"
				 . "\n WHERE a.subtable = 'resources'"
				 . "\n AND a.subid=". $this->_id
				 . "\n ORDER BY ordering, surname, givenName, middleName";

			$this->_db->setQuery( $sql );
			$contributors = $this->_db->loadObjectList();
		}

		if ($contributors) {
			foreach ($contributors as $con)
			{
				$cons[] = $con->id;
			}
		}
		$this->contributorIDs = $cons;
	}

	//----------------------------------------------------------
	// Citations
	//----------------------------------------------------------

	/**
	 * Short description for 'getCitations'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function getCitations()
	{
		if (!$this->_id) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'citation.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'association.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'author.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'secondary.php' );
		$database =& JFactory::getDBO();

		$cc = new CitationsCitation( $database );

		$this->citations = $cc->getCitations( 'resource', $this->_id );
	}

	/**
	 * Short description for 'getCitationsCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function getCitationsCount()
	{
		$citations = $this->citations;
		if (!$citations) {
			$citations = $this->getCitations();
		}

		$this->citationsCount = $citations;
	}

	/**
	 * Short description for 'getLastCitationDate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function getLastCitationDate()
	{
		if ($this->_id) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'citation.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'association.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'author.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'tables'.DS.'secondary.php' );
		$database =& JFactory::getDBO();

		$cc = new CitationsCitation( $database );

		$this->lastCitationDate = $cc->getLastCitationDate( 'resource', $this->_id );
	}

	//----------------------------------------------------------
	// Tags
	//----------------------------------------------------------

	/**
	 * Short description for 'getTags'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $tagger_id Parameter description (if any) ...
	 * @param      integer $strength Parameter description (if any) ...
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getTags($tagger_id=0, $strength=0, $admin=0)
	{
		if ($this->_id == 0) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
        $database =& JFactory::getDBO();
		$rt = new ResourcesTags( $database );
		$this->tags = $rt->get_tags_on_object($this->_id, 0, 0, $tagger_id, $strength, $admin);
		return $this->tags;
	}

	/**
	 * Short description for 'getTagsForEditing'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $tagger_id Parameter description (if any) ...
	 * @param      integer $strength Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getTagsForEditing( $tagger_id=0, $strength=0 )
	{
		if ($this->_id == 0) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
		$database =& JFactory::getDBO();

		$rt = new ResourcesTags( $database );
		$this->tagsForEditing = $rt->get_tag_string( $this->_id, 0, 0, $tagger_id, $strength, 0 );
		return $this->tagsForEditing;
	}

	/**
	 * Short description for 'getTagCloud'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getTagCloud( $admin=0 )
	{
		if ($this->_id == 0) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
		$database =& JFactory::getDBO();

		$rt = new ResourcesTags( $database );
		$this->tagCloud = $rt->get_tag_cloud(0, $admin, $this->_id);
		return $this->tagCloud;
	}

	//----------------------------------------------------------
	// Children, parents, etc.
	//----------------------------------------------------------

	/**
	 * Short description for 'getChildren'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      mixed $limit Parameter description (if any) ...
	 * @param      string $standalone Parameter description (if any) ...
	 * @param      mixed $excludeFirstChild Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getChildren( $id='', $limit=0, $standalone='all', $excludeFirstChild = 0 )
	{
		$children = '';
		if (!$id) {
			$id = $this->_id;
		}
		$sql = "SELECT r.id, r.title, r.introtext, r.type, r.logical_type AS logicaltype, r.created, r.created_by, 
				r.published, r.publish_up, r.path, r.access, r.standalone, r.rating, r.times_rated, r.attribs, r.params, 
				t.type AS logicaltitle, rt.type AS typetitle, a.grouping, a.ordering "
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.parent_id=".$id." AND r.type=rt.id";
		switch ($standalone)
		{
			case 'no': $sql .= " AND r.standalone=0"; break;
			case 'yes': $sql .= " AND r.standalone=1"; break;
			case 'all':
			default: $sql .= ""; break;
		}
		$sql .= "\n ORDER BY a.ordering, a.grouping";
		if ($limit != 0 or $excludeFirstChild) {
			$sql .= $excludeFirstChild ? " LIMIT $excludeFirstChild, 100" : " LIMIT  ".$limit;
		}
		$this->_db->setQuery( $sql );
		$children = $this->_db->loadObjectList();

		if ($limit != 0) {
			return (isset($children[0])) ? $children[0] : NULL;
		} else {
			$this->children = $children;
		}
	}

	/**
	 * Short description for 'getStandaloneCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getStandaloneCount( $filters )
	{
		//$rt = new ResourcesType( $database );
		//$ra = new ResourcesAssoc( $database );
		//$rr = new ResourcesResource( $database );

		$sql = "SELECT COUNT(*)"
			 . " FROM #__resource_types AS rt, #__resources AS r"
			 . " JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . " LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . " WHERE r.published=1 AND a.parent_id=".$filters['id']." AND r.standalone=1 AND r.type=rt.id";
		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getStandaloneChildren'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getStandaloneChildren( $filters )
	{
		$sql = "SELECT r.id, r.title, r.alias, r.introtext, r.fulltext, r.type, r.logical_type AS logicaltype, r.created, r.created_by, 
						r.published, r.publish_up, r.path, r.access, r.standalone, r.rating, r.times_rated, r.attribs, r.ranking,
						r.params, t.type AS logicaltitle, rt.type AS typetitle, a.grouping, 
						(SELECT n.surname FROM #__xprofiles AS n, #__author_assoc AS aa WHERE n.uidNumber=aa.authorid AND aa.subtable='resources' AND aa.subid=r.id ORDER BY ordering LIMIT 1) AS author"
			 . "\n FROM #__resource_types AS rt, #__resources AS r"
			 . "\n JOIN #__resource_assoc AS a ON r.id=a.child_id"
			 . "\n LEFT JOIN #__resource_types AS t ON r.logical_type=t.id"
			 . "\n WHERE r.published=1 AND a.parent_id=".$filters['id']." AND r.standalone=1 AND r.type=rt.id";
		if (isset($filters['year']) && $filters['year'] > 0) {
			$sql .= " AND r.publish_up >= '".$filters['year']."-01-01 00:00:00' AND r.publish_up <= '".$filters['year']."-12-31 23:59:59'";
		}
		$sql .= " ORDER BY ";
		switch ($filters['sortby'])
		{
			case 'ordering': $sql .= "a.ordering, a.grouping";            break;
			case 'date':     $sql .= "r.publish_up DESC";                 break;
			case 'title':    $sql .= "r.title ASC, r.publish_up";         break;
			case 'rating':   $sql .= "r.rating DESC, r.times_rated DESC"; break;
			case 'ranking':  $sql .= "r.ranking DESC"; break;
			case 'author':   $sql .= "author"; break;
		}
		if (isset($filters['limit']) && $filters['limit'] != '' && $filters['limit'] != 0) {
			$sql .= " LIMIT ".$filters['start'].",".$filters['limit']." ";
		}
		//echo '<!-- '.$sql.' -->';
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getFirstChild'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function getFirstChild()
	{
		if ($this->children) {
			$this->firstChild = $this->children[0];
		} else {
			$this->firstChild = $this->getChildren('',1);
		}
	}

	/**
	 * Short description for 'getParents'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function getParents()
	{
		if ($this->_id == 0) {
			return false;
		}

		$sql = "SELECT DISTINCT r.id, r.title, r.alias, r.introtext, r.footertext, r.type, r.logical_type AS logicaltype, 
				r.created, r.published, r.publish_up, r.path, r.standalone, r.hits, r.rating, r.times_rated, r.params, r.ranking,
				t.type AS logicaltitle, rt.type AS typetitle 
				FROM #__resource_types AS rt, #__resources AS r 
				JOIN #__resource_assoc AS a ON r.id=a.parent_id 
				LEFT JOIN #__resource_types AS t ON r.logical_type=t.id 
				WHERE r.published=1 AND a.child_id=".$this->_id." AND r.type=rt.id AND r.type!=8 
				ORDER BY a.ordering, a.grouping";
		$this->_db->setQuery( $sql );
		$parents = $this->_db->loadObjectList();

		$this->parents = $parents;
	}

	//----------------------------------------------------------
	// Reviews
	//----------------------------------------------------------

	/**
	 * Short description for 'getReviews'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function getReviews()
	{
		if ($this->_id == 0) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'review.php' );
		$database =& JFactory::getDBO();

		$rr = new ResourcesReview( $database );

		$this->reviews = $rr->getRatings( $this->_id );
	}

	/**
	 * Short description for 'countQuestions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     integer Return description (if any) ...
	 */
	public function countQuestions()
	{
		return 0;
	}
}
	//----------------------------------------------------------
	// For storing screenshots information
	//----------------------------------------------------------

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
	class ResourceScreenshot extends  JTable
	{

	/**
	 * Description for 'id'
	 * 
	 * @var integer
	 */
		var $id      	   = NULL;  // @var int (primary key)

	/**
	 * Description for 'versionid'
	 * 
	 * @var unknown
	 */
		var $versionid     = NULL;  // @var int

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
		var $title         = NULL;  // @var string (127)

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
		var $ordering      = NULL;  // @var int (11)

	/**
	 * Description for 'filename'
	 * 
	 * @var string
	 */
		var $filename 	   = NULL;  // @var string (100)

	/**
	 * Description for 'resourceid'
	 * 
	 * @var unknown
	 */
		var $resourceid    = NULL;  // @var int

		//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
		public function __construct( &$db )
		{
			parent::__construct( '#__screenshots', 'id', $db );
		}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
		public function check()
		{
			if (trim( $this->filename ) == '') {
				$this->setError( 'Missing filename');
				return false;
			}

			return true;
		}

	/**
	 * Short description for 'loadFromFilename'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
		public function loadFromFilename( $filename, $rid=NULL, $versionid=NULL)
		{
			if ($filename===NULL) {
				return false;
			}
			if ($rid===NULL) {
				return false;
			}

			$query = "SELECT * FROM $this->_tbl as s WHERE s.filename='".$filename."' AND s.resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND s.versionid= '".$versionid."' LIMIT 1";
			}

			$this->_db->setQuery( $query );
			if ($result = $this->_db->loadAssoc()) {
				return $this->bind( $result );
			} else {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
		//-----------

	/**
	 * Short description for 'getScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
		public function getScreenshot( $filename, $rid=NULL, $versionid=NULL)
		{
			if ($filename===NULL) {
				return false;
			}
			if ($rid===NULL) {
				return false;
			}

			$query = "SELECT * FROM $this->_tbl as s WHERE s.filename='".$filename."' AND s.resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND s.versionid= '".$versionid."'";
			}
			$query.= " LIMIT 1";

			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

	/**
	 * Short description for 'getLastOrdering'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
		public function getLastOrdering($rid=NULL, $versionid=NULL) {

			if ($rid===NULL) {
				return false;
			}
			$query = "SELECT ordering FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND s.versionid= '".$versionid."' ";
			}
			$query.= "ORDER BY s.ordering DESC LIMIT 1";

			$this->_db->setQuery( $query );
			return $this->_db->loadResult();

		}
		//-----------

	/**
	 * Short description for 'saveScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      mixed $versionid Parameter description (if any) ...
	 * @param      mixed $ordering Parameter description (if any) ...
	 * @param      boolean $new Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
		public function saveScreenshot( $filename, $rid=NULL, $versionid=0, $ordering = 0, $new=false )
		{
			if ($filename===NULL) {
				return false;
			}
			if ($rid===NULL) {
				return false;
			}
			if (!$new) {
				$this->_db->setQuery( "UPDATE $this->_tbl SET ordering=".$ordering." WHERE filename='".$filename."' AND resourceid='".$rid."' AND versionid='".$versionid."'");
				if ($this->_db->query()) {
					$ret = true;
				} else {
					$ret = false;
				}
			} else {
				$this->ordering = $ordering;
				$this->resourceid = $rid;
				$this->versionid = $versionid;
				$this->filename= $filename;
				$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
			}
			if (!$ret) {
				$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
				return false;
			} else {
				return true;
			}
		}

	/**
	 * Short description for 'deleteScreenshot'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $filename Parameter description (if any) ...
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
		public function deleteScreenshot($filename, $rid=NULL, $versionid=NULL) {
			if ($filename===NULL) {
				return false;
			}
			if ($rid===NULL) {
				return false;
			}

			$query = "DELETE FROM $this->_tbl WHERE filename='".$filename."' AND resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND versionid= '".$versionid."' LIMIT 1";
			}
			$this->_db->setQuery( $query );
			$this->_db->query();
		}

	/**
	 * Short description for 'getScreenshots'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
		public function getScreenshots( $rid=NULL, $versionid=NULL)
		{
			if ($rid===NULL) {
				return false;
			}

			$query = "SELECT * FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND s.versionid= '".$versionid."' ";
			}
			$query.= "ORDER BY s.ordering ASC";

			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

	/**
	 * Short description for 'getFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $versionid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
		public function getFiles( $rid=NULL, $versionid=NULL)
		{
			if ($rid===NULL) {
				return false;
			}

			$query = "SELECT filename FROM $this->_tbl as s WHERE s.resourceid= '".$rid."'";
			if($versionid)  {
			$query.= " AND s.versionid= '".$versionid."' ";
			}
			$query.= "ORDER BY s.ordering ASC";

			$this->_db->setQuery( $query );
			return $this->_db->loadObjectList();
		}

	/**
	 * Short description for 'updateFiles'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $rid Parameter description (if any) ...
	 * @param      string $devid Parameter description (if any) ...
	 * @param      string $currentid Parameter description (if any) ...
	 * @param      integer $copy Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
		public function updateFiles( $rid=NULL, $devid=NULL, $currentid=NULL, $copy=0)
		{
			if ($rid===NULL or $devid===NULL or $currentid===NULL) {
				return false;
			}

			if($copy) {

				$ss = $this->getScreenshots( $rid, $devid);

				if($ss) {
					foreach($ss as $s) {
						$this->id = 0;
						$this->versionid = $currentid;
						$this->filename = 'new.gif';
						$this->resourceid = $rid;
						if (!$this->store()) {
							$this->_error = $this->getError();
							return false;
						}
						$this->checkin();
						$newid = $this->id;

						$query = "UPDATE $this->_tbl as t1, $this->_tbl as t2 ";
						$query.= "SET t2.versionid='".$currentid."', t2.title=t1.title, t2.filename=t1.filename, t2.ordering=t1.ordering, t2.resourceid=t1.resourceid";
						$query.= " WHERE t1.id = '".$s->id."' ";
						$query.= " AND t2.id ='".$newid."'";
						$this->_db->setQuery( $query );
						$this->_db->query();

					}
				}

			}
			else {

				$query = "UPDATE $this->_tbl SET versionid='".$currentid."' WHERE ";
				$query.= " versionid = '".$devid."' ";
				$query.= " AND resourceid='".$rid."'";
				$this->_db->setQuery( $query );
				if($this->_db->query()) { return true; }
				else {
					return false;
				}
			}

		}

	}

// For backwards compatibility

/**
 * Short description for 'ResourceExtended'
 * 
 * Long description (if any) ...
 */
class ResourceExtended extends ResourcesHelper
{
}

