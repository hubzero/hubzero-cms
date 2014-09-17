<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// group params for displaying comments/author
// use global params if group has not set params
$groupParams     = new JRegistry($this->group->get('params'));
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
if (($pagePrivacy== 'registered' && $this->juser->get('guest'))
	||($pagePrivacy == 'members' && !in_array($this->juser->get('id'), $this->group->get('members'))))
{
	$displayComments = 0;
	$this->version->set('content', '<p class="info">' . JText::_('COM_GROUPS_PAGES_PAGE_UNABLE_TO_VIEW') . '</p>');
}
?>

<div class="group-page page-<?php echo $this->page->get('alias'); ?>">

	<?php if ($newerVersion
			&& ($this->authorized == 'manager' || \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.pages'))) : ?>
		<div class="group-page group-page-notice notice-info">
			<h4><?php echo JText::_('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL'); ?></h4>
			<p><?php echo JText::_('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL_DESC'); ?></p>
		</div>
	<?php endif; ?>

	<?php echo $this->version->content('parsed'); ?>

	<?php if ($displayAuthor) : ?>
		<div class="group-page-toolbar grid">
			<?php
				$firstVersion     = $versions->last();
				$currentVersion   = $this->version;
				$createdDate      = ($firstVersion->get('created')) ? JHTML::_('date', $firstVersion->get('created'), 'D F j, Y') : JText::_('COM_GROUPS_PAGES_PAGE_NA');
				$modifiedDate     = ($currentVersion->get('created')) ? JHTML::_('date', $currentVersion->get('created'), 'D F j, Y g:i a') : JText::_('COM_GROUPS_PAGES_PAGE_NA');
				$createdProfile   = \Hubzero\User\Profile::getInstance( $firstVersion->get("created_by") );
				$modifiedProfile  = \Hubzero\User\Profile::getInstance( $currentVersion->get("created_by") );
				$createdBy        = (is_object($createdProfile)) ? $createdProfile->get('name') : JText::_('COM_GROUPS_PAGES_PAGE_SYSTEM');
				$modifiedBy       = (is_object($modifiedProfile)) ? $modifiedProfile->get('name') : JText::_('COM_GROUPS_PAGES_PAGE_SYSTEM');

				$createdLink  = 'javascript:void(0);';
				$modifiedLink = 'javascript:void(0);';
				if (is_object($createdProfile))
				{
					$createdLink      = JRoute::_('index.php?option=com_members&id='.$createdProfile->get('uidNumber'));
				}
				if (is_object($modifiedProfile))
				{
					$modifiedLink     = JRoute::_('index.php?option=com_members&id='.$modifiedProfile->get('uidNumber'));
				}

				$createdLink      = '<a href="'.$createdLink.'">'.$createdBy.'</a>';
				$modifiedLink     = '<a href="'.$modifiedLink.'">'.$modifiedBy.'</a>';

				$editPageLink     = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=edit&pageid='.$this->page->get('id'));
				$setPageHomeLink  = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=sethome&pageid='.$this->page->get('id'));
				$overrideHomeLink = JRoute::_('index.php?option=com_help&component=groups&page=pages&cn='.$this->group->get('cn').'#grouphomepageoverride');

				// current location
				$editPageLink    .= '&return=' . base64_encode(JURI::getInstance()->toString());
				$setPageHomeLink .= '&return=' . base64_encode(JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn')));
				$categoryLink     = JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&filter=' . $category->get('id'));
			?>

			
			<div class="page-meta col span10">
				<?php if ($this->page->get('id') != 0) : ?>
					<span class="created" title="<?php echo JText::sprintf('COM_GROUPS_PAGES_PAGE_CREATED', $createdDate, $createdBy); ?>">
						<?php echo JText::sprintf('COM_GROUPS_PAGES_PAGE_CREATED', $createdLink); ?>
					</span>
					<span class="modified" title="<?php echo JText::sprintf('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate, $modifiedBy); ?>">
						<?php echo JText::sprintf('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate, $modifiedLink); ?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ($this->authorized == 'manager' || \Hubzero\User\Profile::userHasPermissionForGroupAction($this->group, 'group.pages')) : ?>
				<div class="page-controls col span2 omega">
					<ul class="page-controls">
					<?php if ($this->page->get('id') != 0) : ?>
						<li>
							<a class="edit" title="<?php echo JText::_('COM_GROUPS_PAGES_EDIT_PAGE'); ?>" data-title="<?php echo JText::_('COM_GROUPS_PAGES_EDIT_PAGE'); ?>" href="<?php echo $editPageLink; ?>">
								<span><?php echo JText::_('COM_GROUPS_PAGES_EDIT_PAGE'); ?></span>
							</a>
						</li>
						<?php if ($this->page->get('home') != 1) : ?>
							<li>
								<a class="home" title="<?php echo JText::_('COM_GROUPS_PAGES_SET_HOME'); ?>" data-title="<?php echo JText::_('COM_GROUPS_PAGES_SET_HOME'); ?>" href="<?php echo $setPageHomeLink; ?>">
									<span><?php echo JText::_('COM_GROUPS_PAGES_SET_HOME'); ?></span>
								</a>
							</li>
						<?php endif; ?>

						<?php if ($category->get('id') != '') : ?>
							<li>
								<a href="<?php echo $categoryLink; ?>" class="tooltips category" title="In <?php echo $category->get('title'); ?>" style="background-color:#<?php echo $category->get('color'); ?>"></a>
							</li>
						<?php endif; ?>
					<?php else : ?>
						<li>
							<a class="popup override" title="<?php echo JText::_('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?>" data-title="<?php echo JText::_('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?>" href="<?php echo $overrideHomeLink; ?>">
								<span><?php echo JText::_('COM_GROUPS_PAGES_OVERRIDE_PAGE'); ?></span>
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
			JPluginHelper::importPlugin('hubzero');
			$dispatcher = JDispatcher::getInstance();

			$params = new JRegistry();
			$params->set('onCommentMark', function($comment)
			{
				if (in_array($comment->creator('id'), $this->group->get('managers')))
				{
					return 'marked';
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

			$params = array(
				$this->page,
				'com_groups',
				$this->page->url() . '#page-comments',
				$params
			);

			$comments = $dispatcher->trigger( 'onAfterDisplayContent', $params );
			echo $comments[0];
		?>
	</div>
<?php endif; ?>