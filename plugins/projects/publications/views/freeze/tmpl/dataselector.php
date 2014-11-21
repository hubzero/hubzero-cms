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

$multiZip 		= (isset($this->manifest->params->typeParams->multiZip)
				&& $this->manifest->params->typeParams->multiZip == 0)
				? false : true;
$required 		= (isset($this->manifest->params->required) && $this->manifest->params->required) ? true : false;
$complete 		= isset($this->status->status) && $this->status->status == 1 ? 1 : 0;
$elName   		= 'content-element' . $this->elementId;
$max 	  		= $this->manifest->params->max;

// Customize title
$defaultTitle	= $this->manifest->params->title
				? str_replace('{pubtitle}', $this->pub->title,
				$this->manifest->params->title) : NULL;
$defaultTitle	= $this->manifest->params->title
				? str_replace('{pubversion}', $this->pub->version_label,
				$defaultTitle) : NULL;

$error 			= $this->status->getError();

$aboutTxt 		= $this->manifest->adminTips
				? $this->manifest->adminTips
				: $this->manifest->about;

$shorten = ($aboutTxt && strlen($aboutTxt) > 200) ? 1 : 0;

if ($shorten)
{
	$about = \Hubzero\Utility\String::truncate($aboutTxt, 200);
	$about.= ' <a href="#more-' . $elName . '" class="more-content">'
				. JText::_('COM_PUBLICATIONS_READ_MORE') . '</a>';
	$about.= ' <div class="hidden">';
	$about.= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutTxt . '</div>';
	$about.= ' </div>';
}
else
{
	$about = $aboutTxt;
}

// Get version params and extract bundle name
$versionParams 	= new JParameter( $this->pub->params );
$bundleName		= $versionParams->get($elName . 'bundlename', $defaultTitle);
$bundleName		= $bundleName ? $bundleName : 'bundle';
$bundleName	   .= '.zip';

// Get attachment model
$modelAttach = new PublicationsModelAttachments($this->database);

// Get handler model
$modelHandler = new PublicationsModelHandlers($this->database);

// Is there handler choice?
$handlers 	  = $this->manifest->params->typeParams->handlers;

// Is there handler assigned?
$handler 	  = $this->manifest->params->typeParams->handler;
$useHandles   = ($handlers || $handler ) ? true : false;

if ($handler)
{
	// Load handler
	$handler = $modelHandler->ini($handler);
}

$bundleUrl = JRoute::_('index.php?option=com_publications&task=serve&id='
			. $this->pub->id . '&v=' . $this->pub->version_number )
			. '?el=' . $this->elementId . '&download=1';

$props = $this->master->block . '-' . $this->master->sequence . '-' . $this->elementId;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$this->editUrl = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

// Get curator status
if ($this->name == 'curator')
{
	$curatorStatus = $this->pub->_curationModel->getCurationStatus(
		$this->pub,
		$this->master->sequence,
		$this->elementId,
		'curator'
	);
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
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div>
		</div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, $this->editUrl, $this->manifest->label); ?>
		<div class="block-subject">
		<?php } ?>
			<h5 class="element-title"><?php echo $this->manifest->label; ?>
				<?php if (count($this->attachments)) { echo ' (' . count($this->attachments) .')'; }?>
				<?php if (count($this->attachments) > 1 && $multiZip && $this->type == 'file') { ?><span class="download-all"><a href="<?php echo $bundleUrl; ?>" title="<?php echo $bundleName; ?>"><?php echo JText::_('Download all'); ?></a></span><?php } ?></h5>
				<?php if ($this->name == 'curator') { echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', $elName); } ?>
		<?php if (count($this->attachments) > 0) { ?>
		<div class="list-wrapper">
			<ul class="itemlist">
		<?php	$i= 1; ?>
				<?php foreach ($this->attachments as $att) {

					// Collect data
					$data = $modelAttach->buildDataObject(
						$this->type,
						$att,
						$this,
						$i
					);
					if ($data)
					{
						$i++;

						// Draw attachment
						echo $modelAttach->drawAttachment(
							$att->type,
							$data,
							$this->manifest->params->typeParams,
							$handler
						);
					}
				} ?>
			</ul>
		</div>
		<?php } elseif (!$required) {  ?>
			<p class="noresults"><?php echo $this->name == 'curator' ? JText::_('No user input') : JText::_('No items attached'); ?></p>
		<?php } ?>

			<?php if ($error || ($required && !$complete)) { ?>
				<p class="witherror"><?php echo $error ? $error : JText::_('Missing required input'); ?></p>
			<?php } else { ?>

			<?php } ?>
		<?php if ($this->name == 'curator') { ?>
		</div>
		<?php } ?>
	</div>
</div>
