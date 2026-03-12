{{--
  Publications authors list partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

@if($authNames && count($authNames) > 0)
  <ol id="author-list" class="divide-y divide-base-300">
    @foreach($authNames as $authname)
      @php
        $org    = $authname->organization ? e($authname->organization) : '';
        $credit = $authname->credit ? e($authname->credit) : '';
        $userid = $authname->user_id ? $authname->user_id : 'unregistered';
        $editUrl = Route::url(
            'index.php?option=' . $option
            . '&controller=items&task=editauthor&author=' . $authname->id, false
        );
        $deleteUrl = Route::url(
            'index.php?option=' . $option
            . '&controller=items&task=deleteauthor&aid=' . $authname->id, false
        );
      @endphp
      <li id="author_{{ $authname->id }}" class="pick reorder flex items-center gap-2 py-2">
        <span class="cursor-move text-muted-foreground">&#8597;</span>
        <span class="font-medium">{{ $authname->name }}</span>
        <span class="text-muted-foreground text-sm">({{ $userid }})</span>
        @if($org)
          <span class="text-muted-foreground text-sm">— {{ $org }}</span>
        @endif
        <span class="ml-auto flex gap-2">
          <a href="{!! $editUrl !!}" class="btn btn-xs btn-ghost">{{ Lang::txt('COM_PUBLICATIONS_EDIT') }}</a>
          <a href="{!! $deleteUrl !!}" class="btn btn-xs btn-ghost text-error">{{ Lang::txt('COM_PUBLICATIONS_DELETE') }}</a>
        </span>
        @if($credit)
          <div class="w-full text-xs text-muted-foreground mt-0.5">
            {{ Lang::txt('COM_PUBLICATIONS_CREDIT') }}: {{ $credit }}
          </div>
        @endif
      </li>
    @endforeach
  </ol>
  @if(count($authNames) > 1)
    <input type="hidden" value="" name="list" id="neworder" />
    <p class="text-xs text-muted-foreground mt-2">{{ Lang::txt('COM_PUBLICATIONS_AUTHORS_REORDER_TIP') }}</p>
    <button type="button"
            data-submit-task="saveorder"
            class="btn btn-sm btn-ghost mt-1"
            id="saveorder">
      {{ Lang::txt('Save Order') }}
    </button>
  @endif
@else
  <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_AUTHORS') }}</p>
@endif
