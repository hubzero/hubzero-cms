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
?>
<div<?php echo ($this->params->get('cssId')) ? ' id="' . $this->params->get('cssId') . '"' : ''; ?>>
	<form action="/search/" method="get" class="search">
		<fieldset>
			<p>
				<label for="rsearchword"><?php echo JText::_('MOD_FINDRESOURCES_SEARCH_LABEL'); ?></label>
				<input type="text" name="terms" id="rsearchword" value="" />
				<input type="hidden" name="domains[]" value="resources" />
				<input type="submit" value="<?php echo JText::_('MOD_FINDRESOURCES_SEARCH'); ?>" />
			</p>
		</fieldset>
	</form>
<?php if (count($this->tags) > 0) { ?>
	<ol class="tags">
		<li><?php echo JText::_('MOD_FINDRESOURCES_POPULAR_TAGS'); ?></li>
	<?php foreach ($this->tags as $tag) { ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_tags&tag='.$tag->tag); ?>"><?php echo $this->escape(stripslashes($tag->raw_tag)); ?></a></li>
	<?php } ?>
		<li><a href="<?php echo JRoute::_('index.php?option=com_tags'); ?>" class="showmore"><?php echo JText::_('MOD_FINDRESOURCES_MORE_TAGS'); ?></a></li>
	</ol>
<?php } else { ?>
	<p><?php echo JText::_('MOD_FINDRESOURCES_NO_TAGS'); ?></p>
<?php } ?>

<?php if (count($this->categories) > 0) { ?>
	<p>
<?php
	$i = 0;
	foreach ($this->categories as $category)
	{
		$i++;
		$normalized = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($category->type));

		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
?>
		<a href="<?php echo JRoute::_('index.php?option=com_resources&type='.$normalized); ?>"><?php echo $this->escape(stripslashes($category->type)); ?></a><?php echo ($i == count($this->categories)) ? '...' : ', '; ?>
<?php
	}
?>
		<a href="<?php echo JRoute::_('index.php?option=com_resources'); ?>" class="showmore"><?php echo JText::_('MOD_FINDRESOURCES_ALL_CATEGORIES'); ?></a>
	</p>
<?php
}
?>
	<div class="uploadcontent">
		<h4><?php echo JText::_('MOD_FINDRESOURCES_UPLOAD_CONTENT'); ?> <span><a href="<?php echo JRoute::_('index.php?option=com_resources&task=new'); ?>" class="contributelink"><?php echo JText::_('MOD_FINDRESOURCES_GET_STARTED'); ?></a></span></h4>
	</div>
</div><!-- / <?php echo ($this->params->get('cssId')) ? '#' . $this->params->get('cssId') : ''; ?> -->
