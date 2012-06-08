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

if ($this->quote) { ?>
	<h3 class="notable_quote"><?php echo JText::_('Notable Quote'); ?></h3>
	<div class="frontquote">
		<blockquote cite="<?php echo htmlentities(stripslashes($this->quote->fullname), ENT_COMPAT, 'UTF-8'); ?>">
			<p>
				<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($this->quote_to_show), $this->charlimit, 0); ?>
<?php if (strlen($this->quote->quote) > $this->charlimit) { ?>
				<a href="/about/quotes/?quoteid=<?php echo $this->quote->id; ?>" title="<?php echo JText::_('View the full quote by').' '.htmlentities(stripslashes($this->quote->fullname), ENT_COMPAT, 'UTF-8'); ?>" class="showfullquote">...&raquo;</a>
<?php } ?>
			</p>
		</blockquote>
		<p class="cite">
			<cite><?php echo htmlentities(stripslashes($this->quote->fullname), ENT_COMPAT, 'UTF-8'); ?></cite>, 
			<?php echo htmlentities(stripslashes($this->quote->org), ENT_COMPAT, 'UTF-8'); ?> 
			<span>-</span> 
			<span><?php echo JText::_('in'); ?> <a href="/about/quotes"><?php echo JText::_('Notable Quotes'); ?></a></span>
		</p>
	</div>
<?php } ?>