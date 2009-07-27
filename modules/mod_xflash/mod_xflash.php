<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

header ("Cache-Control: cache, must-revalidate");
header ("Pragma: public");

class modXFlash
{
	private $params;

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------
	
	public function display() 
	{
		$params =& $this->params;
		$noflash_link = $params->get('noflash_link');

		ximport('xdocument');
		
		$dynamic = ($params->get('dynamic')) ? $params->get('dynamic') : 0 ;

		if($dynamic) {
			$noflashfile = $params->get('noflash_path');
			$swffile = $params->get('banner_path');
		}
		else {
			$noflashfile = XDocument::getModuleImage('mod_xflash','noflash.jpg');
			$swffile = rtrim( XDocument::getModuleImage('mod_xflash', 'flashrotation.swf'), '.swf');
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

//-------------------------------------------------------------

$modxflash = new modXFlash( $params );

require( JModuleHelper::getLayoutPath('mod_xflash') );
?>
