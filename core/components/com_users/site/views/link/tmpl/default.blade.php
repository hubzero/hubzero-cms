{{--
 * Account linking wizard — step-based prompts for linking
 * a third-party auth account to an existing hub account.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $step = Request::getInt('step', 1);

    $urlStep2 = Route::url(
        'index.php?option=' . $option . '&view=link&step=2', false
    );
    $urlStep3 = Route::url(
        'index.php?option=' . $option . '&view=link&step=3', false
    );
    $urlRegister = Route::url(
        'index.php?option=com_members&controller=register&task=update', false
    );

    $innerLoginUrl = Route::url(
        'index.php?option=' . $option
        . '&view=login&authenticator=' . $hzad->authenticator,
        false
    );
    $innerReturnUrl = Route::url(
        'index.php?option=' . $option
        . '&view=login&reset=1&return=' . base64_encode($innerLoginUrl),
        false
    );
    $urlLogout = Route::url(
        'index.php?option=' . $option
        . '&view=logout&return=' . base64_encode($innerReturnUrl),
        false
    );
@endphp

<x-page-container :title="Lang::txt('Account Setup')">
    <div class="max-w-lg mx-auto">

        {{-- Step 1: Have you logged in before? --}}
        <div class="card bg-base-100 shadow-sm {{ $step === 1 ? '' : 'hidden' }}">
            <div class="card-body text-center">
                <h2 class="card-title justify-center text-lg">
                    {{ Lang::txt('Have you ever logged into %s before?', $sitename) }}
                </h2>
                <div class="card-actions justify-center mt-4">
                    <a class="btn btn-primary" href="{{ $urlStep2 }}">
                        {{ Lang::txt('JYes') }}
                    </a>
                    <a class="btn btn-outline" href="{{ $urlRegister }}">
                        {{ Lang::txt('JNo') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Step 2: Link or create new? --}}
        <div class="card bg-base-100 shadow-sm {{ $step === 2 ? '' : 'hidden' }}">
            <div class="card-body text-center">
                <h2 class="card-title justify-center text-lg">
                    {{ Lang::txt(
                        'Great! Did you want to link your %s account to that existing account or create a new account?',
                        $display_name
                    ) }}
                </h2>
                <div class="card-actions justify-center mt-4">
                    <a class="btn btn-primary" href="{{ $urlStep3 }}">
                        {{ Lang::txt('Link') }}
                    </a>
                    <a class="btn btn-outline" href="{{ $urlRegister }}">
                        {{ Lang::txt('Create new') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Step 3: Confirm and redirect to login --}}
        <div class="card bg-base-100 shadow-sm {{ $step === 3 ? '' : 'hidden' }}">
            <div class="card-body text-center">
                <h2 class="card-title justify-center text-lg">
                    {{ Lang::txt(
                        'We can do that. Just login with that existing account now and we\'ll link them up!'
                    ) }}
                </h2>
                <div class="card-actions justify-center mt-4">
                    <a class="btn btn-primary" href="{{ $urlLogout }}">
                        {{ Lang::txt('OK') }}
                    </a>
                    <a class="btn btn-ghost" href="{{ $urlStep2 }}">
                        {{ Lang::txt('Go back') }}
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-page-container>
