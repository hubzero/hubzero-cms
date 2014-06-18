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

$pubParams = new JParameter( $this->publication->params );

?>
<ul class="pub-selector" id="pub-selector">
	<?php foreach ($this->selections as $item)
	{
		$selected = $this->publication->license_type && $this->publication->license_type == $item->id ? true : false;
		$liId = 'choice-' . $item->id;

		$info = $item->info;
		if ($item->url) {
			$info .= ' <a href="'.$item->url.'" rel="external">Read license terms &rsaquo;</a>';
		}

		?>
		<li class="type-license allowed <?php if ($selected) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
			<span class="item-info"></span>
			<span class="item-wrap">
			<?php if ($item->icon) { echo '<img src="' . $item->icon . '" alt="' . htmlentities($item->title) . '" />'; } ?><?php echo $item->title; ?>
			</span>
			<span class="item-fullinfo">
				<?php echo $info; ?>
			</span>
		</li>
	<?php } ?>
</ul>

<?php if ($this->pubconfig->get('suggest_licence')) { ?>
	<p class="hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_DONT_SEE_YOURS') . ' ' . JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_YOU_CAN') ; ?> <a href="<?php echo $this->url . '?action=suggest_license' . a . 'version=' . $this->publication->version_number; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_SUGGEST'); ?></a></p>
<?php } ?>
