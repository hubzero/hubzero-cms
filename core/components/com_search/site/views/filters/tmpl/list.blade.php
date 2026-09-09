{{--
 * Search filter checkbox list
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<fieldset class="space-y-2">
    <legend class="text-sm font-semibold mb-2">{{ $filter->label }}</legend>
    @foreach($filter->options as $option)
        @php
            $checked = in_array($option->value, $selectedOptions) ? 'checked' : '';
            $countIndex = $filter->field . '_' . $option->id;
            $count = isset($facetCounts[$countIndex]) ? $facetCounts[$countIndex] : '';
        @endphp
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   name="filters[{{ $filter->field }}][{{ $option->id }}]"
                   value="{{ $option->value }}"
                   {{ $checked }} />
            <span class="text-sm">
                {{ $option->value }}
                @if($count)
                    <span class="text-base-content/50">({{ $count }})</span>
                @endif
            </span>
        </label>
    @endforeach
</fieldset>
