{{--
  com_menus items — Batch processing panel partial

  Included from display.blade.php when the user has create+edit+edit.state perms.
  Provides move/copy to menu and optional access-level override.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  // Load the component's HtmlMenu helper directly (Html::menu() is shadowed by the framework's Menu class)
  require_once \Hubzero\Facades\Component::path($option) . '/helpers/html/menu.php';

  $published = $filters['published'] ?? '';
@endphp

<div class="admin-fieldset mt-4">
  <h3 class="admin-fieldset-heading">{{ Lang::txt('COM_MENUS_BATCH_OPTIONS') }}</h3>
  <div class="admin-fieldset-body">
    <p class="text-sm text-muted-foreground mb-4">{{ Lang::txt('COM_MENUS_BATCH_TIP') }}</p>

    <div class="flex flex-wrap gap-6 items-end">

      {{-- Access level override --}}
      <div class="form-control min-w-48">
        <label class="label pb-1" for="batch-access">
          <span class="label-text font-medium">{{ Lang::txt('JLIB_HTML_BATCH_ACCESS_LABEL') }}</span>
        </label>
        <select name="batch[access]" id="batch-access" class="select select-bordered select-sm">
          <option value="">{{ Lang::txt('JLIB_HTML_BATCH_NOCHANGE') }}</option>
          {!! Html::select('options', Html::access('assetgroups'), 'value', 'text') !!}
        </select>
      </div>

      {{-- Menu + move/copy --}}
      @if((string)$published !== '-2')
        <div class="form-control flex-1 min-w-64">
          <label class="label pb-1" for="batch-menu-id">
            <span class="label-text font-medium">{{ Lang::txt('COM_MENUS_BATCH_MENU_LABEL') }}</span>
          </label>
          <select name="batch[menu_id]" id="batch-menu-id"
                  class="select select-bordered select-sm w-full">
            <option value="">{{ Lang::txt('JSELECT') }}</option>
            {!! Html::select('options', \Components\Menus\Helpers\Html\HtmlMenu::menuitems(['published' => (int)$published])) !!}
          </select>
        </div>

        {{-- Copy / Move radio --}}
        <div class="flex gap-4 pb-1">
          <label class="flex items-center gap-1.5 cursor-pointer text-sm">
            <input type="radio" name="batch[move_copy]" value="c"
                   class="radio radio-sm" />
            {{ Lang::txt('JLIB_HTML_BATCH_COPY') }}
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer text-sm">
            <input type="radio" name="batch[move_copy]" value="m"
                   class="radio radio-sm" checked />
            {{ Lang::txt('JLIB_HTML_BATCH_MOVE') }}
          </label>
        </div>
      @endif

    </div>

    <div class="flex gap-2 mt-4">
      <button type="button" class="btn btn-sm btn-primary" id="btn-batch-submit"
              data-submit-task="items.batch">
        {{ Lang::txt('JGLOBAL_BATCH_PROCESS') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost border border-base-300" id="btn-batch-clear"
              data-batch-clear>
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    </div>
  </div>
</div>
