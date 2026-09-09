{{--
 * Project extended layout horizontal tab navigation
 *
 * Variables:
 *   $model      - Project model object
 *   $option     - Component option string
 *   $active     - Active tab name
 *   $tabs       - Array of tab definitions
 *   $guest      - Whether user is a guest
 *   $publicView - Whether this is public/external view
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $assetTabs = [];

    if ($publicView || !isset($tabs)) {
        $tabs = [];
    }
    if ($active == 'edit') {
        $tabs[] = ['name' => 'edit', 'title' => 'Edit', 'submenu' => '', 'show' => true];
    }

    foreach ($tabs as $tab) {
        if (isset($tab['submenu']) && $tab['submenu'] == 'Assets') {
            $assetTabs[] = $tab;
        }
    }

    if (count($assetTabs) > 1) {
        array_splice($tabs, 3, 0, [['name' => 'assets', 'title' => 'Assets', 'show' => true]]);
    }

    $counts = $model->get('counts');
@endphp

<div class="menu-wrapper border-b border-base-300">
    @if ($publicView == false && !empty($tabs))
        <ul class="tabs tabs-border flex-nowrap overflow-x-auto">
            @foreach ($tabs as $tab)
                @php
                    if (isset($tab['submenu']) && $tab['submenu'] == 'Assets' && count($assetTabs) > 1) {
                        continue;
                    }
                    if (isset($tab['alias']) && trim($tab['alias'])) {
                        $tab['name'] = trim($tab['alias']);
                    }
                    $gopanel = $tab['name'] == 'assets' ? 'files' : $tab['name'];
                    $isActive = ($tab['name'] == $active)
                        || ($tab['name'] == 'assets'
                        && isset($tab['submenu'])
                        && $tab['submenu'] == 'Assets');
                    $tabUrl = Route::url(
                        'index.php?option=' . $option
                        . '&alias=' . $model->get('alias')
                        . '&active=' . $gopanel
                    );
                    $tabTitleAttr = ucfirst(Lang::txt('COM_PROJECTS_PROJECT'))
                        . ' ' . ucfirst($tab['title']);
                @endphp

                <li id="tab-{{ $tab['name'] }}" class="relative">
                    <a
                        class="tab tab-{{ $tab['name'] }} {{ $isActive ? 'tab-active' : '' }}"
                        href="{{ $tabUrl }}/"
                        title="{{ $tabTitleAttr }}"
                    >
                        <span>{{ $tab['title'] }}</span>
                        @if ($tab['name'] != 'feed' && isset($counts[$tab['name']]) && $counts[$tab['name']] != 0)
                            <span class="badge badge-sm ml-1" id="c-{{ $tab['name'] }}">
                                <span id="c-{{ $tab['name'] }}-num">{{ $counts[$tab['name']] }}</span>
                            </span>
                        @elseif ($tab['name'] == 'feed')
                            @php
                                $hiddenCls = empty($counts['new']) ? 'hidden' : '';
                                $newCount = empty($counts['new']) ? 0 : $counts['new'];
                            @endphp
                            <span id="c-new" class="badge badge-primary badge-sm ml-1 {{ $hiddenCls }}">
                                <span id="c-new-num">{{ $newCount }}</span>
                            </span>
                        @endif
                    </a>
                    @if ($tab['name'] == 'assets')
                        <div id="asset-selection" class="dropdown-content z-10 menu bg-base-100 shadow-lg rounded-box w-48 p-2 submenu-wrap">
                            @foreach ($assetTabs as $aTab)
                                @php
                                    $aTabUrl = Route::url(
                                        'index.php?option=' . $option
                                        . '&alias=' . $model->get('alias')
                                        . '&active=' . $aTab['name']
                                    );
                                    $aTabTitle = ucfirst(Lang::txt('COM_PROJECTS_PROJECT'))
                                        . ' ' . ucfirst($aTab['title']);
                                @endphp
                                <li>
                                    <a
                                        class="{{ $aTab['name'] }}"
                                        href="{{ $aTabUrl }}/"
                                        title="{{ $aTabTitle }}"
                                        id="tab-{{ $aTab['name'] }}"
                                    >
                                        <span>{{ $aTab['title'] }}</span>
                                        @if (isset($counts[$aTab['name']]) && $counts[$aTab['name']] != 0)
                                            <span class="badge badge-sm" id="c-{{ $aTab['name'] }}">
                                                <span id="c-{{ $aTab['name'] }}-num">
                                                    {{ $counts[$aTab['name']] }}
                                                </span>
                                            </span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        @if (isset($guest) && $guest)
            @php
                $loginUrl = Route::url(
                    'index.php?option=' . $option
                    . '&alias=' . $model->get('alias')
                    . '&task=view'
                ) . '?action=login';
            @endphp
            <p class="py-2 text-sm">
                {{ Lang::txt('COM_PROJECTS_ARE_YOU_MEMBER') }}
                <a href="{{ $loginUrl }}" class="link link-primary">{{ ucfirst(Lang::txt('COM_PROJECTS_LOGIN')) }}</a>
                {{ Lang::txt('COM_PROJECTS_LOGIN_TO_PRIVATE_AREA') }}
            </p>
        @endif
    @endif
</div>
