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

$pubHelper 		= $this->pub->_helpers->pubHelper;

$versionLabel 	= $this->pub->version_label ? $this->pub->version_label : '1.0';
$status 	  	= $pubHelper->getPubStateProperty($this->pub, 'status');
$version 		= $this->pub->version;
$active 		= $this->active ? $this->active : 'status';
$activenum 		= $this->activenum ? $this->activenum : NULL;

// Build url
$route = $this->pub->_project->provisioned
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias='
				. $this->pub->_project->alias . '&active=publications';

// Are we in draft flow?
$move = ($this->pub->state == 3 || $this->pub->state == 4) ? '&move=continue' : '';

$pubRoute = $this->pub->id ? $route . '&pid=' . $this->pub->id : $route;

$blocks = $this->pub->_curationModel->_progress->blocks;
$last	= $this->pub->_curationModel->_progress->lastBlock;

$complete = $this->pub->_curationModel->_progress->complete;

$i = 1;

?>
<?php if ($this->pub->id) { ?>
	<p id="version-label" class="version-label<?php if ($active == 'status') { echo ' active'; } ?><?php if ($this->pub->state == 5 || $this->pub->state == 0) { echo ' nobar'; } ?>">
		<a href="<?php echo JRoute::_( $pubRoute) .'/?action=versions'; ?>" class="versions" id="v-picker"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSIONS'); ?></a> &raquo;
		<a href="<?php echo JRoute::_( $pubRoute . '&version=' . $version); ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION').' '.$versionLabel.' (' . $status . ')'; ?></a>
		<?php if (($this->pub->state == 3 || $this->pub->state == 7) && $complete) { ?>
		- <a href="<?php echo JRoute::_( $pubRoute) . '/?action=review'. a . 'version='.$this->pub->version; ?>" class="readytosubmit"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_DRAFT_READY_TO_SUBMIT'); ?></a>
		<?php } ?>
    </p>
<?php } ?>

<?php
	// No bar when unpublished or pending review
	if ($this->pub->state == 5 || $this->pub->state == 0)
	{
		return false;
	}
?>
	<ul id="status-bar" <?php if ($move) { echo 'class="moving"'; } ?>>
		<?php foreach ($blocks as $sequence => $block ) {

			$blockname = $block->name;
			$status    = $block->review && $block->status->status != 0
						? $block->review->status : $block->status->status;
			$updated   = $block->review ? $block->review->lastupdate : NULL;

			if ($status == 2)
			{
				$class = 'c_incomplete';
			}
			elseif ($updated && $block->status->status != 0)
			{
				$class = 'c_wip';
			}
			else
			{
				$class = $status > 0 ? 'c_passed' : 'c_failed';
			}

			$isComing = $this->pub->_curationModel->isBlockComing($blockname, $sequence, $activenum);
			if (($move && $isComing) && $this->active)
			{
				$class = 'c_pending';
			}

			if ($sequence == $activenum)
			{
				$class = '';
			}
			$i++;

			// Hide review block until in review
			if ($blockname == 'review' && ($sequence != $activenum))
			{
				continue;
			}
		?>
		<li<?php if ($sequence == $activenum) { echo ' class="active"'; } ?>><a href="<?php echo JRoute::_( $pubRoute . '&section=' . $blockname . '&step=' . $sequence . '&move=continue&version=' . $version); ?>" <?php echo $class ? 'class="' . $class . '"' : '' ; ?>><?php echo $block->manifest->label; ?></a></li>

	<?php } ?>
	</ul>
