{{--
 * Logout view — immediate redirect to logout task.
 *
 * This view simply redirects to the logout task URL.
 * It exists as a fallback when the logout view is rendered
 * directly (e.g. via menu item).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\App;
    use Hubzero\Facades\Route;

    App::redirect(
        Route::url(
            'index.php?option=' . $option
            . '&task=user.logout&return=' . $return,
            false
        )
    );
@endphp
