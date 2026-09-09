{{--
  Main page viewer with author metadata and comments.

  Variables:
    $group      — Group object
    $page       — Page model
    $version    — PageVersion model
    $authorized — string: authorization level ('manager', 'member', etc.)
    $config     — Registry: component config

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Group params for comments/author display
  $groupParams     = new \Hubzero\Config\Registry($group->get('params'));
  $displayComments = $groupParams->get('page_comments', $config->get('page_comments', 0));
  $displayAuthor   = $groupParams->get('page_author', $config->get('page_author', 0));

  // Per-page comment override
  if (
      $page->get('comments') !== null
      && in_array($page->get('comments'), [0, 1, 2])
  ) {
      $displayComments = $page->get('comments');
  }

  $versions = $page->versions();
  $category = $page->category();

  if ($category->get('color')) {
      $__view->css('.category-' . $category->get('id')
          . ' { background-color: #' . $category->get('color') . '; }');
  }

  // Newer version pending?
  $newerVersion = false;
  $nextVersion  = $version->get('version') + 1;
  if ($versions->fetch('version', $nextVersion)) {
      $newerVersion = true;
  }

  // Page privacy
  $overviewPageAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'overview');
  $pagePrivacy = ($page->get('privacy') === 'default')
      ? $overviewPageAccess : $page->get('privacy');

  // Check access
  if (
      ($pagePrivacy === 'registered' && User::isGuest())
      || ($pagePrivacy === 'members' && !in_array(User::get('id'), $group->get('members')))
  ) {
      $displayComments = 0;
      if (User::isGuest()) {
          $version->set('content', '<p class="info">'
              . Lang::txt('COM_GROUPS_PAGES_PAGE_LOG_IN') . '</p>');
      } else {
          $version->set('content', '<p class="info">'
              . Lang::txt('COM_GROUPS_PAGES_PAGE_UNABLE_TO_VIEW') . '</p>');
      }
  }

  $perms = '\Components\Groups\Helpers\Permissions';
  $canEditPages = $authorized === 'manager'
      || $perms::userHasPermissionForGroupAction($group, 'group.pages');
@endphp

<div class="group-page page-{{ $page->get('alias') }}">

  @if($newerVersion && $canEditPages)
    <div class="group-page group-page-notice notice-info">
      <h4>{{ Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL') }}</h4>
      <p>{{ Lang::txt('COM_GROUPS_PAGES_PAGE_VERSION_PENDING_APPROVAL_DESC') }}</p>
    </div>
  @endif

  {!! $version->content('parsed') !!}

  @if($displayAuthor)
    @php
      $firstVersion    = $versions->last();
      $currentVersion  = $version;
      $createdDate     = $firstVersion->get('created')
          ? Date::of($firstVersion->get('created'))->toLocal('D F j, Y')
          : Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
      $modifiedDate    = $currentVersion->get('created')
          ? Date::of($currentVersion->get('created'))->toLocal('D F j, Y g:i a')
          : Lang::txt('COM_GROUPS_PAGES_PAGE_NA');

      $createdProfile  = User::getInstance($firstVersion->get('created_by'));
      $modifiedProfile = User::getInstance($currentVersion->get('created_by'));
      $createdBy       = is_object($createdProfile)
          ? $createdProfile->get('name')
          : Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
      $modifiedBy      = is_object($modifiedProfile)
          ? $modifiedProfile->get('name')
          : Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');

      $createdLink  = is_object($createdProfile)
          ? Route::url('index.php?option=com_members&id=' . $createdProfile->get('uidNumber'))
          : '#';
      $modifiedLink = is_object($modifiedProfile)
          ? Route::url('index.php?option=com_members&id=' . $modifiedProfile->get('uidNumber'))
          : '#';

      $editPageLink    = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
          . '&controller=pages&task=edit&pageid=' . $page->get('id'));
      $overrideHomeLink = Route::url('index.php?option=com_help&component=groups&page=pages&cn='
          . $group->get('cn') . '#grouphomepageoverride');
      $editPageLink    .= '&return=' . base64_encode(Request::current(true));
      $categoryLink     = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
          . '&controller=pages&filter=' . $category->get('id'));
    @endphp

    <div class="group-page-toolbar flex flex-wrap gap-4 mt-6 pt-4 border-t text-sm">
      <div class="flex-1">
        @if($page->get('id') != 0)
          <span class="created" title="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_CREATED', $createdDate, $createdBy) }}">
            {!! Lang::txt('COM_GROUPS_PAGES_PAGE_CREATED', '<a href="' . $createdLink . '">' . e($createdBy) . '</a>') !!}
          </span>
          <span class="modified ml-4"
                title="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate, $modifiedBy) }}">
            {!! Lang::txt('COM_GROUPS_PAGES_PAGE_MODIFIED', $modifiedDate,
                '<a href="' . $modifiedLink . '">' . e($modifiedBy) . '</a>') !!}
          </span>
        @endif
      </div>

      @if($canEditPages)
        <div class="page-controls">
          <ul class="flex gap-2">
            @if($page->get('id') != 0)
              <li>
                <a class="btn btn-xs btn-ghost" href="{{ $editPageLink }}"
                   title="{{ Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE') }}">
                  {{ Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE') }}
                </a>
              </li>
              @if($category->get('id') != '')
                <li>
                  <a href="{{ $categoryLink }}"
                     class="tooltips badge badge-sm category-{{ $category->get('id') }}"
                     title="In {{ e($category->get('title')) }}">
                    {{ e($category->get('title')) }}
                  </a>
                </li>
              @endif
            @else
              <li>
                <a class="btn btn-xs btn-ghost popup" href="{{ $overrideHomeLink }}"
                   title="{{ Lang::txt('COM_GROUPS_PAGES_OVERRIDE_PAGE') }}">
                  {{ Lang::txt('COM_GROUPS_PAGES_OVERRIDE_PAGE') }}
                </a>
              </li>
            @endif
          </ul>
        </div>
      @endif
    </div>
  @endif
</div>

@if($displayComments && $page->get('id') > 0)
  <div id="page-comments">
    @php
      // Identify experts
      $experts = [];
      foreach ($group->get('members') as $member) {
          $roles = \Components\Groups\Helpers\Permissions::getGroupMemberRoles(
              $member, $group->get('gidNumber')
          );
          $roles = array_filter(array_map(function ($role) {
              return preg_match('/Expert:(.*)/', $role['name']) ? $role['name'] : null;
          }, $roles));
          if (count($roles) > 0) {
              $experts[] = $member;
          }
      }

      $commentParams = new \Hubzero\Config\Registry();
      $commentParams->set('onCommentMark', function ($comment) use ($experts) {
          return in_array($comment->creator->get('id'), $experts) ? 'expert' : '';
      });

      // Lock comments
      if ($displayComments == 2) {
          $commentParams->set('comments_locked', 1);
          $commentParams->set('access-create-comment', 0);
          $commentParams->set('access-edit-comment', 0);
          $commentParams->set('access-delete-comment', 0);
          $commentParams->set('access-manage-comment', 0);
          $commentParams->set('access-vote-comment', 0);
      }

      if (in_array(User::get('id'), $group->get('managers'))) {
          $commentParams->set('access-create-comment', 1);
          $commentParams->set('access-edit-comment', 1);
          $commentParams->set('access-delete-comment', 1);
          $commentParams->set('access-manage-comment', 1);
          $commentParams->set('access-vote-comment', 1);
      }

      $eventParams = [
          $page,
          'com_groups',
          $page->url() . '#page-comments',
          $commentParams,
      ];

      $comments = Event::trigger('hubzero.onAfterDisplayContent', $eventParams);
    @endphp
    {!! implode("\n", $comments) !!}
  </div>
@endif
