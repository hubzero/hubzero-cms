{{--
  Resource detail view — upper pane (title, authors, launch area, metadata)
  and lower tabbed content with sidebar extras.

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
    @if($model->access('view'))
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
                base64_encode(Request::current(true))
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
              ->whereEquals('standalone', 0)
              ->whereEquals(
                  'published',
                  \Components\Resources\Models\Entry::STATE_PUBLISHED
              )
              ->order('ordering', 'asc')
              ->rows();

          $firstchild = $children->first();

          $launchHtml = ($tab != 'play' && is_object($firstchild))
              ? \Components\Resources\Helpers\Html::primaryChild(
                  $option, $model, $firstchild, ''
              )
              : '';

          if ($children && count($children) > 1) {
              $launchHtml .= \Components\Resources\Helpers\Html::sortSupportingDocs(
                  $model, $option, $children
              );
          }
        @endphp

        {!! $launchHtml !!}

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
  @if($model->access('view'))
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
  @endif

</x-page-container>
