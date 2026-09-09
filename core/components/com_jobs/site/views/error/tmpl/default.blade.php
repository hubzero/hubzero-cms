{{--
  Error display page.

  Variables from controller:
    $title — Page title string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="$title">
  @if($__view->getError())
    <div role="alert" class="alert alert-error">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif
</x-page-container>
