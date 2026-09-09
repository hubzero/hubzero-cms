{{--
  Publications error page.

  Variables from controller:
    $title — page title
    $__view — view object (for getError())

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css()->js();
  $error = $__view->getError();
@endphp

<x-page-container :title="$title">
  @if($error)
    <div role="alert" class="alert alert-error">
      <span>{{ e($error) }}</span>
    </div>
  @endif
</x-page-container>
