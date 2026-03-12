{{--
  com_messages — User settings (rendered in tmpl=component popup)

  Variables: $item (object with lock, mail_on_new, auto_purge)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Html;

  $lock       = (int) $item->get('lock', 0);
  $mailOnNew  = (int) $item->get('mail_on_new', 1);
  $autoPurge  = (int) $item->get('auto_purge', 7);
  $formAction = Route::url('index.php?option=com_messages&controller=configs', false);

  $__view->js('config.blade');
@endphp

<form action="{{ $formAction }}"
      method="post"
      id="config-form"
      class="p-6 space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between border-b border-base-300 pb-4">
    <h2 class="text-base font-semibold">{{ Lang::txt('COM_MESSAGES_MY_SETTINGS') }}</h2>
    <div class="flex gap-2">
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSAVE') }}
      </button>
      <button type="button" id="cfg-cancel" class="btn btn-sm btn-ghost" data-close-modal>
        {{ Lang::txt('JCANCEL') }}
      </button>
    </div>
  </div>

  {{-- Lock inbox --}}
  <div class="admin-field">
    <label class="text-sm font-medium">
      {{ Lang::txt('COM_MESSAGES_FIELD_LOCK_LABEL') }}
    </label>
    <p class="text-xs text-muted-foreground mb-2">
      {{ Lang::txt('COM_MESSAGES_FIELD_LOCK_DESC') }}
    </p>
    <div class="flex items-center gap-6">
      <label class="flex items-center gap-2 cursor-pointer text-sm">
        <input type="radio"
               name="lock"
               value="1"
               class="radio radio-sm"
               {{ $lock ? 'checked' : '' }} />
        {{ Lang::txt('JYES') }}
      </label>
      <label class="flex items-center gap-2 cursor-pointer text-sm">
        <input type="radio"
               name="lock"
               value="0"
               class="radio radio-sm"
               {{ !$lock ? 'checked' : '' }} />
        {{ Lang::txt('JNO') }}
      </label>
    </div>
  </div>

  {{-- Email on new message --}}
  <div class="admin-field">
    <label class="text-sm font-medium">
      {{ Lang::txt('COM_MESSAGES_FIELD_MAIL_ON_NEW_LABEL') }}
    </label>
    <p class="text-xs text-muted-foreground mb-2">
      {{ Lang::txt('COM_MESSAGES_FIELD_MAIL_ON_NEW_DESC') }}
    </p>
    <div class="flex items-center gap-6">
      <label class="flex items-center gap-2 cursor-pointer text-sm">
        <input type="radio"
               name="mail_on_new"
               value="1"
               class="radio radio-sm"
               {{ $mailOnNew ? 'checked' : '' }} />
        {{ Lang::txt('JYES') }}
      </label>
      <label class="flex items-center gap-2 cursor-pointer text-sm">
        <input type="radio"
               name="mail_on_new"
               value="0"
               class="radio radio-sm"
               {{ !$mailOnNew ? 'checked' : '' }} />
        {{ Lang::txt('JNO') }}
      </label>
    </div>
  </div>

  {{-- Auto-purge --}}
  <div class="admin-field">
    <label for="cfg-auto_purge" class="text-sm font-medium">
      {{ Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_LABEL') }}
    </label>
    <p class="text-xs text-muted-foreground mb-2">
      {{ Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_DESC') }}
    </p>
    <input type="number"
           name="auto_purge"
           id="cfg-auto_purge"
           class="input input-bordered input-sm w-32"
           min="0"
           value="{{ $autoPurge }}" />
  </div>

  <input type="hidden" name="option" value="com_messages" />
  <input type="hidden" name="controller" value="configs" />
  <input type="hidden" name="task" value="" />
  {!! Html::input('token') !!}

</form>
