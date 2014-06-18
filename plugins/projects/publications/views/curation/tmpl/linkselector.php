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

$error 			= $this->status->getError();

$required 		= (isset($this->manifest->params->required) && $this->manifest->params->required) ? true : false;
$complete 		= isset($this->status->status) && $this->status->status == 1 ? 1 : 0;
$elName   		= 'element' . $this->elementId;

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

$props = $this->master->block . '-' . $this->master->sequence . '-' . $this->elementId;

$modelAttach = new PublicationsModelAttachmentLink();

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$url = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->master->sequence, $this->elementId, 'curator');

?>

<div id="<?php echo $elName; ?>" class="blockelement fileselector<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php if ($coming) { echo ' el-coming'; } ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated ? ' el-updated' : ''; ?> ">
	<!-- Showing status only -->
	<div class="element_overview">
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div>
		</div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, $url, $this->manifest->label); ?>
		<div class="block-subject">
			<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>
			<?php if (count($this->attachments) > 0) { ?>
			<div class="list-wrapper">
				<ul class="itemlist">
			<?php	$i= 1; ?>
					<?php foreach ($this->attachments as $att) {

						$i++;

						$data 			= new stdClass;
						$data->row 		= $att;
						$data->ordering = $i;
						$data->editUrl  = NULL;
						$data->id		= $att->id;
						$data->props	= $props;
						$data->viewer	= 'curator';

						// Draw attachment
						echo $modelAttach->drawAttachment($data, $this->manifest->params->typeParams);
	 				} ?>
				</ul>
			</div>
			<?php } elseif (!$required) {  ?>
				<p class="noresults">No user input</p>
			<?php } ?>

				<?php if ($error || ($required && !$complete)) { ?>
					<p class="witherror"><?php echo $error ? $error : JText::_('Missing required input'); ?></p>
				<?php } else { ?>

				<?php } ?>
		</div>
	</div>
</div>
