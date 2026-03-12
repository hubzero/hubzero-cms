{{--
  Autogen Story — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('story');
  $text  = ($task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

  $__view->css();
  $__view->js('autogen-story.blade.js');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($type)) }}: {{ $text }}"
    icon="newsletter"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

@if(count($enabledSources) > 0)
  <form action="{!! Route::url('index.php?option=' . $option, false) !!}"
        method="post"
        name="adminForm"
        id="autogen-form"
        data-formwatcher-message="{{ Lang::txt('COM_NEWSLETTER_WANRING_UNSAVED_CHANGES') }}">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Settings --}}
      <div>
        <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_STORY_SETTINGS') }}">

            <div class="admin-field">
              <label for="contentSource" class="label">{{ Lang::txt('COM_NEWSLETTER_STORY_SOURCE') }}</label>
              <select name="contentSource" id="contentSource" class="select select-bordered w-full">
                <option value="none">{{ Lang::txt('MAKE_A_SELECTION') }}</option>
                @foreach($enabledSources as $source)
                  <option value="{{ $source }}">{{ $source }}</option>
                @endforeach
              </select>
            </div>

            <div class="admin-field">
              <label for="story-title" class="label">{{ Lang::txt('COM_NEWSLETTER_STORY_TITLE') }}</label>
              <input type="text"
                     name="title"
                     id="story-title"
                     class="input input-bordered w-full" />
            </div>

            <div class="admin-field">
              <label for="itemCount" class="label">{{ Lang::txt('COM_NEWSLETTER_STORY_ITEM_COUNT') }}</label>
              <input type="text"
                     name="itemCount"
                     id="itemCount"
                     class="input input-bordered w-full"
                     value="5" />
            </div>

            <div class="admin-field">
              <label for="storyLayout" class="label">{{ Lang::txt('COM_NEWSLETTER_STORY_LAYOUT_TEMPLATE') }}</label>
              <select name="layout" id="storyLayout" class="select select-bordered w-full">
                @foreach($layouts as $layout)
                  <option value="{{ $layout }}">{{ $layout }}</option>
                @endforeach
              </select>
            </div>

        </x-admin-fieldset>

        <input type="hidden" name="story[]" value="" />
        <input type="hidden" name="nid" value="{{ $nid }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="type" value="autogen" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="save" />
      </div>

      {{-- Preview --}}
      <div>
        <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_STORY_PREVIEW') }}">
            <div id="previewArea">
              <span id="previewStoryTitle"></span>
              <span id="previewContentArea"></span>
            </div>
        </x-admin-fieldset>
      </div>

    </div>
  </form>
@else
  <div role="alert" class="alert alert-warning">
    {{ Lang::txt('COM_NEWSLETTER_WANRING_NO_SOURCE_PLUGINS') }}
  </div>
@endif
