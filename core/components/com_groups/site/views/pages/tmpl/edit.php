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

// add styles & scripts
$this->css()
	 ->js()
     ->css('jquery.fancyselect.css', 'system')
     ->js('jquery.fancyselect', 'system')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

// define base link
$base_link = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages';

// define return link
$return      = Request::getVar('return', '');
$return_link = $base_link;
if ($return != '')
{
	if (filter_var(base64_decode($return), FILTER_VALIDATE_URL))
	{
		$return_link = base64_decode($return);
	}
}

// default group page vars
$id        = $this->page->get('id', '');
$gidNumber = $this->page->get('gidNumber', '');
$category  = $this->page->get('category', '');
$alias     = $this->page->get('alias', '');
$title     = $this->page->get('title', '');
$content   = $this->version->get('content', '');
$version   = $this->version->get('version', 0);
$ordering  = $this->page->get('ordering', null);
$state     = $this->page->get('state', 1);
$privacy   = $this->page->get('privacy', 'default');
$home      = $this->page->get('home', 0);
$parent    = $this->page->get('parent', 0);

// determine comments setting
$groupParams = new \Hubzero\Config\Registry($this->group->get('params'));
$groupCommentSetting = $groupParams->get('page_comments', $this->config->get('page_comments', 3));
$groupCommentSettingString = ($groupCommentSetting == 1) ? 'Yes' : 'No';
$comments  = intval($this->page->get('comments', 3));

// default some form vars
$pageHeading = Lang::txt("COM_GROUPS_PAGES_ADD_PAGE");

// if we are in edit mode
if ($this->page->get('id'))
{
	$pageHeading = Lang::txt("COM_GROUPS_PAGES_EDIT_PAGE", $title);
}
?>
<header id="content-header">
	<h2><?php echo $pageHeading; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-prev prev btn" href="<?php echo Route::url($base_link); ?>">
				<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES'); ?></a></li>
		</ul>
	</div>
</header>

