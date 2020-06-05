<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	$lid = Request::getInt('lid', (time() . rand(0, 10000)), 'post');
	$lid = '-' . substr($lid, -8);
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
	$authors[] = $auth->user->get('username');
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

<?php if (!$this->sub) { ?>
<section class="main section">
    <div class="aside">
		<?php
		$this->view('wikimenu')
			->set('option', $this->option)
			->set('controller', $this->controller)
			->set('page', $this->page)
			->set('task', $this->task)
			->set('sub', $this->sub)
			->display();
		?>
    </div>
    <div class="subject">
		<?php } ?>

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

		<?php if ($this->sub) { ?>
        <section class="main section">
            <div class="section-inner">
				<?php } ?>

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

                <form action="<?php echo Route::url($this->page->link()); ?>" method="post" id="hubForm" class="full">
					<?php /*if (!$this->sub) { ?>
		<div class="explaination">
			<?php if ($this->page->exists() && $this->page->access('edit')) { ?>
				<p><?php echo Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', Route::url($this->page->link('rename'))); ?></p>
			<?php } ?>
			<div id="file-manager" data-instructions="<?php echo Lang::txt('COM_WIKI_CLICK_OR_DROP_FILE'); ?>" data-action="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=upload&amp;listdir=<?php echo $lid; ?>" data-list="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;no_html=1&amp;controller=media&amp;task=list&amp;listdir=<?php echo $lid; ?>">
				<iframe name="filer" id="filer" src="<?php echo rtrim(Request::base(true), '/'); ?>/index.php?option=com_wiki&amp;tmpl=component&amp;controller=media&amp;scope=<?php echo $this->page->get('scope'); ?>&amp;pagename=<?php echo $this->page->get('pagename'); ?>&amp;listdir=<?php echo $lid; ?>"></iframe>
			</div>
			<div id="file-uploader-list"></div>
		</div>
	<?php } else {*/ ?>
					<?php if ($this->page->exists() && $this->page->access('edit')) { ?>
                        <p><?php echo Lang::txt('COM_WIKI_WARNING_TO_CHANGE_PAGENAME', Route::url($this->page->link('rename'))); ?></p>
					<?php } ?>
					<?php //} ?>
                    <fieldset>
                        <legend><?php echo Lang::txt('COM_WIKI_FIELDSET_PAGE'); ?></legend>

                        <div class="grid">
                            <div class="col span6">
                                <div class="form-group">
                                    <label for="parent">
										<?php echo Lang::txt('COM_WIKI_FIELD_PARENT'); ?>:
                                        <select name="page[parent]" id="parent" class="form-control">
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
                            </div>
                            <div class="col span6 omega">
                                <div class="form-group">
                                    <label for="templates">
										<?php echo Lang::txt('COM_WIKI_FIELD_TEMPLATE'); ?>:
                                        <select name="tplate" id="templates" class="form-control">
                                            <option value="tc"><?php echo Lang::txt('COM_WIKI_FIELD_TEMPLATE_SELECT'); ?></option>
											<?php
											$hi = array();

											$tplate = strtolower(Request::getString('tplate', ''));

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
                        </div>

                        <div class="form-group">
                            <label for="title">
								<?php echo Lang::txt('COM_WIKI_FIELD_TITLE'); ?>:
                                <span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
                                <input type="text" name="page[title]" id="title" class="form-control" value="<?php echo $this->escape($this->page->get('title')); ?>" size="38" />
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="pagetext">
								<?php echo Lang::txt('COM_WIKI_FIELD_PAGETEXT'); ?>:
                                <span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
								<?php
								echo \Components\Wiki\Helpers\Editor::getInstance()->display('revision[pagetext]', 'pagetext', $this->revision->get('pagetext'), 'form-control', '35', '40');
								?>
                            </label>
                        </div>

						<?php //if ($this->sub) { ?>
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
						<?php //} ?>
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
                                    <div class="form-group">
                                        <label for="params_mode">
											<?php echo Lang::txt('COM_WIKI_FIELD_MODE'); ?>: <span class="required"><?php echo Lang::txt('COM_WIKI_REQUIRED'); ?></span>
                                            <select name="params[mode]" id="params_mode" class="form-control">
                                                <option value="knol"<?php if ($mode == 'knol') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_KNOL'); ?></option>
                                                <option value="wiki"<?php if ($mode == 'wiki') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_WIKI'); ?></option>
												<?php if ($this->page->access('admin')) { ?>
                                                    <option value="static"<?php if ($mode == 'static') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_WIKI_FIELD_MODE_STATIC'); ?></option>
												<?php } ?>
                                            </select>
                                        </label>
                                    </div>
								<?php } else { ?>
                                    <input type="hidden" name="params[mode]" id="params_mode" value="<?php echo $mode; ?>" />
								<?php } ?>

                                <div class="form-group">
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
                                            <input type="text" name="authors" id="params_authors" class="form-control" value="<?php echo $this->escape($authors); ?>" />
										<?php } ?>
                                    </label>
                                </div>

                                <div class="form-group form-check">
                                    <label class="<?php echo $cls; ?> form-check-label" for="params_hide_authors">
                                        <input class="option form-check-input" type="checkbox" name="params[hide_authors]" id="params_hide_authors"<?php if ($this->page->param('hide_authors') == 1) { echo ' checked="checked"'; } ?> value="1" />
										<?php echo Lang::txt('COM_WIKI_FIELD_HIDE_AUTHORS'); ?>
                                    </label>
                                </div>

                                <div class="form-group form-check">
                                    <label class="<?php echo $cls; ?> form-check-label" for="params_allow_changes">
                                        <input class="option form-check-input" type="checkbox" name="params[allow_changes]" id="params_allow_changes"<?php if ($this->page->param('allow_changes') == 1) { echo ' checked="checked"'; } ?> value="1" />
										<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_CHANGES'); ?>
                                    </label>
                                </div>

                                <div class="form-group form-check">
                                    <label class="<?php echo $cls; ?> form-check-label" for="params_allow_comments">
                                        <input class="option form-check-input" type="checkbox" name="params[allow_comments]" id="params_allow_comments"<?php if ($this->page->param('allow_comments') == 1) { echo ' checked="checked"'; } ?> value="1" />
										<?php echo Lang::txt('COM_WIKI_FIELD_ALLOW_COMMENTS'); ?>
                                    </label>
                                </div>
							<?php } else { ?>
                                <input type="hidden" name="params[mode]" value="<?php echo $this->escape($this->page->param('mode', 'wiki')); ?>" />
                                <input type="hidden" name="params[hide_authors]" value="<?php echo $this->escape($this->page->param('hide_authors', 1)); ?>" />
                                <input type="hidden" name="params[allow_changes]" value="<?php echo $this->escape($this->page->param('allow_changes', 1)); ?>" />
                                <input type="hidden" name="params[allow_comments]" value="<?php echo $this->escape($this->page->param('allow_comments', 1)); ?>" />
                                <input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($authors); ?>" />
							<?php } ?>

							<?php if ($this->page->access('manage')) { ?>
                                <div class="form-group form-check">
                                    <label for="protected" class="form-check-label">
                                        <input class="option form-check-input" type="checkbox" name="page[protected]" id="protected"<?php if ($this->page->isLocked()) { echo ' checked="checked"'; } ?> value="1" />
										<?php echo Lang::txt('COM_WIKI_FIELD_STATE'); ?>
                                    </label>
                                </div>
							<?php } ?>
                        </fieldset>
                        <div class="clear"></div>
					<?php } else { ?>
                        <input type="hidden" name="page[protected]" value="<?php echo $this->escape($this->page->get('protected', 0)); ?>" />
                        <input type="hidden" name="params[mode]" value="<?php echo $this->escape($this->page->param('mode', 'wiki')); ?>" />
                        <input type="hidden" name="params[hide_authors]" value="<?php echo $this->escape($this->page->param('hide_authors', 1)); ?>" />
                        <input type="hidden" name="params[allow_changes]" value="<?php echo $this->escape($this->page->param('allow_changes', 1)); ?>" />
                        <input type="hidden" name="params[allow_comments]" value="<?php echo $this->escape($this->page->param('allow_comments', 1)); ?>" />
                        <input type="hidden" name="authors" id="params_authors" value="<?php echo $this->escape($authors); ?>" />
					<?php } ?>

					<?php if ($this->page->access('edit')) { ?>
                    <fieldset>
                        <legend><?php echo Lang::txt('COM_WIKI_FIELDSET_METADATA'); ?></legend>

                        <p><?php echo Lang::txt('COM_WIKI_FIELD_TAGS_EXPLANATION'); ?></p>

                        <div class="form-group">
                            <label for="actags">
								<?php echo Lang::txt('COM_WIKI_FIELD_TAGS'); ?>:
								<?php
								$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', $this->escape($tags))));
								if (count($tf) > 0) {
									echo $tf[0];
								} else {
									echo '<input type="text" name="tags" class="form-control" value="'. $this->escape($tags) .'" size="38" />';
								}
								?>
                                <span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_TAGS_HINT'); ?></span>
                            </label>
                        </div>
						<?php } else { ?>
                            <input type="hidden" name="tags" id="actags" value="<?php echo $this->escape($tags); ?>" />
						<?php } ?>

                        <div class="form-group">
                            <label for="field-summary">
								<?php echo Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY'); ?>:
                                <input type="text" name="revision[summary]" id="field-summary" class="form-control" value="<?php echo $this->escape($this->revision->get('summary')); ?>" size="38" />
                                <span class="hint"><?php echo Lang::txt('COM_WIKI_FIELD_EDIT_SUMMARY_HINT'); ?></span>
                            </label>
                        </div>

                        <input type="hidden" name="revision[minor_edit]" value="1" />
                    </fieldset>
                    <div class="clear"></div>

                    <input type="hidden" name="lid" value="<?php echo $lid; ?>" />
                    <input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->get('pagename')); ?>" />

                    <input type="hidden" name="page[id]" value="<?php echo $this->escape($this->page->get('id')); ?>" />
                    <input type="hidden" name="page[access]" value="<?php echo $this->escape($this->page->get('access', 1)); ?>" />
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
            </div>
        </section><!-- / .main section -->