{{--
 * Resource editing wizard — tags step.
 *
 * Variables:
 *   $version — 'dev' or 'current'
 *   $status  — Status array (with 'published' key)
 *   $fats    — Focus area tags array (key => value)
 *   $tagfa   — Currently selected focus area tag value
 *   $tags    — Tags string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;

$tf = Event::trigger(
    'hubzero.onGetMultiEntry',
    [['tags', 'tags', 'actags', '', $tags]]
);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6">
    {{-- Tags fieldset --}}
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_TAGS_ADD') }}
        </legend>

        @if (!empty($fats))
            <fieldset class="fieldset mb-4">
                <legend class="fieldset-legend text-sm font-medium">
                    {{ Lang::txt('COM_TOOLS_TAGS_SELECT_FOCUS_AREA') }}:
                </legend>
                <div class="flex flex-wrap gap-4">
                    @foreach ($fats as $key => $value)
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="tagfa"
                                class="radio radio-primary radio-sm"
                                value="{{ $value }}"
                                @checked($tagfa == $value) />
                            <span class="label-text">{{ $key }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endif

        <div class="form-control w-full mb-4">
            <label class="label" for="actags">
                <span class="label-text">
                    {{ Lang::txt('COM_TOOLS_TAGS_ASSIGNED') }}:
                </span>
            </label>
            @if (count($tf) > 0)
                {!! $tf[0] !!}
            @else
                <textarea name="tags" id="tags-men"
                    class="textarea textarea-bordered w-full"
                    rows="6" cols="35">{{ $tags }}</textarea>
            @endif
        </div>

        <p class="text-sm text-base-content/70">
            {{ Lang::txt('COM_TOOLS_TAGS_NEW_EXPLANATION') }}
        </p>
    </fieldset>

    {{-- Sidebar help --}}
    <aside>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <h4 class="font-semibold">
                    {{ Lang::txt('COM_TOOLS_TAGS_WHAT_ARE_TAGS') }}
                </h4>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_TOOLS_TAGS_EXPLANATION') }}
                </p>
            </div>
        </div>
    </aside>
</div>
