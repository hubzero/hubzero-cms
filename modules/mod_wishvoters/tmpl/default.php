<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<h3><?php echo JText::_('MOD_WISHVOTERS_GIVING_MOST_INPUT'); ?></h3>
<?php if (count($this->rows) <= 0) { ?>
	<p><?php echo JText::_('MOD_WISHVOTERS_NO_VOTES'); ?></p>
<?php } else { ?>
	<ul class="voterslist">
		<li class="title">
			<?php echo JText::_('MOD_WISHVOTERS_COL_NAME'); ?>
			<span><?php echo JText::_('MOD_WISHVOTERS_COL_RANKED'); ?></span>
		</li>
		<?php
			$k=1;
			foreach ($this->rows as $row)
			{
				if ($k <= intval($this->params->get('limit', 10)))
				{
					$name = JText::_('MOD_WISHVOTERS_UNKNOWN');
					$auser = JUser::getInstance($row->userid);
					if (is_object($auser))
					{
						$name  = $auser->get('name');
						$login = $auser->get('username');
					}
					?>
					<li>
						<span class="lnum"><?php echo $k; ?>.</span>
						<?php echo stripslashes($name); ?>
						<span class="wlogin">(<?php echo stripslashes($login); ?>)</span>
						<span><?php echo $row->times; ?></span>
					</li>
					<?php
					$k++;
				}
			}
		?>
	</ul>
<?php } ?>
</div><!-- / <?php echo ($this->params->get('moduleclass')) ? '.' . $this->params->get('moduleclass') : ''; ?> -->