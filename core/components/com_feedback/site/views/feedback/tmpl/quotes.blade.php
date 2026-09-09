{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$addStoryUrl = \Hubzero\Facades\Route::url(
    'index.php?option=com_feedback&task=success_story'
);
@endphp

<x-page-container :title="\Hubzero\Facades\Lang::txt('COM_FEEDBACK')">
    @slot('actions')
        <a class="btn btn-primary btn-sm" href="{{ $addStoryUrl }}">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_ADD_YOUR_STORY') }}
        </a>
    @endslot

    @if ($quotes->count() > 0)
        <div class="space-y-6">
            @foreach ($quotes as $quote)
                @php
                $user = $quote->user;
                $fullnameEsc = e(stripslashes($quote->get('fullname')));
                $orgEsc = e(stripslashes($quote->get('org')));
                $shortQuote = $quote->get('short_quote') ?: $quote->get('quote');
                $fullQuote = stripslashes($quote->get('quote'));
                $hasLongQuote = ($shortQuote != $quote->get('quote'));
                $pictures = $quote->files();
                @endphp

                <div class="card bg-base-100 border border-base-300"
                    id="quote-{{ $quote->get('id') }}">
                    <div class="card-body">
                        <div class="flex gap-4">
                            {{-- Avatar --}}
                            <div class="shrink-0">
                                <img src="{{ $user->picture() }}"
                                    alt=""
                                    class="w-12 h-12 rounded-full" />
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold">{{ $fullnameEsc }}</div>
                                <div class="text-sm text-base-content/60 mb-2">
                                    {{ $orgEsc }}
                                </div>

                                @if ($quoteId && $quoteId == $quote->get('id'))
                                    <blockquote class="border-l-4 border-primary pl-4 italic text-base-content/80">
                                        <p>{{ e($fullQuote) }}</p>
                                    </blockquote>
                                @elseif ($hasLongQuote)
                                    <blockquote class="border-l-4 border-base-300 pl-4 italic text-base-content/80">
                                        <p>
                                            {{ e(rtrim(strip_tags($shortQuote), '.')) }}&#8230;
                                            <a href="{{ \Hubzero\Facades\Route::url('index.php?option=com_feedback&task=quotes&quoteid=' . $quote->get('id')) }}"
                                                class="link link-primary text-sm not-italic">
                                                {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_MORE') }}
                                            </a>
                                        </p>
                                    </blockquote>
                                @else
                                    <blockquote class="border-l-4 border-base-300 pl-4 italic text-base-content/80">
                                        {!! $fullQuote !!}
                                    </blockquote>
                                @endif

                                {{-- Pictures --}}
                                @if (count($pictures))
                                    <div class="flex gap-2 mt-3 flex-wrap">
                                        @foreach ($pictures as $picture)
                                            @php
                                            $img = substr($picture->getPathname(), strlen(PATH_ROOT));
                                            list($ow, $oh) = getimagesize($picture->getPathname());
                                            $num = max($ow / 120, $oh / 120);
                                            $mw = ($num > 1) ? round($ow / $num) : $ow;
                                            $mh = ($num > 1) ? round($oh / $num) : $oh;
                                            @endphp
                                            <a href="{{ $img }}" target="_blank">
                                                <img src="{{ $img }}"
                                                    width="{{ $mw }}"
                                                    height="{{ $mh }}"
                                                    alt=""
                                                    class="rounded border border-base-300" />
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state
            :title="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_NO_QUOTES_FOUND')"
        >
            <a class="btn btn-primary btn-sm" href="{{ $addStoryUrl }}">
                {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_ADD_YOUR_STORY') }}
            </a>
        </x-empty-state>
    @endif
</x-page-container>
