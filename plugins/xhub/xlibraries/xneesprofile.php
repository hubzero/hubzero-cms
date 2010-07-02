<?php
/**
 * @package		NEEShub
 * @author		David Benham <dbenham@purdue.edu>
 * @copyright	Copyright 2010 by NEEScomm IT, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2010 by Purdue Research Foundation, West Lafayette, IN 47906.
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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}
/****************************************************************************************************
 *
 * XNeesProfile - Primary Object for dealing with the NEES profile data
 * 
 ****************************************************************************************************/
class XNeesProfile extends JObject
{
	private $uid;
	private $UserCategory;
	private $NeesAffiliation;
	private $NeesRelationship;
	private $ReceiveMaterials;
	private $UsState;
	var $_required = array();
		
	// Create a new entry in DB
	public function create()
	{
		return $this->_mysql_create();
	}

	// Update existing DB row
	public function update()
	{
		return $this->_mysql_update();
	}

	// Validate form required fields
	public function check()
	{
		//echo 'drb 754';
		//exit;
		
		$returnValue = 1;
		
		if($this->get('UserCategory') == '')
		{
			$this->_required['UserCategory'] = 'User Category is required';
			$returnValue = 0;
		}
		
		if($this->get('NeesAffiliation') == '')
		{
			$this->_required['NeesAffiliation'] = 'NEES Affilication is required';
			$returnValue = 0;
		}

		return $returnValue;
	}
	
	// Grab row from the database
	public function load()
	{
		$this->_mysql_load($this->get('uid'));
	}


	private function _mysql_load($uid)
	{

		$db = &JFactory::getDBO();

		if (empty($uid))
		{
			$this->setError('No uid');
			return false;
		}

		if (is_numeric($uid))
			$query = "SELECT * FROM #__neesprofile WHERE uid = " . $db->Quote(intval($uid)) . ";";
		else
		{
			$this->setError('Error selecting user data to neesprofile table: '
			. $db->getErrorMsg());

			//echo $query;
			//exit;

			return false;
		}
		$db->setQuery($query);
		$result = $db->loadAssoc();

		if (empty($result))
		{
			$this->setError('No such user [' . $uid . ']');
			return false;
		}

		$this->set('NeesAffiliation',  $result['nees_affiliation']);
		$this->set('UserCategory',  $result['user_category']);
		$this->set('NeesRelationship',  $result['nees_relationship']);
		$this->set('ReceiveMaterials', $result['receive_materials']);
		$this->set('UsState', $result['us_state']);
		
	}

	
	private function _mysql_create()
	{
		$db = &JFactory::getDBO();

		if (is_numeric($this->get('uid')))
		{
			$query = 'INSERT INTO #__neesprofile(uid, nees_affiliation) VALUES ('.
			$db->Quote($this->get('uid')) . ',' .
			$db->Quote($this->get('NeesAffiliation')) . ');';

			$db->setQuery( $query );

			if (!$db->query())
			{
				$errno = $db->getErrorNum();

				$this->setError('Error inserting user data to neesprofile table: '
				. $db->getErrorMsg());
					
				return false;
			}
		}

		return true;
	}


	private function _mysql_update()
	{
		$db = &JFactory::getDBO();

		if (is_numeric($this->get('uid')))
		{
			$query = ' UPDATE #__neesprofile ' .
				' SET nees_affiliation = ' . $db->Quote(substr($this->get('NeesAffiliation'),0,100)) . ',' . 
				'  user_category = ' . $db->Quote(substr($this->get('UserCategory'),0,100)) . ',' . 
				'  receive_materials = ' . $db->Quote(substr($this->get('ReceiveMaterials'),0,100)) . ',' . 
				'  us_state = ' . $db->Quote(substr($this->get('UsState'),0,25)) . ',' . 
				'  nees_relationship = ' . $db->Quote(substr($this->get('NeesRelationship'),0,100)) .  
			' WHERE uid = ' . $db->Quote($this->get('uid')) . ';';
			
			//echo $query;
			//exit;
			
			$db->setQuery( $query );

			if (!$db->query())
			{
				$errno = $db->getErrorNum();

				$this->setError('Error updating user data in neesprofile table: '
				. $db->getErrorMsg());
					
				return false;
			}
		}

		return true;
	}

