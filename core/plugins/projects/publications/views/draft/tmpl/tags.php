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

$elName = "tagsPick";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> <?php echo $curatorStatus->status == 1 ? ' el-passed' : ''; echo $curatorStatus->status == 0 ? ' el-failed' : ''; echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : ''; ?>">
			<div class="element_editing">
				<div class="pane-wrapper">
					<span class="checker">&nbsp;</span>
					<label id="<?php echo $elName; ?>-lbl">
						<?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span>
						<?php } else { ?><span class="optional"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_OPTIONAL'); ?></span><?php } ?>
						<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_TAGS')); ?>
					</label>
						<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>

						<?php
						$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->pub->getTagsForEditing())) );

						if (count($tf) > 0) {
							echo $tf[0];
						} else {
							echo '<textarea name="tags" id="tags" rows="6" cols="35">' . $this->pub->getTagsForEditing() . '</textarea>' . "\n";
						}
						?>
				</div>
			</div>
</div>
<?php if ($this->categories && count($this->categories) > 1) { ?>
<div class="blockelement el-optional el-complete">
	<div class="element_editing">
		<div class="pane-wrapper">
				<span class="checker">&nbsp;</span>
				<label><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_SEARCH_CATEGORY'); ?></label>
		<?php foreach ($this->categories as $cat)
		{
			$params = new \Hubzero\Config\Registry($cat->params);

			// Skip inaplicable category
			if (!$params->get('type_' . $this->pub->base, 1))
			{
				continue;
			}
			?>
				<div class="pubtype-block">
					<input type="radio" name="pubtype" value="<?php echo $cat->id; ?>" <?php if ($this->pub->category == $cat->id) { echo 'checked="checked"'; } ?> class="radio" />
					<?php echo $cat->name; ?>
					<span><?php echo $cat->description; ?></span>
				</div>
		<?php } ?>
		</div>
	</div>
</div>
<?php } ?>