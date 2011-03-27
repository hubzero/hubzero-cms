<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


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

