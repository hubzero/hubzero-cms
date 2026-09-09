{{--
 * Project extended layout top header partial
 *
 * Variables:
 *   $model      - Project model object
 *   $option     - Component option string
 *   $publicView - Whether this is public/external view (bool)
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
    $truncTitle = \Hubzero\Utility\Str::truncate(e($model->get('title')), 50);
@endphp

<div id="project-header" class="project-header py-4">
    <div class="flex items-center gap-4">
        <div class="shrink-0 pimage-container">
            @include('projects::_image', [
                'model'  => $model,
                'option' => $option,
            ])
        </div>
        <div class="flex-1 ptitle-container">
            <h2 class="text-xl font-semibold">
                <a href="{{ $projectUrl }}" class="link link-hover">
                    {{ $truncTitle }}
                    <span class="text-base-content/60 text-sm">({{ $model->get('alias') }})</span>
                </a>
            </h2>

            @if ($model->groupOwner())
                <p class="text-sm text-base-content/60">
                    @php
                        if (!$model->isPublic()) {
                            $privacy = '<span class="badge badge-ghost badge-sm">'
                                . ucfirst(Lang::txt('COM_PROJECTS_PRIVATE'))
                                . '</span>';
                        } else {
                            $previewUrl = Route::url(
                                'index.php?option=' . $option
                                . '&alias=' . $model->get('alias') . '&preview=1'
                            );
                            $privacy = '<a href="' . $previewUrl . '" title="'
                                . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE')
                                . '" class="badge badge-outline badge-sm">'
                                . ucfirst(Lang::txt('COM_PROJECTS_PUBLIC'))
                                . '</a>';
                        }

                        $isMember = ($publicView == false && $model->access('member'));
                        $start = $isMember
                            ? '<span class="h-privacy">' . $privacy . '</span> '
                                . strtolower(Lang::txt('COM_PROJECTS_PROJECT'))
                            : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));
                    @endphp

                    {!! $start !!} {{ Lang::txt('COM_PROJECTS_BY') }}
                    @if ($cn = $model->groupOwner('cn'))
                        {{ Lang::txt('COM_PROJECTS_GROUP') }}
                        <a href="{{ Route::url('index.php?option=com_groups&cn=' . $cn) }}" class="link link-primary">{{ $cn }}</a>
                    @else
                        {{ Lang::txt('COM_PROJECTS_UNKNOWN') }} {{ Lang::txt('COM_PROJECTS_GROUP') }}
                    @endif
                </p>
            @endif
        </div>
        <div class="shrink-0">
            @if ($publicView == false)
                @include('projects::_options', [
                    'model'  => $model,
                    'option' => $option,
                ])
            @endif
        </div>
    </div>
</div>
