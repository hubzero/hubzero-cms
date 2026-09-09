{{--
  Page list item partial.

  Variables (passed via $__view->view):
    $page     — Page model
    $category — Category model or null
    $group    — Group object
    $version  — PageVersion model or null
    $checkout — checkout object or null

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $cn       = $group->get('cn');
  $pid      = $page->get('id');
  $pageBase = 'index.php?option=com_groups&cn=' . $cn . '&controller=pages&task=';

  $cls = '';
  if ($category !== null) {
      $cls .= ' category-' . $page->get('category');
      $__view->css('.category-' . $page->get('category')
          . '{ border-left-color: #' . $category->get('color') . '; }');
  }
  if (isset($version) && $version && $version->get('approved') == 0) {
      $cls .= ' not-approved';
  }

  $editUrl    = Route::url($pageBase . 'edit&pageid=' . $pid);
  $previewUrl = Route::url($pageBase . 'preview&pageid=' . $pid);
  $pubUrl     = Route::url($pageBase . 'publish&pageid=' . $pid);
  $unpubUrl   = Route::url($pageBase . 'unpublish&pageid=' . $pid);
  $versUrl    = Route::url($pageBase . 'versions&pageid=' . $pid);
  $delUrl     = Route::url($pageBase . 'delete&pageid=' . $pid);
@endphp

<div class="item-container {{ $cls }}">
  <div class="item-title">
    @if($page->get('privacy') === 'members')
      <span class="icon-lock tooltips"
            title="{{ Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVATE') }}"></span>
    @endif
    <a href="{{ $editUrl }}">{{ $page->get('title') }}</a>
  </div>

  <div class="item-sub">
    <span tabindex="-1">{{ $page->url() }}</span>
  </div>

  @if($checkout)
    @php $checkoutUser = User::getInstance($checkout->userid); @endphp
    <div class="item-checkout">
      <img width="15" src="{{ $checkoutUser->picture() }}"
           alt="{{ e($checkoutUser->get('name')) }}" />
      {!! Lang::txt('COM_GROUPS_PAGES_PAGE_CHECKED_OUT',
          $checkoutUser->get('id'), $checkoutUser->get('name')) !!}
    </div>
  @endif

  @if(isset($version) && $version && $version->get('approved') == 0)
    <div class="item-approved">
      {{ Lang::txt('COM_GROUPS_PAGES_PAGE_PENDING_APPROVAL') }}
    </div>
  @endif

  @if($page->get('home') == 0)
    <div class="item-state">
      @if($page->get('state') == 0)
        <a class="unpublished tooltips"
           title="{{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE') }}"
           href="{{ $pubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE') }}</a>
      @else
        <a class="published tooltips"
           title="{{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE') }}"
           href="{{ $unpubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE') }}</a>
      @endif
    </div>
  @endif

  <div class="item-preview">
    <a class="tooltips page-preview"
       title="{{ Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') }}"
       href="{{ $previewUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') }}</a>
  </div>

  <div class="dropdown dropdown-end">
    <button tabindex="0" role="button" class="btn btn-sm btn-ghost">
      {{ Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE') }}
      <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>
    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">
      <li><a href="{{ $editUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE_BACK') }}</a></li>
      <li><a class="page-preview" href="{{ $previewUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') }}</a></li>
      @if($page->get('home') == 0)
        @if($page->get('state') == 0)
          <li><a href="{{ $pubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE') }}</a></li>
        @else
          <li><a href="{{ $unpubUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE') }}</a></li>
        @endif
      @endif
      <li class="divider"></li>
      <li><a class="page-history" href="{{ $versUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_VERSION_HISTORY_PAGE') }}</a></li>
      @if($page->get('home') == 0)
        <li class="divider"></li>
        <li><a href="{{ $delUrl }}">{{ Lang::txt('COM_GROUPS_PAGES_DELETE_PAGE') }}</a></li>
      @endif
    </ul>
  </div>

  @if($page->get('home') == 0)
    <div class="item-mover"></div>
  @endif
</div>
