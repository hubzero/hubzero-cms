<?php
/**
 * @package		HUBzero CMS
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block\Description as Base;
use stdClass;

/**
 * Release Notes block
 */
class Notes extends Base
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
	* Numeric block ID
	*
	* @var		integer
	*/
	protected	$_blockId 			= 0;

	/**
	 * Get default manifest for the block
	 *
	 * @return  void
	 */
	public function getManifest($new = false)
	{
		// Load config from db
		$obj = new \Components\Publications\Tables\Block($this->_parent->_db);
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