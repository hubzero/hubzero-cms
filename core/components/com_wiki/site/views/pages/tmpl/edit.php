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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->sub)
{
	$this->css();
}
$this->js('wiki.js', 'com_wiki')
     ->js('jquery.fileuploader.js', 'system');

$tags = $this->page->tags('string');

if ($this->page->exists())
{
	$lid = $this->page->get('id');
}
else
{
	$lid = Request::getInt('lid', (time() . rand(0,10000)), 'post');
}

$macros = \Components\Wiki\Models\Page::oneByPath('Help:WikiMacros', 'site', 0);
$macros->set('scope', $this->book->get('scope'))
	->set('scope_id', $this->book->get('scope_id'));

$formatting = \Components\Wiki\Models\Page::oneByPath('Help:WikiFormatting', 'site', 0);
$formatting->set('scope', $this->book->get('scope'))
	->set('scope_id', $this->book->get('scope_id'));

$authors = array();
foreach ($this->page->authors()->rows() as $auth)
{
	$authors[] = $auth->user()->get('username');
}
$authors = implode(', ', $authors);
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->escape($this->page->title); ?></h2>
	<?php
	//if ($this->page->exists())
	//{
		$this->view('authors')
			//->setBasePath($this->base_path)
			->set('page', $this->page)
			->display();
	//}
	?>
</header><!-- /#content-header -->

<?php
	$this->view('submenu')
		//->setBasePath($this->base_path)
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

