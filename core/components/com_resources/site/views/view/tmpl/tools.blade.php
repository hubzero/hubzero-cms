{{--
  Tool resource detail page — upper pane with launch area, version info, DOI,
  source code links, and lower pane with tabbed plugin content.

  Variables:
    $option   — component option string
    $model    — Entry model
    $tconfig  — tool configuration (Registry)
    $thistool — current tool version object (or null)
    $curtool  — current published tool version object (or null)
    $revision — string, revision identifier (e.g. 'dev', version number)
    $cats     — array of tab categories
    $sections — array of plugin sections
    $tab      — string, currently active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();
  $__view->js();

  $mode = strtolower(Request::getWord('mode', ''));

  // Build status label for unpublished/draft/pending/deleted resources
  $statusHtml = '';
  if ($mode != 'preview') {
      $statusMap = [
          2 => 'COM_RESOURCES_DRAFT_EXTERNAL',
          3 => 'COM_RESOURCES_PENDING',
          4 => 'COM_RESOURCES_DELETED',
          5 => 'COM_RESOURCES_DRAFT_INTERNAL',
          0 => 'COM_RESOURCES_UNPUBLISHED',
      ];
      if (isset($statusMap[$model->published])) {
          $statusHtml = '<span class="badge badge-warning badge-sm">'
              . Lang::txt($statusMap[$model->published])
              . '</span> ';
      }
  }

  $dateFmt = Lang::txt('DATE_FORMAT_HZ1');
@endphp

{{-- Upper pane: overview, launch area, metadata sidebar --}}
<x-page-container>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Left column: title, authors, summary --}}
    <div class="lg:col-span-2">
      <header id="content-header">
        <h2>
          {!! $statusHtml !!}{{ e(stripslashes($model->title)) }}
          @if($model->params->get('access-edit-resource'))
            @php
              $editUrl = Route::url(
                  'index.php?option=com_tools&task=resource&step=1&app='
                  . $model->alias
              );
            @endphp
            <a class="btn btn-outline btn-sm ml-2"
               href="{{ $editUrl }}">{{ Lang::txt('COM_RESOURCES_EDIT') }}</a>
          @endif
        </h2>
        <input type="hidden" name="rid" id="rid" value="{{ $model->id }}" />
      </header>

      @if($model->params->get('show_authors', 1))
        <div id="authorslist">
          @include('view::_contributors', [
              'option'       => $option,
              'contributors' => $model->contributors('tool'),
          ])
        </div>
      @endif

      <p class="ataglance">
        @php
          $intro = $model->introtext ?: $model->fulltxt;
        @endphp
        {{ \Hubzero\Utility\Str::truncate(stripslashes($intro), 255) }}
      </p>
    </div>

    {{-- Right column: launch area --}}
    <div class="launcharea">
      @if(!$model->access('view-all'))
        {{-- Access denied --}}
        @php
          $ghtml = [];
          foreach ($model->groups as $allowedgroup) {
              $groupUrl = Route::url(
                  'index.php?option=com_groups&cn=' . $allowedgroup
              );
              $ghtml[] = '<a class="link" href="' . $groupUrl . '">'
                  . e($allowedgroup) . '</a>';
          }
        @endphp
        <div role="alert" class="alert alert-warning">
          @if(User::isGuest())
            {!! Lang::txt(
                'COM_RESOURCES_ERROR_MUST_BE_LOGGED_IN',
                base64_encode(Request::path())
            ) !!}
          @elseif(!empty($group_owner))
            {!! Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP')
                . ' ' . implode(', ', $ghtml) !!}
          @else
            {{ Lang::txt('COM_RESOURCES_ALERTNOTAUTH') }}
          @endif
        </div>
      @else
        {{-- Launch button --}}
        @php
          $children = $model->children()
              ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
              ->whereEquals('standalone', 0)
              ->order('ordering', 'asc')
              ->rows();

          $firstChild = $children->first();
        @endphp
        {!! \Components\Resources\Helpers\Html::primaryChild(
            $option,
            $model,
            $firstChild,
            ''
        ) !!}

        {{-- Version info --}}
        @php
          $versionLabel = '<strong>';
          if ($revision && $thistool) {
              $versionLabel .= e($thistool->version) . '</strong>';
              if ($model->revision != 'dev') {
                  $versionLabel .= '<br> '
                      . ucfirst(Lang::txt('COM_RESOURCES_PUBLISHED_ON')) . ' ';
                  $versionLabel .= ($thistool->released
                      && $thistool->released != '0000-00-00 00:00:00')
                      ? Date::of($thistool->released)->toLocal($dateFmt)
                      : Date::of($model->publish_up)->toLocal($dateFmt);
                  if ($thistool->unpublished
                      && $thistool->unpublished != '0000-00-00 00:00:00'
                  ) {
                      $versionLabel .= ', ' . Lang::txt('COM_RESOURCES_UNPUBLISHED_ON')
                          . ' ' . Date::of($thistool->unpublished)->toLocal($dateFmt);
                  }
              } else {
                  $versionLabel .= ' (' . Lang::txt('COM_RESOURCES_IN_DEVELOPMENT') . ')';
              }
          } elseif ($curtool) {
              $versionLabel .= e($curtool->version) . '</strong> - '
                  . Lang::txt('COM_RESOURCES_PUBLISHED_ON') . ' ';
              $versionLabel .= ($curtool->released
                  && $curtool->released != '0000-00-00 00:00:00')
                  ? Date::of($curtool->released)->toLocal($dateFmt)
                  : Date::of($model->publish_up)->toLocal($dateFmt);
          }

          $versionsUrl = Route::url($model->link() . '&active=versions');
        @endphp

        @if(!$thistool)
          <p class="text-sm mt-2">
            {!! Lang::txt('COM_RESOURCES_VERSION') . ' ' . $versionLabel !!}
          </p>
        @elseif($revision == 'dev')
          <p class="text-sm mt-2">
            <span class="badge badge-warning badge-sm">{{ Lang::txt('COM_RESOURCES_IN_DEVELOPMENT') }}</span>
            {!! Lang::txt('COM_RESOURCES_VERSION') . ' ' . $versionLabel !!}
            @if($model->toolpublished)
              <span>
                {{ Lang::txt('View') }}
                <a class="link" href="{{ $versionsUrl }}">{{ Lang::txt('other versions') }}</a>
              </span>
            @endif
          </p>
        @else
          {{-- Archive version notice --}}
          <div role="alert" class="alert alert-warning mt-2">
            <p>
              <strong>{{ Lang::txt('COM_RESOURCES_ARCHIVE') }}</strong>
              {!! Lang::txt('COM_RESOURCES_VERSION') . ' ' . $versionLabel !!}
              @if($model->curversion)
                @php
                  $revUrl = Route::url(
                      $model->link() . '&rev=' . $curtool->revision
                  );
                @endphp
                <br>
                {{ Lang::txt('COM_RESOURCES_LATEST_VERSION') }}:
                <a class="link" href="{{ $revUrl }}">{{ $model->curversion }}</a>.
              @endif
              <a class="link" href="{{ $versionsUrl }}">{{ Lang::txt('COM_RESOURCES_TOOL_ALL_VERSIONS') }}</a>
            </p>
          </div>
        @endif

        {{-- DOI --}}
        @if($revision != 'dev' && $model->doi && ($model->doi_shoulder || $tconfig->get('doi_shoulder')))
          @php
            $shoulder = $model->doi_shoulder ?: $tconfig->get('doi_shoulder');
            $doi = 'doi:' . $shoulder . '/' . strtoupper($model->doi);
            $aboutUrl = Route::url($model->link() . '&active=about');
          @endphp
          <p class="text-sm mt-2">
            {{ $doi }}
            <a class="link text-xs" href="{{ $aboutUrl }}#citethis">{{ Lang::txt('cite this') }}</a>
          </p>
        @endif

        {{-- Open/closed source --}}
        @if($model->toolsource && $model->tool)
          @php
            $licenseUrl = Route::url(
                'index.php?option=' . $option
                . '&task=license&tool=' . $model->tool
                . '&tmpl=component'
            );
            $srcUrl = Route::url(
                'index.php?option=' . $option
                . '&task=sourcecode&tool=' . $model->tool
            );
          @endphp
          <p class="text-sm mt-2">
            {{ Lang::txt('Open source') }}:
            <a class="link popup" href="{{ $licenseUrl }}">{{ Lang::txt('license') }}</a>
            @if($model->taravailable)
              | <a class="link" href="{{ $srcUrl }}">{{ Lang::txt('download') }}</a>
            @else
              | <span class="text-base-content/50">{{ Lang::txt('code unavailable') }}</span>
            @endif
          </p>
        @elseif(!$model->toolsource)
          <p class="text-sm mt-2 text-base-content/70">
            {{ Lang::txt('COM_RESOURCES_TOOL_IS_CLOSED_SOURCE') }}
          </p>
        @endif

        {{-- Supporting docs (only for published versions) --}}
        @if(!$thistool)
          @php
            $guide = null;
            foreach ($children as $child) {
                $title = $child->logicaltitle
                    ?: stripslashes($child->title);
                if ($child->access == 0 || ($child->access == 1 && !User::isGuest())) {
                    if (stripos($title, 'user guide') !== false) {
                        $guide = $child;
                    }
                }
            }
            $guideUrl = $guide
                ? \Components\Resources\Helpers\Html::processPath($option, $guide, $model->id)
                : '';
            $docsUrl = Route::url($model->link() . '&active=supportingdocs');
          @endphp
          <p class="text-sm mt-2 flex flex-wrap gap-x-3">
            @if($guideUrl)
              <a class="link" href="{{ $guideUrl }}">
                {{ Lang::txt('COM_RESOURCES_TOOL_FIRT_TIME_USER_GUIDE') }}
              </a>
            @endif
            <a class="link" href="{{ $docsUrl }}">
              {{ Lang::txt('COM_RESOURCES_TOOL_VIEW_ALL_SUPPORTING_DOCS') }}
            </a>
          </p>
        @endif
      @endif
    </div>
  </div>

  {{-- Canonical/newer version notice --}}
  @include('view::_canonical', [
      'option' => $option,
      'model'  => $model,
  ])

  @slot('sidebar')
    {{-- Metadata sidebar --}}
    @if(!$thistool)
      @if($model->params->get('show_metadata', 1))
        @include('view::_metadata', [
            'option'   => $option,
            'sections' => $sections,
            'model'    => $model,
        ])
      @endif
    @elseif($revision == 'dev' || !$model->toolpublished)
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body text-sm">
          <p>
            @if($revision == 'dev')
              {{ Lang::txt('This section will be filled when this tool version gets published.') }}
            @else
              {{ Lang::txt('This section is unavailable in an archive version of a tool.') }}
            @endif

            @if($model->curversion)
              @php
                $latestUrl = Route::url(
                    $model->link() . '&rev=' . $curtool->revision
                );
              @endphp
              {{ Lang::txt('Consult the latest published version') }}
              <a class="link" href="{{ $latestUrl }}">{{ $model->curversion }}</a>
              {{ Lang::txt('for most current information.') }}
            @endif
          </p>
        </div>
      </div>
    @endif
  @endslot
</x-page-container>

{{-- Lower pane: tabbed plugin content --}}
@if($model->access('view-all'))
  <x-page-container>
    <div class="tabbed">
      @include('view::_tabs', [
          'option'   => $option,
          'cats'     => $cats,
          'resource' => $model,
          'active'   => $tab,
      ])

      @include('view::_sections', [
          'option'   => $option,
          'sections' => $sections,
          'resource' => $model,
          'active'   => $tab,
      ])
    </div>

    @slot('sidebar')
      @php
        $out = Event::trigger('resources.onResourcesSub', [$model, $option, 1]);
      @endphp
      @foreach($out as $ou)
        @if(isset($ou['html']))
          {!! $ou['html'] !!}
        @endif
      @endforeach

      @if($tab == 'about')
        {!! \Hubzero\Module\Helper::renderModules('extracontent') !!}
      @endif
    @endslot
  </x-page-container>
@endif