<section class="main section edit-group-page">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=save'); ?>" method="POST" id="hubForm" class="full">

		<div class="grid">
			<div class="col span9">
				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_DETAILS'); ?></legend>
					<label for="field-title">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TITLE'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<?php $readonly = ($home) ? 'readonly="readonly"' : ''; ?>
						<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($title)); ?>" <?php echo $readonly; ?> />
					</label>
					<label for="field-url">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_URL'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
						<input type="text" name="page[alias]" id="field-url" value="<?php echo $this->escape($alias); ?>" <?php echo $readonly; ?> />
						<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_URL_HINT'); ?></span>
					</label>
					<label for="pagecontent">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CONTENT'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<?php
							$allowPhp      = true;
							$allowScripts  = true;
							$startupMode   = 'wysiwyg';
							$showSourceBtn = true;

							// only allow super groups to use php & scrips
							// strip out php and scripts if somehow it made it through
							if (!$this->group->isSuperGroup())
							{
								$allowPhp     = false;
								$allowScripts = false;
								$content      = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
								$content      = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
							}

							// open in source mode if contains php or scripts
							if (strstr(stripslashes($content), '<script>') ||
								strstr(stripslashes($content), '<?php'))
							{
								$startupMode  = 'source';
								//$showSourceBtn = false;
							}

							//build config
							$config = array(
								'startupMode'                 => $startupMode,
								'sourceViewButton'            => $showSourceBtn,
								'contentCss'                  => $this->stylesheets,
								//'autoGrowMinHeight'           => 500,
								'height'                      => '500px',
								'fileBrowserWindowWidth'      => 1200,
								'fileBrowserBrowseUrl'        => Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component&' . Session::getFormToken() . '=1', false),
								'fileBrowserImageBrowseUrl'   => Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component&' . Session::getFormToken() . '=1', false),
								'fileBrowserUploadUrl'        => Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=ckeditorupload&tmpl=component&' . Session::getFormToken() . '=1', false),
								'allowPhpTags'                => $allowPhp,
								'allowScriptTags'             => $allowScripts
							);

							// if super group add to templates
							if ($this->group->isSuperGroup())
							{
								$config['templates_replace'] = false;
								$config['templates_files']   = array('pagelayouts' => '/app/site/groups/' . $this->group->get('gidNumber') . '/template/assets/js/pagelayouts.js');
							}

							// display with ckeditor
							$editor = new \Hubzero\Html\Editor('ckeditor');
							echo $editor->display('pageversion[content]', stripslashes($content), '100%', '400', 0, 0, false, 'pagecontent', null, null, $config);
						?>

					</label>
				</fieldset>
			</div>
			<div class="col span3 omega">
				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PUBLISH'); ?></legend>
					<label>
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<select name="page[state]" class="fancy-select" <?php echo $readonly; ?>>
							<option value="1" <?php if ($state == 1) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_PUBLISHED'); ?></option>
							<option value="0" <?php if ($state == 0) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_STATUS_UNPUBLISHED'); ?></option>
						</select>
					</label>

					<label>
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<?php
							$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
							switch ($access)
							{
								case 'anyone':		$name = Lang::txt('COM_GROUPS_PLUGIN_ANYONE');		break;
								case 'registered':	$name = Lang::txt('COM_GROUPS_PLUGIN_REGISTERED');	break;
								case 'members':		$name = Lang::txt('COM_GROUPS_PLUGIN_MEMBERS');	    break;
							}
						?>
						<select name="page[privacy]" class="fancy-select">
							<option value="default" <?php if ($privacy == "default") { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY_INHERIT', $name); ?></option>
							<option value="members" <?php if ($privacy == "members") { echo 'selected="selected"'; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVACY_PRIVATE'); ?></option>
						</select>
					</label>

					<?php if ($this->page->get('id')) : ?>
						<label>
							<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS'); ?>:</strong> <br />
							<a class="btn icon-history" href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=versions&pageid=' . $this->page->get('id')); ?>">
								<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS_BROWSE', $this->page->versions()->count()); ?>
							</a>
						</label>
					<?php endif; ?>
				</fieldset>

				<div class="form-controls cf">
					<a href="<?php echo Route::url($return_link); ?>" class="cancel"><?php echo Lang::txt('COM_GROUPS_PAGES_CANCEL'); ?></a>
					<div class="btn-group save">
						<button type="submit" class="btn btn-info btn-main icon-save"><?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_PAGE'); ?></button>
						<span class="btn dropdown-toggle btn-info"></span>
						<ul class="dropdown-menu">
							<li><a class="icon-save active" data-action="save" href="javascript:void(0);"><?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_PAGE'); ?></a></li>
							<li><a class="icon-apply" data-action="apply" href="javascript:void(0);"><?php echo Lang::txt('COM_GROUPS_PAGES_APPLY_PAGE'); ?></a></li>
						</ul>
					</div>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_SETTINGS'); ?></legend>

					<label for="page-category" class="page-category-label">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
						<select name="page[category]" class="page-category" data-url="<?php echo Route::url('index.php?option=com_groups&cn='. $this->group->get('gidNumber').'&controller=categories&task=add&no_html=1'); ?>">
							<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_NULL'); ?></option>
							<?php foreach ($this->categories as $pageCategory) : ?>
								<?php $sel = ($category == $pageCategory->get('id')) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> data-color="#<?php echo $pageCategory->get('color'); ?>" value="<?php echo $pageCategory->get('id'); ?>"><?php echo $pageCategory->get('title'); ?></option>
							<?php endforeach; ?>
							<option value="other"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_OPTION_OTHER'); ?></a>
						</select>
						<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CATEGORY_HINT'); ?></span>
					</label>

					<?php if ($this->page->get('home') == 0) : ?>
						<label for="page-parent" class="page-parent-label">
							<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
							<select name="page[parent]" class="page-parent">
								<?php foreach ($this->pages as $page) : ?>
									<?php if ($page->get('id') == $id) { continue; } ?>
									<?php $sel = ($parent == $page->get('id')) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $page->get('id'); ?>">
										<?php echo $page->heirarchyIndicator(' &ndash; ') . $page->get('title'); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PARENT_HINT'); ?></span>
						</label>
					<?php endif; ?>


					<?php if ($this->page->get('id') && $this->page->get('home') == 0) : ?>
						<label for="page-ordering">
							<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
							<select name="page[left]" class="page-ordering fancy-select">
								<?php foreach ($this->pages as $page) : ?>

									<?php $sel = ($page->get('title') == $title) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel; ?> data-parent="<?php echo $page->get('parent'); ?>" value="<?php echo $page->get('lft'); ?>">
										<?php echo $page->get('lft') . ' ' . $page->get('title'); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_ORDER_HINT'); ?></span>
						</label>
					<?php endif; ?>

					<hr class="divider" />

					<label>
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
						<select name="page[comments]" class="fancy-select">
							<option value="3" <?php if ($comments === 3) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_INHERIT', $groupCommentSettingString); ?></option>
							<option value="0" <?php if ($comments === 0) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO'); ?></option>
							<option value="1" <?php if ($comments === 1) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES'); ?></option>
							<option value="2" <?php if ($comments === 2) { echo "selected"; } ?>><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK'); ?></option>
						</select>
						<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_HINT'); ?></span>
					</label>

					<?php if ($this->group->isSuperGroup() && count($this->pageTemplates) > 0) : ?>
						<hr class="divider" />

						<label for="page-template">
							<strong><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span>
							<select name="page[template]" class="fancy-select">
								<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_OPTION_NULL'); ?></option>
								<?php foreach ($this->pageTemplates as $name => $file) : ?>
									<?php
										$tmpl = str_replace('.php', '', $file);
										$sel  = ($this->page->get('template') == $tmpl) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $tmpl; ?>"><?php echo $name; ?></option>
								<?php endforeach;?>
							</select>
							<span class="hint"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_TEMPLATE_HINT'); ?></span>
						</label>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>

		<?php echo Html::input('token'); ?>

		<input type="hidden" name="page[id]" value="<?php echo $id; ?>" />
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="controller" value="pages" />
		<input type="hidden" name="return" value="<?php echo $this->escape(Request::getVar('return', '','get')); ?>" />
		<input type="hidden" name="task" value="save" />
	</form>
</section>
