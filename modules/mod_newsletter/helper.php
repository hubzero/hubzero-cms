<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
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

//Dont allow direct access
defined('_JEXEC') or die( 'Restricted access' );

class modNewsletter
{
	
	public function buildNewsletter( ) 
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStyleSheet('mod_newsletter');
		
		// Check if they're a site admin
		if($juser->get('usertype') == 'Super Administrator') {
			$admin = true;
		}
		
		$database =& JFactory::getDBO();
		
		$sql = "SELECT * FROM #__newsletter ORDER BY newsletter_date DESC LIMIT 1";
		$database->setQuery($sql);
		$row = $database->loadAssoc();
	
		$this->newsletter_content = $row['newsletter_content'];
		$this->admin = $admin;
	}
	
	public function sendNewsletter( $content )
	{
		$database =& JFactory::getDBO();
		
		$ncn_log = "/www/nanohub/modules/mod_newsletter/ncnlog_january2011.txt";
		$ncn = fopen($ncn_log, 'w+') or die("can't open file");
		
		$from = '"NCN Newsletter" <contact@nanohub.org>';
		$subject = 'Your nanoHUB.org Newsletter';
		$body = $content;
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: $from" . "\r\n";
		$headers .= "Reply-To: $from". "\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		
		//TO RUN MAILING
		//Comment next line and uncomment line after that to send to real people
		//$rows = array( array('uidNumber' => 1, 'email' => 'csmoak@purdue.edu'),array('uidNumber' => 2, 'email' => 'gperigo@purdue.edu') );
		
		$sql = "SELECT DISTINCT name, email, uidNumber FROM #__xprofiles WHERE emailConfirmed = '1' AND mailPreferenceOption > '0'";
		$database->setQuery($sql);
		$rows = $database->loadAssocList();
		
		$counter = 0;
		foreach($rows as $row) {
			$log =  $row['uidNumber']."\t".$row['email']."\t".date("Y-m-d H:i:s")."\t".'Newsletter'."\t".' January 2011 Newsletter'."\n";
			fwrite($ncn,$log);
			$counter++;
			$email = $row['email'];
			echo "$counter - Send Newsletter to: $email<br>";
			mail($email, $subject, $body, $headers);
		}
		
		fclose($ncn);
	}
	
}