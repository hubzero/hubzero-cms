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

//-------------------------------------------------------------
// Joomla module
// "My Sessions"
//    This module displays the user's active tool sessions
// Middleware component "com_mw" REQUIRED
//-------------------------------------------------------------

include_once( JPATH_ROOT.DS.'components'.DS.'com_mw'.DS.'mw.utils.php' );
include_once( JPATH_ROOT.DS.'components'.DS.'com_mw'.DS.'mw.class.php' );

class modMySessions
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

	//-----------

	private function _setTimeout( $sess ) 
	{
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		$ms->load( $sess );
		$ms->timeout = 1209600;
		$ms->store();
	}
	
	//-----------

	private function _getTimeout( $sess )
	{
		$mwdb =& MwUtils::getMWDBO();
		
		$ms = new MwSession( $mwdb );
		$remaining = $ms->getTimeout();
		
		$tl = 'unknown';

		if (is_numeric($remaining)) {
			$days_left = floor($remaining/60/60/24);
			$hours_left = floor(($remaining - $days_left*60*60*24)/60/60);
			$minutes_left = floor(($remaining - $days_left*60*60*24 - $hours_left*60*60)/60);
			$left = array($days_left, $hours_left, $minutes_left);
			
			$tl  = '';
			$tl .= ($days_left > 0) ? $days_left .' days, ' : '';
			$tl .= ($hours_left > 0) ? $hours_left .' hours, ' : '';
			$tl .= ($minutes_left > 0) ? $minutes_left .' minute' : '';
			$tl .= ($minutes_left > 1) ? 's' : '';
		}
		return $tl;
	}

	//-----------

	private function _session($juser, $app, $authorized) 
	{
		$component = 'index.php?option=com_mw';

		// Build the series of links we need
		$viewurl    = $component.'&task=view&sess='.$app->sessnum.'&tool='.$app->appname;
		$closeurl   = $component.'&task=stop&sess='.$app->sessnum.'&tool='.$app->appname;
		$disconnurl = $component.'&task=unshare&sess='.$app->sessnum.'&tool='.$app->appname;

		// Resume session link
		$html  = "\t\t".' <a href="'.$viewurl.'" title="'.JText::_('MY_SESSIONS_RESUME_TITLE').'">'.$app->sessname;
		if ($authorized === 'admin') {
			$html .= '<br />('.$app->username.')';
		}
		$html .= '</a>'."\n";
		//$html .= ' <span class="time-remaining">'.$this->_getTimeout( $app->session ).'</span>';
	
		// A button to terminate the session.
		if ($juser->get('username') == $app->username || $authorized === 'admin') {
			$html .= "\t\t".' <a class="closetool" href="'.$closeurl.'" title="'.JText::_('MY_SESSIONS_TERMINATE_TITLE').'">'.JText::_('MY_SESSIONS_TERMINATE').'</a>'."\n";
		} else {
			$html .= "\t\t".' <a class="disconnect" href="'.$disconnurl.'" title="'.JText::_('MY_SESSIONS_DISCONNECT_TITLE').'">'.JText::_('MY_SESSIONS_DISCONNECT').'</a> <br />'.JText::_('MY_SESSIONS_OWNER').': '.$app->username."\n";
		}
	
		return $html;
	}

	//-----------

	private function _authorize() 
	{
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			if (in_array('middleware', $xuser->get('admin'))) {
				return 'admin';
			}
		}

		return false;
	}

	//-----------
	
	public function display()
	{
		// Get the module parameters
		$params =& $this->params;
		$moduleclass_sfx = $params->get( 'moduleclass_sfx' );
		$show_storage = $params->get( 'show_storage' );
		
		// Check if the user is an admin.
		$authorized = $this->_authorize();
		
		$juser =& JFactory::getUser();
		
		// Get a list of existing application sessions.
		//$sessions = MwUtils::getSessions( $admin );
		$mwdb =& MwUtils::getMWDBO();
	
		// Start building our HTML
		$html  = '<div class="'.$moduleclass_sfx.'sessionlist">'."\n";
		//$html .= "\t\t".'<h3>'.JText::_('MY_SESSIONS').'</h3>'."\n";
		if (!$mwdb) {
			$html .= "\t".'<p class="error">Middleware component not configured or enabled.</p>'."\n";
			$html .= '</div>'."\n";
			return $html;
		}
		
		//$html .= "\t\t".' <li class="even">Unable to access session database.</li>'."\n";
		$ms = new MwSession( $mwdb );
		$sessions = $ms->getRecords( $juser->get('username'), '', $authorized );
	
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_mysessions');
		
		$html .= "\t".'<ul class="expandedlist">'."\n";
	
		// Iterate through the session list and create links for each.
		$is_even  = 1;
		$appcount = 0;
		if (is_array($sessions))
		foreach ($sessions as $app)
		{
			// If we're on a specific tool page, show sessions for that tool ONLY
			if ($this->specapp && $app->appname != $this->specapp) {
				continue;
			}
				
			// Highlight every other row
			$html .= "\t\t".'<li class="';
			$html .= ($is_even) ? 'even ' : '';
			$html .= 'session">'."\n";
			$html .= $this->_session($juser, $app, $authorized);
			$html .= "\t\t".'</li>'."\n";
			
			$appcount++;
			$is_even ^= 1;
		}
		if ($appcount == 0) {
			if (is_array($sessions))
				$html .= "\t\t".'<li class="session">'.JText::_('MY_SESSIONS_NONE').'</li>'."\n";
			else
				$html .= "\t\t".'<li class="session">Session database is not available.</li>'."\n";
		}
		$html .= "\t".'</ul>'."\n";
		$html .= '</div>'."\n";

		// Get the disk usage
		if ($show_storage) {
			$juser =& JFactory::getUser();
			$du = MwUtils::getDiskUsage($juser->get('username'));
			if (count($du) <=1) {
				// Error
				$config = JFactory::getConfig();

				if ($config->getValue('config.debug')) {
					$html .= '<p class="error">'.JText::_('MY_SESSIONS_ERROR_RETRIEVING_STORAGE').'</p>';
				}
			} else {
				// Calculate the percentage of spaced used
				bcscale(6);
				$val = ($du['softspace'] > 0) ? bcdiv($du['space'], $du['softspace']) : 0;
				$percent = round( $val * 100 );

				// Amount can only have a max of 100 due to some display restrictions
				$amount  = ($percent > 100) ? 100 : $percent;

				// Add the JavaScript file that will do the AJAX magic
				$document =& JFactory::getDocument();
				$document->addScript('modules/mod_mysessions/mod_mysessions.js');

				// Build the HTML
				$html .= "\t\t".'<dl id="diskusage">'."\n";
				$html .= "\t\t".' <dt>'.JText::_('MY_SESSIONS_STORAGE').' (<a href="'.JRoute::_('index.php?option=com_mw&task=storage').'">'.JText::_('MY_SESSIONS_MANAGE').'</a>)</dt>'."\n";
				$html .= "\t\t".' <dd id="du-amount"><div style="width:'.$amount.'%;"><strong>&nbsp;</strong><span>'.$amount.'%</span></div></dd>'."\n";
				if ($percent == 100) {
					$html .= '<dd id="du-msg"><p class="warning">'.JText::_('MY_SESSIONS_MAXIMUM_STORAGE').'</p></dd>'."\n";
				}
				if ($percent > 100) {
					$html .= '<dd id="du-msg"><p class="warning">'.JText::_('MY_SESSIONS_EXCEEDING_STORAGE').'</p></dd>'."\n";
				}
				$html .= "\t\t".'</dl>'."\n";
			}
		}
		
		// Output final HTML
		return $html;
	}
}

//-------------------------------------------------------------

$modmysessions = new modMySessions();
$modmysessions->params = $params;
$modmysessions->specapp = (isset($specapp)) ? $specapp : '';

require( JModuleHelper::getLayoutPath('mod_mysessions') );
?>
