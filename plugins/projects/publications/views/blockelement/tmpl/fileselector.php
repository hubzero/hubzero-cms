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

// Get helpers
$projectsHelper = new ProjectsHelper( $this->database );
$pubHelper 		= new PublicationHelper($this->database);

// Load component configs
$config = JComponentHelper::getParams( 'com_projects' );

// Git path
$gitpath  = $config->get('gitpath', '/opt/local/bin/git');

$multiZip 		= (isset($this->manifest->params->typeParams->multiZip)
				&& $this->manifest->params->typeParams->multiZip == 0)
				? false : true;
$required 		= (isset($this->manifest->params->required) && $this->manifest->params->required) ? true : false;
$complete 		= isset($this->status->status) && $this->status->status == 1 ? 1 : 0;
$elName   		= 'element' . $this->elementId;
$max 	  		= $this->manifest->params->max;
$defaultTitle	= $this->manifest->params->title
				? str_replace('{pubtitle}', $this->pub->title, $this->manifest->params->title) : NULL;

// Get version params and extract bundle name
$versionParams 	= new JParameter( $this->pub->params );
$bundleName		= $versionParams->get($elName . 'bundlename', $defaultTitle);

$error 			= $this->status->getError();

// Determine if current element is active/ not yet filled/ last in order
$active = (($this->active == $this->elementId) || !$this->collapse) ? 1 : 0;
$last   = ($this->order == $this->total) ? 1 : 0;
$coming = $this->pub->_curationModel->isComing($this->master->block, $this->master->sequence, $this->active, $this->elementId);

$prov = $this->pub->_project->provisioned == 1 ? 1 : 0;

// Git helper
$config = JComponentHelper::getParams( 'com_projects' );
include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
$git = new ProjectsGitHelper( $config->get('gitpath', '/opt/local/bin/git'), 0, $config->get('offroot', 0) ? '' : JPATH_ROOT
);

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
		? JRoute::_( $route) . '?active=files' . a . 'action=select' . a . 'p=' . $props
			. a . 'vid=' . $this->pub->version_id
		: JRoute::_( $route . '&active=files&action=select') .'/?p=' . $props . '&pid='
			. $this->pub->id . '&vid=' . $this->pub->version_id;

$editUrl = $prov ? JRoute::_($route) : JRoute::_($route . '&active=publications&pid=' . $this->pub->id);

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

$modelAttach = new PublicationsModelAttachmentFile();

// Get pub path
$pubDir  = isset($this->manifest->params->typeParams->directory) && $this->manifest->params->typeParams->directory
			? $this->manifest->params->typeParams->directory : $this->pub->secret;
$pubPath = $pubHelper->buildPath($this->pub->id, $this->pub->version_id, '',
		   $this->manifest->params->typeParams->directory, 1);

$bundleUrl = JRoute::_('index.php?option=com_publications&task=serve&id='
			. $this->pub->id . '&v=' . $this->pub->version_number )
			. '?el=' . $this->elementId . '&download=1';

