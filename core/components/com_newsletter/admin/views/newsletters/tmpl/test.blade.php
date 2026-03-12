{{--
  Newsletter — Test send view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_TEST_SENDING') }}: {{ $newsletter->name }}"
    icon="newsletter"
    option="{{ $option }}"
    :edit="true"
/>

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  @if($newsletter->id)
    <x-admin-fieldset legend="{{ Lang::txt('COM_NEWSLETTER_TEST_SENDING') }}">

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER') }}</label>
          <p class="text-sm font-semibold">{{ $newsletter->name }}</p>
        </div>

        <div class="admin-field">
          <label for="field-emails" class="label">
            {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS') }}
          </label>
          <input type="text"
                 name="emails"
                 id="field-emails"
                 class="input input-bordered w-full"
                 placeholder="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_PLACEHOLDER') }}"
                 autocomplete="off" />
          <p class="text-xs text-muted-foreground mt-1">
            {{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEST_EMAILS_HINT') }}
          </p>
        </div>

    </x-admin-fieldset>
  @endif

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="dosendtest" />
  <input type="hidden" name="nid" value="{{ $newsletter->id }}" />
</form>
