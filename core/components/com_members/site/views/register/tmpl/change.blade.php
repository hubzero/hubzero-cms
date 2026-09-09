{{--
  Change email address form. Allows users to correct their email
  address and optionally resend the confirmation email.

  Variables from controller:
    $title          — string  Page title
    $option         — string  Component option (com_members)
    $controller     — string  Controller name (register)
    $success        — bool    Whether the update was successful
    $email          — string  Current email address
    $email_confirmed — int    Email confirmation status
    $return         — string  Return URL parameter

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css('register')
           ->js('register');

    $emailInvalid = !$email || !\Components\Members\Helpers\Utility::validemail($email);
@endphp

<x-page-container :title="$title">
    @if ($__view->getError())
        <div class="alert alert-error">{{ $__view->getError() }}</div>
    @endif

    @if ($success)
        <div class="alert alert-success">{{ Lang::txt('Your account has been updated successfully.') }}</div>
    @else
        @if (($email_confirmed != 1) && ($email_confirmed != 3))
            @slot('sidebar')
                <x-sidebar-card>
                    <h4>Never received or cannot find the confirmation email?</h4>
                    <p>
                        You can have a new confirmation email sent to "{{ e($email) }}" by
                        <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=resend&return=' . $return) }}">clicking here</a>.
                    </p>
                </x-sidebar-card>
            @endslot
        @endif

        @php
            $formAction = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller . '&task=change'
            );
        @endphp

        <form action="{{ $formAction }}" method="post" id="hubForm">
            <fieldset>
                <h3>{{ Lang::txt('Correct Email Address') }}</h3>

                <label for="email">
                    {{ Lang::txt('Valid E-mail:') }}
                </label>
                <input
                    name="email"
                    id="email"
                    type="text"
                    size="51"
                    value="{{ e($email) }}"
                    class="input input-bordered w-full{{ $emailInvalid ? ' input-error' : '' }}" />
            </fieldset>

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="task" value="change" />
            <input type="hidden" name="act" value="show" />

            <p class="submit">
                <button type="submit" name="update" class="btn btn-primary">
                    {{ Lang::txt('Update Email') }}
                </button>
            </p>
        </form>
    @endif
</x-page-container>