	// Class property accessor method
	public function get($property)
	{

		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists('XNeesProfile',$property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}
		else
		{
			return $this->$property;
		}

	}

	// Class property set method
	public function set($property, $value)
	{
		if ('_' == substr($property, 0, 1))
		{
			$this->setError("Can't access private properties");
			return false;
		}

		if (!property_exists('XNeesProfile', $property))
		{
			$this->setError("Unknown property: $property");
			return false;
		}

		$this->$property = $value;

		return true;
	}

	// Grab all info from the form and populate the proper member variables
	public function loadPost()
	{
		$this->set('UserCategory', JRequest::getVar('neesusercategory', null, 'post') );
		
		if( JRequest::getVar('receivematerials', null, 'post') != '' )
			$this->set('ReceiveMaterials', implode(',',JRequest::getVar('receivematerials', null, 'post')));
		
		$this->set('UsState', JRequest::getVar('UsState', null, 'post'));

		// If the drop down list has 'other' selected, grab the value from the textbox
		if(JRequest::getVar('neesaffiliation', null, 'post') == 'other')
			$this->set('NeesAffiliation', trim(JRequest::getVar('txtneesaffiliation', null, 'post')));
		else	
			$this->set('NeesAffiliation', JRequest::getVar('neesaffiliation', null, 'post') );
		
			
		// If the drop down list has nothing selected, grab the value from the textbox
		if(JRequest::getVar('neesrelationship', null, 'post') == 'other')
			$this->set('NeesRelationship', trim(JRequest::getVar('txtneesrelationship', null, 'post')));
		else
			$this->set('NeesRelationship', JRequest::getVar('neesrelationship', null, 'post'));
		
	}

	// Format error message helper function
	public function setError($msg)
	{
		$bt = debug_backtrace();
		$error = "XNeesProfile::" . $bt[1]['function'] . "():" . $msg;
		array_push($this->_errors, $error);
	}


}

// Class used to encapsulate the HTML code for additional nees profile info collected on hubzero profile page
class XNeesProfileHtml
{

