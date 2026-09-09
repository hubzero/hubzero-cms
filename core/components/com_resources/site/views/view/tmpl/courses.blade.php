{{--
  Resource detail view for courses — upper pane (title, authors, launch/podcast area,
  metadata) and lower tabbed content with course listing table.

  Variables from controller:
    $option   — component option string
    $model    — resource Entry model
    $sections — sections data for tabs
    $cats     — categories for tabs
    $tab      — active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();
  $__view->js();

  $txt  = '';
  $mode = strtolower(Request::getWord('mode', ''));

  if ($mode != 'preview') {
      switch ($model->published) {
          case 1:
              break;
          case 2:
              $txt .= '<span class="badge badge-warning badge-sm">'
                  . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . '</span> ';
              break;
          case 3:
              $txt .= '<span class="badge badge-warning badge-sm">'
                  . Lang::txt('COM_RESOURCES_PENDING') . '</span> ';
              break;
          case 4:
              $txt .= '<span class="badge badge-warning badge-sm">'
                  . Lang::txt('COM_RESOURCES_DELETED') . '</span> ';
              break;
          case 5:
              $txt .= '<span class="badge badge-warning badge-sm">'
                  . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . '</span> ';
              break;
          case 0:
              $txt .= '<span class="badge badge-warning badge-sm">'
                  . Lang::txt('COM_RESOURCES_UNPUBLISHED') . '</span> ';
              break;
      }
  }

  $pageclassSfx = $model->params->get('pageclass_sfx', '');
@endphp

<x-page-container>
  @slot('sidebar')
    {{-- Upper sidebar: metadata / rank area --}}
    @if($model->params->get('show_metadata', 1))
      @include('view::_metadata', [
          'option'   => $option,
          'sections' => $sections,
          'model'    => $model,
      ])
    @endif

    {{-- Lower sidebar: extra content (only when view access granted) --}}
    @if($model->access('view-all'))
      @php
        $out = Event::trigger(
            'resources.onResourcesSub',
            [$model, $option, 1]
        );
      @endphp
      @if(count($out) > 0)
        @foreach($out as $ou)
          @if(isset($ou['html']))
            {!! $ou['html'] !!}
          @endif
        @endforeach
      @endif

      @if($tab == 'about')
        {!! \Hubzero\Module\Helper::renderModules('extracontent') !!}
      @endif
    @endif
  @endslot

  {{-- ========== Upper pane ========== --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 {{ $pageclassSfx }}">
    {{-- Left column: title + authors --}}
    <div class="lg:col-span-2">
      <header>
        <h2 class="text-2xl font-bold">
          {!! $txt !!}{{ e(stripslashes($model->title)) }}
          @if($model->params->get('access-edit-resource'))
            @php
              $editUrl = Route::url(
                  'index.php?option=com_resources&task=draft&step=1&id='
                  . $model->id
              );
            @endphp
            <a class="btn btn-outline btn-sm ml-2" href="{{ $editUrl }}">
              {{ Lang::txt('COM_RESOURCES_EDIT') }}
            </a>
          @endif
        </h2>
        <input type="hidden" name="rid" id="rid" value="{{ $model->id }}" />
      </header>

      @if($model->params->get('show_authors', 1))
        <div id="authorslist" class="mt-4">
          @include('view::_contributors', [
              'option'       => $option,
              'contributors' => $model->contributors('!submitter'),
          ])
        </div>
      @endif
    </div>

    {{-- Right column: launch area --}}
    <div>
      @if(!$model->access('view-all'))
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
          @elseif($__view->get('group_owner'))
            {!! Lang::txt('COM_RESOURCES_ERROR_MUST_BE_PART_OF_GROUP')
                . ' ' . implode(', ', $ghtml) !!}
          @else
            {{ Lang::txt('COM_RESOURCES_ALERTNOTAUTH') }}
          @endif
        </div>
      @else
        @php
          $schildren = $model->children()
              ->whereEquals('standalone', 1)
              ->whereEquals(
                  'published',
                  \Components\Resources\Models\Entry::STATE_PUBLISHED
              )
              ->order('ordering', 'asc')
              ->rows();

          $ccount = count($schildren);

          if ($ccount > 0) {
              $mesg = Lang::txt('COM_RESOURCES_VIEW') . ' '
                  . $model->type->get('type');
          }
        @endphp

        @if($ccount > 0)
          @include('view::_primary', [
              'option' => $option,
              'class'  => 'download',
              'href'   => Route::url($model->link()) . '#series',
              'title'  => $mesg,
              'xtra'   => '',
              'pop'    => '',
              'action' => '',
              'msg'    => $mesg,
          ])
        @endif

        @php
          $supportHtml = '';

          $thumb = '/site/stats/resource_impact/resource_impact_'
              . $model->id . '_th.gif';
          $full  = '/site/stats/resource_impact/resource_impact_'
              . $model->id . '.gif';
          if (file_exists(PATH_APP . $thumb)) {
              $base = Request::base(true);
              $supportHtml .= '<br />'
                  . '<a id="member-stats-graph" title="'
                  . $model->id . ' Impact Graph" href="'
                  . $base . $full . '" rel="lightbox">'
                  . '<img src="' . $base . $thumb
                  . '" alt="' . $model->id
                  . ' Impact Graph" />'
                  . '</a>';
          }

          // Supporting documents
          $children = $model->children()
              ->whereEquals('standalone', 0)
              ->whereEquals(
                  'published',
                  \Components\Resources\Models\Entry::STATE_PUBLISHED
              )
              ->order('ordering', 'asc')
              ->rows();

          $firstChild = $children->first();

          if ($children && count($children) > 1) {
              $supportHtml .= \Components\Resources\Helpers\Html::sortSupportingDocs(
                  $model, $option, $children
              );
          }

          $live_site = rtrim(Request::base(), '/');
          $feedBase  = $live_site . '/resources/'
              . $model->id . '/feed.rss?content=';
        @endphp

        {!! $supportHtml !!}

        <p>
          <a class="feed" id="resource-audio-feed"
             href="{{ $feedBase . 'audio' }}"
          >{{ Lang::txt('Audio podcast') }}</a><br />
          <a class="feed" id="resource-video-feed"
             href="{{ $feedBase . 'video' }}"
          >{{ Lang::txt('Video podcast') }}</a><br />
          <a class="feed" id="resource-slides-feed"
             href="{{ $feedBase . 'slides' }}"
          >{{ Lang::txt('Slides/Notes podcast') }}</a>
        </p>

        @if($tab != 'play')
          @include('view::_license', [
              'license' => $model->license(),
          ])
        @endif
      @endif
    </div>
  </div>

  {{-- Canonical --}}
  @include('view::_canonical', [
      'option' => $option,
      'model'  => $model,
  ])

  {{-- ========== Lower pane: tabbed content ========== --}}
  @if($model->access('view-all'))
    <div class="{{ $pageclassSfx }}">
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

    {{-- ========== Course listing table ========== --}}
    @if($tab == 'about' && isset($schildren) && $schildren->count())
      <section class="mt-8" id="series">
        <div class="overflow-x-auto">
          <table class="table table-zebra" aria-label="{{ Lang::txt('Lecture Number/Topic') }}">
            <thead>
              <tr>
                <th>{{ Lang::txt('Lecture Number/Topic') }}</th>
                <th class="w-[12%]">{{ Lang::txt('Online Lecture') }}</th>
                <th>{{ Lang::txt('Video') }}</th>
                <th>{{ Lang::txt('Lecture Notes') }}</th>
                <th>{{ Lang::txt('Supplemental Material') }}</th>
                <th>{{ Lang::txt('Suggested Exercises') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($schildren as $child)
                @php
                  $child_params = $child->params;
                  $link_action  = $child_params->get('link_action', '');
                  $childTitle   = e($child->title);
                @endphp
                <tr>
                  <td>
                    @if($child->standalone == 1)
                      @php
                        $childUrl = Route::url(
                            'index.php?option=' . $option
                            . '&id=' . $child->id
                        );
                      @endphp
                      @if($link_action == 1)
                        <a href="{{ $childUrl }}" rel="noreferrer" target="_blank">
                          {{ $childTitle }}
                        </a>
                      @elseif($link_action == 2)
                        <a href="{{ $childUrl }}"
                           data-popup-url="{{ $childUrl }}"
                           data-popup-width="400"
                           data-popup-height="400"
                        >{{ $childTitle }}</a>
                      @else
                        <a href="{{ $childUrl }}">{{ $childTitle }}</a>
                      @endif
                    @endif
                  </td>

                  @php
                    // Retrieve grandchildren
                    $grandchildren = $child->children()
                        ->whereEquals('standalone', 0)
                        ->whereEquals(
                            'published',
                            \Components\Resources\Models\Entry::STATE_PUBLISHED
                        )
                        ->order('ordering', 'asc')
                        ->rows();
                  @endphp

                  @if(count($grandchildren) > 0)
                    @php
                      $videoi       = '';
                      $breeze       = '';
                      $hubpresenter = '';
                      $youtube      = '';
                      $pdf          = '';
                      $video        = '';
                      $exercises    = '';
                      $supp         = '';

                      foreach ($grandchildren as $grandchild) {
                          $gcTitle = e($grandchild->title);
                          $grandchild->set('title', $gcTitle);
                          $gcPath = \Components\Resources\Helpers\Html::processPath(
                              $option, $grandchild, $child->id
                          );
                          $grandchild->set('path', $gcPath);

                          $alias = $grandchild->type->alias;

                          switch ($alias) {
                              case 'player':
                              case 'quicktime':
                                  $videoi .= (!$videoi)
                                      ? '<a href="' . $grandchild->path
                                          . '">' . Lang::txt('View') . '</a>'
                                      : '';
                                  break;
                              case 'breeze':
                                  $gcTitleStr = e(
                                      stripslashes($grandchild->title)
                                  );
                                  $breeze .= (!$breeze)
                                      ? '<a title="'
                                          . e('View Presentation - Flash Version')
                                          . '" class="breeze flash" href="'
                                          . $grandchild->path
                                          . '&amp;no_html=1">'
                                          . Lang::txt('View Flash') . '</a>'
                                      : '';
                                  break;
                              case 'hubpresenter':
                                  $gcTitleStr = e(
                                      stripslashes($grandchild->title)
                                  );
                                  $hubpresenter .= (!$hubpresenter)
                                      ? '<a title="'
                                          . e('View Presentation - HTML5 Version')
                                          . '" class="hubpresenter html5"'
                                          . ' href="' . $grandchild->path
                                          . '">'
                                          . Lang::txt('View HTML') . '</a>'
                                      : '';
                                  break;
                              case 'elink':
                              case 'youtube':
                                  if ($grandchild->get('logical_type') == 68) {
                                      $youtube .= (!$youtube)
                                          ? '<a title="'
                                              . e('View Presentation - YouTube Version')
                                              . '" class="youtube" href="'
                                              . $grandchild->path . '">'
                                              . Lang::txt('View on YouTube')
                                              . '</a>'
                                          : '';
                                      break;
                                  }
                                  // intentional fall through
                              case 'pdf':
                              default:
                                  if ($grandchild->get('logical_type') == 14) {
                                      $ext = Filesystem::extension(
                                          $grandchild->path
                                      );
                                      $ext = (strpos($ext, '?')
                                          ? strstr($ext, '?', true) : $ext);
                                      $pdf .= '<a href="'
                                          . $grandchild->path . '">'
                                          . Lang::txt('Notes')
                                          . ' (' . $ext . ')</a><br />';
                                  } elseif ($grandchild->get('logical_type') == 51) {
                                      $exercises .= '<a href="'
                                          . $grandchild->path . '">'
                                          . stripslashes($grandchild->title)
                                          . '</a><br />';
                                  } else {
                                      $gcParams  = $grandchild->params;
                                      $gcAttribs = $grandchild->attribs;
                                      $gcLinkAction = $gcParams->get(
                                          'link_action', 0
                                      );
                                      $width  = $gcAttribs->get('width', 640) + 20;
                                      $height = $gcAttribs->get('height', 360) + 60;
                                      $gcTitleStr = stripslashes(
                                          $grandchild->title
                                      );

                                      if ($gcLinkAction == 1) {
                                          $supp .= '<a rel="external" href="'
                                              . $grandchild->path . '">'
                                              . $gcTitleStr . '</a><br />';
                                      } elseif ($gcLinkAction == 2) {
                                          $playUrl = Route::url(
                                              'index.php?option=com_resources&id='
                                              . $child->id . '&resid='
                                              . $grandchild->id . '&task=play'
                                          );
                                          $supp .= '<a class="play '
                                              . $width . 'x' . $height
                                              . '" href="' . $playUrl . '">'
                                              . $gcTitleStr . '</a><br />';
                                      } else {
                                          $supp .= '<a href="'
                                              . $grandchild->path . '">'
                                              . $gcTitleStr . '</a><br />';
                                      }
                                  }
                                  break;
                          }
                      }
                    @endphp

                    @if($hubpresenter)
                      <td>{!! $hubpresenter !!}<br />{!! $breeze !!}</td>
                    @elseif($youtube)
                      <td>{!! $youtube !!}</td>
                    @else
                      <td>{!! $breeze !!}</td>
                    @endif
                    <td>{!! $videoi !!}</td>
                    <td>{!! $pdf !!}</td>
                    <td>{!! $supp !!}</td>
                    <td>{!! $exercises !!}</td>
                  @else
                    <td colspan="5">&nbsp;</td>
                  @endif
                </tr>
                @if($child->standalone == 1 && $child->get('type') != 31 && $child->introtext)
                  <tr>
                    <td colspan="6">
                      {{ \Hubzero\Utility\Str::truncate(stripslashes($child->introtext), 200) }}
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>
      </section>
    @endif
  @endif

</x-page-container>
