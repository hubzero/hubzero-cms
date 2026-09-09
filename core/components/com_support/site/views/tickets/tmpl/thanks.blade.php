{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Request;

    $tmpl = Request::getCmd('tmpl');

    $ticketUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=ticket&id=' . $ticket
    );
    $targetAttr = $tmpl ? 'target="_parent"' : '';

    $newUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=new'
    );
@endphp

<x-page-container :title="Lang::txt('COM_SUPPORT')">
    @if ($__view->getError())
        <div class="alert alert-error" role="alert">
            {!! implode('<br />', $__view->getErrors()) !!}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div id="ticket-number" class="card bg-base-100 border border-base-300">
                <div class="card-body items-center text-center">
                    <span class="text-sm text-base-content/60">
                        {{ Lang::txt('COM_SUPPORT_TICKET_NUMBER', ' ') }}
                    </span>
                    <strong class="text-4xl font-bold">
                        <a {!! $targetAttr !!} href="{{ $ticketUrl }}">
                            {{ $ticket }}
                        </a>
                    </strong>
                </div>
            </div>
        </div>

        <div>
            <div id="messagebox" class="card bg-base-100 border border-base-300 mb-4">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_SUPPORT_TROUBLE_THANKS') }}</h3>
                    <p>{!! Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES') !!}</p>
                    @if ($ticket)
                        <p>{!! Lang::txt('COM_SUPPORT_TROUBLE_TICKET_REFERENCE', $ticket) !!}</p>
                    @endif
                </div>
            </div>

            <p>
                <a class="btn btn-primary" href="{{ $newUrl }}">
                    {{ Lang::txt('COM_SUPPORT_NEW_REPORT') }}
                </a>
            </p>
        </div>
    </div>
</x-page-container>