	static function getNeesProfile($uid, $neesaffiliation, $neesusercategory, $neesrelationship, $neesreceivematerials, $neesprofile)
	{
		$document = &JFactory::getDocument();
		$document->addScript( '/templates/newpulse/js/nees.js');
		
		$html = '<div class="explaination">'.n;
		$html.= '<p>By providing this information you are helping us target our efforts to our users.'.n;
		$html.= 'We will <em>not</em> disclose your personal information to others unless required by law,'.n;
		$html.= 'and we will <em>not</em> contact your employer.</p>'.n;
		$html.= '<p>We operate as a community service and are committed to serving a diverse'.n;
		$html.= 'population of users. This information helps us assess our progress '.n;
		$html.= 'towards that goal.</p>'.n;
		$html.= '</div>'.n;

		$html.= '<fieldset>'.n;
		$html.= t.'<h3>NEES Information</h3>'.n;
		
		
		//**** NEES User Category field
		$message = (!empty($neesprofile->_required['UserCategory'])) ? XNeesProfileHtml::error('Please specify a user Category') : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

		$html .= t.t.'<label'.$fieldclass.'>'.n;
		$html.= t.'User Category: <span class="required">required</span>'.n;
		$html.= t.t.'<select name="neesusercategory" id="neesusercategory">'.n;
		$html.= t.t.t.'<option value="">(Select from list)</option>'.n;
		$html.= t.t.t.'<option value="universityundergraduate2yr"' . (($neesusercategory == 'universityundergraduate2yr') ? ' selected="selected"' : '') . '>2 yr University/College Undergraduate</option>'.n;
		$html.= t.t.t.'<option value="universityundergraduate"' . (($neesusercategory == 'universityundergraduate') ? ' selected="selected"' : '') . '>4 yr University/ College Undergraduate</option>'.n;
		$html.= t.t.t.'<option value="universitygraduate"' . (($neesusercategory == 'universitygraduate') ? ' selected="selected"' : '') . '>University / College Graduate Student</option>'.n;
		$html.= t.t.t.'<option value="universitypostdoc"' . (($neesusercategory == 'universitypostdoc') ? ' selected="selected"' : '') . '>University / College Post-Doc</option>'.n;
		$html.= t.t.t.'<option value="universityfaculty"' . (($neesusercategory == 'universityfaculty') ? ' selected="selected"' : '') . '>University / College Faculty</option>'.n;
		$html.= t.t.t.'<option value="universitystaff"' . (($neesusercategory == 'universitystaff') ? ' selected="selected"' : '') . '>University / College Staff</option>'.n;
		$html.= t.t.t.'<option value="precollegestudent"' . (($neesusercategory == 'precollegestudent') ? ' selected="selected"' : '') . '>K-12 (Pre-College) Student</option>'.n;
		$html.= t.t.t.'<option value="precollegefacultystaff"' . (($neesusercategory == 'precollegefacultystaff') ? ' selected="selected"' : '') . '>K-12 (Pre-College) Faculty/Staff</option>'.n;
		$html.= t.t.t.'<option value="nationallab"' . (($neesusercategory == 'nationallab') ? ' selected="selected"' : '') . '>National Laboratory</option>'.n;
		$html.= t.t.t.'<option value="industry"' . (($neesusercategory == 'industry') ? ' selected="selected"' : '') . '>Industry / Private Company</option>'.n;
		$html.= t.t.t.'<option value="government"' . (($neesusercategory == 'government') ? ' selected="selected"' : '') . '>Government Agency</option>'.n;
		$html.= t.t.t.'<option value="military"' . (($neesusercategory == 'military') ? ' selected="selected"' : '') . '>Military</option>'.n;
		$html.= t.t.t.'<option value="unemployed"' . (($neesusercategory == 'unemployed') ? ' selected="selected"' : '') . '>Retired / Unemployed</option>'.n;
		$html.= t.t.'</select>'.n;
		$html.= t.'</label>'.n;
		$html.= $message;
		
		
		//**** NEES Affiliation field
		$neesaffilicationfound = 0;
		$message = (!empty($neesprofile->_required['NeesAffiliation'])) ? XNeesProfileHtml::error('Please specify a NEES Affiliation') : '';
		$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
		$html .= t.t.'<label'.$fieldclass.'>'.n;
				$html.= t.t.'NEES Affiliation: <span class="required">required</span>'.n;
		
				
		$html .= XNeesProfileHtml::createDropdownList('neesaffiliation', 
					'NOTAFFILIATED|UCLA|UCSB|UT|RPI|UCD|CORNELL|LEHIGH|BERKLEY|UI|UM|BUFFALO|UCSD|RENO|OREGON|NEES-PURDUE|NEES-IT|NEES-EOT|NEES-Admin|NEES-R|other',
					'Not Affiliated|University of CA, Los Angeles|University of CA, Santa Barbara|University of Texas, Austin|Rensselaer Polytechnic Institute|University of CA, Davis|Cornell University|Lehigh University|University of CA, Berkley|University of IL, Urbana|University of Minesota|University at Buffalo, SUNY|University of CA, San Diego|University of Nevada, Reno|Oregon State University|Purdue University|NEESComm IT|NEES EOT|NEES Admin|NEESR PI|Other (specify)',
					$neesaffiliation,
					&$neesaffilicationfound,
					'neesAffiliationCheck()');
		$html.= t.'</label>'.n;
		
		// If the value wasn't found in the dropdown, then the 'other' textbox was used
		if($neesaffilicationfound == 0)
		{
			$txtneesaffiliationvalue = $neesaffiliation;
			$txtboxdisabledtext = '';
			$txtboxstylestext = '';
		}
		else
		{
			$txtneesaffiliationvalue = '';
			$txtboxstylestext = 'background-color:#eeeeee';
			$txtboxdisabledtext = "disabled";
		}
		
		$html.= t.t.'<input type="text" id="txtneesaffiliation" name="txtneesaffiliation" ' . 
			'maxlength="100" value="'. $txtneesaffiliationvalue .'" '.$txtboxdisabledtext.' ' .
			'style="' . $txtboxstylestext . '"> ';
		
		$html.= $message;
		
		
		//**** NEES Relationship field
		$neesrelationshipfound = 0;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.'Relationship to NEES:'.n; 
		$html .= XNeesProfileHtml::createDropdownList('neesrelationship', 
					'researcher|sitestaff|neescommitteeworkinggroup|neescommstaff|consultant|govt|grad|ugrad|other',
					'Researcher on currently supported NEESR|Site staff member|Member of NEEScomm committee or working group|NEEScomm staff member|Consultant|Government|Graduate student|Undergraduate student|Other (specify)',
					$neesrelationship,
					&$neesrelationshipfound,
					'relationshipToNeesCheck()');
		$html .= t.t.t.'</label>'.n;
		
		// If the value wasn't found in the dropdown, then the 'other' textbox was used
		if($neesrelationshipfound == 0)
		{
			$txtneesrelationshipvalue = $neesrelationship;
			$txtboxdisabledtext = '';
			$txtboxstylestext = '';
			
		}
		else
		{
			$txtneesrelationshipvalue = '';
			$txtboxstylestext = 'background-color:#eeeeee';
			$txtboxdisabledtext = "disabled";
		}

		$html .= t.t.'<input type="text" id="txtneesrelationship" name="txtneesrelationship" ' . 
			'maxlength="100" value="'. $txtneesrelationshipvalue .'" ' . $txtboxdisabledtext . ' ' .
			'style="' . $txtboxstylestext . '"> ';
		
		
		//**** NEES Receive Materials field
		$html.= t.'<fieldset>'.n;
		$html.= t.t.'<legend>What materials would you like to receive? </legend>'.n;
		$html .= XNeesProfileHtml::createCheckboxList('receivematerials', 
			'student,educator,researcher,materials,k12,ugrad,grad',
			'Students,Educators,Researchers,Software Development,K - 12 (Pre-College),Undergraduate,Graduate / Professional',
			$neesreceivematerials);
		$html.= t.t.'</fieldset>'.n;

		$html.= t.'</fieldset><div class="clear"></div>'.n;


		return $html;
	}

