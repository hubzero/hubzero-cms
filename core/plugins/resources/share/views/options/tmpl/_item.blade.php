{{--
  Share item — single share service link.

  Variables (from parent view):
    $option   — string: component option
    $resource — object: resource model
    $name     — string: share service name

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $shareUrl = Route::url(
      'index.php?option=' . $option
      . '&id=' . $resource->id
      . '&active=share&sharewith=' . strtolower($name)
  );
  $shareTitle = Lang::txt('PLG_RESOURCES_SHARE_ON', Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($name)));
  $shareClass = 'share_' . strtolower($name);
  $shareLabel = Lang::txt('PLG_RESOURCES_SHARE_' . strtoupper($name));
@endphp

<a href="{{ $shareUrl }}"
   title="{{ $shareTitle }}"
   class="popup"
   rel="external">
  <span class="{{ $shareClass }}"><span>{{ $shareLabel }}</span></span>
</a>
