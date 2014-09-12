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
$elName   		= 'content-element' . $this->elementId;
$max 	  		= $this->manifest->params->max;
$defaultTitle	= $this->manifest->params->title
				? str_replace('{pubtitle}', $this->pub->title, $this->manifest->params->title) : NULL;

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

// Git helper
$config = JComponentHelper::getParams( 'com_projects' );
include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
$git = new ProjectsGitHelper( $config->get('gitpath', '/opt/local/bin/git'), 0, $config->get('offroot', 0) ? '' : JPATH_ROOT
);

// Get version params and extract bundle name
$versionParams 	= new JParameter( $this->pub->params );
$bundleName		= $versionParams->get($elName . 'bundlename', $defaultTitle);
$bundleName		= $bundleName ? $bundleName : 'bundle';
$bundleName	   .= '.zip';

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

$props = $this->master->block . '-' . $this->master->sequence . '-' . $this->elementId;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$url = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->master->sequence, $this->elementId, 'curator');

?>
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated ? ' el-updated' : ''; ?>">
	<!-- Showing status only -->
	<div class="element_overview">
		<div class="block-aside"><div class="block-info"><?php echo $about; ?></div>
		</div>
		<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, $url, $this->manifest->label); ?>
		<div class="block-subject">
			<h5 class="element-title"><?php echo $this->manifest->label; ?>
				<?php if (count($this->attachments)) { echo ' (' . count($this->attachments) .')'; }?>
				<?php if (count($this->attachments) > 1) { ?><span class="download-all"><a href="<?php echo $bundleUrl; ?>" title="<?php echo $bundleName; ?>"><?php echo JText::_('Download all'); ?></a></span><?php } ?></h5>
				<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', $elName); ?>
		<?php if (count($this->attachments) > 0) { ?>
		<div class="list-wrapper">
			<ul class="itemlist">
		<?php	$i= 1; ?>
				<?php foreach ($this->attachments as $att) {

					$data 		= new stdClass;
					$data->path = str_replace($this->path . DS, '', $att->path);
					$parts 		= explode('.', $data->path);
					$data->ext 	= strtolower(end($parts));

					// Set default title
					$incNum			   = $max > 1 ? ' (' . $i . ')' : '';
					$dTitle			   = $defaultTitle ? $defaultTitle . $incNum : basename($data->path);
					$data->title 	   = $att->title && $att->title != $defaultTitle ? $att->title : $dTitle;

					$data->ordering    = $i;
					$data->id		   = $att->id;
					$data->projectPath = $this->path;
					$data->git		   = $git;
					$data->pubPath	   = $pubPath;
					$data->md5		   = $att->content_hash;
					$data->viewer	   = 'freeze';
					$data->downloadUrl = JRoute::_('index.php?option=com_publications&task=serve&id='
										. $this->pub->id . '&v=' . $this->pub->version_number )
										. '?el=' . $this->elementId . '&amp;a=' . $att->id . '&amp;download=1';
					$data->allowRename = false;

					// Is attachment (image) also publication thumbnail
					$params = new JParameter( $att->params );
					$data->pubThumb = $params->get('pubThumb', NULL);
					$data->suffix = $params->get('suffix', NULL);

					// Get file size
					$data->size		= $att->vcs_hash
									? $git->gitLog($this->path, $att->path, $att->vcs_hash, 'size')
									: NULL;
					$data->hash	  	= $att->vcs_hash;
					$data->gone 	= is_file($this->path . DS . $att->path) ? false : true;
					$data->gitStatus= $data->gone
								? JText::_('PLG_PROJECTS_PUBLICATIONS_MISSING_FILE')
								: $projectsHelper->showGitInfo($gitpath, $this->path, $att->vcs_hash, $att->path);

					$i++;

					// Draw attachment
					echo $modelAttach->drawAttachment($data, $this->manifest->params->typeParams, $handler);
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
