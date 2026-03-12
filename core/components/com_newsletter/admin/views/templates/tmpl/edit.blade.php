{{--
  Newsletter Template — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('newsletter');
  $text  = ($task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') }}: {{ $text }}"
    icon="template"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_NAME') }}">

      <div class="admin-field">
        <label for="field-name" class="label">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_NAME') }}</label>
        <input type="text"
               name="fields[name]"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ $template->name }}" />
      </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY') }}">

      <div class="admin-field">
        <label for="field-primary_title_color" class="label">
          {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TITLE_COLOR') }}
        </label>
        <input type="text"
               name="fields[primary_title_color]"
               id="field-primary_title_color"
               class="input input-bordered w-full"
               value="{{ $template->primary_title_color }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-primary_text_color" class="label">
          {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TEXT_COLOR') }}
        </label>
        <input type="text"
               name="fields[primary_text_color]"
               id="field-primary_text_color"
               class="input input-bordered w-full"
               value="{{ $template->primary_text_color }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY') }}">

      <div class="admin-field">
        <label for="field-secondary_title_color" class="label">
          {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TITLE_COLOR') }}
        </label>
        <input type="text"
               name="fields[secondary_title_color]"
               id="field-secondary_title_color"
               class="input input-bordered w-full"
               value="{{ $template->secondary_title_color }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-secondary_text_color" class="label">
          {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TEXT_COLOR') }}
        </label>
        <input type="text"
               name="fields[secondary_text_color]"
               id="field-secondary_text_color"
               class="input input-bordered w-full"
               value="{{ $template->secondary_text_color }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_TEMPLATE') }}">

      <div class="admin-field">
        <label for="field-template" class="label">{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_TEMPLATE') }}</label>
        <textarea name="fields[template]"
                  id="field-template"
                  class="textarea textarea-bordered w-full font-mono text-sm"
                  rows="30">{{ $template->template }}</textarea>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS') }}" body-class="text-sm space-y-3">

        @if($config->get('template_tips'))
          <p>
            {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS') }}<br />
            <a href="{{ $config->get('template_tips') }}"
               class="link link-primary text-sm"
               target="_blank">
              {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS_HINT') }}
            </a>
          </p>
        @endif

        @if($config->get('template_templates'))
          <p>
            {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES') }}<br />
            <a href="{{ $config->get('template_templates') }}"
               class="link link-primary text-sm"
               target="_blank">
              {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES_HINT') }}
            </a>
          </p>
        @endif

        <div class="text-muted-foreground">
          {!! Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS_HINT') !!}
        </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $template->id }}" />
</x-admin-edit>
