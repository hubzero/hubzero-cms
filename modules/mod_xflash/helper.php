<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modXFlash'
 * 
 * Long description (if any) ...
 */
class modXFlash
{

	/**
	 * Description for 'params'
	 * 
	 * @var integer
	 */
	private $params;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$params =& $this->params;
		$noflash_link = $params->get('noflash_link');

		ximport('Hubzero_Document');

		$dynamic = ($params->get('dynamic')) ? $params->get('dynamic') : 0 ;

		if ($dynamic) {
			$noflashfile = $params->get('noflash_path');
			$swffile = $params->get('banner_path');
		} else {
			$noflashfile = Hubzero_Document::getModuleImage('mod_xflash','noflash.jpg');
			$swffile = rtrim( Hubzero_Document::getModuleImage('mod_xflash', 'flashrotation.swf'), '.swf');
		}

		$document =& JFactory::getDocument();
		//$document->addScript(JURI::base().'modules/mod_xflash/mod_xflash.js');
		$document->addScript('modules/mod_xflash/mod_xflash.js');
		$document->addScriptDeclaration('HUB.ModXflash.admin="0"; HUB.ModXflash.src="'.$swffile.'";');

?>
			<div id="xflash-container">
				<?php if ($noflash_link) { ?><a href="<?php echo $noflash_link; ?>"><?php } ?><img src="<? echo $noflashfile; ?>" width="600" height="230" id="noflashimg" alt="" /><?php if ($noflash_link) { ?></a><?php } ?>
			</div>
<?php
	}
}
