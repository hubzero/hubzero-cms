<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->sub)
{
	$this->css();
}
$this->js('wiki.js', 'com_wiki')
     ->js('jquery.fileuploader.js', 'system');

$tags = $this->page->tags('string') ? $this->page->tags('string') : Request::getVar('tags', '');

if ($this->page->exists())
{
	$lid = $this->page->get('id');
}
else
{
	$lid = Request::getInt('lid', (time() . rand(0,10000)), 'post');
}

// Incoming
$scope   = Request::getVar('scope', '');

?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->task == 'new' ? 'New Note' : $this->escape('Edit Note'); ?></h2>
</header><!-- /#content-header -->

<?php
	$this->view('submenu')
	     ->setBasePath($this->base_path)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('page', $this->page)
	     ->set('task', $this->task)
	     ->set('sub', $this->sub)
	     ->display();
?>

<section class="main section">
<?php
if ($this->page->exists() && !$this->page->access('modify')) {
	if ($this->page->param('allow_changes') == 1) { ?>
		<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED'); ?></p>
<?php } else { ?>
		<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php }
}
?>

<?php if ($this->page->isLocked() && !$this->page->access('manage')) { ?>
	<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_NOT_AUTH_EDITOR'); ?></p>
<?php } ?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->preview) { ?>
	<div id="preview">
		<section class="main section">
			<p class="warning"><?php echo Lang::txt('COM_WIKI_WARNING_PREVIEW_ONLY'); ?></p>

			<div class="wikipage">
				<?php echo $this->revision->get('pagehtml'); ?>
			</div>
		</section><!-- / .section -->
	</div>
<?php } ?>

<form action="<?php echo $this->page->link('save'); ?>" method="post" id="hubForm"<?php echo ($this->sub) ? ' class="full"' : ''; ?>>
<?php if (!$this->sub) { ?>
	<div class="explaination">
	<?php if ($this->page->exists() && $this->page->access('edit')) { ?>
		<p><?php echo Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', Route::url($this->page->link('rename'))); ?></p>
	<?php } ?>
		<p><?php echo Lang::txt('COM_WIKI_IMAGE_MACRO_HINT', Route::url('index.php?option=com_wiki&pagename=Help:WikiMacros#image')); ?></p>
		<p><?php echo Lang::txt('COM_WIKI_FILE_MACRO_HINT', Route::url('index.php?option=com_wiki&pagename=Help:WikiMacros#file')); ?></p>

		<div id="file-manager" data-instructions="<?php echo Lang::txt('COM_WIKI_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=upload&amp;listdir=<?php echo $lid; ?>" data-list="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=list&amp;listdir=<?php echo $lid; ?>">
			<iframe name="filer" id="filer" src="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;tmpl=component&amp;controller=media&amp;scope=<?php echo $this->page->get('scope'); ?>&amp;pagename=<?php echo $this->page->get('pagename'); ?>&amp;listdir=<?php echo $lid; ?>"></iframe>
		</div>
		<div id="file-uploader-list"></div>
	</div>
<?php } else { ?>
	<?php if ($this->page->exists() && $this->page->access('edit')) { ?>
		<p><?php echo Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', Route::url($this->page->link('rename'))); ?></p>
	<?php } ?>
<?php } ?>
	<fieldset>
		<legend><?php echo Lang::txt('COM_WIKI_FIELDSET_PAGE'); ?></legend>

	<?php if ($this->page->access('edit')) { ?>
		<label for="title">
			<?php echo Lang::txt('COM_WIKI_FIELD_TITLE'); ?>:
			<span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
			<input type="text" name="page[title]" id="title" value="<?php echo $this->task == 'new' ? 'New Note' : $this->escape($this->page->get('title')); ?>" size="38" />
		</label>
	<?php } else { ?>
		<input type="hidden" name="page[title]" id="title" value="<?php echo $this->escape($this->page->get('title')); ?>" />
	<?php } ?>

		<label for="pagetext" style="position: relative;">
			<?php echo Lang::txt('COM_WIKI_FIELD_PAGETEXT'); ?>:
			<span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
			<?php
			echo Components\Wiki\Helpers\Editor::getInstance()->display('revision[pagetext]', 'pagetext', $this->revision->get('pagetext'), '', '35', '20');
			?>
		</label>
		<p class="ta-right hint">
			<?php echo Lang::txt('COM_WIKI_FIELD_PAGETEXT_HINT', Route::url('index.php?option=com_wiki&pagename=Help:WikiFormatting')); ?>
		</p>

	<?php if ($this->sub) { ?>
		<div class="field-wrap">
			<div class="grid">
				<div class="col span-half">
					<div id="file-manager" data-instructions="<?php echo Lang::txt('COM_WIKI_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=upload&amp;listdir=<?php echo $lid; ?>" data-list="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=list&amp;listdir=<?php echo $lid; ?>">
						<iframe name="filer" id="filer" src="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;tmpl=component&amp;controller=media&amp;scope=<?php echo $this->page->get('scope'); ?>&amp;pagename=<?php echo $this->page->get('pagename'); ?>&amp;listdir=<?php echo $lid; ?>"></iframe>
					</div>
					<div id="file-uploader-list"></div>
				</div>
				<div class="col span-half omega">
					<p><?php echo Lang::txt('COM_WIKI_IMAGE_MACRO_HINT', Route::url('index.php?option=com_wiki&pagename=Help:WikiMacros#image')); ?></p>
					<p><?php echo Lang::txt('COM_WIKI_FILE_MACRO_HINT', Route::url('index.php?option=com_wiki&pagename=Help:WikiMacros#file')); ?></p>
				</div>
			</div><!-- / .grid -->
		</div>
	<?php } ?>
	</fieldset><div class="clear"></div>

