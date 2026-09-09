{{--
  Page version history viewer.

  Variables from controller:
    $group — Group object
    $page  — Page model (with versions)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $cn = $group->get('cn');
  $editPageUrl = 'index.php?option=com_groups&cn=' . $cn
      . '&controller=pages&task=edit&pageid=' . $page->get('id');

  // Add page stylesheets
  $stylesheets = \Components\Groups\Helpers\View::getPageCss($group);
  foreach ($stylesheets as $stylesheet) {
      Document::addStyleSheet($stylesheet);
  }

  $__view->css()
         ->js()
         ->js('jquery.cycle2', 'system');

  $versionCount = $page->versions()->count();
@endphp

<x-page-container :title="Lang::txt('COM_GROUPS_PAGES_VERSIONS_FOR_PAGE', $page->get('title'))">
  @slot('actions')
    <a class="btn btn-sm" href="{{ Route::url($editPageUrl) }}">
      {{ Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE_BACK') }}
    </a>
  @endslot

  <div class="version-manager">
    <div class="toolbar flex flex-col md:flex-row gap-4 mb-4">
      <div class="flex-1 flex items-center gap-2">
        <h3 class="btn version-title text-lg font-semibold"></h3>
        <button type="button" class="btn btn-sm btn-ghost version-source" role="button">
          {{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_VIEW_SOURCE') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost version-meta"
                title="{{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_TOGGLE_METADATA') }}"
                role="button">&hellip;</button>
      </div>
      <div class="flex items-center gap-2">
        <div class="join">
          <button type="button" class="btn btn-sm join-item version-prev" role="button">
            {{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_PREVIOUS') }}
          </button>
          <span class="version-jumpto-container">
            <select class="select select-bordered select-sm join-item version-jumpto">
              @foreach($page->versions() as $v)
                <option value="{{ $v->get('version') }}">{{ $v->get('version') }}</option>
              @endforeach
            </select>
          </span>
          <button type="button" class="btn btn-sm join-item version-next" role="button">
            {{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_NEXT') }}
          </button>
        </div>
        <button type="button" class="btn btn-sm btn-info version-restore" role="button">
          {{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_RESTORE') }}
        </button>
      </div>
    </div>

    <div class="content">
      <div class="versions">
        @foreach($page->versions()->reverse() as $k => $pageVersion)
          @php
            $isCurrent = ($k + 1 == $versionCount);
            $cls       = $isCurrent ? ' current' : '';

            $created = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
            if ($pageVersion->get('created') != null) {
                $created = Date::of($pageVersion->get('created'))
                    ->toLocal('F d, Y @ g:ia');
            }

            $created_by = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
            $creatorProfile = null;
            if ($pageVersion->get('created_by') == 1000) {
                $created_by = Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
            } elseif ($pageVersion->get('created_by') != null
                && is_numeric($pageVersion->get('created_by'))
            ) {
                $creatorProfile = User::getInstance($pageVersion->get('created_by'));
                $url = Route::url('index.php?option=com_members&id=' . $creatorProfile->get('id'));
                $created_by = '<a href="' . $url . '">' . e($creatorProfile->get('name')) . '</a>';
            }

            $approved_on = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
            if ($pageVersion->get('approved_on') != null) {
                $approved_on = Date::of($pageVersion->get('approved_on'))
                    ->toLocal('F d, Y @ g:ia');
            }

            $approved_by = Lang::txt('COM_GROUPS_PAGES_PAGE_NA');
            $approverProfile = null;
            if ($pageVersion->get('approved_by') == 1000) {
                $approved_by = Lang::txt('COM_GROUPS_PAGES_PAGE_SYSTEM');
            } elseif ($pageVersion->get('approved_by') != null
                && is_numeric($pageVersion->get('approved_by'))
            ) {
                $approverProfile = User::getInstance($pageVersion->get('approved_by'));
                $url = Route::url('index.php?option=com_members&id=' . $approverProfile->get('id'));
                $approved_by = '<a href="' . $url . '">' . e($approverProfile->get('name')) . '</a>';
            }

            $restoreUrl = !$isCurrent ? $pageVersion->url('restore') : null;
          @endphp
          <div class="version {{ $cls }}"
               data-cycle-hash="v{{ $pageVersion->get('version') }}"
               data-cycle-title="Version # {{ $pageVersion->get('version') }}"
               data-raw-url="{{ $pageVersion->url('raw') }}"
               data-restore-url="{{ $restoreUrl }}">

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 version-metadata text-sm mb-4">
              <div>
                <span class="font-semibold block">{{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_CREATED') }}</span>
                {{ $created }}
              </div>
              <div>
                <span class="font-semibold block">{{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_CREATED_BY') }}</span>
                @if($creatorProfile)
                  <img class="inline w-5 h-5 rounded-full mr-1"
                       src="{{ $creatorProfile->picture() }}"
                       alt="{{ e($creatorProfile->get('name')) }}" />
                @endif
                {!! $created_by !!}
              </div>
              <div>
                <span class="font-semibold block">{{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_APPROVED') }}</span>
                {{ $approved_on }}
              </div>
              <div>
                <span class="font-semibold block">{{ Lang::txt('COM_GROUPS_PAGES_VERSIONS_APPROVED_BY') }}</span>
                @if($approverProfile)
                  <img class="inline w-5 h-5 rounded-full mr-1"
                       src="{{ $approverProfile->picture() }}"
                       alt="{{ e($approverProfile->get('name')) }}" />
                @endif
                {!! $approved_by !!}
              </div>
            </div>

            <div class="version-content">
              {!! \Components\Groups\Helpers\Pages::generatePreview(
                  $page,
                  $pageVersion->get('version'),
                  true
              ) !!}
            </div>

            <div class="version-code">
              @php
                $current = explode("\n", $pageVersion->content('raw'));
                $previousVersion = $pageVersion->get('version') - 1;
                if ($previousVersion == 0) {
                    $previous = [];
                } else {
                    $previous = $page->version($previousVersion);
                    $previous = !empty($previous) ? explode("\n", $previous->content('raw')) : [];
                }

                $contextFormatter = function ($context) {
                    return htmlentities($context);
                };

                $formatter = new \Components\Wiki\Helpers\TableDiffFormatter();
                $diff = $formatter->format(
                    new \Components\Wiki\Helpers\Diff($previous, $current),
                    $contextFormatter
                );
              @endphp
              {!! $diff !!}
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</x-page-container>
