{{--
  Quick Tips module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($rows)
  <div class="{{ $params->get('moduleclass_sfx') }}">
    @foreach ($rows as $row)
      <div class="prose prose-sm max-w-none mb-3">
        <p>{!! stripslashes($row->introtext) !!}</p>
      </div>
      <p>
        @php
          $tipUrl = Route::url('index.php?option=com_content&task=view&id=' . $row->id);
        @endphp
        <a href="{{ $tipUrl }}" class="btn btn-sm btn-outline">
          {{ Lang::txt('MOD_QUICKTIPS_LEARN_MORE') }}
        </a>
      </p>
    @endforeach
  </div>
@endif
