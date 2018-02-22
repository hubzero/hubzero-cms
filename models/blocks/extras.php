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

use Components\Publications\Models\Block\Content as Base;
use stdClass;

/**
 * Content block
 */
class Extras extends Base
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
	* Numeric block ID
	*
	* @var		integer
	*/
	protected	$_blockId 		= 0;

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
				'name' 			=> 'extras',
				'label' 		=> 'Extras',
				'title' 		=> 'Publication Gallery and Supporting Docs',
				'draftHeading' 	=> 'Let\'s jazz up the publication page',
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