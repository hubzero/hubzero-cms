{{--
  Registration — method selection page.

  Lets the user choose to sign in via an existing realm account
  or create a new local account.

  Variables from controller:
    $title    — Page title (string)
    $sitename — Site name (string)
    $realms   — Associative array of realm key => label pairs

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<x-page-container :title="$title">

  <form action="index.php" method="post" id="hubForm">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="md:col-span-2">
        <fieldset>
          <h3>Register with {{ e($sitename) }}</h3>

          <fieldset>
            <legend>Register by signing in with your</legend>

            @foreach ($realms as $key => $value)
              <label class="block">
                <input class="radio" type="radio" name="realm" value="{{ $key }}" />
                {{ $value }}
              </label>
            @endforeach

            <p class="mt-4">
              <input class="btn btn-primary" type="submit" name="login" value="Log In" />
            </p>
          </fieldset>

          <h3>Or Create a New Account</h3>

          <fieldset>
            <legend>Create a separate account for {{ e($sitename) }}</legend>

            <p class="mt-4">
              <input class="btn btn-primary" type="submit" name="register" value="Create a New Account" />
            </p>
          </fieldset>
        </fieldset>
      </div>

      <div>
        <p>
          Registering at {{ e($sitename) }} is easy: just sign in using an
          account you may already have at one of the listed
          sites/organizations or create a new {{ e($sitename) }} account.
        </p>

        <h4>Why is registration required for parts of the {{ e($sitename) }}?</h4>

        <p>
          Our sponsors ask us who uses the {{ e($sitename) }} and what they
          use it for. Registration helps us answer these questions. Usage
          statistics also focus our attention on improvements, making the
          {{ e($sitename) }} experience better for <em>you</em>.
        </p>
      </div>
    </div>

    <input type="hidden" name="option" value="com_members" />
    <input type="hidden" name="controller" value="register" />
    <input type="hidden" name="task" value="select" />
    <input type="hidden" name="act" value="submit" />
  </form>

</x-page-container>
