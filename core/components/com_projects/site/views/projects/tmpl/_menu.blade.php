{{--
 * Project standard layout sidebar navigation tabs
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *   $active - Active tab name
 *   $tabs   - Array of tab definitions
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Event;

    $counts = $model->get('counts');
@endphp

<ul class="menu bg-base-200 rounded-box w-full projecttools">
    @foreach ($tabs as $tab)
        @php
            if (!isset($tab['icon'])) {
                $tab['icon'] = 'f009';
            }
            $tabUrl = Route::url(
                'index.php?option=' . $option
                . '&alias=' . $model->get('alias')
                . '&active=' . $tab['name']
            );
            $tabTitle = Lang::txt('COM_PROJECTS_VIEW')
                . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT'))
                . ' ' . strtolower($tab['title']);
        @endphp

        <li>
            <a
                class="tab-{{ $tab['name'] }} {{ $tab['name'] == $active ? 'active' : '' }}"
                data-icon="&#x{{ $tab['icon'] }}"
                href="{{ $tabUrl }}"
                title="{{ $tabTitle }}"
            >
                <span>{{ $tab['title'] }}</span>
                @if (isset($counts[$tab['name']]) && $counts[$tab['name']] != 0)
                    <span class="badge badge-sm" id="c-{{ $tab['name'] }}">
                        <span id="c-{{ $tab['name'] }}-num">{{ $counts[$tab['name']] }}</span>
                    </span>
                @endif
            </a>
            @if (isset($tab['children']) && count($tab['children']) > 0)
                <ul>
                    @foreach ($tab['children'] as $item)
                        <li @if ($item['class']) class="{{ $item['class'] }}" @endif>
                            <a
                                class="tab-{{ $item['name'] }}"
                                data-icon="&#x{{ $item['icon'] }}"
                                href="{{ Route::url($item['url']) }}"
                            >
                                <span>{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
@php
    $integrations = Event::trigger('projects.onProjectIntegrationList', [$model]);
    $integrations = array_filter($integrations);
@endphp
@if (!empty($integrations))
    <ul class="menu bg-base-200 rounded-box w-full mt-2">
        @foreach ($integrations as $integration)
            <li>{!! $integration !!}</li>
        @endforeach
    </ul>
@endif
