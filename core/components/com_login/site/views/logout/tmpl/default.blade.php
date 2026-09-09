{{--
  Logout redirect pass-through.

  This view immediately redirects; it renders no HTML.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Route;

  App::redirect(
      Route::url(
          'index.php?option=' . $user . '&task=logout&return='
          . $return,
          false
      )
  );
@endphp
