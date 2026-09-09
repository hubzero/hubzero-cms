{{--
 * Latest poll — voting form
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Html;

    $browseUrl  = Route::url('index.php?option=com_poll', false);
    $voteAction = Route::url('index.php?option=com_poll&task=vote', false);
    $resultsUrl = Route::url(
        'index.php?option=com_poll&view=poll&id='
        . $poll->id . ':' . $poll->alias, false
    );
@endphp

<x-page-container :title="Lang::txt('COM_POLL') . ': ' . Lang::txt('COM_POLL_LATEST')">
    @slot('actions')
        <a class="btn" href="{{ $browseUrl }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
            </svg>
            {{ Lang::txt('COM_POLL_BROWSE') }}
        </a>
    @endslot

    @if($options->count() > 0)
        <div class="poll-featured">
            <h2 class="poll-featured-title">{{ $poll->title }}</h2>

            <form id="pollform" method="post" action="{{ $voteAction }}">
                <ul class="poll-options poll-options-lg">
                    @foreach($options as $option)
                        <li>
                            <label class="poll-option-label">
                                <input type="radio" name="voteid"
                                       value="{{ $option->id }}"
                                       class="radio radio-primary" />
                                <span>{{ $option->text }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>

                <div class="poll-featured-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ Lang::txt('COM_POLL_VOTE') }}
                    </button>
                    <a class="btn btn-ghost" href="{{ $resultsUrl }}">
                        {{ Lang::txt('COM_POLL_RESULTS') }}
                    </a>
                </div>

                <input type="hidden" name="option" value="com_poll" />
                <input type="hidden" name="task" value="vote" />
                <input type="hidden" name="id" value="{{ $poll->id }}" />
                {!! Html::input('token') !!}
            </form>
        </div>
    @else
        <x-empty-state
            :title="Lang::txt('COM_POLL_NO_RESULTS')"
            :message="Lang::txt('COM_POLL_NO_ACTIVE_POLLS')"
        />
    @endif
</x-page-container>
