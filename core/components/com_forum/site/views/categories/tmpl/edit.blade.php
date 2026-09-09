{{--
  Category edit/new form.

  Variables from controller (editTask):
    $category — Category model instance (new or existing)
    $forum    — Manager model instance
    $section  — Section model instance
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();

  $editTitle = $category->get('id')
      ? Lang::txt('COM_FORUM_EDIT_CATEGORY')
      : Lang::txt('COM_FORUM_NEW_CATEGORY');

  $forumUrl  = Route::url('index.php?option=' . $option, false);
  $saveUrl   = Route::url('index.php?option=' . $option, false);
  $access    = $category->get('access', 1);
@endphp

<x-page-container :title="Lang::txt('COM_FORUM') . ': ' . $editTitle">
  @slot('actions')
    <a class="btn" href="{{ $forumUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
      </svg>
      {{ Lang::txt('COM_FORUM_ALL_CATEGORIES') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_FORUM_WHAT_IS_LOCKING')">
      <p class="text-sm">{{ Lang::txt('COM_FORUM_LOCKING_EXPLANATION') }}</p>
    </x-sidebar-card>
  @endslot

  <form action="{{ $saveUrl }}" method="post" id="hubForm" class="space-y-6">
    <x-form-section :heading="$editTitle">
      {{-- Closed / Access row --}}
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div class="flex items-end h-full">
          <label class="checkbox-label mb-2.5">
            <input type="checkbox" class="checkbox" name="fields[closed]"
                   id="field-closed" value="3"
                   @if($category->get('closed')) checked @endif />
            <span>{{ Lang::txt('COM_FORUM_FIELD_CLOSED') }}</span>
          </label>
        </div>

        <x-form-field name="field-access"
                      :label="Lang::txt('COM_FORUM_FIELD_VIEW_ACCESS')">
          <select id="field-access" name="fields[access]" class="select w-full">
            <option value="1" @if($access == 1) selected @endif>
              {{ Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC') }}
            </option>
            <option value="2" @if($access == 2) selected @endif>
              {{ Lang::txt('COM_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED') }}
            </option>
          </select>
        </x-form-field>
      </div>

      {{-- Section --}}
      <x-form-field name="field-section_id"
                    :label="Lang::txt('COM_FORUM_FIELD_SECTION')"
                    :required="true">
        <select id="field-section_id" name="fields[section_id]" class="select w-full" required>
          @foreach($forum->sections(['state' => 1])->rows() as $sec)
            <option value="{{ $sec->get('id') }}"
                    @if($category->get('section_id') == $sec->get('id')) selected @endif>
              {{ e(stripslashes($sec->get('title'))) }}
            </option>
          @endforeach
        </select>
      </x-form-field>

      {{-- Title --}}
      <x-form-field name="field-title"
                    :label="Lang::txt('COM_FORUM_FIELD_TITLE')"
                    :required="true">
        <input type="text" id="field-title" name="fields[title]" class="input w-full"
               value="{{ e(stripslashes($category->get('title', ''))) }}" required />
      </x-form-field>

      {{-- Description --}}
      <x-form-field name="field-description"
                    :label="Lang::txt('COM_FORUM_FIELD_DESCRIPTION')">
        <textarea id="field-description" name="fields[description]" class="textarea w-full"
                  rows="5">{{ e(stripslashes($category->get('description', ''))) }}</textarea>
      </x-form-field>
    </x-form-section>

    {{-- Form actions --}}
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">{{ Lang::txt('JSUBMIT') }}</button>
      <a class="btn btn-ghost" href="{{ $forumUrl }}">{{ Lang::txt('JCANCEL') }}</a>
    </div>

    {{-- Hidden fields --}}
    <input type="hidden" name="fields[alias]" value="{{ $category->get('alias') }}" />
    <input type="hidden" name="fields[id]" value="{{ $category->get('id') }}" />
    <input type="hidden" name="fields[state]" value="{{ $category->get('state', 1) }}" />
    <input type="hidden" name="fields[scope]" value="site" />
    <input type="hidden" name="fields[scope_id]" value="0" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="categories" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
  </form>
</x-page-container>