<form action="<?php echo Route::url($this->page->link()); ?>" method="post" id="hubForm"<?php echo ($this->sub) ? ' class="full"' : ''; ?>>
	<?php if (!$this->sub) { ?>
		<div class="explaination">
			<?php if ($this->page->exists() && $this->page->access('edit')) { ?>
				<p><?php echo Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', Route::url($this->page->link('rename'))); ?></p>
			<?php } ?>
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

		<div class="grid">
			<div class="col span6">
				<label for="parent">
					<?php echo Lang::txt('COM_WIKI_FIELD_PARENT'); ?>:
					<select name="page[parent]" id="parent">
						<option value="0"><?php echo Lang::txt('COM_WIKI_NONE'); ?></option>
						<?php
						if ($this->tree)
						{
							foreach ($this->tree as $item)
							{
								if ($this->page->get('id') == $item->get('id'))
								{
									continue;
								}
								?>
								<option value="<?php echo $item->get('id'); ?>"<?php if ($this->page->get('parent') == $item->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($item->get('pagename'))); ?></option>
								<?php
							}
						}
						?>
					</select>
				</label>
			</div>
			<div class="col span6 omega">
				<label for="templates">
					<?php echo Lang::txt('COM_WIKI_FIELD_TEMPLATE'); ?>:
					<select name="tplate" id="templates">
						<option value="tc"><?php echo Lang::txt('COM_WIKI_FIELD_TEMPLATE_SELECT'); ?></option>
						<?php
							$hi = array();

							$tplate = strtolower(Request::getVar('tplate', ''));

							foreach ($this->book->templates()->rows() as $template)
							{
								$tmpltags = $template->tags('string');
								if ($tplate == strtolower($template->get('pagename')))
								{
									$tags = $tmpltags;
								}

								echo "\t" . '<option value="t' . $template->get('id') . '"';
								if ($tplate == strtolower($template->get('pagename'))
								 || $tplate == 't' . $template->get('id'))
								{
									echo ' selected="selected"';
									if (!$this->page->exists())
									{
										$this->revision->set('pagetext', stripslashes($template->version->get('pagetext')));
									}
								}
								echo '>' . $this->escape(stripslashes($template->get('title'))) . '</option>' . "\n";

								$j  = '<input type="hidden" name="t' . $template->get('id') . '" id="t' . $template->get('id') . '" value="' . $this->escape(stripslashes($template->version->get('pagetext'))) . '" />' . "\n";
								$j .= '<input type="hidden" name="t' . $template->get('id') . '_tags" id="t' . $template->get('id') . '_tags" value="' . $this->escape(stripslashes($tmpltags)) . '" />' . "\n";

								$hi[] = $j;
							}
						?>
					</select>
				</label>
				<?php echo implode("\n", $hi); ?>
			</div>
		</div>

		<?php if ($this->page->access('edit')) { ?>
			<label for="title">
				<?php echo Lang::txt('COM_WIKI_FIELD_TITLE'); ?>:
				<span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
				<input type="text" name="page[title]" id="title" value="<?php echo $this->escape($this->page->get('title')); ?>" size="38" />
			</label>
		<?php } else { ?>
			<input type="hidden" name="page[title]" id="title" value="<?php echo $this->escape($this->page->get('title')); ?>" />
		<?php } ?>

		<label for="pagetext" style="position: relative;">
			<?php echo Lang::txt('COM_WIKI_FIELD_PAGETEXT'); ?>:
			<span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
			<?php
			echo \Components\Wiki\Helpers\Editor::getInstance()->display('revision[pagetext]', 'pagetext', $this->revision->get('pagetext'), '', '35', '40');
			?>
		</label>

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
					<?php if ($macros) { ?>
						<p><?php echo Lang::txt('COM_WIKI_IMAGE_MACRO_HINT', Route::url($macros->link() . '#image')); ?></p>
						<p><?php echo Lang::txt('COM_WIKI_FILE_MACRO_HINT', Route::url($macros->link() . '#file')); ?></p>
					<?php } ?>
				</div>
			</div><!-- / .grid -->
		</div>
	<?php } ?>
	</fieldset><div class="clear"></div>

	<?php if (!$this->page->exists() || $this->page->get('created_by') == User::get('id') || $this->page->access('manage')) {?>
		<fieldset>
			<legend><?php echo Lang::txt('COM_WIKI_FIELDSET_ACCESS'); ?></legend>

			<?php if ($this->page->access('edit')) {
				$mode = $this->page->param('mode', 'wiki');
				$cls = 'params-knol';
				if ($mode && $mode != 'knol')
				{
					$cls .= ' hide';
				}

				if (!$this->page->exists() || $this->page->get('created_by') == User::get('id') || $this->page->access('manage')) { ?>
					<label for="params_mode">
						<?php echo Lang::txt('COM_WIKI_FIELD_MODE'); ?>: <span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
						<select name="params[mode]" id="params_mode">
							<option value="knol"<?php if ($mode == 'knol') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_KNOL'); ?></option>
							<option value="wiki"<?php if ($mode == 'wiki') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_WIKI'); ?></option>
							<?php if ($this->page->access('admin')) { ?>
								<option value="static"<?php if ($mode == 'static') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_STATIC'); ?></option>
							<?php } ?>
						</select>
					</label>
				<?php } else { ?>
					<input type="hidden" name="params[mode]" id="params_mode" value="<?php echo $mode; ?>" />
				<?php } ?>

					<label class="<?php echo $cls; ?>" for="params_authors">
						<?php echo Lang::txt('COM_WIKI_FIELD_AUTHORS'); ?>:
						<?php
						$mc = Event::trigger(
							'hubzero.onGetMultiEntry',
							array(array(
								'members',
								'authors',
								'params_authors',
								'',
								$authors
							))
						);
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
						<input type="text" name="authors" id="params_authors" value="<?php echo $this->escape($authors); ?>" />
						<?php } ?>
					</label>

					<label class="<?php echo $cls; ?>" for="params_hide_authors">
						<input class="option" type="checkbox" name="params[hide_authors]" id="params_hide_authors"<?php if ($this->page->param('hide_authors') == 1) { echo ' checked="checked"'; } ?> value="1" />
						<?php echo Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS'); ?>
					</label>

					<label class="<?php echo $cls; ?>" for="params_allow_changes">
						<input class="option" type="checkbox" name="params[allow_changes]" id="params_allow_changes"<?php if ($this->page->param('allow_changes') == 1) { echo ' checked="checked"'; } ?> value="1" />
						<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES'); ?>
					</label>

					<label class="<?php echo $cls; ?>" for="params_allow_comments">
						<input class="option" type="checkbox" name="params[allow_comments]" id="params_allow_comments"<?php if ($this->page->param('allow_comments') == 1) { echo ' checked="checked"'; } ?> value="1" />
						<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS'); ?>
					</label>
			<?php } else { ?>
					<input type="hidden" name="params[mode]" value="<?php echo $this->page->param('mode', 'wiki'); ?>" />
					<input type="hidden" name="params[hide_authors]" value="<?php echo $this->page->param('hide_authors', 1); ?>" />
					<input type="hidden" name="params[allow_changes]" value="<?php echo $this->page->param('allow_changes', 1); ?>" />
					<input type="hidden" name="params[allow_comments]" value="<?php echo $this->page->param('allow_comments', 1); ?>" />
					<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($authors); ?>" />
			<?php } ?>

			<?php if ($this->page->access('manage')) { ?>
				<label for="protected">
					<input class="option" type="checkbox" name="page[protected]" id="protected"<?php if ($this->page->isLocked()) { echo ' checked="checked"'; } ?> value="1" />
					<?php echo Lang::txt('COM_WIKI_FIELD_STATE'); ?>
				</label>
			<?php } ?>
		</fieldset>
		<div class="clear"></div>
	<?php } else { ?>
		<input type="hidden" name="page[protected]" value="<?php echo $this->escape($this->page->get('protected', 0)); ?>" />
		<input type="hidden" name="params[mode]" value="<?php echo $this->page->param('mode', 'wiki'); ?>" />
		<input type="hidden" name="params[hide_authors]" value="<?php echo $this->page->param('hide_authors', 1); ?>" />
		<input type="hidden" name="params[allow_changes]" value="<?php echo $this->page->param('allow_changes', 1); ?>" />
		<input type="hidden" name="params[allow_comments]" value="<?php echo $this->page->param('allow_comments', 1); ?>" />
		<input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($authors); ?>" />
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
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', $tags)));
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<input type="text" name="tags" value="'. $tags .'" size="38" />';
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

		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->get('pagename')); ?>" />

		<input type="hidden" name="page[id]" value="<?php echo $this->escape($this->page->get('id')); ?>" />
		<input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->get('access', 0)); ?>" />
		<input type="hidden" name="page[state]" value="<?php echo $this->escape($this->page->get('state', 1)); ?>" />
		<input type="hidden" name="page[scope]" value="<?php echo $this->escape($this->page->get('scope', 'site')); ?>" />
		<input type="hidden" name="page[scope_id]" value="<?php echo $this->escape($this->page->get('scope_id', 0)); ?>" />

		<input type="hidden" name="revision[id]" value="<?php echo $this->escape($this->revision->get('id')); ?>" />
		<input type="hidden" name="revision[page_id]" value="<?php echo $this->escape($this->page->get('id')); ?>" />
		<input type="hidden" name="revision[version]" value="<?php echo $this->escape($this->revision->get('version')); ?>" />
		<input type="hidden" name="revision[created_by]" value="<?php echo $this->escape($this->revision->get('created_by')); ?>" />
		<input type="hidden" name="revision[created]" value="<?php echo $this->escape($this->revision->get('created')); ?>" />

		<?php foreach ($this->page->adapter()->routing('save') as $name => $val) { ?>
			<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
		<?php } ?>

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" class="btn" name="preview" value="<?php echo Lang::txt('COM_WIKI_PREVIEW'); ?>" /> &nbsp;
			<input type="submit" class="btn btn-success" name="submit" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
