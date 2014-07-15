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
 * Release Notes block
 */
class PublicationsBlockNotes extends PublicationsBlockDescription
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name 				= 'notes';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected	$_parentname 		= 'description';

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected	$_manifest 			= NULL;

	/**
	* Step number
	*
	* @var		integer
	*/
	protected	$_sequence 			= 0;

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
				'name' 			=> 'notes',
				'label' 		=> 'Notes',
				'title' 		=> 'Version Release Notes',
				'draftHeading' 	=> 'Add version release notes',
				'draftTagline'	=> '',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 	=> array(
					10 => array (
						'name' 		=> 'metadata',
						'type' 		=> 'metadata',
						'label'		=> 'Release Notes',
						'about'		=> '<p>Version release notes usually include statements about version limitations and/or differences with previous versions, as well as any miscellaneous information that couldn\'t fit elsewhere.</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'required' 		=> 0,
							'aliasmap' 		=> 'release_notes',
							'field' 		=> 'release_notes',
							'input' 		=> 'editor',
							'placeholder'	=> 'Type version release notes',
							'default'		=> '',
							'maxlength' 	=> '3000',
							'cols'			=> '50',
							'rows'			=> '6'
						)
					)
				),
				'params'	=> array( 'required' => 0, 'published_editing' => 0, 'collapse_elements' => 1 )
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}