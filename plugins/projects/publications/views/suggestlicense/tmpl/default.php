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

// Get publication properties
$typetitle = PublicationHelper::writePubCategory($this->pub->cat_alias, $this->pub->cat_name);

// Suggest new label
$suggested = is_numeric($this->pub->version_label) ? number_format(($this->pub->version_label + 1.0), 1, '.', '') : '';

?>
<div id="abox-content">
<?php if($this->ajax) { ?>
<h3><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_FOR_NEXT_PUB'); ?></h3>
<?php } ?>
<?php
// Display error  message
if ($this->getError()) {
	echo ('<p class="error">'.$this->getError().'</p>');
} ?>

<?php if(!$this->ajax) { ?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=publications'); ?>" method="post" id="plg-form" >
	<div id="plg-header">
	<?php if($this->project->provisioned == 1 ) { ?>
		<h3 class="prov-header"><a href="<?php echo $this->route; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE')); ?></h3>
	<?php } else { ?>
		<h3 class="publications"><a href="<?php echo $this->route; ?>"><?php echo $this->title; ?></a><span class="indlist"> &raquo; <?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE')); ?></span>
		</h3>
	<?php } ?>
	</div>
	<h4><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_FOR_NEXT_PUB'); ?></h4>
<?php } else { ?>
<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
<?php } ?>
	<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE_HOW'); ?></p>
	<fieldset>
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="save_license" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $this->pub->id; ?>" />
		<input type="hidden" name="version" id="version" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
		<?php if($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
	</fieldset>
	<div <?php if(!$this->ajax) { echo 'class="vform"'; } ?>>
		<label class="a-label">
			<span class="faded block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_TITLE'); ?></span>
			<input type="text" name="license_title"  class="long" value="" />
		</label>
		<label class="a-label">
			<span class="faded block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_URL'); ?></span>
			<input type="text" name="license_url"  class="long" value="" />
		</label>
		<label class="a-label">
			<span class="faded block"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_DETAILS'); ?></span>
			<textarea name="details" id="details" rows="10" cols="50" class="long"></textarea>
		</label>
	</div>
		<p class="submitarea">
			<input type="submit" id="submit-ajaxform" class="btn" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SUGGEST_LICENSE'); ?>" />
			<?php if($this->ajax) { ?>
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else { ?>
			<a href="<?php echo $this->url . '?section=license' . a . 'version=' . $this->version; ?>" class="btn btn-cancel"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
			<?php } ?>
		</p>
</form>
</div>