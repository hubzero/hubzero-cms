{{--
  Poll module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $formId = $params->get('moduleclass_sfx') ?: ('poll' . rand());
  $resultsUrl = Route::url('index.php?option=com_poll&view=poll&id=' . e($poll->id . ':' . $poll->alias));
@endphp
<form id="{{ $formId }}"
      method="post"
      action="{{ Route::url('index.php?option=com_poll') }}">
  <fieldset class="fieldset">
    <legend class="fieldset-legend">{{ e($poll->title) }}</legend>
    <div class="space-y-2 my-2">
      @foreach ($poll->options()->where('text', '!=', '')->order('id', 'asc')->rows() as $option)
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio" name="voteid" value="{{ e($option->id) }}"
                 class="radio radio-sm" />
          <span>{{ e(str_replace('&#039;', "'", $option->text)) }}</span>
        </label>
      @endforeach
    </div>
  </fieldset>
  <div class="flex items-center gap-3 mt-3">
    <button type="submit" name="task_button" class="btn btn-primary btn-sm">
      {{ Lang::txt('MOD_POLL_VOTE') }}
    </button>
    <a href="{{ $resultsUrl }}" class="link link-hover text-sm">
      {{ Lang::txt('MOD_POLL_RESULTS') }}
    </a>
  </div>
  <input type="hidden" name="option" value="com_poll" />
  <input type="hidden" name="task" value="vote" />
  <input type="hidden" name="id" value="{{ e($poll->id) }}" />
  {!! Html::input('token') !!}
</form>
