<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Side text from block manifest
$about = $this->manifest->adminTips ? $this->manifest->adminTips : $this->manifest->about;

// Get curator status
$curatorStatus = $this->pub->curation()->getCurationStatus($this->pub, $this->step, 0, 'curator');

?>
<div class="curation-block">
	<h4><?php echo $this->manifest->title; ?></h4>
	<?php if (!$this->pub->curation('blocks', $this->step, 'hasElements')) { ?>
		<div id="<?php echo 'element' . $this->active; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional'; echo $complete ? ' el-complete' : ' el-incomplete'; echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?>">
		<div class="element_overview">
			<div class="block-aside">
				<div class="block-info"><?php echo $about; ?></div>
			</div>
			<?php echo $this->pub->curation()->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->title); ?>
			<div class="block-subject">
				<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>
				<?php echo $this->pub->curation()->drawCurationNotice($curatorStatus, $props, 'curator', 'element' . $this->active); ?>
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