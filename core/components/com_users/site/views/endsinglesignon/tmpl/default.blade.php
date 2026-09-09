{{--
 * End single sign-on confirmation page.
 *
 * Offers the user the choice to fully sign out of the
 * third-party SSO provider or leave those sessions active.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $urlLogoutAll = Route::url(
        'index.php?option=' . $option
        . '&task=user.logout&sso=all&authenticator=' . $authenticator,
        false
    );
    $urlLogoutNone = Route::url(
        'index.php?option=' . $option
        . '&task=user.logout&sso=none&authenticator=' . $authenticator
        . '&return=' . Request::base(),
        false
    );
@endphp

<x-page-container :title="Lang::txt('JLOGOUT')">
    <div class="max-w-lg mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body text-center">
                <h2 class="card-title justify-center text-lg">
                    {{ Lang::txt(
                        'Would you like to completely log out of your %s account?',
                        $display_name
                    ) }}
                </h2>

                <p class="text-base-content/70 mt-2">
                    {{ Lang::txt('Your %s session has ended.', $sitename) }}
                </p>
                <p class="text-base-content/70">
                    {{ Lang::txt(
                        'If you would like to end all %s account shared sessions as well, you may do so now.',
                        $display_name
                    ) }}
                </p>

                <div class="card-actions justify-center mt-4 flex-col gap-2">
                    <a class="btn btn-error w-full" href="{{ $urlLogoutAll }}">
                        {{ Lang::txt('End all %s account sessions!', $display_name) }}
                    </a>
                    <a class="btn btn-ghost w-full" href="{{ $urlLogoutNone }}">
                        {{ Lang::txt(
                            'Leave other %s account sessions untouched.',
                            $display_name
                        ) }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-page-container>
