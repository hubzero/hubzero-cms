{{--
  Group category add/edit form.

  Variables from controller:
    $group         — Group object
    $category      — Category model
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css()
         ->js()
         ->css('jquery.colpick.css', 'system')
         ->js('jquery.colpick', 'system');

  $isEdit  = (bool) $category->get('id');
  $title   = $isEdit
      ? Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY')
      : Lang::txt('COM_GROUPS_PAGES_ADD_CATEGORY');
  $cn      = $group->get('cn');
  $backUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=pages#categories');
  $saveUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&controller=categories&task=savecategory');
  $no_html = Request::getInt('no_html', 0);
@endphp

@if(!$no_html)
<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $backUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_PAGES') }}
    </a>
  @endslot
@endif

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <form action="{{ $saveUrl }}" method="post" id="hubForm" class="editcategory">
    <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_CATEGORY_DETAILS')">
      <x-form-field name="category[title]" inputId="field-category-title"
                    :label="Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE')"
                    :required="true">
        <input type="text" name="category[title]" id="field-category-title"
               class="input input-bordered w-full"
               value="{{ e($category->get('title')) }}" />
      </x-form-field>

      <x-form-field name="category[color]" inputId="field-category-color"
                    :label="Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR')">
        <input type="text" name="category[color]" id="field-category-color"
               maxlength="6"
               class="input input-bordered w-full"
               value="{{ e($category->get('color')) }}" />
      </x-form-field>
    </x-form-section>

    <input type="hidden" name="option" value="com_groups" />
    <input type="hidden" name="controller" value="categories" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="category[id]" value="{{ intval($category->get('id', 0)) }}" />

    <div class="mt-6 flex gap-2">
      <button type="submit" class="btn btn-info">
        {{ Lang::txt('COM_GROUPS_PAGES_SAVE_CATEGORY') }}
      </button>
      <a href="{{ $backUrl }}" class="btn">
        {{ Lang::txt('COM_GROUPS_PAGES_CANCEL') }}
      </a>
    </div>
  </form>

@if(!$no_html)
</x-page-container>
@endif
