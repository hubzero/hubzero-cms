<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	protected	$_manifest 			= null;

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

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
