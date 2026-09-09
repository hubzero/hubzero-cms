{{--
 * Resource editing wizard — attachments and screenshots step.
 *
 * Variables:
 *   $option  — Component option (com_tools)
 *   $row     — Resource row object
 *   $status  — Status array (with 'published' key)
 *   $version — 'dev' or 'current'
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;

$allowupload = ($version == 'current' || !$status['published']) ? 1 : 0;

$attachesSrc = 'index.php?option=' . $option
    . '&amp;controller=attachments&amp;rid=' . $row->id
    . '&amp;tmpl=component&amp;type=7'
    . '&amp;allowupload=' . $allowupload;

$screensSrc = 'index.php?option=' . $option
    . '&amp;controller=screenshots&amp;rid=' . $row->id
    . '&amp;tmpl=component&amp;version=' . $version;
@endphp

{{-- Attachments --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6 mb-6">
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_ATTACH_ATTACHMENTS') }}
        </legend>
        <div class="w-full">
            <iframe name="attaches" id="attaches"
                src="{{ $attachesSrc }}"
                class="border border-base-300 rounded-lg w-full"
                width="100%" height="200" frameborder="0"></iframe>
        </div>
    </fieldset>

    <aside>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <h4 class="font-semibold">
                    {{ Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_ATTACHMENTS') }}
                </h4>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_TOOLS_ATTACH_EXPLANATION') }}
                </p>
            </div>
        </div>
    </aside>
</div>

{{-- Screenshots --}}
<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6">
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS') }}
        </legend>
        <div class="w-full">
            <iframe name="screens" id="screens"
                src="{{ $screensSrc }}"
                class="border border-base-300 rounded-lg w-full"
                width="100%" height="400" frameborder="0"></iframe>
        </div>
    </fieldset>

    <aside>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <h4 class="font-semibold">
                    {{ Lang::txt('COM_TOOLS_ATTACH_WHAT_ARE_SCREENSHOTS') }}
                </h4>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_TOOLS_ATTACH_SCREENSHOTS_EXPLANATION') }}
                </p>
            </div>
        </div>
    </aside>
</div>
