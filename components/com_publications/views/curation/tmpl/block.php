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

$project = $this->pub->_project;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

$url = $this->pub->id ? JRoute::_($route . '&pid=' . $this->pub->id) : JRoute::_($route);

$title 	 = $this->manifest->title;

// Get block completion status
$step 	  =  $this->step;
$complete =  $this->pub->_curationModel->_progress->blocks->$step->status->status;

$manifest = $this->pub->_curationModel->_progress->blocks->$step->manifest;
$name	  = $this->pub->_curationModel->_progress->blocks->$step->name;

// Is panel content (of any kind) required?
$required = isset($manifest->params->required) ? $manifest->params->required : 0;

$noElements = $manifest->elements ? false : true;

$about = $manifest->adminTips ? $manifest->adminTips : $manifest->about;

$props = $name . '-' . $step;

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $step, 0, 'curator');

?>
<div class="curation-block">
	<h4><?php echo $title; ?></h4>
	<?php if ($noElements) { ?>
		<div id="<?php echo 'element' . $this->active; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional'; echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?>">
		<div class="element_overview">
			<div class="block-aside"><div class="block-info"><?php echo $about; ?></div>
			</div>
			<?php echo $this->pub->_curationModel->drawChecker($props, $curatorStatus, $url, $title); ?>
			<div class="block-subject">
				<h5 class="element-title"><?php echo $manifest->label; ?></h5>
				<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'curator', 'element' . $this->active); ?>
				<?php echo $this->content; ?>
			</div>
		</div>
		</div>
	<?php } else { ?>
	<div class="curation-item">
		<?php echo $this->content; ?>
	</div>
	<?php } ?>
</div>