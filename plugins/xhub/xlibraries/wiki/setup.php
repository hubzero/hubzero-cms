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

//----------------------------------------------------------

class WikiSetup 
{
	public function initialize( $option )
	{
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();
	
		$pages = WikiSetup::defaultPages();
		
		if (count($pages) <= 0) {
			return JText::_('No default pages found');
		}
		
		foreach ($pages as $f=>$c) 
		{
			$f = str_replace('_',':',$f);

			// Instantiate a new page
			$page = new WikiPage( $database );
			$page->pagename = $f;
			$page->params = 'mode=wiki'.n;

			// Check content
			if (!$page->check()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
			// Store content
			if (!$page->store()) {
				echo WikiHtml::alert( $page->getError() );
				exit();
			}
			// Ensure we have a page ID
			if (!$page->id) {
				$page->id = $database->insertid();
			}

			// Instantiate a new revision
			$revision = new WikiPageRevision( $database );
			$revision->pageid     = $page->id;
			$revision->created    = date( 'Y-m-d H:i:s', time() );
			$revision->created_by = $juser->get('id');
			$revision->minor_edit = 0;
			$revision->version    = 1;
			$revision->pagetext   = $c;
			$revision->approved   = 1;

			// Transform the wikitext to HTML
			$p = new WikiParser( $page->pagename, $option, $page->scope, $page->pagename );
			$revision->pagehtml = $p->parse( $revision->pagetext );

			// Check content
			if (!$revision->check()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
			// Store content
			if (!$revision->store()) {
				echo WikiHtml::alert( $revision->getError() );
				exit();
			}
		}
		
		return null;
	}
	
	//-----------
	
	public function defaultPages() 
	{
		$path = dirname(__FILE__);
		$d = @dir($path.DS.'default');
		$pages = array();

		if ($d) {
			jimport('joomla.filesystem.file');
			
			while (false !== ($entry = $d->read())) 
			{
				$file = $entry; 
				if (is_file($path.DS.'default'.DS.$file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "txt", $file )) {
						$name = substr($file,0,(strlen($file) - 4));
						$pages[$name] = JFile::read( $path.DS.'default'.DS.$file );
					}
				}
			}
			$d->close();
		}
		
		return $pages;
	}
}
?>