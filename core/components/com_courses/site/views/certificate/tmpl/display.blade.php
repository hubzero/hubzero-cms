{{--
  Certificate unavailable message.

  Shown when a certificate is not available for the current course/section.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="Lang::txt('COM_COURSES_COMPLETION_CERTIFICATE')">
  <div class="alert alert-warning" role="status">
    {{ Lang::txt('COM_COURSES_COMPLETION_CERTIFICATE_NONE') }}
  </div>
</x-page-container>
