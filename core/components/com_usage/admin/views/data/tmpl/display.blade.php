{{--
  Usage — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_USAGE') }}"
    icon="usage"
    option="{{ $option }}"
/>

<div role="alert" class="alert alert-warning">
  {!! Lang::txt('COM_USAGE_WARNING') !!}
</div>
