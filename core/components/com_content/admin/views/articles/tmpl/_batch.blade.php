{{--
  Batch processing — partial included from article list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<details class="collapse collapse-arrow bg-base-200 mt-4 rounded-box">
  <summary class="collapse-title font-medium">
    {{ Lang::txt('COM_CONTENT_BATCH_OPTIONS') }}
  </summary>
  <div class="collapse-content">
    <p class="text-sm text-muted-foreground mb-4">
      {{ Lang::txt('COM_CONTENT_BATCH_TIP') }}
    </p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-3">
        <div class="admin-field">
          {!! Html::batch('access') !!}
        </div>
        <div class="admin-field">
          {!! Html::batch('language') !!}
        </div>
      </div>
      <div class="space-y-3">
        @if(($filters['published'] ?? '') >= 0)
          <div class="admin-field">
            {!! Html::batch('item', 'com_content') !!}
          </div>
        @endif
        <div class="flex gap-2">
          <button type="submit" class="btn btn-sm btn-primary"
                  data-task="batch">
            {{ Lang::txt('JGLOBAL_BATCH_PROCESS') }}
          </button>
          <button type="button" class="btn btn-sm btn-ghost"
                  id="btn-batch-clear">
            {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</details>
