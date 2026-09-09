{{--
  Order history — list of completed transactions.

  Variables from controller (homeTask):
    $transactions — array of transaction objects
    $total        — total transaction count
    $filters      — array: start, limit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $shopUrl = Route::url('index.php?option=com_storefront', false);
@endphp

<x-page-container :title="Lang::txt('COM_CART_ORDERS')">
  @if(!$transactions)
    <x-empty-state :title="Lang::txt('COM_CART_NO_ORDERS')">
      {!! Lang::txt('COM_CART_NO_ORDERS_SHOP', $shopUrl) !!}
    </x-empty-state>
  @else
    <div class="space-y-4">
      @foreach($transactions as $transaction)
        {!! $__view->view('transaction', 'orders')
              ->set('transaction', $transaction)
              ->loadTemplate() !!}
      @endforeach
    </div>

    {{-- Pagination --}}
    @php
      $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
    @endphp
    <nav aria-label="Page navigation" class="flex justify-center mt-8">
      {!! $pageNav->render() !!}
    </nav>
  @endif
</x-page-container>
