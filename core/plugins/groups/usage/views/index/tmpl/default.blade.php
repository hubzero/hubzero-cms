{{--
  Group Usage — page views chart, overview stats, and activity log counts.

  Variables from plugin:
    $group      — group object
    $authorized — authorization level
    $pages      — array of group pages (id, title)
    $pid        — selected page ID
    $start      — start date string
    $end        — end date string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Plugins\Groups\Usage\Usage as plgGroupsUsage;
  use Components\Groups\Models\Log\Archive;

  $logger = Archive::getInstance();
  $gidNumber = $group->get('gidNumber');

  // Activity log counts
  $logActions = [
      'group_edits'          => 'group_edited',
      'membership_requests'  => 'membership_requested',
      'membership_accepted'  => 'membership_approved',
      'membership_denied'    => 'membership_denied',
      'membership_cancelled' => 'membership_cancelled',
      'invites_sent'         => 'membership_invites_sent',
      'invites_accepted'     => 'membership_invite_accepted',
      'promotions'           => 'membership_promoted',
      'demotions'            => 'membership_demoted',
  ];
  $logCounts = [];
  foreach ($logActions as $key => $action) {
      $logCounts[$key] = $logger->logs(
          'list',
          ['gidNumber' => $gidNumber, 'action' => $action],
          true
      )->count();
  }

  // Group stats
  $openForums   = plgGroupsUsage::getForumCount($gidNumber, $authorized, 'open');
  $closedForums = plgGroupsUsage::getForumCount($gidNumber, $authorized, 'closed');
  $stickyForums = plgGroupsUsage::getForumCount($gidNumber, $authorized, 'sticky');
  $wikiPages    = plgGroupsUsage::getWikipageCount($gidNumber, $authorized);
  $wikiFiles    = plgGroupsUsage::getWikifileCount($gidNumber, $authorized);

  $usageUrl = Route::url(
      'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=usage'
  );
@endphp

<h3 class="text-lg font-semibold mb-4">{{ Lang::txt('USAGE') }}</h3>

<section>
  {{-- Page Views --}}
  <div id="page_views" class="mb-8">
    <div class="flex flex-wrap items-end justify-between gap-4 mb-4">
      <h4 class="text-base font-semibold">Group Page Views</h4>

      <form name="page_selector" action="{{ $usageUrl }}" method="get"
            class="flex flex-wrap items-end gap-2">
        <div class="form-control">
          <select name="pid" id="page_view_selector" class="select select-bordered select-sm">
            <option value="" @if($pid == '') selected @endif>All Group Page Views</option>
            @foreach ($pages as $page)
              <option value="{{ $page['id'] }}" @if($pid == $page['id']) selected @endif>
                {{ $page['title'] }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-control">
          <input type="text" name="start" id="date_start"
                 class="input input-bordered input-sm w-28"
                 value="{{ date('m/d/Y', strtotime($start)) }}" />
        </div>
        <span class="text-base-content/60 text-sm">&ndash;</span>
        <div class="form-control">
          <input type="text" name="end" id="date_end"
                 class="input input-bordered input-sm w-28"
                 value="{{ date('m/d/Y', strtotime($end)) }}" />
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Go</button>
      </form>
    </div>

    <div id="page_views_chart"
         data-group="{{ $group->get('cn') }}"
         data-pid="{{ $pid }}"
         data-start="{{ date('m/d/Y', strtotime($start)) }}"
         data-end="{{ date('m/d/Y', strtotime($end)) }}">
      <noscript>
        <p class="alert alert-info">To view this page views graph, Javascript must be enabled.</p>
      </noscript>
    </div>
  </div>

  {{-- Overview table --}}
  <div class="overflow-x-auto mb-8">
    <table class="table table-zebra w-full">
      <caption class="text-left text-sm font-semibold mb-2">
        {{ Lang::txt('TBL_CAPTION_OVERVIEW') }}
      </caption>
      <thead>
        <tr>
          <th>{{ Lang::txt('TBL_TH_ITEM') }}</th>
          <th class="text-right">{{ Lang::txt('TBL_TH_VALUE') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_PAGES') }}:</th>
          <td class="text-right">{{ plgGroupsUsage::getGroupPagesCount($group) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_MEMBERS') }}:</th>
          <td class="text-right">{{ count($group->get('members')) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_RESOURCES') }}:</th>
          <td class="text-right">{{ plgGroupsUsage::getResourcesCount($group->get('cn'), $authorized) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_OPEN_DISCUSSIONS') }}:</th>
          <td class="text-right">{{ $openForums }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_CLOSED_DISCUSSIONS') }}:</th>
          <td class="text-right">{{ $closedForums }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_STICKY_DISCUSSIONS') }}:</th>
          <td class="text-right">{{ $stickyForums }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_WIKI_PAGES') }}:</th>
          <td class="text-right">{{ $wikiPages }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_WIKI_FILES') }}:</th>
          <td class="text-right">{{ $wikiFiles }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_BLOG') }}:</th>
          <td class="text-right">{{ plgGroupsUsage::getGroupBlogCount($gidNumber) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_BLOG_COMMENTS') }}:</th>
          <td class="text-right">{{ plgGroupsUsage::getGroupBlogCommentCount($gidNumber) }}</td>
        </tr>
        <tr>
          <th scope="row">{{ Lang::txt('TBL_TH_CALENDAR') }}:</th>
          <td class="text-right">{{ plgGroupsUsage::getGroupCalendarCount($gidNumber) }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  {{-- Activity log table --}}
  <div class="overflow-x-auto">
    <table class="table table-zebra w-full">
      <caption class="text-left text-sm font-semibold mb-2">
        {{ Lang::txt('TBL_CAPTION_ACTIVITY') }}
      </caption>
      <thead>
        <tr>
          <th>{{ Lang::txt('TBL_TH_ITEM') }}</th>
          <th class="text-right">{{ Lang::txt('TBL_TH_VALUE') }}</th>
        </tr>
      </thead>
      <tbody>
        @php
          $activityLabels = [
              'group_edits'          => Lang::txt('TBL_GROUP_EDITS'),
              'membership_requests'  => Lang::txt('TBL_MEMBERSHIP_REQUESTS'),
              'membership_accepted'  => Lang::txt('TBL_MEMBERSHIP_ACCEPTED'),
              'membership_denied'    => Lang::txt('TBL_MEMBERSHIP_DENIED'),
              'membership_cancelled' => Lang::txt('TBL_MEMBERSHIP_CANCELLED'),
              'invites_sent'         => Lang::txt('TBL_INVITES_SENT'),
              'invites_accepted'     => Lang::txt('TBL_INVITES_ACCEPTED'),
              'promotions'           => Lang::txt('TBL_PROMOTIONS'),
              'demotions'            => Lang::txt('TBL_DEMOTIONS'),
          ];
        @endphp
        @foreach ($activityLabels as $key => $label)
          <tr>
            <th scope="row">{{ $label }}:</th>
            <td class="text-right">{{ $logCounts[$key] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</section>
