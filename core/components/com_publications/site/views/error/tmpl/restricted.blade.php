{{--
  Publications restricted access page.

  Variables from controller:
    $title — page title
    $error — error message string

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css()->js();
@endphp

<x-page-container :title="$title">
  @if($error)
    <div role="alert" class="alert alert-error">
      <span>{{ e($error) }}</span>
    </div>
  @endif
</x-page-container>