<?php if (!$this->page->exists() || $this->page->get('created_by') == User::get('id') || $this->page->access('manage')) {?>
	<fieldset class="hidden">
		<legend><?php echo Lang::txt('COM_WIKI_FIELDSET_ACCESS'); ?></legend>

		<?php if ($this->page->access('edit')) {
			$mode = $this->page->param('mode', 'wiki');
			$cls = ' class="hide"';
?>
				<label<?php echo $cls; ?>>
					<input class="option" type="checkbox" name="params[hide_authors]" id="params_hide_authors"<?php if ($this->page->param('hide_authors') == 1) { echo ' checked="checked"'; } ?> value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS'); ?>
				</label>
				&nbsp;

				<label<?php echo $cls; ?> for="params_allow_changes">
					<input class="option" type="checkbox" name="params[allow_changes]" id="params_allow_changes"<?php if ($this->page->param('allow_changes') == 1) { echo ' checked="checked"'; } ?> value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES'); ?>
				</label>

				<label<?php echo $cls; ?> for="params_allow_comments">
					<input class="option" type="checkbox" name="params[allow_comments]" id="params_allow_comments"<?php if ($this->page->param('allow_comments') == 1) { echo ' checked="checked"'; } ?> value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS'); ?>
				</label>
		<?php } else { ?>
				<input type="hidden" name="params[mode]" value="<?php echo $this->page->param('mode', 'wiki'); ?>" />
				<input type="hidden" name="params[allow_changes]" value="<?php echo ($this->page->param('allow_changes') == 1) ? '1' : '0'; ?>" />
				<input type="hidden" name="params[allow_comments]" value="<?php echo ($this->page->param('allow_comments') == 1) ? '1' : '0'; ?>" />
				<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($this->page->authors('string')); ?>" />
				<input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->get('access')); ?>" />
		<?php } ?>

			<input type="hidden" name="page[group]" value="<?php echo $this->escape($this->page->get('group_cn')); ?>" />

			<?php if ($this->page->access('manage')) { ?>
				<label for="state">
					<input class="option" type="checkbox" name="page[state]" id="state"<?php if ($this->page->isLocked()) { echo ' checked="checked"'; } ?> value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_STATE'); ?>
				</label>
			<?php } ?>
		</fieldset>
		<div class="clear"></div>
<?php } else { ?>
		<input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->get('access')); ?>" />
		<input type="hidden" name="page[group]" value="<?php echo $this->escape($this->page->get('group_cn')); ?>" />
		<input type="hidden" name="page[state]" value="<?php echo $this->escape($this->page->get('state'), 0); ?>" />
		<input type="hidden" name="authors" value="<?php echo $this->escape($this->page->authors('string')); ?>" />
		<input type="hidden" name="params[mode]" value="<?php echo $this->page->param('mode', 'wiki'); ?>" />
		<input type="hidden" name="params[allow_changes]" value="<?php echo ($this->page->param('allow_changes') == 1) ? '1' : '0'; ?>" />
		<input type="hidden" name="params[allow_comments]" value="<?php echo ($this->page->param('allow_comments') == 1) ? '1' : '0'; ?>" />
<?php } ?>

<?php if ($this->page->access('edit')) { ?>
	<?php if (!$this->sub) { ?>
		<div class="explaination">
			<p><?php echo Lang::txt('COM_WIKI_FIELD_TAGS_EXPLANATION'); ?></p>
		</div>
	<?php } ?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_WIKI_FIELDSET_METADATA'); ?></legend>
			<label>
				<?php echo Lang::txt('COM_WIKI_FIELD_TAGS'); ?>:
				<?php
				$tf = Event::trigger( 'hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $tags)) );
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<input type="text" name="tags" value="' . $tags . '" size="38" />';
				}
				?>
				<span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_TAGS_HINT'); ?></span>
			</label>
<?php } else { ?>
			<input type="hidden" name="tags" value="<?php echo $this->escape($tags); ?>" />
<?php } ?>

			<label for="field-summary">
				<?php echo Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY'); ?>:
				<input type="text" name="revision[summary]" id="field-summary" value="<?php echo $this->escape($this->revision->get('summary')); ?>" size="38" />
				<span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY_HINT'); ?></span>
			</label>

			<input type="hidden" name="revision[minor_edit]" value="1" />
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="page[id]" value="<?php echo $this->escape($this->page->get('id')); ?>" />
		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="pagename" value="<?php echo $this->task == 'new' ? '' : $this->escape($this->page->get('pagename')); ?>" />

		<input type="hidden" name="revision[id]" value="<?php echo $this->escape($this->revision->get('id')); ?>" />
		<input type="hidden" name="revision[pageid]" value="<?php echo $this->escape($this->page->get('id')); ?>" />
		<input type="hidden" name="revision[version]" value="<?php echo $this->escape($this->revision->get('version')); ?>" />
		<input type="hidden" name="revision[created_by]" value="<?php echo $this->escape($this->revision->get('created_by')); ?>" />
		<input type="hidden" name="revision[created]" value="<?php echo $this->escape($this->revision->get('created')); ?>" />
		<input type="hidden" name="params[mode]" id="params_mode" value="wiki" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="page[group_cn]" value="<?php echo $this->escape($this->page->get('group_cn')); ?>" />
		<input type="hidden" name="active" value="notes" />
		<input type="hidden" name="scope" value="<?php echo trim($scope, DS); ?>" />
		<input type="hidden" name="action" value="save" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" class="btn" name="preview" value="<?php echo Lang::txt('COM_WIKI_PREVIEW'); ?>" /> &nbsp;
			<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->