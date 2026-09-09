{{--
 * Project thumbnail image partial
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $projectUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
    );
    $linkTitle = e($model->get('title')) . ' - ' . Lang::txt('COM_PROJECTS_VIEW_UPDATES');
    $thumbUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
        . '&controller=media&media=thumb'
    );
@endphp

<div id="pimage" class="pimage mb-4">
    <a href="{{ $projectUrl }}" title="{{ $linkTitle }}">
        <img
            src="{{ $model->picture('master') }}"
            alt="{{ e($model->get('title')) }}"
            class="w-full rounded-lg object-cover"
        />
    </a>
</div>
