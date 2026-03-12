{{--
  Support — ACL ARO row toggle field partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $updateValue = $isEnabled ? '0' : '1';
  $token       = Session::getFormToken();
  $actionUrl   = 'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=update&id=' . $id
      . '&action=' . $action
      . '&value=' . $updateValue
      . '&' . $token . '=1';
  $actionHref = Route::url($actionUrl, false);

  if ($isEnabled) {
      $alt   = Lang::txt('JYES');
      $class = 'publish';
  } else {
      $alt   = Lang::txt('JNO');
      $class = 'unpublish';
  }
@endphp

<td class="text-center">
    <a href="{{ $actionHref }}"
       class="badge {{ $isEnabled ? 'badge-success' : 'badge-ghost' }} cursor-pointer hover:opacity-80"
       title="{{ $alt }}"
    >
        {{ $alt }}
    </a>
</td>
