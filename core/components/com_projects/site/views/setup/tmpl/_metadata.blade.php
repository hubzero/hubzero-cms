{{--
 * Project metadata display partial (image, title, alias, date)
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *   $step   - Current step number
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
@endphp

@if ($model->exists())
    <div class="info_blurb grid grid-cols-12 gap-4">
        <div class="col-span-1">
            @php
                $mediaUrl = Route::url(
                    'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=media'
                );
            @endphp
            <img src="{{ $mediaUrl }}" alt="" />
        </div>
        <div class="col-span-6">
            <span class="font-semibold">{{ Lang::txt('COM_PROJECTS_PROJECT') }}</span>:
            {{ e($model->get('title')) }}
            (<span>{{ $model->get('alias') }}</span>)
            <span class="block text-base-content/60">
                {{ Lang::txt('COM_PROJECTS_CREATED') }} {{ $model->created('date') }}
            </span>
        </div>
        <div class="col-span-5">
        </div>
    </div>
@endif
