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

// Get block properties
$complete = $this->pub->curation('blocks', $this->master->blockId, 'complete');
$props    = $this->pub->curation('blocks', $this->master->blockId, 'props') . '-' . $this->elementId;
$required = $this->pub->curation('blocks', $this->master->blockId, 'required');

$elName   		= 'description-element' . $this->elementId;
$aliasmap 		= $this->manifest->params->aliasmap;
$field 			= $this->manifest->params->field;
$value 			= $this->pub && isset($this->pub->$field) ? $this->pub->$field : NULL;

$editor			= $this->manifest->params->input == 'editor' ? 1 : 0;
$aboutTxt 		= $this->manifest->adminTips
				? $this->manifest->adminTips
				: $this->manifest->about;

$shorten = ($aboutTxt && strlen($aboutTxt) > 200) ? 1 : 0;

if ($shorten)
{
	$about = \Hubzero\Utility\String::truncate($aboutTxt, 200);
	$about.= ' <a href="#more-' . $elName . '" class="more-content">'
				. Lang::txt('COM_PUBLICATIONS_READ_MORE') . '</a>';
	$about.= ' <div class="hidden">';
	$about.= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutTxt . '</div>';
	$about.= ' </div>';
}
else
{
	$about = $aboutTxt;
}

// Get curator status
if ($this->name == 'curator')
{
	$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->master->blockId, $this->elementId, 'curator');
}

?>
<?php if ($this->name == 'curator') { ?>
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; echo ($curatorStatus->status == 3 && !$complete) ? ' el-skipped' : ''; ?>">
<?php } else { ?>
	<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete'; ?>">
<?php } ?>
	<!-- Showing status only -->
	<div class="element_overview">
		<?php if ($this->name == 'curator') { ?>
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div></div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->label); ?>
		<div class="block-subject">
		<?php } ?>
			<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>
			    <?php if ($this->name == 'curator') { $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', $elName); } ?>
			<?php if ($value) {
				// Parse editor text
				if ($editor)
				{
					$model = new \Components\Publications\Models\Publication($this->pub);
					$value = $model->parse($aliasmap, $field, 'parsed');
				}
				?>
				<div class="element-value"><?php echo $value; ?></div>
			<?php } else { ?>
				<p class="noresults">No user input</p>
				<?php if (!$this->pub->isPublished() && ($this->status->getError() || ($required && !$complete))) { ?>
					<p class="witherror"><?php echo $this->status->getError() ? $this->status->getError() : Lang::txt('Missing required input'); ?></p>
				<?php } ?>
			<?php } ?>
		<?php if ($this->name == 'curator') { ?>
		</div>
		<?php } ?>
	</div>
</div>