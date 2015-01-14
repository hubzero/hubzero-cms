<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_search'); ?>" method="get" id="searchform" <?php if ($moduleclass_sfx) { echo ' class="' . $moduleclass_sfx . '"'; } ?>>
	<fieldset>
		<legend><?php echo $text; ?></legend>

		<?php
			$output  = '<label for="searchword" id="searchword-label">' . $label . '</label>';
			$output .= '<input type="text" name="searchword" id="searchword" size="' . $width . '" placeholder="' . $text . '" />';

			if ($button) :
				$button = '<input type="submit" value="' . $button_text . '" />';
			endif;

			switch ($button_pos) :
				case 'top' :
					$output = $button . '<br />' . $output;
					break;

				case 'bottom' :
					$output = $output . '<br />' . $button;
					break;

				case 'right' :
					$output = $output . $button;
					break;

				case 'left' :
				default :
					$output = $button . $output;
					break;
			endswitch;

			echo $output;
		?>
	</fieldset>
</form>
