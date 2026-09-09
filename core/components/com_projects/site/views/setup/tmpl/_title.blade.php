{{--
 * Setup page title partial
 *
 * Variables:
 *   $model  - Project model object
 *   $step   - Current step number
 *   $option - Component option string
 *   $title  - Fallback page title
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $pageTitle = $model->get('title')
        ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . $model->get('title')
        : $title;
@endphp

<header id="content-header" class="mb-6">
    <h2 class="text-2xl font-bold">
        {{ $pageTitle }}
        @if ($model->groupOwner() && ($cn = $model->groupOwner('cn')))
            {{ Lang::txt('COM_PROJECTS_FOR') }}
            {{ ucfirst(Lang::txt('COM_PROJECTS_GROUP')) }}
            <a href="{{ Route::url('index.php?option=com_groups&cn=' . $cn) }}" class="link link-primary">
                {{ \Hubzero\Utility\Str::truncate($model->groupOwner('description'), 50) }}
            </a>
        @endif
    </h2>
</header>
