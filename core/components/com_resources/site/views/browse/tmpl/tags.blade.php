{{--
  Resources tag browser page — type selector + 3-column AJAX tag browser + top rated.

  The tag browser columns (tags_list.php) are AJAX fragments loaded by JS and
  are NOT converted to Blade. This template provides the shell and top-rated list.

  Variables from controller (browsetagsTask):
    $title        — page title
    $option       — component option string
    $config       — component params (Registry)
    $filters      — array: type, ...
    $types        — collection of Type models
    $tag          — current tag filter
    $tag2         — secondary tag filter
    $supportedtag — supported tag string or null
    $results      — top-rated resources
    $authorized   — bool

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  \Hubzero\Document\Assets::addPluginStylesheet('resources', 'share');

  $__view->css();
  $__view->js();
  $__view->js('tagbrowser');

  $formAction = Route::url('index.php?option=' . $option);
  $viewAllUrl = Route::url('index.php?option=' . $option . '&type=' . $filters['type']);
@endphp

<x-page-container :title="$title">
  @slot('actions')
    @foreach($types as $type)
      @if($type->id == $filters['type'] && $type->contributable)
        @php
          if ($type->id == 7) {
              $newUrl = Route::url('index.php?option=com_tools&task=create');
          } else {
              $newUrl = Route::url(
                  'index.php?option=' . $option . '&task=draft&step=1&type=' . $type->id
              );
          }
          $typeName = $type->type;
          if (substr($type->type, -1) == 's') {
              $typeName = substr($type->type, 0, -1);
          }
        @endphp
        <a class="btn btn-primary" href="{{ $newUrl }}">
          {{ Lang::txt('COM_RESOURCES_START_NEW_TYPE', e(stripslashes($typeName))) }}
        </a>
      @endif
    @endforeach
  @endslot

  {{-- Type selector --}}
  <form action="{{ $formAction }}" method="get" id="tagBrowserForm" class="mb-6">
    <div class="flex items-end gap-3">
      <div>
        <label for="browse-type" class="label">
          <span class="label-text">{{ Lang::txt('COM_RESOURCES_TYPE') }}:</span>
        </label>
        <select name="type" id="browse-type" class="select select-bordered">
          @foreach($types as $type)
            @if(!$type->state)
              @continue
            @endif
            <option value="{{ e($type->alias) }}"
                    @selected($type->id == $filters['type'])>
              {{ e(stripslashes($type->type)) }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_RESOURCES_GO') }}
      </button>
      <input type="hidden" name="task" value="browsetags" />
    </div>
  </form>

  {{-- Tag browser (3-column AJAX widget) --}}
  <div id="tagbrowser"
       class="mb-6"
       data-loader="{{ Request::base(true) }}/core/components/com_resources/site/assets/img/loading.gif">
    <div role="alert" class="alert alert-info mb-4">
      <span>{{ Lang::txt('COM_RESOURCES_TAGBROWSER_EXPLANATION') }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div id="level-1">
        <h3 class="font-semibold mb-2">{{ Lang::txt('COM_RESOURCES_TAG') }}</h3>
        <ul>
          <li id="level-1-loading"></li>
        </ul>
      </div>
      <div id="level-2">
        <h3 class="font-semibold mb-2">{{ Lang::txt('COM_RESOURCES') }}</h3>
        <ul>
          <li id="level-2-loading"></li>
        </ul>
      </div>
      <div id="level-3">
        <h3 class="font-semibold mb-2">{{ Lang::txt('COM_RESOURCES_INFO') }}</h3>
        <ul>
          <li>{{ Lang::txt('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION') }}</li>
        </ul>
      </div>
    </div>

    <input type="hidden" name="id" id="id" value="" />
    <input type="hidden" name="pretype" id="pretype" value="{{ e($filters['type']) }}" />
    <input type="hidden" name="preinput" id="preinput" value="{{ e($tag) }}" />
    <input type="hidden" name="preinput2" id="preinput2" value="{{ e($tag2) }}" />
  </div>

  <p class="mb-6">
    <a class="link link-hover text-primary" href="{{ $viewAllUrl }}">
      {{ Lang::txt('COM_RESOURCES_VIEW_MORE') }}
    </a>
  </p>

  {{-- Supported tag info --}}
  @if($supportedtag)
    @php
      $supportedTagModel = \Components\Tags\Models\Tag::oneByTag($supportedtag);
      $sl = $config->get('supportedlink');
      $supportedLink = $sl ?: Route::url('index.php?option=com_tags&tag=' . $supportedTagModel->get('tag'));
    @endphp
    <p class="mb-6">
      <span class="badge badge-success gap-1">
        {{ Lang::txt('COM_RESOURCES_WHATS_THIS') }}
        <a class="link" href="{{ $supportedLink }}">
          {{ Lang::txt('COM_RESOURCES_ABOUT_TAG', $supportedTagModel->get('raw_tag')) }}
        </a>
      </span>
    </p>
  @endif

  {{-- Top rated section --}}
  @if($results)
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-8">
      <div class="lg:col-span-3">
        <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_RESOURCES_TOP_RATED') }}</h2>

        @php
          $supported = [];
          if ($supportedtag) {
              $rt = new \Components\Resources\Helpers\Tags(0);
              $supported = $rt->getTagUsage($supportedtag, 'id');
          }
        @endphp

        <ul class="list bg-base-100 rounded-box shadow-sm"
            aria-label="{{ Lang::txt('COM_RESOURCES_TOP_RATED') }}">
          @foreach($results as $line)
            @php
              $params = $line->params;
              $extras = \Hubzero\Facades\Event::trigger('resources.onResourcesList', [$line]);
              $isSupported = ($params->get('supportedtag') && in_array($line->id, $supported));
            @endphp
            <li class="list-row">
              <div class="list-col-grow">
                <h3 class="text-base font-semibold">
                  <a class="link link-hover text-primary" href="{{ Route::url($line->link()) }}">
                    {{ e(stripslashes($line->title)) }}
                  </a>
                  @if($isSupported)
                    <span class="badge badge-success badge-sm ml-1">
                      {{ Lang::txt('COM_RESOURCES_SUPPORTED') }}
                    </span>
                  @endif
                </h3>

                @if(!empty($extras))
                  {!! implode("\n", $extras) !!}
                @endif

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

                @php $tc = $line->tags('cloud'); @endphp
                @if($tc)
                  <div class="mt-1">
                    {!! $tc->render() !!}
                  </div>
                @endif
              </div>
            </li>
          @endforeach
        </ul>
      </div>
      <aside class="lg:col-span-1" aria-label="{{ Lang::txt('COM_RESOURCES_TOP_RATED') }}">
        <p class="text-sm text-base-content/70">
          {{ Lang::txt('COM_RESOURCES_TOP_RATED_EXPLANATION') }}
        </p>
      </aside>
    </div>
  @endif

</x-page-container>
