{{--
 * Browse all polls
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Document;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Html;

    Document::setTitle(Lang::txt('COM_POLL'));

    $latestUrl = Route::url('index.php?option=com_poll&view=latest', false);
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

    @if($polls->count())
        <x-card-grid cols="3">
            @foreach($polls as $poll)
                @php
                    $pollOptions = $poll->options()->where('text', '!=', '')->ordered()->rows();
                    $voteAction = Route::url('index.php?option=com_poll&task=vote', false);
                    $resultsUrl = Route::url('index.php?option=com_poll&view=poll&id=' . $poll->get('id'), false);
                    $votesTotal = $poll->dates()->total();
                    $isOpen = (bool) $poll->get('open');
                @endphp

                <div class="poll-card">
                    @if($isOpen)
                        {{-- Open poll — show voting form --}}
                        <form method="post" action="{{ $voteAction }}">
                            <fieldset class="poll-card-body">
                                <legend class="poll-card-title">{{ $poll->get('title') }}</legend>

                                <ul class="poll-options">
                                    @foreach($pollOptions as $option)
                                        <li>
                                            <label class="poll-option-label">
                                                <input type="radio" name="voteid"
                                                       value="{{ $option->id }}"
                                                       class="radio radio-primary radio-sm" />
                                                <span>{{ str_replace('&#039;', "'", $option->text) }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="poll-card-actions">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ Lang::txt('COM_POLL_VOTE') }}
                                    </button>
                                    <a class="btn btn-ghost btn-sm" href="{{ $resultsUrl }}">
                                        {{ Lang::txt('COM_POLL_RESULTS') }}
                                    </a>
                                </div>

                                <input type="hidden" name="option" value="com_poll" />
                                <input type="hidden" name="task" value="vote" />
                                <input type="hidden" name="id" value="{{ $poll->id }}" />
                                {!! Html::input('token') !!}
                            </fieldset>
                        </form>
                    @else
                        {{-- Closed poll — show results summary --}}
                        <div class="poll-card-body">
                            <h3 class="poll-card-title">{{ $poll->get('title') }}</h3>

                            <ul class="poll-results-compact">
                                @foreach($pollOptions as $option)
                                    @php
                                        $percent = $poll->voters
                                            ? round(100 * $option->hits / $poll->voters, 1)
                                            : 0;
                                    @endphp
                                    <li>
                                        <div class="poll-result-row">
                                            <span class="poll-result-text">{{ str_replace('&#039;', "'", $option->text) }}</span>
                                            <span class="poll-result-pct">{{ $percent }}%</span>
                                        </div>
                                        <div class="poll-bar-track">
                                            <div class="poll-bar-fill" data-style-width="{{ $percent }}%"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="poll-card-footer">
                        <span class="poll-vote-count">
                            {{ Lang::txt('COM_POLL_VOTES', $votesTotal) }}
                        </span>
                        <span class="poll-status {{ $isOpen ? 'poll-status-open' : 'poll-status-closed' }}">
                            {{ $isOpen ? Lang::txt('open') : Lang::txt('closed') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </x-card-grid>
    @else
        <x-empty-state
            :title="Lang::txt('COM_POLL_NO_RESULTS')"
            :message="Lang::txt('COM_POLL_NO_POLLS_AVAILABLE')"
        />
    @endif
</x-page-container>
