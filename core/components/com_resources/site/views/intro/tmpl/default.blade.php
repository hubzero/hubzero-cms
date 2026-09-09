{{--
  Resources landing page — intro text + category grid.

  Variables from controller (introTask):
    $title      — page title
    $option     — component option string
    $categories — collection of Type models

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('introduction.css', 'system');
  $__view->css();
  $__view->js();

  $submitUrl = Route::url('index.php?option=' . $option . '&task=new');
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
  $helpUrl   = Route::url('index.php?option=com_help&component=resources&page=index');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $submitUrl }}">
      {{ Lang::txt('COM_RESOURCES_SUBMIT_A_RESOURCE') }}
    </a>
  @endslot

  {{-- Intro section --}}
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div class="lg:col-span-3">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h2 class="text-lg font-semibold mb-2">{{ Lang::txt('COM_RESOURCES_WHAT_ARE_RESOURCES') }}</h2>
          <p class="text-base-content/70">{{ Lang::txt('COM_RESOURCES_WHAT_ARE_RESOURCES_EXPLANATION') }}</p>
        </div>
        <div>
          <h2 class="text-lg font-semibold mb-2">{{ Lang::txt('COM_RESOURCES_WHO_CAN_SUBMIT') }}</h2>
          <p class="text-base-content/70">{{ Lang::txt('COM_RESOURCES_WHO_CAN_SUBMIT_EXPLANATION') }}</p>
        </div>
      </div>
    </div>
    <div>
      <p>
        <a class="link link-hover" href="{{ $helpUrl }}">
          {{ Lang::txt('COM_RESOURCES_NEED_HELP') }}
        </a>
      </p>
    </div>
  </div>

  {{-- Search + browse --}}
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div>
      <h2 class="text-lg font-semibold">{{ Lang::txt('COM_RESOURCES_FIND_RESOURCE') }}</h2>
    </div>
    <div class="lg:col-span-3">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <x-search-bar
              :action="$browseUrl"
              :placeholder="Lang::txt('COM_RESOURCES_SEARCH_LABEL')"
              :label="Lang::txt('COM_RESOURCES_SEARCH_LABEL')"
              :buttonLabel="Lang::txt('COM_RESOURCES_SEARCH')"
              name="search" />
        </div>
        <div>
          <p>
            <a class="btn btn-outline" href="{{ $browseUrl }}">
              {{ Lang::txt('COM_RESOURCES_BROWSE_LIST') }}
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>

  {{-- Categories grid --}}
  @if($categories && $categories->count())
    @php
      $activeCategories = [];
      foreach ($categories as $cat) {
          if (!$cat->state) {
              continue;
          }
          if ($cat->isForTools() && !Component::isEnabled('com_tools', true)) {
              continue;
          }
          $activeCategories[] = $cat;
      }
    @endphp

    @if(count($activeCategories))
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div>
          <h2 class="text-lg font-semibold">{{ Lang::txt('COM_RESOURCES_CATEGORIES') }}</h2>
        </div>
        <div class="lg:col-span-3">
          <x-card-grid :cols="3">
            @foreach($activeCategories as $category)
              @php
                $catUrl = Route::url(
                    'index.php?option=' . $option . '&type=' . $category->alias
                );
                $catDescription = html_entity_decode(
                    strip_tags(stripslashes($category->description))
                );
                $catType = e(strip_tags(stripslashes($category->type)));
              @endphp
              <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                  <h3 class="card-title text-sm">
                    <a class="link link-hover" href="{{ $catUrl }}">
                      {{ $catType }}
                    </a>
                  </h3>
                  <p class="text-sm text-base-content/70">{{ $catDescription }}</p>
                  <p>
                    <a class="link link-hover text-sm text-primary" href="{{ $catUrl }}">
                      {!! Lang::txt('COM_RESOURCES_BROWSE_CATEGORY', $catType) !!}
                    </a>
                  </p>
                </div>
              </div>
            @endforeach
          </x-card-grid>
        </div>
      </div>
    @endif
  @endif

</x-page-container>
