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

$versionLabel 	= $this->pub->version_label ? $this->pub->version_label : '1.0';
$status 	  	= \Components\Publications\Helpers\Html::getPubStateProperty($this->pub, 'status');
$version 		= $this->pub->versionAlias;
$active 		= $this->active ? $this->active : 'status';
$activenum 		= $this->activenum ? $this->activenum : NULL;

// Are we in draft flow?
$move = ($this->pub->state == 3 || $this->pub->state == 4) ? '&move=continue' : '';

// Build url
$route    = $this->pub->link('editbase');
$pubRoute = $this->pub->id ? $route . '&pid=' . $this->pub->id : $route;

$blocks = $this->pub->_curationModel->_progress->blocks;
$last	= $this->pub->_curationModel->_progress->lastBlock;

$complete = $this->pub->_curationModel->_progress->complete;

$i = 1;

?>
<?php if ($this->pub->id) { ?>
	<p id="version-label" class="version-label<?php if ($active == 'status') { echo ' active'; } ?><?php if ($this->pub->state == 5 || $this->pub->state == 0) { echo ' nobar'; } ?>">
		<a href="<?php echo Route::url( $pubRoute . '&action=versions'); ?>" class="versions" id="v-picker"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSIONS'); ?></a> &raquo;
		<a href="<?php echo Route::url( $pubRoute . '&version=' . $version); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $versionLabel . ' (' . $status . ')'; ?></a>
		<?php if (($this->pub->state == 3 || $this->pub->state == 7) && $complete) { ?>
		- <a href="<?php echo Route::url( $pubRoute . '&action=review&version=' . $version); ?>" class="readytosubmit"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DRAFT_READY_TO_SUBMIT'); ?></a>
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
		<?php foreach ($blocks as $blockId => $block ) {

			$blockname = $block->name;
			$status    = $block->review && $block->review->status != 2
						? $block->review->status : $block->status->status;
			$updated   = $block->review && $this->pub->state != 1 ? $block->review->lastupdate : NULL;

			if ($updated && $block->review->status != 2)
			{
				$class = 'c_wip';
			}
			elseif ($status == 2 || $status == 3)
			{
				$class = 'c_incomplete';
			}
			else
			{
				$class = ($status > 0 || $this->pub->state == 1) ? 'c_passed' : 'c_failed';
			}

			$isComing = $this->pub->_curationModel->isBlockComing($blockname, $blockId, $activenum);
			if (($move && $isComing) && $this->active)
			{
				$class = 'c_pending';
			}

			if ($blockId == $activenum)
			{
				$class = '';
			}
			$i++;

			// Hide review block until in review
			if ($blockname == 'review' && ($blockId != $activenum))
			{
				continue;
			}
		?>
		<li<?php if ($blockId == $activenum) { echo ' class="active"'; } ?>>
			<a href="<?php echo Route::url( $pubRoute . '&section=' . $blockname . '&step=' . $blockId . '&move=continue&version=' . $version); ?>" <?php echo $class ? 'class="' . $class . '"' : '' ; ?>><?php echo $block->manifest->label; ?></a>
		</li>
	<?php } ?>
	</ul>
