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

$editPageUrl = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id');

// add page stylesheets
$stylesheets = \Components\Groups\Helpers\View::getPageCss($this->group);

foreach ($stylesheets as $stylesheet)
{
	Document::addStylesheet($stylesheet);
}

include_once(Component::path('com_wiki') . DS . 'helpers' . DS . 'differenceengine.php');

// add styles & scripts
$this->css()
	 ->js()
	 ->js('jquery.cycle2', 'system');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_FOR_PAGE', $this->page->get('title')); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-edit edit btn" href="<?php echo Route::url($editPageUrl); ?>">
				<?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE_BACK'); ?>
			</a></li>
		</ul>
	</div>
</header>

<section class="main section">
	<div class="version-manager">
		<div class="toolbar grid">
			<div class="col span6 title">
				<div class="btn-group">
					<h3 class="btn version-title"></h3>
					<a class="btn version-source" href="javascript:void(0);"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_VIEW_SOURCE'); ?></a>
				</div>
				<a class="btn version-meta" title="<?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_TOGGLE_METADATA'); ?>" href="javascript:void(0);">&hellip;</a>
			</div>
			<div class="col span6 omega controls">
				<div class="btn-group">
					<a href="javascript:void(0);" class="btn icon-prev version-prev">
						<?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_PREVIOUS'); ?>
					</a>
					<span class="version-jumpto-container">
						<select class="btn version-jumpto icon-prev">
							<?php foreach ($this->page->versions() as $version) :?>
								<option value="<?php echo $version->get('version'); ?>"><?php echo $version->get('version'); ?></option>
							<?php endforeach; ?>
						</select>
					</span>
					<a href="javascript:void(0);" class="btn icon-next opposite version-next">
						<?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_NEXT'); ?>
					</a>
				</div>
				<a href="javascript:void(0);" class="btn btn-info version-restore">
					<?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_RESTORE'); ?>
				</a>
			</div>
		</div>

		<div class="content">
			<div class="versions">
				<?php foreach ($this->page->versions()->reverse() as $k => $pageVersion) : ?>
					<?php $cls = ($k+1 == $this->page->versions()->count()) ? ' current' : ''; ?>
					<div class="version <?php echo $cls; ?>"
						data-cycle-hash="v<?php echo $pageVersion->get('version'); ?>"
						data-cycle-title="Version # <?php echo $pageVersion->get('version'); ?>"
						data-raw-url="<?php echo $pageVersion->url('raw'); ?>"
						data-restore-url="<?php echo ($k+1 != $this->page->versions()->count()) ? $pageVersion->url('restore') : null; ?>">

						<?php
							$created = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('created') != null)
							{
								$created = Date::of($pageVersion->get('created'))->toLocal('F d, Y @ g:ia');
							}

							$created_by = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('created_by') == 1000)
							{
								$created_by = Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
							}
							else if ($pageVersion->get('created_by') != null && is_numeric($pageVersion->get('created_by')))
							{
								$profile = User::getInstance( $pageVersion->get('created_by') );
								$created_by = '<a href="'.Route::url('index.php?option=com_members&id=' . $profile->get('id')).'">'.$profile->get('name').'</a>';
							}

							$approved_on = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('approved_on') != null)
							{
								$approved_on = Date::of($pageVersion->get('approved_on'))->toLocal('F d, Y @ g:ia');
							}

							$approved_by = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
							if ($pageVersion->get('approved_by') == 1000)
							{
								$approved_by = Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
							}
							else if ($pageVersion->get('approved_by') != null && is_numeric($pageVersion->get('approved_by')))
							{
								$profile = User::getInstance( $pageVersion->get('approved_by') );
								$approved_by = '<a href="'.Route::url('index.php?option=com_members&id=' . $profile->get('id')).'">'.$profile->get('name').'</a>';
							}
						?>
						<div class="grid version-metadata">
							<div class="col span3">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_CREATED'); ?></span>
								<?php echo $created; ?></span>
							</div>
							<div class="col span3">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_CREATED_BY'); ?></span>
								<?php if ($created_by != 'n/a' && $created_by != 'System') : ?>
									<img align="left" width="20" src="<?php echo $profile->picture(); ?>" />
								<?php endif; ?>
								<?php echo $created_by; ?>
							</div>
							<div class="col span3">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_APPROVED'); ?></span>
								<?php echo $approved_on; ?>
							</div>
							<div class="col span3 omega">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_VERSIONS_APPROVED_BY'); ?></span>
								<?php if ($approved_by != 'n/a' && $approved_by != 'System') : ?>
									<img align="left" width="20" src="<?php echo $profile->picture()); ?>" />
								<?php endif; ?>
								<?php echo $approved_by; ?>
							</div>
						</div>
						<div class="version-content">
							<?php echo \Components\Groups\Helpers\Pages::generatePreview($this->page, $pageVersion->get('version'), true); ?>
						</div>
						<div class="version-code">
							<?php
								$current = explode("\n", $pageVersion->content('raw'));
								$previousVersion = $pageVersion->get('version') - 1;
								if ($previousVersion == 0)
								{
									$previous = array();
								}
								else
								{
									$previous = $this->page->version($previousVersion);

									// make view OK with the LIMIT
									if (!empty($previous))
									{
										$previous = explode("\n", $previous->content('raw'));
									}
								}

								// define function to format context's
								// basically make sure lines that are not different
								// are outputted as code not rendered html
								$contextFormatter = function($context)
								{
									return htmlentities($context);
								};

								// out formatted diff table
								$formatter = new TableDiffFormatter();
								$diff = $formatter->format(new Diff($previous, $current), $contextFormatter);
								echo $diff;
							?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>