// Allow rename?
$allowRename = isset($this->manifest->params->typeParams->allowRename)
			 ? $this->manifest->params->typeParams->allowRename
			 : false;

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
			<h5 class="element-title"><?php echo $this->manifest->label; ?> <?php if (count($this->attachments)) { echo '(' . count($this->attachments) .')'; }?>
			<span class="element-options"><a href="<?php echo $this->pub->url . '?version=' . $this->pub->version . '&el=' . $this->elementId . '#' . $elName; ?>"><?php echo JText::_('[edit]'); ?></a></span>
			</h5>
		</div>
	</div>
	<!-- Active editing -->
	<div class="element_editing<?php if (!$active) { echo ' hidden'; } ?>">
		<?php if (count($this->attachments) > 0 && $useHandles)  { ?>
		<div class="handler-aside">
			<?php
				// Present handler options
				echo $modelHandler->showHandlers($this->pub, $this->elementId, $handlers, $handler, $this->attachments);
			?>
		</div>
		<?php } else { ?>
		<div class="block-aside">
			<?php if (count($this->attachments) > 1 && $multiZip )
			{  // Default handler for multiple files - zip together
				?>
				<div class="handler-controls block">
					<div class="handler-type multizip">
						<p><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_MULTI_DOWNLOAD'); ?> <a href="<?php echo $bundleUrl; ?>" title="<?php echo $bundleName; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_ZIP_BUNDLE'); ?>.</a>
						</p>
						<label><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_BUNDLE_NAME'); ?>
							<input type="text" name="elt[<?php echo $this->elementId; ?>][bundlename]" id="<?php echo $elName . 'bundlename'; ?>" value="<?php echo $bundleName; ?>">
							<span class="save-param-status"></span>
							<span class="save-param-wrap"><a href="<?php echo $prov ? JRoute::_( $route ) . '?action=saveparam&vid=' . $this->pub->version_id : JRoute::_( $route . '&active=publications&pid=' . $this->pub->id ) . '?action=saveparam&vid=' . $this->pub->version_id; ?>" class="btn save-param"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?></a></span>
						</label>
					</div>
				</div>
			<?php }
			?>
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
		</div></div>
		<?php } ?>

		<div class="block-subject">
			<span class="checker">&nbsp;</span>
			<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo JText::_('Required'); ?></span><?php } ?><?php if (!$required) { ?><span class="optional"><?php echo JText::_('Optional'); ?></span><?php } ?>
				<?php echo $this->manifest->label; ?> <?php if (count($this->attachments)) { echo '(' . count($this->attachments) .')'; }?>
			</label>
			<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
			<div class="list-wrapper">
			<ul class="itemlist">
		<?php if (count($this->attachments) > 0) {
			$i= 1; ?>
				<?php foreach ($this->attachments as $att) {

					$data 		= new stdClass;
					$data->path = str_replace($this->path . DS, '', $att->path);
					$data->ext 	= strtolower(end(explode('.', $data->path)));

					// Set default title
					$incNum			= $max > 1 ? ' (' . $i . ')' : '';
					$dTitle			= $defaultTitle ? $defaultTitle . $incNum : basename($data->path);
					$data->title 	= $att->title && $att->title != $defaultTitle ? $att->title : $dTitle;
					$data->ordering = $i;
					$data->editUrl  = $editUrl;
					$data->id		= $att->id;
					$data->props	= $props;

					$data->projectPath = $this->path;
					$data->git		   = $git;
					$data->pubPath	   = $pubPath;
					$data->md5		   = $att->content_hash;
					$data->viewer	   = 'edit';
					$data->allowRename = $allowRename;

					$data->downloadUrl = JRoute::_('index.php?option=com_publications&task=serve&id='
										. $this->pub->id . '&v=' . $this->pub->version_number )
										. '?el=' . $this->elementId . a . 'a=' . $att->id . a . 'download=1';

					// Is attachment (image) also publication thumbnail
					$params = new JParameter( $att->params );
					$data->pubThumb = $params->get('pubThumb', NULL);

					// Get file size
					$data->size		= $att->vcs_hash
									? $git->gitLog($this->path, $att->path, $att->vcs_hash, 'size') : NULL;
					$data->hash	  	= $att->vcs_hash;
					$data->gone 	= is_file($this->path . DS . $att->path) ? false : true;
					$data->gitStatus= $data->gone
								? JText::_('PLG_PROJECTS_PUBLICATIONS_MISSING_FILE')
								: $projectsHelper->showGitInfo($gitpath, $this->path, $att->vcs_hash, $att->path);

					$i++;

					// Draw attachment
					echo $modelAttach->drawAttachment($data, $this->manifest->params->typeParams, $handler);
				}
			}  ?>
				</ul>
				<?php if ($max > count($this->attachments)) { ?>
				<div class="item-new">
					<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox nox"><?php echo $prov ? JText::_('PLG_PROJECTS_PUBLICATIONS_ADD_FILES') :  JText::_('PLG_PROJECTS_PUBLICATIONS_CHOOSE_FROM_FILES'); ?></a></span>
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
