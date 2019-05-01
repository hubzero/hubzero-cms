<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	protected	$_manifest 		= null;

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
								'handlers'			=> null,
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
								'handler'			=> null,
								'handlers'			=> null,
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

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
