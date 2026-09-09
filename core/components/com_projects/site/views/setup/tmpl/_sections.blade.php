{{--
 * Edit section tab menu partial
 *
 * Variables:
 *   $model    - Project model object
 *   $option   - Component option string
 *   $sections - Array of section names
 *   $section  - Currently active section name
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
@endphp

<ul id="panelist" class="menu bg-base-200 rounded-box w-full">
    @foreach ($sections as $sec)
        @if ($sec != 'info')
            <li>
                <a
                    href="{{ Route::url('index.php?option=' . $option . '&task=edit&alias=' . $model->get('alias') . '&active=' . strtolower($sec)) }}"
                    @class(['active' => $sec == $section])
                >
                    {{ Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($sec)) }}
                </a>
            </li>
        @else
            <li>
                <a
                    href="{{ Route::url('index.php?option=' . $option . '&task=edit&alias=' . $model->get('alias') . '&active=' . strtolower($sec)) }}"
                    @class(['active' => $section == 'info' || $section == 'info_custom'])
                >
                    {{ Lang::txt('COM_PROJECTS_EDIT_PROJECT_PANE_' . strtoupper($sec)) }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
