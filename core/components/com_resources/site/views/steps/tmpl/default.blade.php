{{--
  Resources contribution — default error/status page.

  Variables from controller:
    $title  — page title
    $error  — error message (if any), from $__view->getError()

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__view->css('create.css');
  $__view->js('create.js');

  $error = $__view->getError();
@endphp

<x-page-container :title="$title">
  @if($error)
    <div role="alert" class="alert alert-error">
      <span>{{ e($error) }}</span>
    </div>
  @endif
</x-page-container>
