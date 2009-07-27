<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class modMySubmissions
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//----------------------------------------------------------
	// Checks
	//----------------------------------------------------------
	
	protected function step_type_check( $id )
	{
		// do nothing
	}
	
	//-----------
	
	protected function step_compose_check( $id )
	{
		return $id;
	}

	//-----------
	
	protected function step_attach_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$ra = new ResourcesAssoc( $database );
			$total = $ra->getCount( $id );
		} else {
			$total = 0;
		}
		return $total;
	}

	//-----------
	
	protected function step_authors_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$rc = new ResourcesContributor( $database );
			$contributors = $rc->getCount( $id, 'resources' );
		} else {
			$contributors = 0;
		}

		return $contributors;
	}
	
	//-----------
	
	protected function step_tags_check( $id )
	{
		$database =& JFactory::getDBO();

		$rt = new ResourcesTags( $database );
		$tags = $rt->getTags( $id );

		if (count($tags) > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	
	//-----------

	protected function step_review_check( $id ) 
	{
		return 0;
	}

	//-----------
	
	private function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function display()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return $this->warning( 'To contribute content, you must be logged in. Don\'t have an account yet? <a href="/register">Create an account</a>.' );
		}
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php' );

		$steps = array('Type','Compose','Attach','Authors','Tags','Review');
		
		$database =& JFactory::getDBO();
		
		$rr = new ResourcesResource( $database );
		$rt = new ResourcesType( $database );
		
		$query = "SELECT r.*, t.type AS typetitle 
			FROM ".$rr->getTableName()." AS r 
			LEFT JOIN ".$rt->getTableName()." AS t ON r.type=t.id 
			WHERE r.published=2 AND r.standalone=1 AND r.type!=7 AND r.created_by=".$juser->get('id');
	    $database->setQuery( $query );
	    $rows = $database->loadObjectList();
		
		$html = ''; 
		if (!empty($rows)) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.assoc.php');
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.contributor.php');
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );
			
			$stepchecks = array();
			$laststep = (count($steps) - 1);
			
			foreach ($rows as $row)
			{
				$html .= '<div class="submission">'."\n";
				$html .= '<h4>'.stripslashes($row->title).' <a href="'.JRoute::_('index.php?option=com_contribute&step=1&id='.$row->id).'">'.JText::_('edit').'</a></h4>'."\n";
				$html .= '<table summary="'.JText::_('A list of submissions in progress').'">'."\n";
				$html .= "\t".'<tbody>'."\n";
				$html .= "\t\t".'<tr>'."\n";
				$html .= "\t\t\t".'<th>'.JText::_('Type:').'</th>'."\n";
				$html .= "\t\t\t".'<td colspan="2">'.$row->typetitle.'</td>'."\n";
				$html .= "\t\t".'</tr>'."\n";

				for ($i=1, $n=count( $steps ); $i < $n; $i++) 
				{
					if ($i != $laststep) {
						$check = 'step_'.$steps[$i].'_check';
						$stepchecks[$steps[$i]] = $this->$check( $row->id );
						
						if ($stepchecks[$steps[$i]]) {
							$completed = '<span class="yes">'.JText::_('completed').'</span>';
						} else {
							$completed = '<span class="no">'.JText::_('not completed').'</span>';
						}

						$html .= "\t\t".'<tr>'."\n";
						$html .= "\t\t\t".'<th>'.$steps[$i].'</th>'."\n";
						$html .= "\t\t\t".'<td>'.$completed.'</td>'."\n";
						$html .= "\t\t\t".'<td><a href="'.JRoute::_('index.php?option=com_contribute&step='.$i.'&amp;id='.$row->id).'">'.JText::_('edit').'</a></td>';
						$html .= "\t\t".'</tr>'."\n";
					}
				}
				$html .= '</table>'."\n";
				$html .= '<p class="discrd"><a href="'.JRoute::_('index.php?option=com_contribute&task=discard&id='.$row->id).'">'.JText::_('Delete').'</a></p>'."\n";
				$html .= '<p class="review"><a href="'.JRoute::_('index.php?option=com_contribute&step='.$laststep.'&id='.$row->id).'">'.JText::_('Review &amp; Submit').'</a></p>'."\n";
				$html .= '<div class="clear"></div>';
				$html .= '</div>'."\n";
			}
		} else {
			$html .= '<p>'.JText::_('No submissions in progress').'</p>'.n;
		}
		
		return $html;
	}
}

//-------------------------------------------------------------

$modmysubmissions = new modMySubmissions();
$modmysubmissions->params = $params;

require( JModuleHelper::getLayoutPath('mod_mysubmissions') );
