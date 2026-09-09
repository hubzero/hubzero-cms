{{--
 * Setup wizard step navigation partial
 *
 * Delegates to the shared <x-step-nav> component.
 *
 * Variables:
 *   $model  - Project model object
 *   $step   - Current step number (0=describe, 1=team, 2=finalize)
 *   $option - Component option string (optional, defaults to com_projects)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $option = $option ?? 'com_projects';

    $describeUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=setup&section=describe'
    );
    $teamUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=setup&section=team'
    );

    $steps = [
        ['label' => Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'), 'url' => $describeUrl],
        ['label' => Lang::txt('COM_PROJECTS_ADD_TEAM'), 'url' => $teamUrl],
    ];

    if ($step == 2) {
        $steps[] = Lang::txt('COM_PROJECTS_SETUP_ONE_LAST_THING');
    }

    $steps[] = Lang::txt('COM_PROJECTS_READY_TO_GO');
@endphp

<x-step-nav :steps="$steps" :current="$step" />
