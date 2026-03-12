{{--
  Wiki Pages — Admin delete confirmation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}: {{ Lang::txt('COM_WIKI_PAGE') }}: {{ Lang::txt('COM_WIKI_DELETE') }}"
    icon="wiki"
    option="{{ $option }}"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_WIKI_CONFIRM_DELETE') }}">

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-2">
          <input type="checkbox"
                 name="confirm"
                 id="confirm"
                 class="checkbox"
                 value="1" />
          <span>{{ Lang::txt('COM_WIKI_DELETE') }}</span>
        </label>
      </div>

      <div>
        <button type="submit" class="btn btn-sm btn-error">
          {{ Lang::txt('COM_WIKI_NEXT') }}
        </button>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <div role="alert" class="alert alert-warning">
      {{ Lang::txt('COM_WIKI_DELETE_WARNING') }}
    </div>
  @endslot

  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="task" value="{{ $task }}" />
  @foreach ($ids as $id)
    <input type="hidden" name="id[]" value="{{ $id }}" />
  @endforeach
</x-admin-edit>
