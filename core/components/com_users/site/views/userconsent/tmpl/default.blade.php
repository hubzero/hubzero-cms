{{--
 * User consent / usage agreement page.
 *
 * Displays the system usage consent message and requires
 * the user to agree before proceeding.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $consentUrl = Route::url(
        'index.php?option=' . $option . '&task=user.consent', false
    );
@endphp

<x-page-container :title="Lang::txt('COM_USERS_USERCONSENT')">
    <div class="max-w-lg mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="alert alert-warning mb-4">
                    <span>{{ Lang::txt('COM_USERS_USERCONSENT_MESSAGE') }}</span>
                </div>

                <form method="post" action="{{ $consentUrl }}">
                    <input type="hidden" name="return"
                           value="{{ base64_encode(Request::current(true)) }}" />
                    {!! Html::input('token') !!}

                    <div class="card-actions justify-end">
                        <a class="btn btn-ghost" href="/">
                            {{ Lang::txt('COM_USERS_USERCONSENT_CANCEL') }}
                        </a>
                        <button class="btn btn-primary" type="submit">
                            {{ Lang::txt('COM_USERS_USERCONSENT_AGREE') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-page-container>
