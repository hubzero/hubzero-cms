{{--
  Latest Groups module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if (!empty($groups))
  <ul class="list bg-base-100 rounded-box">
    @foreach ($groups as $g)
      <li class="list-row p-3">
        <div class="min-w-0 grow">
          <h4 class="font-semibold">
            <a href="{{ Route::url('index.php?option=com_groups&cn=' . $g->cn) }}" class="link link-hover">
              {{ stripslashes($g->description) }}
            </a>
          </h4>
          @if ($g->public_desc)
            <p class="text-sm text-base-content/60 mt-0.5">
              {{ stripslashes($g->public_desc) }}
            </p>
          @endif
        </div>
      </li>
    @endforeach
  </ul>
@else
  <p class="text-base-content/60">{{ Lang::txt('MOD_LATESTGROUPS_NO_RESULTS') }}</p>
@endif

<p class="mt-2">
  <a href="{{ Route::url('index.php?option=com_groups') }}" class="btn btn-sm btn-outline">
    {{ Lang::txt('MOD_LATESTGROUPS_ALL') }}
  </a>
</p>
