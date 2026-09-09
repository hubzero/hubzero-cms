@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $supportUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
@endphp

<x-page-container :title="Lang::txt('COM_TOOLS_BADPARAMS')">
    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{!! $__view->getError() !!}</span>
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <p class="mb-4">{{ Lang::txt('COM_TOOLS_BADPARAMS_MESSAGE') }}</p>
        <pre class="bg-base-200 p-4 rounded-lg overflow-x-auto text-sm mb-4">{{ e($badparams) }}</pre>
        <p>{!! Lang::txt('COM_TOOLS_BADPARAMS_OPT_CONTACT_SUPPORT', $supportUrl) !!}</p>
    </div>
</x-page-container>
