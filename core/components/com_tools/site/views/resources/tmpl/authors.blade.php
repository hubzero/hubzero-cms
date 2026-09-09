{{--
 * Resource editing wizard — authors step.
 *
 * Variables:
 *   $option  — Component option (com_tools)
 *   $row     — Resource row object
 *   $version — 'dev' or 'current'
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;

$authorsSrc = 'index.php?option=' . $option
    . '&amp;controller=authors&amp;rid=' . $row->id
    . '&amp;tmpl=component&amp;version=' . $version;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-[1fr_16rem] gap-6">
    {{-- Authors fieldset --}}
    <fieldset class="fieldset">
        <legend class="fieldset-legend text-base font-semibold">
            {{ Lang::txt('COM_TOOLS_AUTHORS_AUTHORS') }}
        </legend>
        <div class="w-full">
            <iframe name="authors" id="authors"
                src="{{ $authorsSrc }}"
                class="border border-base-300 rounded-lg w-full"
                width="100%" height="400" frameborder="0"></iframe>
        </div>
    </fieldset>

    {{-- Sidebar help --}}
    <aside>
        <div class="card bg-base-200 shadow-sm">
            <div class="card-body text-sm">
                <h4 class="font-semibold">
                    {{ Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN') }}
                </h4>
                <p class="text-base-content/70">
                    {{ Lang::txt('COM_TOOLS_AUTHORS_NO_LOGIN_EXPLANATION') }}
                </p>
            </div>
        </div>
    </aside>
</div>
