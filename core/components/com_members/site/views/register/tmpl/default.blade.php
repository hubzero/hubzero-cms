{{--
  Registration — master registration/update/edit/proxycreate form.

  Handles account creation, profile updates, profile editing,
  and proxy account creation via a single multi-section form.

  Variables from controller:
    $task                       — create, update, edit, proxycreate
    $registration               — array of form values
    $xregistration              — validation object with _invalid and _missing arrays
    $showMissing                — whether to show missing field errors
    $password_rules             — password validation rules list
    $fields                     — profile Field collection
    $sitename                   — site name
    $isSelf                     — is editing own profile
    $option                     — component option
    $controller                 — controller name
    $registrationUsername       — Field state constant for username
    $registrationPassword       — Field state constant for password
    $registrationConfirmPassword — Field state constant for confirm password
    $registrationFullname       — Field state constant for full name
    $registrationEmail          — Field state constant for email
    $registrationConfirmEmail   — Field state constant for confirm email
    $registrationOptIn          — Field state constant for email opt-in
    $registrationCAPTCHA        — Field state constant for captcha
    $registrationTOU            — Field state constant for terms of use

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

@php
  use Components\Members\Models\Profile\Field;
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('register')->js('register');

  // Get return url
  $form_redirect = '';
  if ($form_redirect = Request::getString('return', '', 'get')) {
      // urldecode is due to round trip XSS protection added to this field, see ticket 1411
      $form_redirect = urldecode($form_redirect);
  }
  $current = Request::path();
  if (!$form_redirect && !in_array($current, ['/register/update', '/members/update', '/members/register/update'])) {
      $form_redirect = Request::current();
  }
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_REGISTER_' . strtoupper($task))">

  {{-- Help blocks based on task --}}
  @switch($task)
    @case('update')
      @if (!empty($xregistration->_missing))
        <div class="help">
          {{ $sitename }} requires additional registration
          information before your account can be used.<br />
          All fields marked <span class="text-error text-sm">required</span> must be filled in.
        </div>
      @endif

      @if (!Request::getBool('update', false, 'post'))
        @php $showMissing = false; @endphp
      @endif
      @break

    @case('edit')
      @if ($isSelf)
        <div class="help">
          <h4>How do I change my password?</h4>
          @php
            $chgPassUrl = Route::url(
                'index.php?option=com_members&id='
                . User::get('id') . '&task=changepassword'
            );
          @endphp
          <p>Passwords can be changed with
            <a href="{{ $chgPassUrl }}" title="Change password form">this form</a>.</p>
        </div>
      @endif
      @break

    @case('proxycreate')
      <div class="help">
        <h4>Proxy Account Creation Instructions</h4>
        <p>
          Simply fill out the form below and an account will be created for that person.
          You will then be shown the basic text of an email which you <strong>MUST</strong> then copy
          and paste and send to that person. This email will provide them with the initial password
          set for them below as well as their email confirmation link. You may add any other information
          that you deem appropriate, including contributed resources or the reason for their account.
        </p>
      </div>
      @break

    @default
      @break
  @endswitch

  <form method="post" id="hubForm">

    {{-- Auth providers (create task only) --}}
    @if ($task == 'create' && empty($xregistration->_invalid) && empty($xregistration->_missing))
      @php
        $__view->css('providers.css', 'com_login');

        // Check to see if third party auth plugins are enabled
        Plugin::import('authentication');
        $plugins        = Plugin::byType('authentication');
        $authenticators = [];

        foreach ($plugins as $p) {
            if ($p->name != 'hubzero') {
                $pparams = new \Hubzero\Config\Registry($p->params);
                $display = $pparams->get('display_name', ucfirst($p->name));
                $authenticators[] = [
                    'name'    => $p->name,
                    'display' => $display,
                ];
            }
        }

        // Build provider HTML
        $provider_html = '';
        foreach ($authenticators as $a) {
            $authClass = 'Plugins\\Authentication\\' . ucfirst($a['name']) . '\\' . ucfirst($a['name']);
            $refl = new \ReflectionClass($authClass);
            if ($refl->hasMethod('onRenderOption')) {
                $html = $refl->getMethod('onRenderOption')->invoke(null);
                $provider_html .= is_array($html) ? implode("\n", $html) : $html;
            } else {
                $provider_html .= '<a class="'
                    . $a['name']
                    . ' account" href="'
                    . Route::url('index.php?option=com_users&view=login&authenticator=' . $a['name'])
                    . '">';
                $provider_html .= '<div class="signin">'
                    . Lang::txt('COM_MEMBERS_LOGIN_SIGN_IN_WITH_METHOD', $a['display'])
                    . '</div>';
                $provider_html .= '</a>';
            }
        }
      @endphp

      @if (!empty($provider_html))
        <div class="explaination">
          <p class="info">You can choose to log in via one of these services,
            and we'll help you fill in the info below!</p>
          <p>Already have an account? <a href="{{ Route::url('index.php?option=com_login') }}">Log in here.</a></p>
        </div>
        <fieldset>
          <legend>Connect With</legend>
          <div id="providers" class="auth">
            {!! $provider_html !!}
          </div>
        </fieldset>
        <div class="clear"></div>
      @endif
    @endif

    {{-- Duplicate email warning --}}
    @php
      $emailusers = User::oneByEmail($registration['email'])->get('id');
    @endphp

    @if (($task == 'create' || $task == 'proxycreate') && $emailusers)
      <div class="alert alert-error">
        <p>The email address "{{ e($registration['email']) }}"
          is already registered. If you have lost or forgotten this
          {{ $sitename }} login information, we can help you recover it:</p>
        <p class="submit"><a
            href="{{ Route::url('index.php?option=com_members&task=remind') }}"
            class="btn btn-danger">Email Existing Account Information</a></p>
        <p>If you are aware you already have another account registered to this
          email address, and are requesting another account because you need more
          resources, {{ $sitename }} would be happy to work with you
          to raise your resource limits instead:</p>
        <p class="submit"><a
            href="{{ Route::url('index.php?option=com_support&controller=tickets&task=new') }}"
            class="btn btn-danger">Submit Request to Raise Existing Limits</a></p>
      </div>
    @endif

    {{-- Validation errors --}}
    @if (!empty($xregistration->_invalid) || !empty($xregistration->_missing))
      <div class="alert alert-error">
        Please correct the indicated invalid fields in the form below.

        @if ($showMissing && !empty($xregistration->_missing))
          @if ($task == 'update')
            <br />We are missing some vital information regarding your account!
            Please confirm the information below so we can better serve you. Thank you!
          @else
            <br />Missing required information:
          @endif
          <ul>
            @foreach ($xregistration->_missing as $miss)
              <li>{{ $miss }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    @endif

    {{-- Login information fieldset --}}
    @if ($registrationUsername != Field::STATE_HIDDEN || $registrationPassword != Field::STATE_HIDDEN)
      <div class="explaination">
        <p>{{ Lang::txt('COM_MEMBERS_REGISTER_CANNOT_CHANGE_USERNAME') }}</p>

        @if ($task == 'create' || $task == 'proxycreate')
          <p>{{ Lang::txt('COM_MEMBERS_REGISTER_PASSWORD_CHANGE_HINT') }}</p>
        @endif
      </div>

      <fieldset>
        <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_LOGIN_INFORMATION') }}</legend>

        @if ($registrationUsername == Field::STATE_READONLY)
          <div class="form-group">
            <label for="userlogin">
              {{ Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN') }}<br />
              {{ e($registration['login']) }}
              <input name="login" id="userlogin" class="input input-bordered" type="hidden"
                     value="{{ e($registration['login']) }}" />
            </label>
          </div>
        @elseif ($registrationUsername != Field::STATE_HIDDEN)
          <div class="form-group">
            @php
              $loginInvalid = !empty($xregistration->_invalid['login']);
            @endphp
            <label for="userlogin">
              {{ Lang::txt('COM_MEMBERS_REGISTER_USER_LOGIN') }}
              @if ($registrationUsername == Field::STATE_REQUIRED)
                <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
              @endif
              <input name="login" id="userlogin"
                     class="input input-bordered{{ $loginInvalid ? ' input-error' : '' }}"
                     type="text" maxlength="32"
                     value="{{ e($registration['login']) }}" />
              <p class="hint" id="usernameHint">{{ Lang::txt('COM_MEMBERS_REGISTER_USERNAME_HINT') }}</p>
              @if ($loginInvalid)
                <span class="text-error text-sm">{{ $xregistration->_invalid['login'] }}</span>
              @endif
            </label>
          </div>
        @endif

        @if ($registrationPassword != Field::STATE_HIDDEN)
          @php
            $passSpan = $registrationConfirmPassword != Field::STATE_HIDDEN ? '6' : '12';
            $passInvalid = !empty($xregistration->_invalid['password'])
                && !is_array($xregistration->_invalid['password']);
          @endphp
          <div class="grid">
            <div class="col span{{ $passSpan }}">
              <div class="form-group">
                <label for="password">
                  {{ Lang::txt('COM_MEMBERS_REGISTER_PASSWORD') }}
                  @if ($registrationPassword == Field::STATE_REQUIRED)
                    <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                  @endif
                  <input name="password" id="password"
                         class="input input-bordered{{ $passInvalid ? ' input-error' : '' }}"
                         type="password"
                         value="{{ e($registration['password']) }}"
                         autocomplete="off" />
                  @if ($passInvalid)
                    <span class="text-error text-sm">{{ $xregistration->_invalid['password'] }}</span>
                  @endif
                </label>
              </div>
            </div>
            @if ($registrationConfirmPassword != Field::STATE_HIDDEN)
              @php
                $confPassInvalid = !empty($xregistration->_invalid['confirmPassword']);
              @endphp
              <div class="col span6 omega">
                <div class="form-group">
                  <label for="password2">
                    {{ Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_PASSWORD') }}
                    @if ($registrationConfirmPassword == Field::STATE_REQUIRED)
                      <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                    @endif
                    <input name="password2" id="password2"
                           class="input input-bordered{{ $confPassInvalid ? ' input-error' : '' }}"
                           type="password"
                           value="{{ e($registration['confirmPassword']) }}"
                           autocomplete="off" />
                    @if ($confPassInvalid)
                      <span class="text-error text-sm">{{ $xregistration->_invalid['confirmPassword'] }}</span>
                    @endif
                  </label>
                </div>
              </div>
            @endif
          </div>

          @if (count($password_rules) > 0)
            <div class="grid">
              <ul id="passrules">
                @foreach ($password_rules as $rule)
                  @if (!empty($rule))
                    @php
                      $ruleErr = false;
                      if (!empty($xregistration->_invalid['password'])
                          && is_array($xregistration->_invalid['password'])) {
                          $ruleErr = in_array($rule, $xregistration->_invalid['password']);
                      }
                    @endphp
                    <li class="{{ $ruleErr ? 'error' : 'empty' }}">{{ $rule }}</li>
                  @endif
                @endforeach
                @if (!empty($xregistration->_invalid['password'])
                    && is_array($xregistration->_invalid['password']))
                  @foreach ($xregistration->_invalid['password'] as $msg)
                    @if (!in_array($msg, $password_rules))
                      <li class="error">{{ $msg }}</li>
                    @endif
                  @endforeach
                @endif
              </ul>
            </div>
          @endif
        @endif
      </fieldset>
      <div class="clear"></div>
    @endif

    {{-- Contact information fieldset --}}
    @if ($registrationFullname != Field::STATE_HIDDEN || $registrationEmail != Field::STATE_HIDDEN)
      <div class="explaination">
        @if ($task == 'create')
          <p>{{ Lang::txt('COM_MEMBERS_REGISTER_ACTIVATION_EMAIL_HINT') }}</p>
        @endif
        <p>{{ Lang::txt('COM_MEMBERS_REGISTER_PRIVACY_HINT') }}</p>
      </div>

      <fieldset>
        <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_CONTACT_INFORMATION') }}</legend>

        @if ($registrationFullname != Field::STATE_HIDDEN)
          @php
            $nameRequired = ($registrationFullname == Field::STATE_REQUIRED);
            $nameInvalid = !empty($xregistration->_invalid['name']);
            $nameMessage = $nameInvalid
                ? $xregistration->_invalid['name']
                : '';

            $givenName  = '';
            $middleName = '';
            $surname    = '';

            $bits = explode(' ', $registration['name'] == null ? '' : $registration['name']);
            $surname = array_pop($bits);
            if (count($bits) >= 1) {
                $givenName = array_shift($bits);
            }
            if (count($bits) >= 1) {
                $middleName = implode(' ', $bits);
            }
          @endphp
          <div class="grid">
            <div class="col span4">
              <div class="form-group">
                <label for="first-name">
                  {{ Lang::txt('COM_MEMBERS_REGISTER_FIRST_NAME') }}
                  @if ($nameRequired)
                    <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                  @endif
                  <input type="text"
                         class="input input-bordered{{ $nameInvalid ? ' input-error' : '' }}"
                         name="name[first]" id="first-name"
                         value="{{ e(\Hubzero\Utility\Sanitize::cleanProperName($givenName)) }}" />
                </label>
              </div>
            </div>
            <div class="col span4">
              <div class="form-group">
                <label for="middle-name">
                  {{ Lang::txt('COM_MEMBERS_REGISTER_MIDDLE_NAME') }}
                  <input type="text"
                         class="input input-bordered"
                         name="name[middle]" id="middle-name"
                         value="{{ e(\Hubzero\Utility\Sanitize::cleanProperName($middleName)) }}" />
                </label>
              </div>
            </div>
            <div class="col span4 omega">
              <div class="form-group">
                <label for="last-name">
                  {{ Lang::txt('COM_MEMBERS_REGISTER_LAST_NAME') }}
                  @if ($nameRequired)
                    <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                  @endif
                  <input type="text"
                         class="input input-bordered{{ $nameInvalid ? ' input-error' : '' }}"
                         name="name[last]" id="last-name"
                         value="{{ e(\Hubzero\Utility\Sanitize::cleanProperName($surname)) }}" />
                </label>
              </div>
            </div>
          </div>
          @if ($nameMessage)
            <div class="alert alert-error">{{ $nameMessage }}</div>
          @endif
        @endif

        @if ($registrationEmail != Field::STATE_HIDDEN || $registrationConfirmEmail != Field::STATE_HIDDEN)
          <div class="grid">
            @if ($registrationEmail != Field::STATE_HIDDEN)
              @php
                $emailInvalid = !empty($xregistration->_invalid['email']);
              @endphp
              <div class="col span6">
                <div class="form-group">
                  <label for="email">
                    {{ Lang::txt('COM_MEMBERS_REGISTER_VALID_EMAIL') }}
                    @if ($registrationEmail == Field::STATE_REQUIRED)
                      <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                    @endif
                    <input class="input input-bordered{{ $emailInvalid ? ' input-error' : '' }}"
                           name="email" id="email" type="email"
                           value="{{ e($registration['email']) }}" />
                    @if ($emailInvalid)
                      <span class="text-error text-sm">{{ $xregistration->_invalid['email'] }}</span>
                    @endif
                  </label>
                </div>
              </div>
            @endif
            @if ($registrationConfirmEmail != Field::STATE_HIDDEN)
              @php
                if (!empty($xregistration->_invalid['email'])) {
                    $registration['confirmEmail'] = '';
                }
                $confirmEmailInvalid = !empty($xregistration->_invalid['confirmEmail']);
              @endphp
              <div class="col span6 omega">
                <div class="form-group">
                  <label for="email2">
                    {{ Lang::txt('COM_MEMBERS_REGISTER_CONFIRM_EMAIL') }}
                    @if ($registrationConfirmEmail == Field::STATE_REQUIRED)
                      <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
                    @endif
                    <input class="input input-bordered{{ $confirmEmailInvalid ? ' input-error' : '' }}"
                           name="email2" id="email2" type="email"
                           value="{{ e($registration['confirmEmail']) }}" />
                    @if ($confirmEmailInvalid)
                      <span class="text-error text-sm">{{ $xregistration->_invalid['confirmEmail'] }}</span>
                    @endif
                  </label>
                </div>
              </div>
            @endif
          </div>

          @if ($registrationEmail != Field::STATE_HIDDEN)
            @if ($task == 'proxycreate')
              <div class="alert alert-warning">
                Important! The user <strong>MUST</strong> click on the email confirmation link that you
                will send them in order for them to start using the account you have created for them.
              </div>
            @elseif ($task == 'create')
              @php
                $usersConfig    = Component::params('com_members');
                $useractivation = $usersConfig->get('useractivation', 1);
              @endphp
              @if ($useractivation != 0)
                <div class="alert alert-warning">{!! Lang::txt(
                    'COM_MEMBERS_REGISTER_YOU_MUST_CONFIRM_EMAIL',
                    \Hubzero\Utility\Str::obfuscate(Config::get('mailfrom'))
                ) !!}</div>
              @endif
            @else
              <div class="alert alert-warning">
                Important! If you change your e-mail address you <strong>must</strong> confirm receipt
                of the confirmation e-mail from {!! \Hubzero\Utility\Str::obfuscate(Config::get('mailfrom')) !!} in order to re-activate your
                account.
              </div>
            @endif
          @endif
        @endif
      </fieldset>
      <div class="clear"></div>
    @endif

    {{-- Profile fields --}}
    @php
      // Convert to XML so we can use the Form processor
      $xml = Field::toXml($fields, 'create');

      // Gather data to pass to the form processor
      $data = new Hubzero\Config\Registry();

      // Create a new form
      Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

      $form = new Hubzero\Form\Form('profile', ['control' => 'profile']);
      $form->load($xml);
      $form->bind($data);

      $scripts = [];
      $toggle = [];
    @endphp

    @if ($fields->count() > 0)
      <fieldset>
        <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_LEGEND_PERSONAL_INFO') }}</legend>

        @foreach ($fields as $field)
          @php
            // Add in class for JS selector to conditionally retrieve data from RoR Api
            $rorApiBoolean = Component::params('com_members')->get('rorApi');
          @endphp

          @if (strtolower($field->get('name')) == 'profile[organization]'
              && strtolower($field->get('type')) == 'text'
              && $rorApiBoolean)
            <span class="hidden rorApiAvailable"></span>
          @endif

          @php
            $formfield = $form->getField($field->get('name'));

            if ($field->options->count()) {
                $i = 0;
                $hasEvents = false;
                $opts = [];
                $hide = [];

                foreach ($field->options as $option) {
                    $opts[] = '#' . $formfield->id . $i;
                    $i++;

                    if (!$option->get('dependents')) {
                        continue;
                    }

                    $events = json_decode($option->get('dependents'));
                    $option->set('dependents', $events);

                    if (empty($events)) {
                        continue;
                    }

                    $hasEvents = true;
                }

                if ($hasEvents) {
                    if ($field->get('type') == 'dropdown') {
                        $scripts[] = "\t" . '$("#' . $formfield->id . '").on("change", function(e){';
                    } else {
                        $scripts[] = "\t" . '$("' . implode(',', $opts) . '").on("change", function(e){';
                    }
                }

                $i = 0;
                foreach ($field->options as $option) {
                    if (!$option->get('dependents')) {
                        continue;
                    }

                    $events = $option->get('dependents');

                    if ($field->get('type') == 'dropdown') {
                        $optVal = $option->value ? $option->value : $option->label;
                        $scripts[] = "\t\t" . 'if ($(this).val() == "' . $optVal . '") {';
                        $show = [];
                        foreach ($events as $s) {
                            $show[] = '#input-' . $s;
                        }
                        $hide = array_merge($hide, $show);
                        $scripts[] = "\t\t\t" . '$("' . implode(', ', $show) . '").show();';
                        $scripts[] = "\t\t" . '} else {';
                        $scripts[] = "\t\t\t" . '$("' . implode(', ', $show) . '").hide();';
                        $scripts[] = "\t\t" . '}';

                        $toggle[] = "\t" . 'if ($("#profile_' . $field->get('name') . '").val() == "' . $optVal . '") {';
                        $toggle[] = "\t\t" . '$("' . implode(', ', $show) . '").show();';
                        $toggle[] = "\t" . '} else {';
                        $toggle[] = "\t\t" . '$("' . implode(', ', $show) . '").hide();';
                        $toggle[] = "\t" . '}';
                    } else {
                        $optVal = $option->value ? $option->value : $option->label;
                        $scripts[] = "\t\t" . 'if ($(this).is(":checked") && $(this).val() == "' . $optVal . '") {';
                        $show = [];
                        foreach ($events as $s) {
                            $show[] = '#input-' . $s;
                        }
                        $hide = array_merge($hide, $show);
                        $scripts[] = "\t\t\t" . '$("' . implode(', ', $show) . '").show();';
                        $scripts[] = "\t\t" . '} else {';
                        $scripts[] = "\t\t\t" . '$("' . implode(', ', $show) . '").hide();';
                        $scripts[] = "\t\t" . '}';

                        $toggle[] = "\t" . 'if ($("#profile_' . $field->get('name') . $i . '").is(":checked") && $("#profile_' . $field->get('name') . $i . '").val() == "' . $optVal . '") {';
                        $toggle[] = "\t\t" . '$("' . implode(', ', $show) . '").show();';
                        $toggle[] = "\t" . '} else {';
                        $toggle[] = "\t\t" . '$("' . implode(', ', $show) . '").hide();';
                        $toggle[] = "\t" . '}';
                    }

                    $i++;
                }

                if ($hasEvents) {
                    $scripts[] = "\t" . '});';
                    $scripts[] = implode("\n", $toggle);
                }
            }

            if ($value = $field->get('default_value')) {
                $formfield->setValue($value);
            }

            if (isset($registration['_profile'][$field->get('name')])) {
                $formfield->setValue($registration['_profile'][$field->get('name')]);
            }

            $fieldErrors = !empty($xregistration->_invalid[$field->get('name')])
                ? $xregistration->_invalid[$field->get('name')]
                : '';
          @endphp
          <div class="form-group{{ $fieldErrors ? ' fieldWithErrors' : '' }}"
               id="input-{{ $field->get('name') }}">
            {!! $formfield->label !!}
            {!! $formfield->input !!}
            @if ($fieldErrors)
              <span class="text-error text-sm">{{ $fieldErrors }}</span>
            @endif
          </div>
        @endforeach
      </fieldset>
    @endif

    @php
      if (!empty($scripts)) {
          $__view->js("jQuery(document).ready(function(\$){\n" . implode("\n", $scripts) . "\n});");
      }
    @endphp

    {{-- Email updates --}}
    @if ($registrationOptIn != Field::STATE_HIDDEN)
      @php
        $sendEmailInvalid = !empty($xregistration->_invalid['sendEmail']);
        $sendEmailMessage = $sendEmailInvalid
            ? $xregistration->_invalid['sendEmail']
            : '';

        // If we dont have a mail pref option set to unanswered
        if (!isset($registration['sendEmail']) || $registration['sendEmail'] == '') {
            $registration['sendEmail'] = '-1';
        }

        $sendChecked = ($registration['sendEmail'] == '1' || $registration['sendEmail'] == '-1');
      @endphp
      <fieldset>
        <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_LEGEND_EMAIL_UPDATES') }}</legend>

        <div class="form-group">
          <label for="sendEmail">
            <input class="checkbox" type="checkbox" name="sendEmail" id="sendEmail"
                   value="1" {{ $sendChecked ? 'checked="checked"' : '' }} />
            {{ Lang::txt('COM_MEMBERS_REGISTER_RECEIVE_EMAIL_UPDATES') }}
            @if ($registrationOptIn == Field::STATE_REQUIRED)
              <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
            @endif
          </label>
          @if ($sendEmailMessage)
            <span class="text-error text-sm">{{ $sendEmailMessage }}</span>
          @endif
        </div>
      </fieldset>
      <div class="clear"></div>
    @endif

    {{-- CAPTCHA --}}
    @if ($registrationCAPTCHA != Field::STATE_HIDDEN)
      @php
        $captchas = Event::trigger('captcha.onDisplay');
      @endphp

      @if (count($captchas) > 0)
        <fieldset>
          <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_HUMAN_CHECK') }}</legend>
          @if (isset($xregistration->_invalid['captcha']) && !empty($xregistration->_invalid['captcha']))
            <span class="text-error text-sm">{{ $xregistration->_invalid['captcha'] }}</span>
          @endif
      @endif

      <label id="botcheck-label" for="botcheck">
        {{ Lang::txt('COM_MEMBERS_REGISTER_BOT_CHECK_LABEL') }}
        @if ($registrationCAPTCHA == Field::STATE_REQUIRED)
          <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
        @endif
        <input type="text" class="input input-bordered" name="botcheck" id="botcheck" value="" />
      </label>

      @if (count($captchas) > 0)
        {!! implode("\n", $captchas) !!}
        </fieldset>
      @endif
    @endif

    {{-- Terms of use --}}
    @if ($registrationTOU != Field::STATE_HIDDEN)
      <fieldset>
        <legend>{{ Lang::txt('COM_MEMBERS_REGISTER_TERMS_AND_CONDITIONS') }}</legend>

        <div class="form-group">
          @php
            $touInvalid = !empty($xregistration->_invalid['usageAgreement']);
          @endphp
          <label for="usageAgreement">
            <input type="checkbox" class="checkbox" id="usageAgreement"
                   value="1" name="usageAgreement"
                   {{ $registration['usageAgreement'] ? 'checked="checked"' : '' }} />
            {!! Lang::txt('COM_MEMBERS_REGISTER_TOS', Request::base(true)) !!}
            @if ($registrationTOU == Field::STATE_REQUIRED)
              <span class="text-error text-sm">{{ Lang::txt('COM_MEMBERS_REGISTER_FORM_REQUIRED') }}</span>
            @endif
          </label>

          @if ($touInvalid)
            <span class="text-error text-sm">{{ $xregistration->_invalid['usageAgreement'] }}</span>
          @endif
        </div>
      </fieldset>
      <div class="clear"></div>
    @elseif ($registration['usageAgreement'])
      <input name="usageAgreement" type="hidden" id="usageAgreement" value="checked" />
      <div class="clear"></div>
    @endif

    {{-- Submit --}}
    <p class="submit">
      <button type="submit" class="btn btn-success" name="{{ $task }}">
        {{ Lang::txt('COM_MEMBERS_REGISTER_BUTTON_' . strtoupper($task)) }}
      </button>
    </p>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="{{ $task }}" />
    <input type="hidden" name="act" value="submit" />
    {!! Html::input('token') !!}
    <input type="hidden" name="base_uri" id="base_uri" value="{{ rtrim(Request::base(true), '/') }}" />
    {{-- urlencode is XSS protection added to this field, see ticket 1411 --}}
    <input type="hidden" name="return" value="{{ urlencode($form_redirect) }}" />
  </form>

</x-page-container>
