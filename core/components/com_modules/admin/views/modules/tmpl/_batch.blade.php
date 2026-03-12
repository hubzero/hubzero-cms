{{--
  com_modules — Batch processing partial

  Variables: $filters (client_id, state)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $clientId  = $filters['client_id'] ?? 0;
  $published = $filters['state'] ?? '';
@endphp

<details class="admin-fieldset mt-4">
  <summary class="admin-fieldset-heading">{{ Lang::txt('COM_MODULES_BATCH_OPTIONS') }}</summary>
  <div class="admin-fieldset-body space-y-4">

    <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_MODULES_BATCH_TIP') }}</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="space-y-4">
        <div class="admin-field">
          {!! Html::batch('access') !!}
        </div>
        <div class="admin-field">
          {!! Html::batch('language') !!}
        </div>
      </div>

      <div class="space-y-4">
        @if ($published >= 0)
          <div class="admin-field">
            <label for="batch-position-id" class="admin-field-label">
              {{ Lang::txt('COM_MODULES_BATCH_POSITION_LABEL') }}
            </label>
            <select name="batch[position_id]"
                    id="batch-position-id"
                    class="select select-bordered select-sm w-full">
              <option value="">{{ Lang::txt('JSELECT') }}</option>
              <option value="nochange">{{ Lang::txt('COM_MODULES_BATCH_POSITION_NOCHANGE') }}</option>
              <option value="noposition">{{ Lang::txt('COM_MODULES_BATCH_POSITION_NOPOSITION') }}</option>
              {!! Html::select('options', \Components\Modules\Helpers\Modules::positionList($clientId)) !!}
            </select>
            <div class="flex gap-4 mt-2">
              <label class="flex items-center gap-1.5 cursor-pointer text-sm">
                <input type="radio"
                       name="batch[move_copy]"
                       id="batch[move_copy]c"
                       value="c"
                       class="radio radio-sm" />
                {{ Lang::txt('JLIB_HTML_BATCH_COPY') }}
              </label>
              <label class="flex items-center gap-1.5 cursor-pointer text-sm">
                <input type="radio"
                       name="batch[move_copy]"
                       id="batch[move_copy]m"
                       value="m"
                       class="radio radio-sm"
                       checked />
                {{ Lang::txt('JLIB_HTML_BATCH_MOVE') }}
              </label>
            </div>
          </div>
        @endif

        <div class="flex gap-2">
          <button type="submit" id="btn-batch-submit" class="btn btn-sm btn-primary">
            {{ Lang::txt('JGLOBAL_BATCH_PROCESS') }}
          </button>
          <button type="button" id="btn-batch-clear" class="btn btn-sm btn-ghost">
            {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
          </button>
        </div>
      </div>
    </div>

  </div>
</details>
