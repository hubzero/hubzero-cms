{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$mainUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-ghost btn-sm" href="{{ $mainUrl }}">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_MAIN') }}
        </a>
    @endslot

    <h2 class="text-xl font-bold mb-1">
        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_HAVE_AN_OPINION') }}
    </h2>
    <p class="text-base-content/60 mb-6">
        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_CAST_A_VOTE') }}
    </p>

    @if (\Hubzero\Facades\Module::isEnabled('mod_poll'))
        {!! \Hubzero\Facades\Module::render(
            \Hubzero\Facades\Module::byName('mod_poll')
        ) !!}
    @else
        <div class="alert alert-warning" role="alert">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_NO_ACTIVE_POLLS') }}
        </div>
    @endif
</x-page-container>
