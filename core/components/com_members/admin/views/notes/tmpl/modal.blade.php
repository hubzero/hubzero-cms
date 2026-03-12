{{--
  User Notes — Modal popup view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="p-4">
  <h2 class="text-lg font-semibold mb-4">
    {!! Lang::txt('COM_MEMBERS_NOTES_FOR_USER', e($user->get('name')), $user->get('id')) !!}
  </h2>

  @if(!$rows->count())
    <div class="alert alert-info">
      {{ Lang::txt('COM_MEMBERS_NO_NOTES') }}
    </div>
  @else
    <div class="space-y-3">
      @foreach($rows as $row)
        <div class="bg-base-100 rounded-box border border-base-300 p-3">
          <h4 class="font-medium text-sm">
            @if($row->get('subject'))
              {!! Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), e($row->get('subject'))) !!}
            @else
              {!! Lang::txt('COM_MEMBERS_NOTE_N_SUBJECT', (int) $row->get('id'), Lang::txt('COM_MEMBERS_EMPTY_SUBJECT')) !!}
            @endif
          </h4>
          <div class="text-xs text-muted-foreground mt-1">
            {{ Date::of($row->get('created_time'))->toLocal('D d M Y H:i') }},
            <em>{{ $row->category->get('title') }}</em>
          </div>
          @if($row->get('body'))
            <div class="mt-2 text-sm prose prose-sm max-w-none">
              {!! $row->get('body') !!}
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endif
</div>
