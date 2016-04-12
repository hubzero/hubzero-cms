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

// group params for displaying comments/author
// use global params if group has not set params
$groupParams     = new \Hubzero\Config\Registry($this->group->get('params'));
$displayComments = $groupParams->get('page_comments', $this->config->get('page_comments', 0));

$displayAuthor   = $groupParams->get('page_author', $this->config->get('page_author', 0));

// take page setting if we have one
if ($this->page->get('comments') !== NULL
	&& in_array($this->page->get('comments'), array(0,1,2)))
{
	$displayComments = $this->page->get('comments');
}

// get page versions
$versions = $this->page->versions();

// get page category
$category = $this->page->category();

// is ther a newer version of this page
$newerVersion = false;
$nextVersion  = $this->version->get('version') + 1;
if ($versions->fetch('version', $nextVersion))
{
	$newerVersion = true;
}

// get page privacy level
$overviewPageAccess = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
$pagePrivacy = ($this->page->get('privacy') == 'default') ? $overviewPageAccess : $this->page->get('privacy');

// check to make sure user has access this page
if (($pagePrivacy== 'registered' && User::isGuest())
	||($pagePrivacy == 'members' && !in_array(User::get('id'), $this->group->get('members'))))
{
	$displayComments = 0;
	$this->version->set('content', '<p class="info">' . Lang::txt('COM_GROUPS_PAGES_PAGE_UNABLE_TO_VIEW') . '</p>');
}
?>

<div class="group-page page-<?php echo $this->page->get('alias'); ?>">

	<?php if ($newerVersion
			&& ($this->authorized == 'manager' || \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.pages'))) : ?>
		<div class="group-page group-page-notice notice-info">
			<h4><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL'); ?></h4>
			<p><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL_DESC'); ?></p>
		</div>
	<?php endif; ?>

	<?php echo $this->version->content('parsed'); ?>

	<?php if ($displayAuthor) : ?>
		<div class="group-page-toolbar grid">
			<?php
				$firstVersion     = $versions->last();
				$currentVersion   = $this->version;
				$createdDate      = ($firstVersion->get('created')) ? Date::of($firstVersion->get('created'))->toLocal('D F j, Y') : Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
				$modifiedDate     = ($currentVersion->get('created')) ? Date::of($currentVersion->get('created'))->toLocal('D F j, Y g:i a') : Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
				$createdProfile   = \Hubzero\User\Profile::getInstance($firstVersion->get("created_by"));
				$modifiedProfile  = \Hubzero\User\Profile::getInstance($currentVersion->get("created_by"));
				$createdBy        = (is_object($createdProfile)) ? $createdProfile->get('name') : Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
				$modifiedBy       = (is_object($modifiedProfile)) ? $modifiedProfile->get('name') : Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');

				$createdLink  = 'javascript:void(0);';
				$modifiedLink = 'javascript:void(0);';
				if (is_object($createdProfile))
				{
					$createdLink      = Route::url('index.php?option=com_members&id='.$createdProfile->get('uidNumber'));
				}
				if (is_object($modifiedProfile))
				{
					$modifiedLink     = Route::url('index.php?option=com_members&id='.$modifiedProfile->get('uidNumber'));
				}

				$createdLink      = '<a href="'.$createdLink.'">'.$createdBy.'</a>';
				$modifiedLink     = '<a href="'.$modifiedLink.'">'.$modifiedBy.'</a>';

				$editPageLink     = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id'));
				$setPageHomeLink  = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$this->page->get('id'));
				$overrideHomeLink = Route::url('index.php?option=com_help&component=groups&page=pages&cn='.$this->group->get('cn').'#grouphomepageoverride');

				// current location
				$editPageLink    .= '&return=' . base64_encode(Request::current(true));
				$setPageHomeLink .= '&return=' . base64_encode(Route::url('index.php?option=com_groups&cn='.$this->group->get('cn')));
				$categoryLink     = Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&filter=' . $category->get('id'));
			?>


			<div class="page-meta col span10">
				<?php if ($this->page->get('id') != 0) : ?>
					<span class="created" title="<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CREATED', $createdDate, $createdBy); ?>">
						<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CREATED', $createdLink); ?>
					</span>
					<span class="modified" title="<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate, $modifiedBy); ?>">
						<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate, $modifiedLink); ?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ($this->authorized == 'manager' || \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.pages')) : ?>
				<div class="page-controls col span2 omega">
					<ul class="page-controls">
					<?php if ($this->page->get('id') != 0) : ?>
						<li>
							<a class="edit" title="<?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE'); ?>" data-title="<?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE'); ?>" href="<?php echo $editPageLink; ?>">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE'); ?></span>
							</a>
						</li>
						<?php /*if ($this->page->get('home') != 1) : ?>
							<li>
								<a class="home" title="<?php echo Lang::txt('COM_GROUPS_PAGES_SET_HOME'); ?>" data-title="<?php echo Lang::txt('COM_GROUPS_PAGES_SET_HOME'); ?>" href="<?php echo $setPageHomeLink; ?>">
									<span><?php echo Lang::txt('COM_GROUPS_PAGES_SET_HOME'); ?></span>
								</a>
							</li>
						<?php endif;*/ ?>

						<?php if ($category->get('id') != '') : ?>
							<li>
								<a href="<?php echo $categoryLink; ?>" class="tooltips category" title="In <?php echo $category->get('title'); ?>" style="background-color:#<?php echo $category->get('color'); ?>"></a>
							</li>
						<?php endif; ?>
					<?php else : ?>
						<li>
							<a class="popup override" title="<?php echo Lang::txt('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?>" data-title="<?php echo Lang::txt('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?>" href="<?php echo $overrideHomeLink; ?>">
								<span><?php echo Lang::txt('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?></span>
							</a>
						</li>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<?php if ($displayComments && $this->page->get('id') > 0) : ?>
	<div id="page-comments">
		<?php
			// get experts
			$experts = array();
			foreach ($this->group->get('members') as $member)
			{
				// get each members roles
				$roles = Hubzero\User\Profile::getGroupMemberRoles($member, $this->group->get('gidNumber'));

				// make sure roles match pattern "Expert: ..."
				$roles = array_map(function($role)
				{
					if (preg_match('/Expert:(.*)/', $role['name']))
					{
						return $role['name'];
					}
				}, $roles);

				// if we are in any expert role mark as expert
				if (count($roles) > 0)
				{
					$experts[] = $member;
				}
			}

			// mark comments for experts
			$params = new \Hubzero\Config\Registry();
			$params->set('onCommentMark', function($comment) use ($experts)
			{
				if (in_array($comment->creator('id'), $experts))
				{
					return 'expert';
				}
				return '';
			});

			// lock comments
			if ($displayComments == 2)
			{
				$params->set('comments_locked', 1);
				$params->set('access-create-comment', 0);
				$params->set('access-edit-comment', 0);
				$params->set('access-delete-comment', 0);
				$params->set('access-manage-comment', 0);
				$params->set('access-vote-comment', 0);
			}

			if (in_array(User::get('id'), $this->group->get('managers')))
			{
				$params->set('access-create-comment', 1);
				$params->set('access-edit-comment', 1);
				$params->set('access-delete-comment', 1);
				$params->set('access-manage-comment', 1);
				$params->set('access-vote-comment', 1);
			}

			$params = array(
				$this->page,
				'com_groups',
				$this->page->url() . '#page-comments',
				$params
			);

			$comments = Event::trigger('hubzero.onAfterDisplayContent', $params);
			echo implode("\n", $comments);
		?>
	</div>
<?php endif; ?>