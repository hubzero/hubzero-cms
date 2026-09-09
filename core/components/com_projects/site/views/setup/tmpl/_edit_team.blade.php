{{--
 * Team editing partial
 *
 * Variables:
 *   $model   - Project model object
 *   $content - Team plugin content HTML
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
@endphp

<h4 class="text-lg font-semibold">{{ ucwords(Lang::txt('COM_PROJECTS_EDIT_TEAM')) }}</h4>
<div id="cbody">
    {!! $content !!}
</div>
<h5 class="text-base font-medium mt-4">
    {{ Lang::txt('COM_PROJECTS_PROJECT') }} {{ Lang::txt('COM_PROJECTS_OWNER') }}
    @if ($model->access('manager'))
        <span class="text-sm ml-2">
            <a
                href="{{ Route::url($model->link('team') . '&action=changeowner') }}"
                class="showinbox"
            >{{ ucfirst(Lang::txt('COM_PROJECTS_EDIT')) }}</a>
        </span>
    @endif
</h5>
@php
    if ($model->groupOwner() && ($cn = $model->groupOwner('cn'))) {
        $ownedby = ucfirst(Lang::txt('COM_PROJECTS_GROUP'))
            . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">'
            . ' ' . $model->groupOwner('description')
            . ' (' . $cn . ')</a>';
    } else {
        $ownedby = '<a href="' . Route::url('index.php?option=com_members&id=' . $model->owner('id')) . '">'
            . $model->owner('name') . '</a>';
    }
@endphp
<span class="text-sm ml-2">{!! $ownedby !!}</span>
