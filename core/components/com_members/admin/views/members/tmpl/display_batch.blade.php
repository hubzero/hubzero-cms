{{--
  Members — Batch operations panel

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $batchOptions = [
      Html::select('option', 'add', Lang::txt('COM_MEMBERS_BATCH_ADD')),
      Html::select('option', 'del', Lang::txt('COM_MEMBERS_BATCH_DELETE')),
      Html::select('option', 'set', Lang::txt('COM_MEMBERS_BATCH_SET')),
  ];
@endphp

<fieldset class="batch">
  <legend>{{ Lang::txt('COM_MEMBERS_BATCH_OPTIONS') }}</legend>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <div class="admin-field">
        <label for="batch-group-id" class="label">
          {{ Lang::txt('COM_MEMBERS_BATCH_GROUP') }}
        </label>
        <select name="batch[group_id]"
                id="batch-group-id"
                class="select select-bordered w-full">
          <option value="">{{ Lang::txt('JSELECT') }}</option>
          {!! Html::select(
              'options',
              \Components\Members\Helpers\Admin::getAccessGroups()
          ) !!}
        </select>
      </div>

      <div class="admin-field">
        {!! Html::select(
            'radiolist',
            $batchOptions,
            'batch[group_action]',
            '',
            'value',
            'text',
            'add'
        ) !!}
      </div>
    </div>
    <div class="flex items-end gap-2">
      <button type="submit"
              id="btn-batch-submit"
              class="btn btn-sm btn-primary">
        {{ Lang::txt('JGLOBAL_BATCH_PROCESS') }}
      </button>
      <button type="button"
              id="btn-batch-clear"
              class="btn btn-sm">
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    </div>
  </div>
</fieldset>
