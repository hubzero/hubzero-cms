{{--
  Resource workshop view — upper pane with overview, podcast feeds, and child
  resource listing with sort controls and pagination.

  Variables from controller:
    $model    — Entry model
    $option   — component option string
    $sections — array of tab section content
    $cats     — array of tab categories
    $tab      — active tab name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
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
              $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') . ']</span> ';
              break;
          case 3:
              $txt .= '<span>[' . Lang::txt('COM_RESOURCES_PENDING') . ']</span> ';
              break;
          case 4:
              $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DELETED') . ']</span> ';
              break;
          case 5:
              $txt .= '<span>[' . Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') . ']</span> ';
              break;
          case 0:
              $txt .= '<span>[' . Lang::txt('COM_RESOURCES_UNPUBLISHED') . ']</span> ';
              break;
      }
  }

  $pageclassSfx = $model->params->get('pageclass_sfx', '');
@endphp

<section class="main section upperpane {{ $pageclassSfx }}">
  <div class="section-inner hz-layout-with-aside">
    <div class="subject">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
          <header id="content-header">
            <h2>
              {!! $txt !!}{{ e(stripslashes($model->title)) }}
              @if($model->params->get('access-edit-resource'))
                @php
                  $editUrl = Route::url(
                      'index.php?option=com_resources'
                      . '&task=draft&step=1&id='
                      . $model->id
                  );
                @endphp
                <a class="btn btn-outline btn-sm"
                   href="{{ $editUrl }}">{{ Lang::txt('COM_RESOURCES_EDIT') }}</a>
              @endif
            </h2>
            <input type="hidden" name="rid" id="rid" value="{{ $model->id }}" />
          </header>

          @if($model->params->get('show_authors', 1))
            <div id="authorslist">
              @include('view::_contributors', [
                  'option'       => $option,
                  'contributors' => $model->contributors('!submitter'),
              ])
            </div>
          @endif
        </div>

        <div class="launcharea">
          @if(!$model->access('view-all'))
            @php
              $ghtml = [];
              foreach ($model->groups as $allowedgroup) {
                  $groupUrl = Route::url(
                      'index.php?option=com_groups&cn=' . $allowedgroup
                  );
                  $ghtml[] = '<a href="' . $groupUrl . '">'
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
              $children = $model->children()
                  ->whereEquals('standalone', 1)
                  ->whereEquals('published', 1)
                  ->order('ordering', 'asc')
                  ->rows();

              $ccount = count($children);
            @endphp

            @if($ccount > 0)
              @php
                $mesg = Lang::txt('COM_RESOURCES_VIEW')
                    . ' ' . $model->type->get('type');
              @endphp
              @include('view::_primary', [
                  'option'   => $option,
                  'class'    => 'download',
                  'href'     => Route::url($model->link()) . '#series',
                  'title'    => $mesg,
                  'xtra'     => '',
                  'pop'      => '',
                  'action'   => '',
                  'msg'      => $mesg,
                  'disabled' => false,
              ])
            @endif

            @php
              $video = 0;
              $audio = 0;
              $notes = 0;

              foreach ($children as $child) {
                  $grandchildren = $child->children()
                      ->whereEquals('standalone', 0)
                      ->whereEquals('published', 1)
                      ->order('ordering', 'asc')
                      ->rows();

                  foreach ($grandchildren as $grandchild) {
                      $ext = Filesystem::extension($grandchild->path);
                      if (in_array($ext, ['m4v','mp4','wmv','mov','qt','mpg','mpeg','mpe','mp2','mpv2'])) {
                          $video++;
                      } elseif (in_array($ext, ['mp3','m4a','aiff','aif','wav','ra','ram'])) {
                          $audio++;
                      } elseif (in_array($ext, ['ppt','pps','pdf','doc','txt','html','htm'])) {
                          $notes++;
                      }
                  }
              }

              $liveSite = rtrim(Request::base(), '/');
              $feedBase = $liveSite . '/resources/' . $model->id . '/feed.rss?content=';
            @endphp

            @if($notes || $audio || $video)
              <p>
                @if($audio)
                  <a id="resource-audio-feed"
                     href="{{ $feedBase . 'audio' }}">{{ Lang::txt('Audio podcast') }}</a><br />
                @endif
                @if($video)
                  <a id="resource-video-feed"
                     href="{{ $feedBase . 'video' }}">{{ Lang::txt('Video podcast') }}</a><br />
                @endif
                @if($notes)
                  <a id="resource-slides-feed"
                     href="{{ $feedBase . 'slides' }}">{{ Lang::txt('Slides/Notes podcast') }}</a>
                @endif
              </p>
            @endif

            @if($tab != 'play')
              @include('view::_license', [
                  'license' => $model->license(),
              ])
            @endif
          @endif
        </div>
      </div>

      @include('view::_canonical', [
          'option' => $option,
          'model'  => $model,
      ])
    </div>

    <aside class="aside rankarea">
      @if($model->params->get('show_metadata', 1))
        @include('view::_metadata', [
            'option'   => $option,
            'sections' => $sections,
            'model'    => $model,
        ])
      @endif
    </aside>
  </div>
</section>

@if($model->access('view-all'))
  <section class="main section {{ $pageclassSfx }}">
    <div class="section-inner hz-layout-with-aside">
      <div class="subject tabbed">
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

      <div class="aside extracontent">
        @php
          $out = Event::trigger(
              'resources.onResourcesSub',
              [$model, $option, 1]
          );
        @endphp
        @foreach($out as $ou)
          @if(isset($ou['html']))
            {!! $ou['html'] !!}
          @endif
        @endforeach

        @if($tab == 'about')
          {!! \Hubzero\Module\Helper::renderModules('extracontent') !!}
        @endif
      </div>
    </div>
  </section>

  {{-- Child resource listing (workshop items) --}}
  @if($tab == 'about' && $ccount > 0)
    @php
      $filters = [
          'sortby' => Request::getString('sortby', $model->params->get('sort_children', 'ordering')),
          'limit'  => Request::getInt('limit', 0),
          'start'  => Request::getInt('limitstart', 0),
          'id'     => $model->id,
      ];

      // Validate sortby to prevent SQL injection
      $allowedSorts = ['date', 'date_published', 'date_created', 'date_modified',
                       'title', 'rating', 'ranking', 'random'];
      if (!in_array($filters['sortby'], $allowedSorts)) {
          App::abort(403, Lang::txt('Invalid sort value'));
      }

      $children = $model->children()
          ->whereEquals('standalone', 1)
          ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
          ->order(($filters['sortby'] == 'date' ? 'created' : $filters['sortby']), 'asc')
          ->limit($filters['limit'])
          ->start($filters['start'])
          ->rows();

      $sortbys = [
          'date'     => Lang::txt('DATE'),
          'title'    => Lang::txt('TITLE'),
          'author'   => Lang::txt('AUTHOR'),
          'ordering' => Lang::txt('ORDERING'),
      ];
      if ($model->params->get('show_ranking')) {
          $sortbys['ranking'] = Lang::txt('RANKING');
      }
    @endphp

    <form method="get" id="series" action="{{ Route::url($model->link()) }}">
      <section class="section">
        <div class="section-inner hz-layout-with-aside">
          <div class="subject">
            <h3>{{ Lang::txt('In This Workshop') }}</h3>

            @if($children->count())
              <ul class="list bg-base-100 rounded-box shadow-sm"
                  aria-label="{{ Lang::txt('In This Workshop') }}">
                @foreach($children as $line)
                  @php
                    $params = $line->params;
                  @endphp
                  <li class="list-row">
                    <div class="list-col-grow">
                      <h4 class="text-base font-semibold">
                        <a class="link link-hover text-primary"
                           href="{{ Route::url($line->link()) }}">
                          {{ e(stripslashes($line->title)) }}
                        </a>
                      </h4>

                      <div class="flex flex-wrap items-baseline gap-x-3 text-sm text-base-content/70">
                        @if($params->get('show_type'))
                          <strong>{{ stripslashes($line->type->get('type')) }}</strong>
                        @endif
                        @if($thedate = $line->date)
                          <span>{{ $thedate }}</span>
                        @endif
                        @if($line->authors->count() && $params->get('show_authors'))
                          @php $authors = $line->authorsList(); @endphp
                          @if(trim($authors))
                            <span>{!! Lang::txt('COM_RESOURCES_CONTRIBUTORS') . ': ' . $authors !!}</span>
                          @endif
                        @endif
                      </div>

                      @if($params->get('show_ranking'))
                        @php
                          $ranking = round($line->get('ranking'), 1);
                          $r = 10 * $ranking;
                        @endphp
                        <div class="flex items-center gap-2 mt-1">
                          <div class="w-24 bg-base-200 rounded-full h-2"
                               role="img"
                               aria-label="{{ Lang::txt('COM_RESOURCES_RANKING') }}: {{ number_format($ranking, 1) }}">
                            <div class="bg-primary h-2 rounded-full"
                                 style="width: {{ $r }}%"></div>
                          </div>
                          <span class="text-xs text-base-content/60">
                            {{ number_format($ranking, 1) }} {{ Lang::txt('COM_RESOURCES_RANKING') }}
                          </span>
                        </div>
                      @elseif($params->get('show_rating'))
                        <div class="mt-1"
                             role="img"
                             aria-label="{{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}">
                          <span class="text-xs text-base-content/60">
                            {{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}
                          </span>
                        </div>
                      @endif

                      @php
                        $content = '';
                        if ($line->get('introtext')) {
                            $content = $line->get('introtext');
                        } elseif ($line->get('fulltxt')) {
                            $content = $line->get('fulltxt');
                            $content = preg_replace('#<nb:(.*?)>(.*?)</nb:(.*?)>#s', '', $content);
                            $content = trim($content);
                        }
                        $snippet = \Hubzero\Utility\Str::truncate(
                            strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))),
                            300
                        );
                      @endphp
                      <p class="text-sm text-base-content/70 mt-1 line-clamp-3">
                        {{ $snippet }}
                      </p>
                    </div>
                  </li>
                @endforeach
              </ul>
            @endif

            @php
              $pageNav = $__view->pagination(
                  $ccount,
                  $filters['start'],
                  $filters['limit']
              );
              $pageNav->setAdditionalUrlParam('id', $model->id);
              $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
            @endphp
            <nav aria-label="{{ Lang::txt('JGLOBAL_PAGINATION') }}" class="flex justify-center mt-8">
              {!! $pageNav->render() !!}
            </nav>
          </div>

          <div class="aside">
            <div class="card bg-base-100 shadow-sm">
              <div class="card-body">
                <label for="sortby">
                  {{ Lang::txt('COM_RESOURCES_SORT_BY') }}:
                </label>
                <select name="sortby" id="sortby"
                        class="select select-bordered select-sm">
                  @foreach($sortbys as $sortKey => $sortLabel)
                    <option value="{{ $sortKey }}"
                            @selected($filters['sortby'] == $sortKey)>
                      {{ $sortLabel }}
                    </option>
                  @endforeach
                </select>
                <p class="mt-2">
                  <button type="submit"
                          class="btn btn-primary btn-sm">{{ Lang::txt('COM_RESOURCES_GO') }}</button>
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </form>
  @endif
@endif
