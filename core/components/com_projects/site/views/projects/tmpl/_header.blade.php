{{--
 * Project standard header partial (privacy badge, thumbnail, title)
 *
 * Variables:
 *   $model         - Project model object
 *   $option        - Component option string
 *   $showPic       - Whether to show project thumbnail (0/1)
 *   $showPrivacy   - Privacy display mode (0=none, 2=badge)
 *   $showOptions   - Whether to show member options dropdown (optional)
 *   $goBack        - Unused (kept for interface compat)
 *   $showUnderline - Unused (kept for interface compat)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $privacyTxt = !$model->isPublic()
        ? Lang::txt('COM_PROJECTS_PRIVATE')
        : Lang::txt('COM_PROJECTS_PUBLIC');

    if (!$model->isPublic()) {
        $privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
    } else {
        $previewUrl = Route::url(
            'index.php?option=' . $option . '&alias=' . $model->get('alias') . '&preview=1'
        );
        $privacy = '<a href="' . $previewUrl . '" title="'
            . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">'
            . ucfirst($privacyTxt) . '</a>';
    }

    $showPrivacy = $showPrivacy ?? 0;
    $start = ($showPrivacy == 2 && $model->access('member'))
        ? '<span class="h-privacy">' . $privacy . '</span> '
            . strtolower(Lang::txt('COM_PROJECTS_PROJECT'))
        : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));

    $thumbClass = '';
    if (!$model->get('picture')) {
        $thumbClass = ' no-picture';
    }

    $projectUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
    );
    $thumbUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
        . '&controller=media&media=thumb'
    );
    $truncTitle = \Hubzero\Utility\Str::truncate(e($model->get('title')), 50);
@endphp

<header class="flex items-center gap-4 py-4 content-header @if (!$showPic) nopic @endif">
    @if ($showPic)
        <div class="shrink-0 pthumb{{ $thumbClass }}">
            <a
                href="{{ $projectUrl }}"
                title="{{ Lang::txt('COM_PROJECTS_VIEW_UPDATES') }}"
            ><img
                src="{{ $thumbUrl }}"
                alt="{{ e($model->get('title')) }}"
                class="w-16 h-16 rounded-lg object-cover"
            /></a>
        </div>
    @endif
    <div class="flex-1 ptitle">
        <h2 class="text-xl font-semibold">
            <a href="{{ Route::url($model->link()) }}" class="link link-hover">{{ $truncTitle }}</a>
        </h2>
        @if ($model->groupOwner())
            <p class="text-sm text-base-content/60 groupowner">
                {{ ucfirst(Lang::txt('COM_PROJECTS_PROJECT')) }}
                {{ Lang::txt('COM_PROJECTS_BY') }}
                @if ($cn = $model->groupOwner('cn'))
                    {{ Lang::txt('COM_PROJECTS_GROUP') }}
                    <a href="{{ Route::url('index.php?option=com_groups&cn=' . $cn) }}" class="link link-primary">{{ $cn }}</a>
                @else
                    {{ Lang::txt('COM_PROJECTS_UNKNOWN') }} {{ Lang::txt('COM_PROJECTS_GROUP') }}
                @endif
            </p>
        @endif
    </div>
    @if (!empty($showOptions))
        <div class="shrink-0">
            @include('projects::_options', [
                'model'  => $model,
                'option' => $option,
            ])
        </div>
    @endif
</header>