	public function javascriptFunctions()
	{
		
		
	}
	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	
	static function createDropdownList($listName, $listItems, $listItemsLabels, $selectedItem, &$foundselecteditem, $jsOnChangeFunctionName = null)
	{
		$html = '';
		$count = 0;
		$foundselecteditem = 0;
		
		$listItemsArray = explode('|', $listItems);
		$listItemsLabelsArray = explode('|', $listItemsLabels);

		$onChageText = $jsOnChangeFunctionName != null ? 'onChange="' . $jsOnChangeFunctionName . '"' : '';
		
		$html .= '<select name="' . $listName . '" id="' . $listName . '"' . $onChageText . '>'.n;
		$html .= '<option value="">(Select from list)</option>' .n;
		
		foreach ($listItemsArray as $itemiterator)
		{
				if($itemiterator == $selectedItem)
				{
					$selected_text = 'selected';
					$foundselecteditem = 1;
				}
				elseif($itemiterator=='other' and ($selectedItem != '') and $foundselecteditem != 1 )
				{
					// If we get to the 'other' item and a value has been specified for the selected item in the list
					// then select the 'other' item and break. Note: if the other item in the list is not the last
					// item, then you might incorrectly select 'other'. i.e., make sure the 'other' item is last
					// cause once we hit here, the code assumes it can stop checking
					$selected_text = 'selected';
					$foundselecteditem = 0;
				} 
				else
				{
					$selected_text = '';
				}
				
				
				
			$html .= '<option value="' . $itemiterator. '" ' . $selected_text . '>' . $listItemsLabelsArray[$count] . '</option>' .n;
			$count++;	
		}
		
		$html .= '</select>'.n;
		
		return $html;
	}
	
	
	
	
	
	
	static function createCheckboxList($listName, $listItems, $listItemsLabels, $listSelectedItems)
	{
		$html = '';
		$count = 0;
		
		//echo $listItems.br;
		//echo $listItemsLabels.br;
		//echo $listSelectedItems.br;		
		//exit;
		
		$listItemsArray = explode(',', $listItems);
		$listItemsLabelsArray = explode(',', $listItemsLabels);
		$listSelectedItemsArray = explode(',', $listSelectedItems);
		
		foreach ($listItemsArray as $item)
		{
			// See if the item is selected
			foreach ( $listSelectedItemsArray as $selectedItem)
			{
				if($selectedItem == $item)
				{
					$selected_text = 'checked';
					break;
				}
				else
					$selected_text = '';
			}
			
			$html .= '<label><input class="option" name="'.$listName.'[]" value="'.$item.'" id=" '.$listName.' " type="checkbox" ' . $selected_text . '>' . $listItemsLabelsArray[$count] . '</label>'.n;
			$count++;	
		}
		
		return $html;
	}
	
	
	static function cblCheckedText($selectedValuesArray, $value)
	{
		foreach ($selectedValuesArray as $v)
		{
			if ($v = $value) 
				return 'checked';
		}
	}
	
	
	// here or database, it's a tossup. Last state addition was 50 years ago ;)
	// and no, I didn't type this in, Google helped
	static function stateddl($selectedStateCode)
	{
		$html = '<select name="UsState" id="UsState">'.n;
		$html .= '<option value="" selected="selected">(select from list)</option> '.n;
		$html .= '<option value="AL"' . XNeesProfileHtml::stateselect('AL', $selectedStateCode) . '>Alabama</option>'.n;
		$html .= '<option value="AK"' . XNeesProfileHtml::stateselect('AK', $selectedStateCode) . '>Alaska</option> '.n;
		$html .= '<option value="AZ"' . XNeesProfileHtml::stateselect('AZ', $selectedStateCode) . '>Arizona</option> '.n;
		$html .= '<option value="AR"' . XNeesProfileHtml::stateselect('AR', $selectedStateCode) . '>Arkansas</option> '.n;
		$html .= '<option value="CA"' . XNeesProfileHtml::stateselect('CA', $selectedStateCode) . '>California</option> '.n;
		$html .= '<option value="CO"' . XNeesProfileHtml::stateselect('CO', $selectedStateCode) . '>Colorado</option> '.n;
		$html .= '<option value="CT"' . XNeesProfileHtml::stateselect('CT', $selectedStateCode) . '>Connecticut</option> '.n;
		$html .= '<option value="DE"' . XNeesProfileHtml::stateselect('DE', $selectedStateCode) . '>Delaware</option> '.n;
		$html .= '<option value="DC"' . XNeesProfileHtml::stateselect('DC', $selectedStateCode) . '>District Of Columbia</option> '.n;
		$html .= '<option value="FL"' . XNeesProfileHtml::stateselect('FL', $selectedStateCode) . '>Florida</option> '.n;
		$html .= '<option value="GA"' . XNeesProfileHtml::stateselect('GA', $selectedStateCode) . '>Georgia</option>'.n;
		$html .= '<option value="HI"' . XNeesProfileHtml::stateselect('HI', $selectedStateCode) . '>Hawaii</option> '.n;
		$html .= '<option value="ID"' . XNeesProfileHtml::stateselect('ID', $selectedStateCode) . '>Idaho</option> '.n;
		$html .= '<option value="IL"' . XNeesProfileHtml::stateselect('IL', $selectedStateCode) . '>Illinois</option> '.n;
		$html .= '<option value="IN"' . XNeesProfileHtml::stateselect('IN', $selectedStateCode) . '>Indiana</option> '.n;
		$html .= '<option value="IA"' . XNeesProfileHtml::stateselect('IA', $selectedStateCode) . '>Iowa</option> '.n;
		$html .= '<option value="KS"' . XNeesProfileHtml::stateselect('KS', $selectedStateCode) . '>Kansas</option>'.n;
		$html .= '<option value="KY"' . XNeesProfileHtml::stateselect('KY', $selectedStateCode) . '>Kentucky</option>'.n;
		$html .= '<option value="LA"' . XNeesProfileHtml::stateselect('LA', $selectedStateCode) . '>Louisiana</option> '.n;
		$html .= '<option value="ME"' . XNeesProfileHtml::stateselect('ME', $selectedStateCode) . '>Maine</option>'.n;
		$html .= '<option value="MD"' . XNeesProfileHtml::stateselect('MD', $selectedStateCode) . '>Maryland</option>'.n;
		$html .= '<option value="MA"' . XNeesProfileHtml::stateselect('MA', $selectedStateCode) . '>Massachusetts</option> '.n;
		$html .= '<option value="MI"' . XNeesProfileHtml::stateselect('MI', $selectedStateCode) . '>Michigan</option> '.n;
		$html .= '<option value="MN"' . XNeesProfileHtml::stateselect('MN', $selectedStateCode) . '>Minnesota</option>'.n;
		$html .= '<option value="MS"' . XNeesProfileHtml::stateselect('MS', $selectedStateCode) . '>Mississippi</option> '.n;
		$html .= '<option value="MO"' . XNeesProfileHtml::stateselect('MO', $selectedStateCode) . '>Missouri</option> '.n;
		$html .= '<option value="MT"' . XNeesProfileHtml::stateselect('MT', $selectedStateCode) . '>Montana</option> '.n;
		$html .= '<option value="NE"' . XNeesProfileHtml::stateselect('NE', $selectedStateCode) . '>Nebraska</option> '.n;
		$html .= '<option value="NV"' . XNeesProfileHtml::stateselect('NV', $selectedStateCode) . '>Nevada</option>'.n;
		$html .= '<option value="NH"' . XNeesProfileHtml::stateselect('NH', $selectedStateCode) . '>New Hampshire</option> '.n;
		$html .= '<option value="NJ"' . XNeesProfileHtml::stateselect('NJ', $selectedStateCode) . '>New Jersey</option>'.n;
		$html .= '<option value="NM"' . XNeesProfileHtml::stateselect('NM', $selectedStateCode) . '>New Mexico</option> '.n;
		$html .= '<option value="NY"' . XNeesProfileHtml::stateselect('NY', $selectedStateCode) . '>New York</option>'.n;
		$html .= '<option value="NC"' . XNeesProfileHtml::stateselect('NC', $selectedStateCode) . '>North Carolina</option>'.n;
		$html .= '<option value="ND"' . XNeesProfileHtml::stateselect('ND', $selectedStateCode) . '>North Dakota</option> '.n;
		$html .= '<option value="OH"' . XNeesProfileHtml::stateselect('OH', $selectedStateCode) . '>Ohio</option> '.n;
		$html .= '<option value="OK"' . XNeesProfileHtml::stateselect('OK', $selectedStateCode) . '>Oklahoma</option> '.n;
		$html .= '<option value="OR"' . XNeesProfileHtml::stateselect('OR', $selectedStateCode) . '>Oregon</option>'.n;
		$html .= '<option value="PA"' . XNeesProfileHtml::stateselect('PA', $selectedStateCode) . '>Pennsylvania</option>'.n;
		$html .= '<option value="RI"' . XNeesProfileHtml::stateselect('RI', $selectedStateCode) . '>Rhode Island</option>'.n;
		$html .= '<option value="SC"' . XNeesProfileHtml::stateselect('SC', $selectedStateCode) . '>South Carolina</option>'.n;
		$html .= '<option value="SD"' . XNeesProfileHtml::stateselect('SD', $selectedStateCode) . '>South Dakota</option>'.n;
		$html .= '<option value="TN"' . XNeesProfileHtml::stateselect('TN', $selectedStateCode) . '>Tennessee</option>'.n;
		$html .= '<option value="TX"' . XNeesProfileHtml::stateselect('TX', $selectedStateCode) . '>Texas</option> '.n;
		$html .= '<option value="UT"' . XNeesProfileHtml::stateselect('UT', $selectedStateCode) . '>Utah</option>'.n;
		$html .= '<option value="VT"' . XNeesProfileHtml::stateselect('VT', $selectedStateCode) . '>Vermont</option>'.n;
		$html .= '<option value="VA"' . XNeesProfileHtml::stateselect('VA', $selectedStateCode) . '>Virginia</option>'.n;
		$html .= '<option value="WA"' . XNeesProfileHtml::stateselect('WA', $selectedStateCode) . '>Washington</option>'.n;
		$html .= '<option value="WV"' . XNeesProfileHtml::stateselect('WV', $selectedStateCode) . '>West Virginia</option>'.n;
		$html .= '<option value="WI"' . XNeesProfileHtml::stateselect('WI', $selectedStateCode) . '>Wisconsin</option>'.n;
		$html .= '<option value="WY"' . XNeesProfileHtml::stateselect('WY', $selectedStateCode) . '>Wyoming</option>'.n;
		$html .= '</select>'.n;

		return $html;
	}

	private function stateselect($stateCode, $selectedStateCode)
	{
		if($stateCode === $selectedStateCode)
		return 'selected';
	}





}


?>

