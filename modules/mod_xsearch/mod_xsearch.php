<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

class modXSearch
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	//-----------
	
	public function display()
	{
		$params =& $this->params;
		$width  = intval( $params->get( 'width', 20 ) );
		$text   = $params->get( 'text', JText::_('SEARCH_BOX') );
		$clasfx = $params->get( 'moduleclass_sfx' );
		?>

		<form method="get" action="<?php echo JRoute::_('index.php?option=com_xsearch'); ?>" id="searchform"<?php if ($clasfx) { echo ' class="'.$clasfx.'"'; } ?>>
			<fieldset>
				<legend><?php echo $text; ?></legend>
				<label for="searchword"><?php echo $text; ?></label>
				<input type="text" name="searchword" id="searchword" size="<?php echo $width; ?>" value="<?php echo $text; ?>" />
			</fieldset>
		</form>

		<?php
	}
}

//-------------------------------------------------------------

$modxsearch = new modXSearch();
$modxsearch->params = $params;

require( JModuleHelper::getLayoutPath('mod_xsearch') );
?>