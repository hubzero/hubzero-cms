{{--
 * Usage statistics — tabbed display of plugin-generated sections.
 *
 * Variables from controller (defaultTask):
 *   $title    — Page title (e.g. "Usage: Overview")
 *   $cats     — Array of category arrays from usage plugins [{name => label}]
 *   $sections — Array of HTML strings from usage plugins (parallel to $cats)
 *   $task     — Active category/task name
 *   $no_html  — If true, skip page shell (AJAX fragment mode)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Route;
@endphp

@if($no_html)
    {{-- AJAX fragment: render only the active section --}}
    @if($sections)
        @foreach($sections as $k => $section)
            @if($section !== '' && isset($cats[$k]) && key($cats[$k]) === $task)
                {!! $section !!}
            @endif
        @endforeach
    @endif
@else
    @php
        $tabOptions = [];
        $activeTab = '';
        if ($cats) {
            foreach ($cats as $cat) {
                $name = key($cat);
                if ($cat[$name] !== '') {
                    $tabUrl = Route::url(
                        'index.php?option=' . $option . '&task=' . $name,
                        false
                    );
                    $tabOptions[$tabUrl] = $cat[$name];
                    if (strtolower($name) === $task) {
                        $activeTab = $tabUrl;
                    }
                }
            }
        }
    @endphp

    <x-page-container :title="$title">
        @if(count($tabOptions) > 1)
            @slot('tabs')
                <x-filter-tabs :options="$tabOptions" :active="$activeTab" />
            @endslot
        @endif

        @if($sections)
            @foreach($sections as $k => $section)
                @if($section !== '' && isset($cats[$k]))
                    <div id="usage-{{ e(key($cats[$k])) }}"
                         class="{{ key($cats[$k]) !== $task ? 'hidden' : '' }}">
                        {!! $section !!}
                    </div>
                @endif
            @endforeach
        @endif
    </x-page-container>
@endif
