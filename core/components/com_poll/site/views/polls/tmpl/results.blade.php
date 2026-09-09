{{--
 * Poll results — bar graph with poll switcher
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Lang;

    $latestUrl = Route::url('index.php?option=com_poll&view=latest', false);
    $browseUrl = Route::url('index.php?option=com_poll', false);

    // Build poll switcher list
    $allPolls = Components\Poll\Models\Poll::all()
        ->whereEquals('state', 1)
        ->rows();

    $totalVotes = 0;
    foreach ($votes as $v) {
        $totalVotes += $v->hits;
    }
@endphp

<x-page-container :title="Lang::txt('COM_POLL')">
    @slot('actions')
        <a class="btn" href="{{ $latestUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
            </svg>
            {{ Lang::txt('COM_POLL_TAKE_LATEST_POLL') }}
        </a>
    @endslot

    @slot('sidebar')
        {{-- Poll switcher --}}
        <div class="poll-sidebar-card">
            <h3 class="poll-sidebar-heading">{{ Lang::txt('COM_POLL_SELECT') }}</h3>
            <ul class="poll-switcher">
                @foreach($allPolls as $p)
                    @php
                        $pollUrl = Route::url('index.php?option=com_poll&id=' . $p->get('id') . ':' . $p->get('alias'), false);
                        $isCurrent = $poll->get('id') == $p->get('id');
                    @endphp
                    <li>
                        <a href="{{ $pollUrl }}"
                           class="poll-switcher-link {{ $isCurrent ? 'poll-switcher-active' : '' }}">
                            {{ $p->get('title') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Vote stats --}}
        @if($poll->get('id'))
            <x-stat-card
                :label="Lang::txt('COM_POLL_NUMBER_OF_VOTERS')"
                :value="$totalVotes ?: '--'"
            />
            <x-stat-card
                :label="Lang::txt('COM_POLL_FIRST_VOTE')"
                :value="$first_vote ?: '--'"
            />
            <x-stat-card
                :label="Lang::txt('COM_POLL_LAST_VOTE')"
                :value="$last_vote ?: '--'"
            />
        @endif
    @endslot

    @if($poll->get('id'))
        <h2 class="poll-results-title">{{ $poll->get('title') }}</h2>

        <div class="poll-results-table" role="table" aria-label="{{ $poll->get('title') }}">
            <div class="poll-results-header" role="row">
                <span role="columnheader">{{ Lang::txt('COM_POLL_OPTION') }}</span>
                <span role="columnheader">{{ Lang::txt('COM_POLL_PERCENTAGE') }}</span>
                <span role="columnheader">{{ Lang::txt('COM_POLL_NUMBER_OF_VOTERS') }}</span>
            </div>

            @foreach($votes as $vote)
                <div class="poll-results-row" role="row">
                    <span class="poll-results-option" role="cell">
                        {{ $vote->text }}
                    </span>
                    <span class="poll-results-bar" role="cell">
                        <div class="poll-bar-track">
                            <div class="poll-bar-fill" data-style-width="{{ $vote->percent }}%"></div>
                        </div>
                        <span class="poll-bar-label">{{ $vote->percent }}%</span>
                    </span>
                    <span class="poll-results-hits" role="cell">
                        {{ $vote->hits }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state
            :title="Lang::txt('COM_POLL_SELECT_POLL')"
            :message="Lang::txt('COM_POLL_SELECT_POLL_MESSAGE')"
        />
    @endif
</x-page-container>
