{{--
  Story — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('story');
  $text  = ($task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
  $typeTxt = Lang::txt('COM_NEWSLETTER_STORY_' . ucfirst($type));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($type)) }}: {{ $text }}"
    icon="newsletter"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($type)) }}">

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER') }}</label>
        <p class="text-sm font-semibold">{{ $newsletter->name }}</p>
      </div>

      <div class="admin-field">
        <label class="label">{{ Lang::txt('COM_NEWSLETTER_STORY_TYPE') }}</label>
        <p class="text-sm">{{ $typeTxt }}</p>
        <input type="hidden" name="type" value="{{ strtolower($type) }}" />
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE') }}</label>
        <input type="text"
               name="story[title]"
               id="field-title"
               class="input input-bordered w-full"
               value="{{ $story->title }}" />
      </div>

      @if($story->id)
        <div class="admin-field">
          <label for="field-order" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER') }}</label>
          <input type="text"
                 name="story[order]"
                 id="field-order"
                 class="input input-bordered w-full"
                 readonly
                 value="{{ $story->order }}" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER_HINT') }}
          </p>
        </div>
      @endif

      <div class="admin-field">
        <label for="field-story" class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY') }}</label>
        {!! $__view->editor(
            'story[story]',
            $story->story,
            50,
            10,
            'field-story',
            ['full_paths' => true]
        ) !!}
        <p class="text-xs text-muted-foreground mt-1">
          {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT1') }}
        </p>
        <p class="text-xs text-muted-foreground">
          {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY_HINT2') }}
        </p>
      </div>

      <fieldset class="border border-base-300 rounded-box p-4">
        <legend class="text-sm font-semibold px-2">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE') }}</legend>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="field-readmore_title" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_TITLE_PLACEHOLDER') }}</label>
            <input type="text"
                   name="story[readmore_title]"
                   id="field-readmore_title"
                   class="input input-bordered w-full"
                   value="{{ $story->readmore_title }}"
                   placeholder="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_TITLE_PLACEHOLDER') }}" />
          </div>
          <div class="md:col-span-2">
            <label for="field-readmore_link" class="sr-only">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_PLACEHOLDER') }}</label>
            <input type="text"
                   name="story[readmore_link]"
                   id="field-readmore_link"
                   class="input input-bordered w-full"
                   value="{{ $story->readmore_link }}"
                   placeholder="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE_LINK_PLACEHOLDER') }}" />
          </div>
        </div>
      </fieldset>

  </x-admin-fieldset>

  <input type="hidden" name="story[id]" value="{{ $story->id }}" />
  <input type="hidden" name="story[nid]" value="{{ $newsletter->id }}" />
  <input type="hidden" name="nid" value="{{ $newsletter->id }}" />
  <input type="hidden" name="task" value="save" />
</x-admin-edit>
