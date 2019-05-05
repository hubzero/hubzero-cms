<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Build url
$selectUrl = Route::url( $this->pub->link('editversionid') . '&active=team&action=select' . '&p=' . $props);

$showSubmitter  = $this->manifest->params->submitter;
$showGroupOwner = isset($this->manifest->params->group_owner) ? $this->manifest->params->group_owner : '';

$elName = "authorList";

// Get curator status
$curatorStatus = $this->pub->_curationModel->getCurationStatus($this->pub, $this->step, 0, 'author');

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete';
	echo $curatorStatus->status == 1 ? ' el-passed' : '';
	echo $curatorStatus->status == 0 ? ' el-failed' : '';
	echo $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : '';
	?>">
<div class="element_editing">
	<div class="pane-wrapper">
		<span class="checker">&nbsp;</span>
		<label id="<?php echo $elName; ?>-lbl"> <?php if ($required) { ?><span class="required"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REQUIRED'); ?></span><?php } ?>
			<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_AUTHORS')); ?>
		</label>
		<?php echo $this->pub->_curationModel->drawCurationNotice($curatorStatus, $props, 'author', $elName); ?>
	<div class="list-wrapper">
	<?php if (count($this->pub->authors()) > 0) {
		$i= 1; ?>
			<ul class="itemlist" id="author-list">
			<?php foreach ($this->pub->authors() as $author) {
					$org = $author->organization ? $author->organization : $author->p_organization;
					$name = $author->name ? $author->name : $author->p_name;
					$name = trim($name) ? $name : $author->invited_name;
					$name = trim($name) ? $name : $author->invited_email;

					$active 	= in_array($author->project_owner_id, $this->teamids) ? true : false;
					$confirmed 	= $author->user_id ? true : false;

					$details = $author->credit ? stripslashes($author->credit) : null;

					if (!$active)
					{
						$details .= $details ? ' | ' : '';
						$details .= Lang::txt('PLG_PROJECTS_PUBLICATIONS_MISSING_AUTHOR');
					}
				 ?>
				<li class="reorder pick" id="pick-<?php echo $author->id; ?>">
					<span class="item-options">
						<span>
							<?php if (count($this->pub->authors()) > 1) { ?>
							<span class="hint-reorder"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DRAG_TO_REORDER'); ?></span>
							<?php } ?>
							<a href="<?php echo Route::url( $this->pub->link('editversionid') . '&active=publications&action=editauthor&aid=' . $author->id . '&p=' . $props); ?>" class="showinbox item-edit" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_EDIT'); ?>">&nbsp;</a>
							<a href="<?php echo Route::url( $this->pub->link('editversion') . '&active=publications&action=deleteitem&aid=' . $author->id . '&p=' . $props); ?>" class="item-remove" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_REMOVE'); ?>">&nbsp;</a>
						</span>
					</span>
					<span class="item-order"><?php echo $i; ?></span>
					<span class="item-title"><?php echo $name; ?> <span class="item-subtext"><?php echo $org ? ' - ' . $org : ''; ?></span></span>
					<span class="item-details"><?php echo $details; ?></span>
				</li>
		<?php	$i++; } ?>
			</ul>
		<?php  }  ?>
			<div class="item-new">
				<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CHOOSE_AUTHORS'); ?></a></span>
			</div>
		</div>

		<?php if (count($this->pub->authors()) > 1) { ?>
		<p class="hint">*<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_AUTHORS_HINT_DRAG'); ?></p>
		<?php } ?>

		<?php
			// Showing submitter?
			if ($showSubmitter && $this->pub->submitter())
			{ ?>

			<div class="submitter"><p><strong><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTER'); ?>*: </strong>
				<?php echo $this->pub->submitter()->name; ?><?php echo $this->pub->submitter()->organization ? ', ' . $this->pub->submitter()->organization : ''; ?></p>
				<p class="hint">* <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SUBMITTER_ABOUT'); ?>
				</p>
			</div>
		<?php }
			if (($showGroupOwner && $this->groups) || $this->pub->_project->groupOwner())
			{
				if ($this->pub->_project->groupOwner())
				{
					$this->groups[] = $this->pub->_project->groupOwner();
				}
				$used = array();

				?>
			<div class="submitter groupowner"><p><strong><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER'); ?>*: </strong> <?php if ($this->pub->_project->groupOwner()) { echo $this->pub->_project->groupOwner('description') . '(' . $this->pub->_project->groupOwner('cn') . ')'; } ?></p>
				<?php if (!$this->pub->_project->groupOwner()) { ?>
					<select name="group_owner">
						<option value=""><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_NONE'); ?></option>
						<?php foreach ($this->groups as $g) {
							if (in_array($g->gidNumber, $used))
							{
								continue;
							}
							$used[] = $g->gidNumber;
							?>
							<option value="<?php echo $g->gidNumber; ?>" <?php if ($this->pub->groupOwner('id') == $g->gidNumber) { echo 'selected="selected"'; } ?>><?php echo \Hubzero\Utility\Str::truncate($g->description, 30) . ' (' . $g->cn . ')'; ?></option>
						<?php } ?>
					</select>
				<?php } else { ?>
				<input type="hidden" name="group_owner" value="<?php echo $this->pub->_project->groupOwner('id'); ?>" />
				<?php } ?>
				<p class="hint">* <?php echo $this->pub->_project->groupOwner() ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_ABOUT_PROJECT') : Lang::txt('PLG_PROJECTS_PUBLICATIONS_GROUP_OWNER_ABOUT'); ?></p>
			</div>
		<?php }
		?>
	</div>
</div>
</div>
