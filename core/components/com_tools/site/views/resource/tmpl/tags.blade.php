{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $showwarning = ($version == 'current' || !$status['published']) ? 0 : 1;
@endphp

<div class="explaination">
    <h4>{{ Lang::txt('COM_TOOLS_TAGS_WHAT_ARE_TAGS') }}</h4>
    <p>{{ Lang::txt('COM_TOOLS_TAGS_EXPLANATION') }}</p>
</div>
<fieldset>
    <legend>{{ Lang::txt('COM_TOOLS_TAGS_ADD') }}</legend>

    @if (!empty($fats))
        <fieldset>
            <legend>{{ Lang::txt('COM_TOOLS_TAGS_SELECT_FOCUS_AREA') }}:</legend>
            @foreach ($fats as $key => $value)
                <label>
                    <input
                        class="radio"
                        type="radio"
                        name="tagfa"
                        value="{{ $value }}"
                        @if ($tagfa == $value) checked @endif
                    />
                    {{ $key }}
                </label>
            @endforeach
        </fieldset>
    @endif

    <label>
        {{ Lang::txt('COM_TOOLS_TAGS_ASSIGNED') }}:
        @php
            $tf = Event::trigger(
                'hubzero.onGetMultiEntry',
                [['tags', 'tags', 'actags', '', $tags]]
            );
        @endphp
        @if (count($tf) > 0)
            {!! $tf[0] !!}
        @else
            <textarea name="tags" id="tags-men" rows="6" cols="35" class="textarea textarea-bordered w-full">{{ $tags }}</textarea>
        @endif
    </label>
    <p>{{ Lang::txt('COM_TOOLS_TAGS_NEW_EXPLANATION') }}</p>
</fieldset>
<div class="clear"></div>
