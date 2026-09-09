{{--
 * Pending approval message
 *
 * Variables:
 *   (none)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $supportUrl = Route::url('index.php?option=com_support&task=new');
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_PENDING_APPROVAL')">
    <p>
        {!! Lang::txt('COM_MEMBERS_PENDING_APPROVAL_MESSAGE', $supportUrl) !!}
    </p>
</x-page-container>
