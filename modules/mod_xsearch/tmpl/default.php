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

?>
<form method="get" action="<?php echo JRoute::_('index.php?option=com_ysearch'); ?>" id="searchform"<?php if ($modxsearch->clasfx) { echo ' class="'.$modxsearch->clasfx.'"'; } ?>>
	<fieldset>
		<legend><?php echo $modxsearch->text; ?></legend>
		<label for="searchword" id="searchword-label"><?php echo $modxsearch->text; ?></label>
		<input type="text" name="terms" id="searchword" size="<?php echo $modxsearch->width; ?>" value="<?php echo $modxsearch->text; ?>" />
	</fieldset>
</form>
