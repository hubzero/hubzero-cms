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
?>
	<?php if(!$this->getError()) { ?>
		<ul id="c-browser">
			<?php
			if(count($this->tags) > 0) {
				$i = 0; ?>
				<li class="c-head"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_SUGGESTED'); ?><a id="more-tags" href="<?php echo $this->url.'?section=tags'; ?>">	 &#43; <?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_MORE'); ?></a></li>
			<?php
				foreach($this->tags as $tag) {
					 ?>
					<li class="c-click tag:<?php echo urlencode(htmlspecialchars($tag->raw_tag)); ?>" id="tag:<?php echo $tag->id; ?>">
						<span class="pubtag"></span> <?php echo stripslashes($tag->raw_tag); ?>
					</li>
			<?php
				$i++;
			?>
			<?php }
			}
			else { ?>
			<li class="noresults"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_TAGS_NO_SUGGESTED'); ?></li>
		<?php	}
			?>
		</ul>
	<?php }	?>
