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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
$jconfig =& JFactory::getConfig();

// Get parameters
$rparams = new JParameter( $this->resource->params );
$params = $this->config;
$params->merge( $rparams );
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php

if ($this->progress['submitted'] == 1) {
	if (substr($params->get('license'), 0, 2) == 'cc') {
		/*?>
		<p>This resource is licensed under the <a class="popup" href="legal/cc/">Creative Commons 3.0</a> license recommended by <?php echo $hubShortName; ?>. 
		The <a class="popup" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">license terms</a> support 
		non-commercial use, require attribution, and require sharing derivative works under the same license.</p>
		<?php*/
	} else {
		?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h4>What happens after I submit?</h4>
				<p>The submission will be licensed under Creative Commons</p>
			</div>
			<fieldset>
				<h3>Licensing</h3>
				<label for="license">
					<?php echo JText::_('License'); ?>
					<select name="license" id="license">
						<option value=""><?php echo JText::_('Select license...'); ?></option>
						<option value="custom"><?php echo JText::_('Custom'); ?></option>
				<?php 
				$l = array();
				$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(JText::_('[ENTER LICENSE HERE]')) . '" />';
				foreach ($this->licenses as $license) 
				{
					if (substr($license, 0, 6) == 'custom' && intval(substr($license, 7)) != $this->id) 
					{
						continue;
					}
					?>
						<option value="<?php echo $this->escape($license->name); ?>"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo $this->escape($license->title); ?></option>
					<?php 
					$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
				} 
				?>
					</select>
					<div id="license-preview" style="display:none;"><?php echo JText::_('License preview.'); ?></div>
					<?php echo implode("\n", $l); ?>
				</label>
				<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $this->escape(JText::_('[ENTER LICENSE HERE]')); ?></textarea>
				
				<label><input class="option" type="checkbox" name="license" value="1" /> <span class="optional">optional</span> 
				License the work under the <a class="popup" href="legal/cc/">Creative Commons 3.0</a> license recommended by <?php echo $jconfig->getValue('config.sitename'); ?>. 
				The <a class="popup" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">license terms</a> support 
				non-commercial use, require attribution, and require sharing derivative works under the same license.</label>
			
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
				<input type="hidden" name="published" value="1" />
		 	</fieldset><div class="clear"></div>
			<p class="submit">
				<input type="submit" value="Save" />
			</p>
		</form>
		<?php
	}
	?>
	<p class="help">This contribution has already been submitted and passed review. <a href="<?php echo JRoute::_('index.php?option=com_resources&id='.$this->id); ?>">View it here</a></p>
	<?php
} else {
	?>
	<form action="index.php?option=<?php echo $this->option; ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4>What happens after I submit?</h4>
			<p>Your submission will be reviewed. If it is accepted, the submission will be given a "live" status and will appear 
			in our <a href="<?php echo JRoute::_('index.php?option=com_resources'); ?>">resources</a> and at the top of our <a href="<?php echo JRoute::_('index.php?option=com_whatsnew'); ?>">What's New</a> listing.</p>
		</div>
		<fieldset>
			<h3>Authorization</h3>
			
			<label for="authorization">
				<input class="option" type="checkbox" name="authorization" id="authorization" value="1" /> <span class="required">required</span> 
				I certify that I am the owner of all submitted materials or am authorized by the owner to grant license to its use and that I 
				hereby grant <?php echo $jconfig->getValue('config.sitename'); ?> license to copy, distribute, display, and perform the materials 
				here submitted and any derived or collected works based upon them in perpetuity. <?php echo$jconfig->getValue('config.sitename'); ?> may 
				make modifications to the submitted materials or build upon them as necessary or appropriate for their services. 
				
				<?php echo $jconfig->getValue('config.sitename'); ?> must attribute these materials to the author(s). This is a human-readable summary 
				of the Legal Code (<a class="popup 760x560" href="/legal/license">the full license</a>).
			</label>
			
<?php if ($this->config->get('cc_license')) { ?>
			<label for="license">
				<?php echo JText::_('License'); ?>
				<select name="license" id="license">
					<option value=""><?php echo JText::_('Select license...'); ?></option>
			<?php if ($this->config->get('cc_license_custom')) { ?>
					<option value="custom"><?php echo JText::_('Custom'); ?></option>
			<?php } ?>
			<?php 
			$l = array();
			$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(JText::_('[ENTER LICENSE HERE]')) . '" />';
			foreach ($this->licenses as $license) 
			{ 
				?>
					<option value="<?php echo $this->escape($license->name); ?>"><?php echo $this->escape($license->title); ?></option>
				<?php 
				$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
			} 
			?>
				</select>
				<div id="license-preview" style="display:none;"><?php echo JText::_('License preview.'); ?></div>
				<?php echo implode("\n", $l); ?>
			</label>
			<?php if ($this->config->get('cc_license_custom')) { ?>
				<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $this->escape(JText::_('[ENTER LICENSE HERE]')); ?></textarea>
			<?php } ?>
<?php } ?>
			
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
			<input type="hidden" name="published" value="0" />
	 	</fieldset><div class="clear"></div>
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('COM_CONTRIBUTE_SUBMIT_CONTRIBUTION'); ?>" />
		</div>
	</form>
	
	<h1 id="preview-header"><?php echo JText::_('COM_CONTRIBUTE_REVIEW_PREVIEW'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->id . '&tmpl=component&mode=preview'); ?>"></iframe>
	</div>
	<?php 
}
?>
</div><!-- / .main section -->
