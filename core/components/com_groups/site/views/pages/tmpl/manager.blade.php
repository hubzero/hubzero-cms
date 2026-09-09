{{--
  Page manager with tabs (pages, categories, modules).

  Variables from controller:
    $title         — string: page title
    $option        — string: component option
    $group         — Group object
    $pages         — array: page tree
    $categories    — collection of Category models
    $modules       — collection of Module models
    $config        — Registry: component config
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()
         ->js()
         ->css('jquery.fancyselect.css', 'system')
         ->js('jquery.fancyselect', 'system')
         ->js('jquery.nestedsortable', 'system');

  $cn = $group->get('cn');

  $hasHomeOverride = file_exists(
      PATH_APP . DS . $group->getBasePath() . DS . 'pages' . DS . 'overview.php'
  );

  $uploadUrl = Route::url(
      'index.php?option=' . $option . '&cn=' . $cn
      . '&controller=media&task=filebrowser&tmpl=component&path=/uploads'
  );
  $groupUrl = Route::url('index.php?option=' . $option . '&cn=' . $cn);
  $pagesUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=pages');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm popup 1200x600" href="{{ $uploadUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_UPLOAD_MANAGER') }}
    </a>
    <a class="btn btn-sm" href="{{ $groupUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP') }}
    </a>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  @if($group->isSuperGroup() && $hasHomeOverride)
    <div class="alert alert-info" role="alert">
      {{ Lang::txt('COM_GROUPS_PAGES_SUPER_GROUP_HAS_HOME_OVERRIDE') }}
    </div>
  @endif

  <div class="group-page-manager">
    <div role="tablist" class="tabs tabs-border mb-4">
      <a role="tab" class="tab tab-active" data-tab="pages"
         href="{{ $pagesUrl }}#pages">
        {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGES') }}
      </a>
      <a role="tab" class="tab" data-tab="categories"
         href="{{ $pagesUrl }}#categories">
        {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE_CATEGORIES') }}
      </a>
      @if($group->isSuperGroup() || $config->get('page_modules', 0) == 1)
        <a role="tab" class="tab" data-tab="modules"
           href="{{ $pagesUrl }}#modules">
          {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_MODULES') }}
        </a>
      @endif
    </div>

    <form action="{{ $pagesUrl }}" method="post" id="hubForm">
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="cn" value="{{ $cn }}" />
      <input type="hidden" name="task" value="pages" />

      <div data-tab-content="pages">
        {!! $__view->view('display')
             ->set('group', $group)
             ->set('categories', $categories)
             ->set('pages', $pages)
             ->set('config', $config)
             ->set('search', $search ?? '')
             ->display() !!}
      </div>

      <div data-tab-content="categories" style="display:none">
        {!! $__view->view('display', 'categories')
             ->set('group', $group)
             ->set('categories', $categories)
             ->display() !!}
      </div>

      @if($group->isSuperGroup() || $config->get('page_modules', 0) == 1)
        <div data-tab-content="modules" style="display:none">
          {!! $__view->view('display', 'modules')
               ->set('group', $group)
               ->set('modules', $modules)
               ->display() !!}
        </div>
      @endif
    </form>
  </div>
</x-page-container>
