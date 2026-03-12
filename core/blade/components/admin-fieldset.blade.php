{{--
  Admin fieldset — section with a heading and body.

  Equivalent to the repeated div pattern:
    <div class="admin-fieldset">
      <h3 class="admin-fieldset-heading">…</h3>
      <div class="admin-fieldset-body space-y-4">…</div>
    </div>

  Usage:
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      …fields…
    </x-admin-fieldset>

    // prose info block:
    // <x-admin-fieldset legend="Overview" body-class="prose prose-sm max-w-none">
    //   <p>…</p>
    // </x-admin-fieldset>

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@props(['legend' => '', 'bodyClass' => 'space-y-4'])

<div {{ $attributes->merge(['class' => 'admin-fieldset']) }}>
  @if ($legend)
    <h3 class="admin-fieldset-heading">{!! $legend !!}</h3>
  @endif
  <div class="admin-fieldset-body {{ $bodyClass }}">
    {{ $slot }}
  </div>
</div>
