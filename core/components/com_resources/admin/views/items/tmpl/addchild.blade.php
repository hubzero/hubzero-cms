{{--
  Resource — Add child dialog

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_ADD_CHILD') }}"
    icon="resources"
    :edit="true"
/>

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="item-form">

  <h3 class="text-lg font-semibold mb-4">{{ $parent->title }}</h3>

  <x-admin-fieldset legend="{{ Lang::txt('COM_RESOURCES_ADD_CHILD_CHOOSE') }}">

      <div class="grid grid-cols-2 gap-6">
        <div>
          <label class="cursor-pointer flex items-center gap-2 mb-2">
            <input type="radio"
                   name="method"
                   id="child_create"
                   value="create"
                   class="radio radio-sm"
                   checked />
            {{ Lang::txt('COM_RESOURCES_ADD_CHILD_CREATE') }}
          </label>
        </div>
        <div>
          <label class="cursor-pointer flex items-center gap-2 mb-2">
            <input type="radio"
                   name="method"
                   id="child_existing"
                   value="existing"
                   class="radio radio-sm" />
            {{ Lang::txt('COM_RESOURCES_ADD_CHILD_EXISTING') }}
          </label>
          <div class="admin-field mt-2">
            <label for="childid" class="label">
              {{ Lang::txt('COM_RESOURCES_FIELD_RESOURCE_ID') }}:
            </label>
            <input type="text"
                   name="childid"
                   id="childid"
                   class="input input-bordered input-sm w-full"
                   value="" />
          </div>
        </div>
      </div>

      <input type="hidden" name="step" value="2" />
      <input type="hidden" name="task" value="{{ $task }}" />
      <input type="hidden" name="pid" value="{{ $pid }}" />
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="{{ $controller }}" />

      {!! Html::input('token') !!}
  </x-admin-fieldset>

  <div class="text-center mt-4">
    <button type="submit" class="btn btn-primary">
      {{ Lang::txt('COM_RESOURCES_NEXT') }}
    </button>
  </div>
</form>
