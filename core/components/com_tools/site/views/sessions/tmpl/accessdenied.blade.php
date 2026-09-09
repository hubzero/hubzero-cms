@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $supportUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
    $browseUrl = Route::url('index.php?option=' . $option);
@endphp

<x-page-container :title="Lang::txt('COM_TOOLS_ACCESSDENIED')">
    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{!! $__view->getError() !!}</span>
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <p class="mb-4">{{ Lang::txt('COM_TOOLS_ACCESSDENIED_MESSAGE') }}</p>
        <h3 class="text-lg font-semibold mb-2">{{ Lang::txt('COM_TOOLS_ACCESSDENIED_HOW_TO_FIX') }}</h3>
        <ul class="list-disc pl-6 space-y-1">
            <li>{!! Lang::txt('COM_TOOLS_ACCESSDENIED_OPT_CONTACT_SUPPORT', $supportUrl) !!}</li>
            <li>{!! Lang::txt('COM_TOOLS_ACCESSDENIED_OPT_BROWSE', $browseUrl) !!}</li>
        </ul>
    </div>
</x-page-container>
