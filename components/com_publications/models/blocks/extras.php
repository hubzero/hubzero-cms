<?php
/**
 * @package		HUBzero CMS
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Content block
 */
class PublicationsBlockExtras extends PublicationsBlockContent
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name 			= 'extras';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected	$_parentname 	= 'content';

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected	$_manifest 		= NULL;

	/**
	* Step number
	*
	* @var		integer
	*/
	protected	$_sequence 		= 0;

	/**
	 * Get default manifest for the block
	 *
	 * @return  void
	 */
	public function getManifest($new = false)
	{
		// Load config from db
		$obj = new PublicationBlock($this->_parent->_db);
		$manifest = $obj->getManifest($this->_name);

		// Fall back
		if (!$manifest)
		{
			$manifest = array(
				'name' 			=> 'extras',
				'label' 		=> 'Extras',
				'title' 		=> 'Publication Gallery and Supporting Docs',
				'draftHeading' 	=> 'Let\'s jazz up publication page',
				'draftTagline'	=> 'Add images/supporting docs:',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 		=> array(
					2 => array (
						'name'		=> 'dataselector',
						'type' 		=> 'attachment',
						'label'		=> 'Image Gallery',
						'about'		=> '<p>Select image file(s) from the project repository</p>',
						'aboutProv'	=> '<p>Attach image file(s) for publication gallery</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'type'			=> 'file',
							'title'			=> '',
							'required' 		=> 0,
							'min' 			=> 0,
							'max' 			=> 50,
							'role' 			=> 3,
							'typeParams'	=> array(
								'allowed_ext' 		=> array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
								'required_ext'  	=> array(),
								'handler'			=> 'imageviewer',
								'handlers'			=> NULL,
								'directory'			=> 'gallery',
								'reuse' 			=> 1,
								'dirHierarchy' 		=> 0,
								'multiZip'			=> 0
							)
						)
					),
					3 => array (
						'name'		=> 'dataselector',
						'type' 		=> 'attachment',
						'label'		=> 'Supporting Docs',
						'about'		=> '<p>And supporting materials related to publication</p>',
						'aboutProv'	=> '<p>Attach a file or a number of files</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'type'			=> 'file',
							'title'			=> '',
							'required' 		=> 0,
							'min' 			=> 0,
							'max' 			=> 500,
							'role' 			=> 2,
							'typeParams'	=> array(
								'allowed_ext' 		=> array(),
								'required_ext'  	=> array(),
								'handler'			=> NULL,
								'handlers'			=> NULL,
								'directory'			=> '',
								'reuse' 			=> 1,
								'dirHierarchy' 		=> 1,
								'multiZip'			=> 0
							)
						)
					)
				),
				'params'	=> array(
					'required' 			=> 0,
					'published_editing' => 1,
					'collapse_elements' => 0,
					'verify_types'		=> 1
				)
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}