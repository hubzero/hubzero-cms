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

$this->css()
     ->js();

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent);

$endPath = ' &raquo; <span class="subheader"><a href="' . $this->url . '/?action=history&amp;asset=' . urlencode($this->file) . '&amp;subdir=' . $this->subdir . '">' . Lang::txt('PLG_PROJECTS_FILES_SHOW_REV_HISTORY_FOR') . ' <span class="italic">' . \Components\Projects\Helpers\Html::shortenFileName($this->file, 40) . '</span></a></span> &raquo; <span class="subheader">' . Lang::txt('PLG_PROJECTS_FILES_SHOW_HISTORY_DIFF') . '</span>';

?>

<?php if ($this->ajax) { ?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_SHOW_HISTORY_DIFF'); ?></h3>
<?php
// Display error
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php } ?>

<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="get" action="<?php echo $this->url; ?>">
	<?php if (!$this->ajax && $this->case == 'files') { ?>
		<div id="plg-header">
			<h3 class="files">
				<a href="<?php echo $this->url; ?>"><?php echo $this->title; ?></a><?php if ($this->subdir) { ?> <?php echo $bc; ?><?php } ?>
			<?php echo $endPath; ?>
			</h3>
		</div>
	<?php } ?>

	<fieldset class="diff-form">
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="file" value="<?php echo $this->file; ?>" />
		<input type="hidden" name="action" value="diff" />

		<?php if (!$this->getError()) { ?>
		<input type="hidden" name="old" value="<?php echo $this->old['val']; ?>" />
		<input type="hidden" name="new" value="<?php echo $this->new['val']; ?>" />
		<?php } ?>

		<?php if (!$this->getError()) { ?>
		<div class="diff-legend">
			<span class="prominent"><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF'); ?>:</span>
			<label><input type="radio" value="side-by-side" name="mode"  <?php if ($this->mode == 'side-by-side') { echo 'checked="checked"'; } ?>  /> <?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_SIDE_BY_SIDE'); ?>
			</label>
			<label><input type="radio" value="inline" name="mode" <?php if ($this->mode == 'inline') { echo 'checked="checked"'; } ?> /> <?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_INLINE'); ?>
			</label>
			<input type="submit" value="Update" id="diff-update" class="btn" />
			<dl>
	            <dt class="diff-unmod"></dt><dd><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_UNMODIFIED'); ?></dd>
	            <dt class="diff-add"></dt><dd><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_ADDED'); ?></dd>
	            <dt class="diff-rem"></dt><dd><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_REMOVED'); ?></dd>
	            <dt class="diff-mod"></dt><dd><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_MODIFIED'); ?></dd>
			 </dl>
		</div>
		<?php } ?>
			<ul class="sample">
				<?php
					// Display list item with file data
					$this->view('default', 'selected')
					     ->set('skip', false)
					     ->set('item', $this->file)
					     ->set('remote', $this->remote)
					     ->set('type', 'file')
					     ->set('action', 'diff')
					     ->set('subdir', $this->subdir)
					     ->set('mult', '')
					     ->display();
				?>
			</ul>

			<?php if ($this->mode == 'side-by-side' && !$this->getError()) { ?>
			<table id="table-diff" class="diff diffSideBySide">
			 <thead>
				<tr>
					<th colspan="2"><?php echo Lang::txt('PLG_PROJECTS_FILES_REV') . ' @' . $this->old['rev'] . ' (' . $this->old['hash'] . ')'; ?></th>
					<th colspan="2"><?php echo Lang::txt('PLG_PROJECTS_FILES_REV') . ' @' . $this->new['rev'] . ' (' . $this->new['hash'] . ')'; ?></th>
				</tr>
			 </thead>

			<?php if ($this->diff) { echo $this->diff; } else {  ?>
				<tr>
					<td colspan="4"><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_UNAVAILABLE'); ?></td>
				</tr>
			<?php } ?>
			</table>
			<?php } ?>

			<?php if ($this->mode == 'inline' && !$this->getError()) { ?>
			<table id="table-diff" class="diff diffInline">
			 <thead>
				<tr>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_REV') . ' @' . $this->old['rev']; ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_REV') . ' @' . $this->new['rev']; ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_DIFFERENCES'); ?></th>
				</tr>
			 </thead>

			<?php if ($this->diff) { echo $this->diff; } else {  ?>
				<tr>
					<td colspan="4"><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_UNAVAILABLE'); ?></td>
				</tr>
			<?php } ?>
			</table>
			<?php } ?>

			<?php if ($this->mode == 'git' && !$this->getError()) {
					$file = $this->old['fpath'] == $this->new['fpath'] ? $this->new['fpath'] : '';
				?>
				<div class="diffGit">
					<h5>
					<?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_COMPARING') . '  @' . $this->old['rev'] . ' (' . $this->old['hash'] . ') and @' . $this->new['rev'] . ' (' . $this->new['hash'] . ') '; ?></h5>
					<?php if ($this->diff) { echo '<pre>' . $this->diff . '</pre>'; } else {  ?>
					<pre><?php echo Lang::txt('PLG_PROJECTS_FILES_DIFF_EMPTY_IDENTICAL'); ?></pre>
				<?php } ?>
				</div>
			<?php } ?>

			<?php if ($this->getError()) {
				echo ('<p class="witherror">'.$this->getError().'</p>');
			} ?>
		</fieldset>
</form>

<?php if ($this->ajax) { ?>
</div>
<?php } ?>