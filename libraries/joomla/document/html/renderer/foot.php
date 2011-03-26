<?php
/**
 * @package      hubzero-cms-joomla
 * @file         libraries/joomla/document/html/renderer/foot.php
 * @author       Chris Smoak <csmoak@purdue.edu>
 * @copyright    Copyright (c) 2011 Purdue University. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl2.html GPLv2
 *
 * Copyright (c) 2011 Purdue University
 * All rights reserved.
 *
 * This file is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 2 of the License, or (at your
 * option) any later version.
 *
 * This file is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * This file incorporates work covered by the following copyright and  
 * permission notice:  
 *
 *     @version		$Id: head.php 14401 2010-01-26 14:10:00Z louis $
 *     @package		Joomla.Framework
 *     @subpackage	Document
 *     @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 *     @license		GNU/GPL, see LICENSE.php
 *     Joomla! is free software. This version may have been modified pursuant
 *     to the GNU General Public License, and as distributed it includes or
 *     is derivative of works licensed under the GNU General Public License or
 *     other free or open source software licenses.
 *     See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * JDocument head renderer
 *
 * @package		Joomla.Framework
 * @subpackage	Document
 * @since		1.5
 */
class JDocumentRendererFoot extends JDocumentRenderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @access public
	 * @param string 	$name		(unused)
	 * @param array 	$params		Associative array of values
	 * @return string	The output of the script
	 */
	function render( $head = null, $params = array(), $content = null )
	{
		ob_start();

		echo $this->fetchFoot($this->_doc);

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Generates the head html and return the results as a string
	 *
	 * @access public
	 * @return string
	 */
	function fetchFoot(&$document)
	{
		// get line endings
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();

		$tagEnd	= ' />';

		$strHtml = '';


		// Generate script file links
		foreach ($document->_foot_scripts as $strSrc => $strType) {
			$strHtml .= $tab.'<script type="'.$strType.'" src="'.$strSrc.'"></script>'.$lnEnd;
		}

		// Generate script declarations
		foreach ($document->_foot_script as $type => $content)
		{
			$strHtml .= $tab.'<script type="'.$type.'">'.$lnEnd;

			// This is for full XHTML support.
			if ($document->_mime != 'text/html' ) {
				$strHtml .= $tab.$tab.'<![CDATA['.$lnEnd;
			}

			$strHtml .= $content.$lnEnd;

			// See above note
			if ($document->_mime != 'text/html' ) {
				$strHtml .= $tab.$tab.'// ]]>'.$lnEnd;
			}
			$strHtml .= $tab.'</script>'.$lnEnd;
		}

		return $strHtml;
	}
}
