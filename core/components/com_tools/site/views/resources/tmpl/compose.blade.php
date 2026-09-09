{{--
 * Resource editing wizard — description/content step.
 *
 * Variables:
 *   $step       — Current step number
 *   $option     — Component option (com_tools)
 *   $controller — Controller name
 *   $version    — 'dev' or 'current'
 *   $row        — Resource row object
 *   $status     — Status array (with fulltxt, title, description)
 *   $dev        — Whether editing dev version (1 or 0)
 *   $tags       — Tags string
 *   $tagfa      — Selected focus area tag
 *   $fats       — Focus area tags array
 *   $authors    — Authors array
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

$status['fulltxt'] = stripslashes($status['fulltxt']);

$type = \Components\Resources\Models\Type::one(7);

$data = [];
preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $status['fulltxt'], $matches, PREG_SET_ORDER);
if (count($matches) > 0) {
    foreach ($matches as $match) {
        $data[$match[1]] = trim($match[2]);
    }
}

$status['fulltxt'] = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $status['fulltxt']);
$status['fulltxt'] = trim($status['fulltxt']);

$elements = new \Components\Resources\Models\Elements($data, $type->customFields);
$fields = $elements->render();

$sideMsg = $dev
    ? Lang::txt('COM_TOOLS_SIDE_EDIT_PAGE')
    : Lang::txt('COM_TOOLS_SIDE_EDIT_PAGE_CURRENT');
$titleVal = $__view->escape(stripslashes($status['title']));
$descVal  = $__view->escape(stripslashes($status['description']));
$txtVal   = $__view->escape(stripslashes($status['fulltxt']));
$filerUrl = Request::base(true) . '/index.php?option=' . $option
    . '&amp;controller=media&amp;tmpl=component&amp;resource=' . $row->id;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6 mb-6">
    {{-- About fieldset --}}
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_COMPOSE_ABOUT') }}
        </legend>

        {{-- Title --}}
        <div class="form-control w-full mb-4">
            <label class="label" for="field-title">
                <span class="label-text">
                    {{ Lang::txt('COM_TOOLS_COMPOSE_TITLE') }}:
                    <span class="text-error">{{ Lang::txt('COM_TOOLS_REQUIRED') }}</span>
                </span>
            </label>
            @if ($dev)
                <input type="text" name="title" id="field-title"
                    class="input input-bordered w-full"
                    maxlength="127" value="{{ $titleVal }}" />
            @else
                <input type="text" name="rtitle" id="field-title"
                    class="input input-bordered w-full"
                    maxlength="127" value="{{ $titleVal }}"
                    disabled />
                <input type="hidden" name="title" maxlength="127"
                    value="{{ $titleVal }}" />
                <div class="alert alert-warning mt-2 text-sm">
                    {{ Lang::txt('COM_TOOLS_TITLE_CANT_CHANGE') }}
                </div>
            @endif
        </div>

        {{-- Description --}}
        <div class="form-control w-full mb-4">
            <label class="label" for="field-description">
                <span class="label-text">
                    {{ Lang::txt('COM_TOOLS_COMPOSE_AT_A_GLANCE') }}:
                    <span class="text-error">{{ Lang::txt('COM_TOOLS_REQUIRED') }}</span>
                </span>
            </label>
            <input type="text" name="description" id="field-description"
                class="input input-bordered w-full"
                maxlength="256" value="{{ $descVal }}" />
        </div>

        {{-- Abstract (WYSIWYG) --}}
        <div class="form-control w-full mb-4">
            <label class="label" for="field-fulltxt">
                <span class="label-text">
                    {{ Lang::txt('COM_TOOLS_COMPOSE_ABSTRACT') }}:
                    <span class="text-error">{{ Lang::txt('COM_TOOLS_REQUIRED') }}</span>
                </span>
            </label>
            {!! $__view->editor('fulltxt', $txtVal, 50, 20, 'field-fulltxt') !!}
        </div>

        {{-- File manager --}}
        <fieldset class="fieldset mt-4">
            <legend class="fieldset-legend text-sm font-medium">
                {{ Lang::txt('COM_TOOLS_MANAGE_FILES') }}
            </legend>
            <div class="w-full">
                <iframe width="100%" height="160" name="filer" id="filer"
                    class="border border-base-300 rounded-lg"
                    src="{{ $filerUrl }}"></iframe>
            </div>
        </fieldset>
    </fieldset>

    {{-- Sidebar help --}}
    <aside class="space-y-3">
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <p class="font-medium">{!! $sideMsg !!}</p>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_TOOLS_COMPOSE_ABSTRACT_HINT') }}
                </p>
            </div>
        </div>
    </aside>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6">
    {{-- Details / custom fields --}}
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_COMPOSE_DETAILS') }}
        </legend>
        {!! $fields !!}
    </fieldset>

    {{-- Sidebar help --}}
    <aside>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <p>{{ Lang::txt('COM_TOOLS_COMPOSE_CUSTOM_FIELDS_EXPLANATION') }}</p>
            </div>
        </div>
    </aside>
</div>
