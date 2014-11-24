<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$base = rtrim(JURI::getInstance()->base(true), '/');

if ($this->quote)
{
	?>
	<div class="<?php echo $this->module->module; ?>"<?php if ($this->params->get('moduleid')) { echo ' id="' . $this->params->get('moduleid') . '"'; } ?>>
		<blockquote cite="<?php echo $this->escape(stripslashes($this->quote->fullname)); ?>">
			<p>
				<?php
				$text = stripslashes($this->escape($this->quote->quote)) . ' ';
				$text = substr($text, 0, $this->charlimit);
				$text = substr($text, 0, strrpos($text, ' '));

				echo $text;	 ?>
				<?php if (strlen($this->quote->quote) > $this->charlimit) { ?>
					<a href="<?php echo $base; ?>/about/quotes/?quoteid=<?php echo $this->quote->id; ?>" title="<?php echo JText::sprintf('MOD_RANDOMQUOTE_VIEW_FULL', $this->escape(stripslashes($this->quote->fullname))); ?>" class="showfullquote">
						<?php echo JText::_('MOD_RANDOMQUOTE_VIEW'); ?>
					</a>
				<?php } ?>
			</p>
		</blockquote>
		<p class="cite">
			<cite><?php echo $this->escape(stripslashes($this->quote->fullname)); ?></cite>,
			<?php echo $this->escape(stripslashes($this->quote->org)); ?>
			<span>-</span>
			<span><?php echo JText::sprintf('MOD_RANDOMQUOTE_IN', $base . '/about/quotes'); ?></span>
		</p>
	</div>
	<?php
}