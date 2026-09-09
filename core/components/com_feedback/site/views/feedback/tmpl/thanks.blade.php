{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$mainUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option);
$fullnameEsc = e($row->get('fullname'));
$orgEsc = e($row->get('org'));
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-ghost btn-sm" href="{{ $mainUrl }}">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_MAIN') }}
        </a>
    @endslot

    <div class="alert alert-success mb-6" role="status">
        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_THANKS') }}
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <div class="flex gap-4">
                {{-- Avatar --}}
                @if ($row->get('user_id'))
                    <div class="shrink-0">
                        <img src="{{ $row->user->picture() }}"
                            alt=""
                            class="w-12 h-12 rounded-full" />
                    </div>
                @endif

                {{-- Content --}}
                <div class="min-w-0 flex-1">
                    <div class="font-semibold">{{ $fullnameEsc }}</div>
                    <div class="text-sm text-base-content/60 mb-3">
                        {{ $orgEsc }}
                    </div>

                    <blockquote class="border-l-4 border-primary pl-4 italic text-base-content/80">
                        {{ e(stripslashes($row->get('quote'))) }}
                    </blockquote>

                    {{-- Uploaded pictures --}}
                    @if (count($addedPictures))
                        <div class="flex gap-2 mt-4 flex-wrap">
                            @foreach ($addedPictures as $img)
                                <img src="{{ $path . '/' . $img }}"
                                    alt=""
                                    class="rounded border border-base-300 max-h-32" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-page-container>
