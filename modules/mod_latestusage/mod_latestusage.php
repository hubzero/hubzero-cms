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

if (!class_exists('modLatestusage')) {
	class modLatestusage
	{
		private $params;

		//-----------

		public function __construct( $params ) 
		{
			$this->params = $params;
		}

		//-----------
		
		private function getOnlineCount() 
		{
		    $db =& JFactory::getDBO();
			$sessions = null;
			
			// calculate number of guests and members
			$result = array();
			$user_array = 0;
			$guest_array = 0;

			$query = "SELECT guest, usertype, client_id FROM #__session WHERE client_id = 0";
			$db->setQuery($query);
			$sessions = $db->loadObjectList();

			if ($db->getErrorNum()) {
				JError::raiseWarning( 500, $db->stderr() );
			}

			if (count($sessions)) {
				foreach ($sessions as $session) 
				{
					// If guest increase guest count by 1
					if ($session->guest == 1 && !$session->usertype) {
						$guest_array++;
					}
					// If member increase member count by 1
					if ($session->guest == 0) {
						$user_array++;
					}
				}
			}

			$result['user']  = $user_array;
			$result['guest'] = $guest_array;

			return $result;
		}
		
		//-----------

		public function display() 
		{
			$database =& JFactory::getDBO();

			$params =& $this->params;
			
			//$count = $this->getOnlineCount();
			
			include_once( JPATH_ROOT.DS.'components'.DS.'com_usage'.DS.'usage.helper.php' );
			$udb =& UsageHelper::getUDBO();
			
			$cls = trim($params->get( 'moduleclass_sfx' ));
			
			if ($udb) {
				$udb->setQuery( 'SELECT value FROM summary_user_vals WHERE datetime = (SELECT MAX(datetime) FROM summary_user_vals) AND period = "12" AND colid = "1" AND rowid = "1"' );
				$users = $udb->loadResult();
				
				$udb->setQuery( 'SELECT value FROM summary_simusage_vals WHERE datetime  = (SELECT MAX(datetime) FROM summary_simusage_vals) AND period = "12" AND colid = "1" AND rowid = "2"' );
				$sims = $udb->loadResult();
			} else {
				$database->setQuery( "SELECT COUNT(*) FROM #__users" );
				$users = $database->loadResult();
				
				$sims = 0;
			}
			
			$database->setQuery( "SELECT COUNT(*) FROM #__resources WHERE standalone=1 AND published=1" );
			$resources = $database->loadResult();
			
			$database->setQuery( "SELECT COUNT(*) FROM #__resources WHERE standalone=1 AND published=1 AND type=7" );
			$tools = $database->loadResult();
			
			$html  = '<table ';
			$html .= ($cls) ? 'class="'.$cls.'" ' : '';
			$html .= 'summary="'.JText::_('Latest usage').'">'."\n";
			/*$html .= "\t".'<thead>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th colspan="2">'.JText::_('Usage for prior 12 months').'</th>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t".'</thead>'."\n";
			$html .= "\t".'<tbody>'."\n";*/
			$html .= "\t".'<caption>'.JText::_('Usage for prior 12 months').'</caption>'."\n";
			$html .= "\t".'<tfoot>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<td><a href="'.JRoute::_('index.php?option=com_usage&task=maps&type=online').'">'.JText::_('Who\'s online?').'</a></td>'."\n";
			$html .= "\t\t\t".'<td class="more"><a href="'.JRoute::_('index.php?option=com_usage').'">'.JText::_('More &rsaquo;').'</a></td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t".'</tfoot>'."\n";
			$html .= "\t".'<tbody>'."\n";
			/*$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Guests Online').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$count['guest'].'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Members Online').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$count['user'].'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";*/
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Users').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$users.'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Resources').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$resources.'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Tools').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$tools.'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t\t".'<tr>'."\n";
			$html .= "\t\t\t".'<th scope="row">'.JText::_('Simulations').'</th>'."\n";
			$html .= "\t\t\t".'<td class="numerical-data">'.$sims.'</td>'."\n";
			$html .= "\t\t".'</tr>'."\n";
			$html .= "\t".'</tbody>'."\n";
			$html .= '</table>'."\n";
			
			// Output HTML
			return $html;
		}
	}
}

//-------------------------------------------------------------

$modlatestusage = new modLatestusage( $params );

require( JModuleHelper::getLayoutPath('mod_latestusage') );
