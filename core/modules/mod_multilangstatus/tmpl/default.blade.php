{{--
  Multilanguage Status module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $langUrl = Route::url('index.php?option=com_languages&view=multilangstatus&tmpl=component');
@endphp
<span class="multilanguage">
  <a class="link link-hover" href="{{ $langUrl }}">
    {{ Lang::txt('MOD_MULTILANGSTATUS') }}
  </a>
</span>
