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

$prov = $this->pub->_project->provisioned == 1 ? 1 : 0;

// Get block properties
$step 	  = $this->step;
$block	  = $this->pub->_curationModel->_progress->blocks->$step;
$complete = $block->status->status;
$name	  = $block->name;

$props = $name . '-' . $this->step;

// Build url
$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=publications&action=select'
		: JRoute::_( $route . '&active=publications&action=select') .'/?p=' . $props . '&pid='
		. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);

// Are we in draft flow?
$move = JRequest::getVar( 'move', '' );
$move = $move ? '&move=continue' : '';

$required 		= $this->manifest->params->required;

$elName = "licensePick";

// Get version params and extract agreement
$versionParams 	= new JParameter( $this->pub->params );
$agreed			= $versionParams->get('licenseagreement', 0);

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $step, 0, 'author');

$defaultText = $this->license ? $this->license->text : NULL;
$text = $this->pub->license_text ? $this->pub->license_text : $defaultText;

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete == 1 ? ' el-complete' : ' el-incomplete'; echo ($complete == 0 && $this->license) ? ' el-partial' : ''; ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated ? ' el-updated' : ''; ?> ">
	<div class="element_editing">
		<div class="pane-wrapper">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required && ($complete == 0 && !$this->license)) { ?><span class="required"><?php echo JText::_('Required'); ?></span><?php } ?>
				<?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_LICENSE')); ?> <?php if ($this->license && count($this->selections) > 1) { ?>
				<span class="edit-choice"><a href="<?php echo $selectUrl; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT_LICENSE_CHOICE'); ?></a></span><?php } ?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
				<?php if ($this->license) {
						$info = $this->license->info;
						if ($this->license->url) {
							 $info .= ' <a href="' . $this->license->url . '" class="popup">' . JText::_('PLG_PROJECTS_PUBLICATIONS_READ_LICENSE_TERMS') . '</a>';
						} elseif ($this->license->text)
						{
							$info .= ' <a href="#more-lic" class="more-content">'
							. JText::_('PLG_PROJECTS_PUBLICATIONS_READ_LICENSE_TERMS')
							. '</a>';
							$info .= ' <div class="hidden">';
							$info .= ' 	<div class="full-content" id="more-lic"><pre>' . preg_replace("/\r\n/", "\r", $text) . '</pre></div>';
							$info .= ' </div>';
						} ?>
				<div class="chosenitem">
						<p class="item-title">
					<?php if ($this->license) { echo '<img src="' . $this->license->icon . '" alt="' . htmlentities($this->license->title) . '" />'; } ?><?php echo $this->license->title; ?> 						<span class="item-sub-details"><?php echo $info; ?></span></p>
					<input type="hidden" name="license" id="license" value="<?php echo $this->license->id; ?>" />
					<?php if ($this->license->customizable) { ?>
					<div class="agreements">
						<label><span class="required"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
							<?php echo $this->license->text ? JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_WRITE') : JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_WRITE_AND_CUSTOMIZE'); ?>
							<textarea name="license_text" id="license-text" cols="50" rows="10" class="pubinput"><?php echo preg_replace("/\r\n/", "\r", trim($text)); ?></textarea>
						</label>
						<p class="hidden" id="license-template"><?php echo preg_replace("/\r\n/", "\r", $this->license->text); ?></p>
						<p class="hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_REMOVE_DEFAULTS'); ?></p>
						<span class="mini pub-edit" id="reload"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_RELOAD_TEMPLATE_TEXT'); ?></span>
					</div>
					<?php } else {
						// Word replacements required?
						preg_match_all('/\[([^\]]*)\]/', $this->license->text, $substitutes);
						preg_match_all('/\[([^]]+)\]/', $this->pub->license_text, $matches);
						$i = 0;

						if ($this->license->text && count($substitutes) > 1) { ?>
						<div class="replacements">
							<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_REPLACE_DEFAULTS'); ?></p>
						<?php
							$subs = array_unique($substitutes[1]);
							foreach ($subs as $sub)
							{ ?>
							<label>[<?php echo $sub; ?>]<span class="required"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><input name="substitute[<?php echo $sub; ?>]" type="text" value="<?php echo $versionParams->get('licensecustom' . strtolower($sub), ''); ?>" class="customfield" /></label>
						<?php $i++; } ?>
						</div>
					<?php } ?>

					<?php } ?>
					<?php if ($this->license->agreement == 1) {
							$txt = JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_AGREED') . ' ' . $this->license->title.' '.JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE');
							if ($this->license->url) {
								 $txt = preg_replace("/license terms/", '<a href="' . $this->license->url . '" rel="external">license terms</a>', $txt);
							}
							$txt = preg_replace("/" . $this->license->title . "/", '<strong>' . $this->license->title . '</strong>', $txt);
							?>
						<div class="agreements">
							<label><span class="required"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><input type="checkbox" name="agree" value="1" class="check-required" id="agreement" <?php echo $agreed ? 'checked="checked"' : '';  ?> /><?php echo $txt; ?>.
							</label>
						</div>
						<?php } ?>
				</div>
				<?php } else { ?>
				<div class="list-wrapper">
				<ul class="itemlist" id="license-list">
				<li class="item-new">
					<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CHOOSE_LICENSE'); ?></a></span>
				</li>
				</ul>
				</div>
				<?php } ?>
		</div>
	</div>
</div>