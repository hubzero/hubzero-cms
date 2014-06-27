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

$active = (($this->active == $this->elementId) || !$this->collapse) ? 1 : 0;
$coming = $this->pub->_curationModel->isComing($this->master->block, $this->master->sequence, $this->active, $this->elementId);
$last   = ($this->order == $this->total) ? 1 : 0;
$max 	= $this->manifest->params->max;
$prov 	= $this->pub->_project->provisioned == 1 ? 1 : 0;

$aboutText = $this->manifest->about ? $this->manifest->about : NULL;

if ($prov == 1 && isset($this->manifest->aboutProv))
{
	$aboutText = $this->manifest->aboutProv;
}

$section  = $this->master->block;
$sequence = $this->master->sequence;

$props = $this->master->block . '-' . $this->master->sequence . '-' . $this->elementId;

$route = $prov
		? 'index.php?option=com_publications&task=submit&pid=' . $this->pub->id
		: 'index.php?option=com_projects&alias=' . $this->pub->_project->alias;
$selectUrl   = $prov
		? JRoute::_( $route) . '?active=links' . a . 'action=select' . a . 'p=' . $props
			. a . 'vid=' . $this->pub->version_id
		: JRoute::_( $route . '&active=links&action=select') .'/?p=' . $props . '&pid='
			. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);
$nextEl  = 'element' . ($this->elementId + 1);

$modelAttach = new PublicationsModelAttachmentLink();

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->master->sequence, $this->elementId, 'author');

?>

<div id="<?php echo $elName; ?>" class="blockelement fileselector<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php if ($coming) { echo ' el-coming'; } ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated ? ' el-updated' : ''; ?> ">
	<!-- Showing status only -->
	<div class="element_overview<?php if ($active) { echo ' hidden'; } ?>">
		<div class="block-aside"></div>
		<div class="block-subject">
			<span class="checker">&nbsp;</span>
			<h5 class="element-title"><?php echo $this->manifest->label; ?>
			<span class="element-options"><a href="<?php echo $this->pub->url . '?version=' . $this->pub->version . '&el=' . $this->elementId . '#' . $elName; ?>"><?php echo JText::_('[edit]'); ?></a></span>
			</h5>
		</div>
	</div>
	<!-- Active editing -->
	<div class="element_editing<?php if (!$active) { echo ' hidden'; } ?>">
		<div class="block-aside">
			<div class="block-info">
			<?php
				$shorten = ($aboutText && strlen($aboutText) > 200) ? 1 : 0;

				if ($shorten)
				{
					$about = \Hubzero\Utility\String::truncate($aboutText, 200, array('html' => true));
					$about.= ' <a href="#more-' . $elName . '" class="more-content">'
								. JText::_('PLG_PROJECTS_PUBLICATIONS_READ_MORE') . '</a>';
					$about.= ' <div class="hidden">';
					$about.= ' 	<div class="full-content" id="more-' . $elName . '">' . $aboutText . '</div>';
					$about.= ' </div>';
				}
				else
				{
					$about = $aboutText;
				}

				echo $about;
			?>
			</div>
		</div>

		<div class="block-subject">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo JText::_('Required'); ?></span><?php } ?><?php if (!$required) { ?><span class="optional"><?php echo JText::_('Optional'); ?></span><?php } ?>
				<?php echo $this->manifest->label; ?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<div class="list-wrapper">
			<ul class="itemlist">
		<?php if (count($this->attachments) > 0) {
			$i= 1; ?>
				<?php foreach ($this->attachments as $att) {

					$i++;

					$data 			= new stdClass;
					$data->row 		= $att;
					$data->ordering = $i;
					$data->editUrl  = $editUrl;
					$data->id		= $att->id;
					$data->props	= $props;
					$data->viewer	= 'edit';

					// Draw attachment
					echo $modelAttach->drawAttachment($data, $this->manifest->params->typeParams);
				}
			}  ?>
				</ul>
				<?php if ($max > count($this->attachments)) { ?>
				<div class="item-new">
					<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox nox"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_URL'); ?></a></span>
				</div>
				<?php } ?>
			</div>
			<?php if ($active && !$last && $this->collapse) { ?>
				<p class="element-move">
				<?php // display error
				 if ($error) { echo '<span class="element-error">' . $error . '</span>'; } ?>
				<span class="button-wrapper icon-next">
					<input type="button" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_GO_NEXT'); ?>" id="<?php echo $elName; ?>-apply" class="save-element btn icon-next"/>
				</span>
				</p>
			<?php } ?>
		</div>
	</div>
</div>
