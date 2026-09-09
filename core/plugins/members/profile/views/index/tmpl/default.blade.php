{{--
  Member Profile — main profile display view.

  Variables from plugin:
    $profile             — member profile model
    $fields              — profile fields collection
    $params              — plugin params Registry
    $registration_update — registration update object (optional)
    $completeness        — completeness percentage
    $completeness_level  — completeness level string (optional)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
  use Hubzero\Facades\Event;
  use Components\Members\Models\Profile\Field;

  /**
   * Renders JSON values as a table if the value is JSON-encoded.
   */
  if (!function_exists('renderIfJson')) :
      function renderIfJson($v)
      {
          if (strstr($v == null ? '' : $v, '{')) {
              $v = json_decode((string)$v, true);
              if (!$v || json_last_error() !== JSON_ERROR_NONE) {
                  return $v;
              }
              $o = ['<table>', '<tbody>'];
              foreach ($v as $nm => $vl) {
                  if (!trim($vl)) continue;
                  $o[] = '<tr><th>' . $nm . ':</th><td>' . $vl . '</td></tr>';
              }
              $o[] = '</tbody>';
              $o[] = '</table>';
              $v = implode("\n", $o);
          }
          return $v;
      }
  endif;

  $__view->css()
         ->js()
         ->js('jquery.fileuploader.js', 'system');

  $loggedin = !User::isGuest();
  $isUser   = (User::get('id') == $profile->get('id'));

  // Registration update
  $update_missing = [];
  $invalid = [];
  if (isset($registration_update)) {
      $update_missing = $registration_update->_missing;
      $invalid = $registration_update->_invalid;
  }

  // Incremental registration
  $uid = (int)$profile->get('id');
  $incrOpts = new \Components\Members\Models\Incremental\Options();
  $isIncrementalEnabled = $incrOpts->isEnabled($uid);

  // Profile info — query profile data with labels
  $entries = $profile->profiles();
  $p = $entries->getTableName();
  $f = Field::blank()->getTableName();
  $o = \Components\Members\Models\Profile\Option::blank()->getTableName();

  $profiles = $entries
      ->select($p . '.*,' . $o . '.label')
      ->join($f, $f . '.name', $p . '.profile_key', 'inner')
      ->joinRaw($o, $o . '.field_id=' . $f . '.id AND ' . $o . '.value=' . $p . '.profile_value', 'left')
      ->ordered()
      ->rows();

  // Build form from XML
  $xml = Field::toXml($fields, 'edit');
  $data = new \Hubzero\Config\Registry(
      \Components\Members\Models\Profile::collect($profiles)
  );

  \Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');
  $form = new \Hubzero\Form\Form('profile', ['control' => 'profile']);
  $form->load($xml);
  $form->bind($data);

  // Build fields lookup
  $fieldMap = [];
  foreach ($profiles as $prof) {
      $key = $prof->get('profile_key');
      if (isset($fieldMap[$key])) {
          $values = $fieldMap[$key]->get('profile_value');
          if (!is_array($values)) {
              $values = [$values => $fieldMap[$key]->get('label', $values)];
          }
          $values[$prof->get('profile_value')] = $prof->get('label', $prof->get('profile_value'));
          $fieldMap[$key]->set('profile_value', $values);
      } else {
          $fieldMap[$key] = $prof;
      }
  }

  // Legacy access values
  $legacy = [
      0 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_PUBLIC'),
      1 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_REGISTERED'),
      2 => Lang::txt('COM_MEMBERS_FIELD_ACCESS_PRIVATE'),
  ];

  $stateHidden   = Field::STATE_HIDDEN;
  $stateReadonly = Field::STATE_READONLY;

  $profileUrl = Route::url(
      'index.php?option=com_members&id=' . $profile->get('uidNumber') . '&active=profile'
  );
@endphp

@if ($__view->getError())
  <p class="error">{{ $__view->getError() }}</p>
@endif

<div id="profile-page-content" data-url="{{ $profileUrl }}">
  <h3 class="section-header">{{ Lang::txt('PLG_MEMBERS_PROFILE') }}</h3>

  {{-- Validation / update warnings --}}
  @if (count($invalid) > 0)
    <div class="error member-update-missing">
      <strong>{{ Lang::txt('PLG_MEMBERS_PROFILE_USER_INVALID') }}</strong>
      <ul>
        @foreach ($invalid as $i)
          <li>{{ $i }}</li>
        @endforeach
      </ul>
    </div>
  @elseif (count($update_missing) > 0)
    @if (!(count($update_missing) == 1 && in_array('usageAgreement', array_keys($update_missing))))
      <div class="error member-update-missing">
        <strong>{{ Lang::txt('PLG_MEMBERS_PROFILE_UPDATE_BEFORE_CONTINUING') }}</strong>
        <ul>
          @foreach ($update_missing as $um)
            <li>{{ $um }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  @endif

  {{-- Completeness meter --}}
  @if ($isUser)
    <ul>
      <li id="member-profile-completeness" class="hide">
        {{ Lang::txt('PLG_MEMBERS_PROFILE_COMPLETENESS') }}
        <div id="meter">
          <span id="meter-percent"
                data-percent="{{ $completeness ?? '' }}"
                data-percent-level="{{ $completeness_level ?? '' }}"></span>
        </div>
        @if ($isUser && $isIncrementalEnabled)
          <span id="completeness-info">
            {{ Lang::txt('PLG_MEMBERS_PROFILE_COMPLETENESS_MEANS') }}
          </span>
        @endif
      </li>
    </ul>
  @endif

  {{-- Incremental registration awards --}}
  @if ($isUser && $isIncrementalEnabled)
    @php
      $awards = new \Components\Members\Models\Incremental\Awards($profile);
      $awards = $awards->award();
      $storeUrl = Route::url('index.php?option=com_store');
      $answersUrl = Route::url('index.php?option=com_answers');
      $wishlistUrl = Route::url('index.php?option=com_wishlist');
    @endphp
    <div id="award-info">
      <p>{!! Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_OFFERING_POINTS', $storeUrl) !!}</p>
      @if ($awards['prior'])
        <p>{{ Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_AWARDED_POINTS', $awards['prior']) }}</p>
      @endif
      @if ($awards['new'])
        <p>{{ Lang::txt('PLG_MEMBERS_PROFILE_INCREMENTAL_EARNED_POINTS', $awards['new']) }}</p>
      @endif
      <p>{!! Lang::txt(
          'PLG_MEMBERS_PROFILE_INCREMENTAL_EARN_MORE_POINTS',
          $incrOpts->getAwardPerField(),
          $storeUrl, $answersUrl, $wishlistUrl
      ) !!}</p>
    </div>
    <div id="wallet"><span>{{ $awards['prior'] + $awards['new'] }}</span></div>
    <script type="application/json" id="profile-incremental-data">
      {!! json_encode([
          'bonus_eligible_fields' => $awards['eligible'],
          'bonus_amount' => $incrOpts->getAwardPerField(),
      ]) !!}
    </script>
  @endif

  {{-- Usage agreement dialog --}}
  @if (isset($update_missing) && in_array('usageAgreement', array_keys($update_missing)))
    <dialog id="usage-agreement-dialog" class="usage-agreement-dialog">
      <div id="usage-agreement-popup">
        <form action="{{ Route::url('index.php?option=com_members') }}"
              method="post"
              data-section-registration="usageAgreement"
              data-section-profile="usageAgreement">
          <h2>{{ Lang::txt('PLG_MEMBERS_PROFILE_NEW_TERMS_OF_USE') }}</h2>
          <div id="usage-agreement-box">
            <div id="usage-agreement">
              @php
                $db = App::get('db');
                $db->setQuery("SELECT * FROM `#__content` WHERE `alias`=" . $db->quote('terms'));
                $page = $db->loadObject();
                if ($page && $page->id) {
                    $page->text = $page->fulltext ?: $page->introtext;
                    $touParams = new \Hubzero\Config\Registry($page->attribs);
                    Event::trigger('content.onContentPrepare', ['com_content.article', &$page, &$touParams, 0]);
                    echo $page->text;
                }
              @endphp
            </div>
            <div id="usage-agreement-last-chance">
              <h3>{{ Lang::txt('PLG_MEMBERS_PROFILE_ARE_YOU_SURE') }}</h3>
              <p>{{ Lang::txt('PLG_MEMBERS_PROFILE_ARE_YOU_SURE_EXPLANATION') }}</p>
            </div>
          </div>
          <div id="usage-agreement-buttons">
            <button class="section-edit-cancel usage-agreement-do-not-agree" type="button">
              {{ Lang::txt('PLG_MEMBERS_PROFILE_TERMS_NOT_AGREE') }}
            </button>
            <button class="section-edit-submit" type="submit">
              {{ Lang::txt('PLG_MEMBERS_PROFILE_TERMS_AGREE') }}
            </button>
          </div>
          <div id="usage-agreement-last-chance-buttons">
            <button class="section-edit-cancel usage-agreement-back-to-agree" type="button">
              {{ Lang::txt('PLG_MEMBERS_PROFILE_TERMS_GO_BACK') }}
            </button>
            <button class="section-edit-cancel usage-agreement-dont-accept" type="button">
              {{ Lang::txt('PLG_MEMBERS_PROFILE_TERMS_I_DO_NOT_AGREE') }}
            </button>
          </div>
          <input type="hidden" name="declinetou" value="0" />
          <input type="hidden" name="usageAgreement" value="1" />
          <input type="hidden" name="field_to_check[]" value="usageAgreement" />
          <input type="hidden" name="option" value="com_members" />
          <input type="hidden" name="controller" value="profiles" />
          <input type="hidden" name="id" value="{{ User::get('id') }}" />
          <input type="hidden" name="task" value="save" />
          {!! Html::input('token') !!}
        </form>
      </div>
    </dialog>
  @endif

  <ul id="profile">
    {{-- ── Name field ────────────────────────────────── --}}
    @php $fullnameState = Field::state('registrationFullname', 'RRRR', 'edit'); @endphp
    @if ($isUser && $fullnameState != $stateHidden)
      <li class="profile-name section hidden">
        <div class="section-content">
          <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_NAME') }}</div>
          <div class="value">{{ e($profile->get('name')) }}</div>
          <br class="clear" />
          @php
            $nameInputs  = '<label class="side-by-side three">'
                . Lang::txt('PLG_MEMBERS_PROFILE_FIRST_NAME')
                . ' <input type="text" name="name[first]" id="first-name" class="input-text" value="'
                . e($profile->get('givenName')) . '" /></label>';
            $nameInputs .= '<label class="side-by-side three">'
                . Lang::txt('PLG_MEMBERS_PROFILE_MIDDLE_NAME')
                . ' <input type="text" name="name[middle]" id="middle-name" class="input-text" value="'
                . e($profile->get('middleName')) . '" /></label>';
            $nameInputs .= '<label class="side-by-side three no-padding-right">'
                . Lang::txt('PLG_MEMBERS_PROFILE_LAST_NAME')
                . ' <input type="text" name="name[last]" id="last-name" class="input-text" value="'
                . e($profile->get('surname')) . '" /></label>';
          @endphp
          @if ($fullnameState != $stateReadonly)
            {!! $__view->view('default', 'edit')
                 ->set('registration_field', 'name')
                 ->set('profile_field', 'name')
                 ->set('registration', $profile->get('name'))
                 ->set('field_state', $fullnameState)
                 ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_NAME'))
                 ->set('profile', $profile)
                 ->set('isUser', $isUser)
                 ->set('inputs', $nameInputs)
                 ->set('access', '')
                 ->loadTemplate() !!}
          @endif
        </div>
        @if ($isUser && $fullnameState != $stateReadonly)
          <div class="section-edit">
            <a class="edit-profile-section" href="#">{{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}</a>
          </div>
        @endif
      </li>
    @endif

    {{-- ── Username field ────────────────────────────── --}}
    @php $usernameState = Field::state('registrationUsername', 'RRRR', 'edit'); @endphp
    @if ($isUser && $usernameState != $stateHidden)
      <li class="profile-name section hidden">
        <div class="section-content">
          <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_USERNAME') }}</div>
          <div class="value">{{ e($profile->get('username')) }}</div>
          <br class="clear" />
        </div>
      </li>
    @endif

    {{-- ── Password field ────────────────────────────── --}}
    @php
      $passwordState = Field::state('registrationPassword', 'RRRR', 'edit');
    @endphp
    @if ($isUser && $passwordState != $stateHidden)
      @php
        $hzup = \Hubzero\User\Password::getInstance($profile->get('id'));
        if ($hzup && !empty($hzup->passhash)) {
            $passtype = array_key_exists('auth_link_id', User::toArray()) ? 'changelocal' : 'changehub';
        } else {
            $passtype = 'set';
        }
      @endphp
      @if (!Plugin::isEnabled('members', 'account'))
        <li class="profile-password section hidden">
          <div class="section-content">
            <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD') }}</div>
            <div class="value">
              @if ($passtype == 'changelocal' || $passtype == 'changehub')
                ***************
              @else
                {{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NOT_SET') }}
              @endif
            </div>
            <br class="clear" />
            <div class="section-edit-container">
              <div class="section-edit-content">
                <form action="{{ Route::url('index.php?option=com_members') }}"
                      method="post"
                      data-section-registration="password"
                      data-section-profile="password">
                  <span class="section-edit-errors"></span>
                  @if ($passtype == 'changelocal' || $passtype == 'changehub')
                    <div class="input-wrap">
                      <label for="password">
                        {{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CURRENT') }}
                        <input type="password" name="oldpass" id="password" class="input-text" />
                      </label>
                    </div>
                  @endif
                  <div class="input-wrap">
                    <label for="newpass" class="side-by-side">
                      {{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NEW') }}
                      <input type="password" name="newpass" id="newpass" class="input-text" />
                    </label>
                    <label for="newpass2" class="side-by-side no-padding-right">
                      {{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_CONFIRM') }}
                      <input type="password" name="newpass2" id="newpass2" class="input-text" />
                    </label>
                  </div>
                  <input type="hidden" name="change" value="1" />
                  <input type="submit" class="section-edit-submit btn"
                         value="{{ Lang::txt('PLG_MEMBERS_PROFILE_SAVE') }}" />
                  <input type="reset" class="section-edit-cancel btn"
                         value="{{ Lang::txt('JCANCEL') }}" />
                  <input type="hidden" name="option" value="com_members" />
                  <input type="hidden" name="controller" value="profiles" />
                  <input type="hidden" name="id" value="{{ $profile->get('id') }}" />
                  <input type="hidden" name="task" value="changepassword" />
                  <input type="hidden" name="no_html" value="1" />
                  {!! Html::input('token') !!}
                </form>
              </div>
            </div>
          </div>
          @if ($isUser && $passwordState != $stateReadonly)
            <div class="section-edit">
              <a class="edit-profile-section" href="#">{{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}</a>
            </div>
          @endif
        </li>
      @else
        <li class="profile-password section hidden">
          <div class="section-content">
            <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD') }}</div>
            <div class="value">
              @if ($passtype == 'changelocal' || $passtype == 'changehub')
                ***************
              @else
                {{ Lang::txt('PLG_MEMBERS_PROFILE_PASSWORD_NOT_SET') }}
              @endif
            </div>
          </div>
          @if ($isUser && $passwordState != $stateReadonly)
            <div class="section-edit">
              <a href="{{ Route::url($profile->link() . '&active=account') }}">
                {{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}
              </a>
            </div>
          @endif
        </li>
      @endif
    @endif

    {{-- ── Email field ───────────────────────────────── --}}
    @php $emailState = Field::state('registrationEmail', 'RRRR', 'edit'); @endphp
    @if ($profile->get('email') && $emailState != $stateHidden)
      @if (
        $params->get('access_email', 2) == 0
        || ($params->get('access_email', 2) == 1 && $loggedin)
        || ($params->get('access_email', 2) == 2 && $isUser)
      )
        @php
          $emailCls = '';
          if ($params->get('access_email', 2) == 2) {
              $emailCls .= 'private';
          }
          if ($profile->get('email') == '' || is_null($profile->get('email'))) {
              $emailCls .= $isUser ? ' hidden' : ' hide';
          }

          $emailSelect  = '<select name="access[email]" class="input-select">' . "\n";
          foreach ($legacy as $k => $v) {
              $selected = ($k == $params->get('access_email', 2)) ? ' selected=1' : '';
              $emailSelect .= ' <option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
          }
          $emailSelect .= '</select>' . "\n";

          $obfuscatedEmail = \Components\Members\Helpers\Html::obfuscate($profile->get('email'));
        @endphp
        <li class="profile-email section {{ $emailCls }}">
          <div class="section-content">
            <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_EMAIL') }}</div>
            <div class="value">
              <a class="email" href="mailto:{!! $obfuscatedEmail !!}" rel="nofollow">
                {!! $obfuscatedEmail !!}
              </a>
            </div>
            @if ($isUser && $emailState != $stateReadonly)
              <br class="clear" />
              <input type="hidden" class="input-text" name="email" id="email"
                     value="{{ e($profile->get('email')) }}" />
              @php
                if ($profile->get('access') > 2) {
                    $emailAccess  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . '</label>';
                    $emailAccess .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
                    $emailAccess .= '<input type="hidden" name="access[email]" value="'
                        . $params->get('access_email') . '" />';
                } else {
                    $emailAccess = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')
                        . $emailSelect . '</label>';
                }

                $emailInputs = '<label class="side-by-side">'
                    . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_VALID')
                    . ' <input type="text" class="input-text" name="email" id="profile-email" value="'
                    . e($profile->get('email')) . '" /></label>'
                    . '<label class="side-by-side no-padding-right">'
                    . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_CONFIRM')
                    . ' <input type="text" class="input-text" name="email2" id="profile-email2" value="'
                    . e($profile->get('email')) . '" /></label>'
                    . '<br class="clear" />'
                    . '<p class="warning no-margin-top">'
                    . Lang::txt('PLG_MEMBERS_PROFILE_EMAIL_WARNING') . '</p>';
              @endphp
              {!! $__view->view('default', 'edit')
                   ->set('registration_field', 'email')
                   ->set('profile_field', 'email')
                   ->set('registration', 1)
                   ->set('field_state', $emailState)
                   ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAIL'))
                   ->set('profile', $profile)
                   ->set('isUser', $isUser)
                   ->set('inputs', $emailInputs)
                   ->set('access', $emailAccess)
                   ->loadTemplate() !!}
            @endif
          </div>
          @if ($isUser && $emailState != $stateReadonly)
            <div class="section-edit">
              <a class="edit-profile-section" href="#">{{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}</a>
            </div>
          @endif
        </li>
      @endif
    @endif

    {{-- ── Dynamic profile fields ────────────────────── --}}
    @php
      // Collect dependent field options
      $depFields = [];
      foreach ($fields as $field) {
          if ($field->options->count()) {
              foreach ($field->options as $option) {
                  if (!$option->get('dependents')) continue;
                  $events = json_decode($option->get('dependents'));
                  $option->set('dependents', $events);
                  if (empty($events)) continue;
                  if (!isset($depFields[$field->get('name')])) {
                      $depFields[$field->get('name')] = [];
                  }
                  $depFields[$field->get('name')][$option->get('value')] = $events;
              }
          }
      }

      $depShow = [];
      $depHide = [];

      // Build dependency toggle data for JS
      $depConfig = [];
    @endphp

    @foreach ($fields as $field)
      @php
        // RoR API class
        $rorApiBoolean = Component::params('com_members')->get('rorApi');
        $isOrgTextField = strtolower($field->get('name')) == 'organization'
            && strtolower($field->get('type')) == 'text';
      @endphp
      @if ($isOrgTextField && $rorApiBoolean)
        <span class="hidden rorApiAvailable"></span>
      @endif

      @php
        // Build dependency config for this field
        if ($isUser && $field->options->count()) {
            $i = 0;
            $hasEvents = false;
            $fieldDeps = [];

            foreach ($field->options as $option) {
                $i++;
                $events = $option->get('dependents');
                if (!empty($events)) {
                    $hasEvents = true;
                    $optVal = $option->value ?: $option->label;
                    $fieldDeps[$optVal] = $events;
                }
            }

            if ($hasEvents) {
                $depConfig[] = [
                    'field' => $field->get('name'),
                    'type'  => $field->get('type'),
                    'deps'  => $fieldDeps,
                ];
            }
        }

        // Get profile value
        if (!isset($fieldMap[$field->get('name')])) {
            $fieldMap[$field->get('name')] = \Components\Members\Models\Profile::blank();
            $fieldMap[$field->get('name')]->set('access', 1);
        }

        $fieldProfile = $fieldMap[$field->get('name')];
        if (!$fieldProfile->get('access')) {
            $fieldProfile->set('access', 5);
        }

        $accessLevel = $fieldProfile->get('access', $field->get('access', 5));
      @endphp

      @if (in_array($accessLevel, User::getAuthorisedViewLevels()) || $isUser)
        @php
          $cls = ['profile-' . $field->get('name')];

          if ($fieldProfile->get('access', $field->get('access')) == 2) {
              $cls[] = 'registered';
          }
          if ($fieldProfile->get('access', $field->get('access')) == 5) {
              $cls[] = 'private';
          }

          // Get display value
          if ($field->get('type') == 'tags') {
              $value = $profile->tags();
          } else {
              $value = $fieldProfile->get('profile_value');
              if (!is_array($value)) {
                  $value = $fieldProfile->get('label', $value);
              }
              $value = $value ?: $profile->get($field->get('name'), $field->get('default_value'));

              if ($value) {
                  if ($field->get('type') == 'textarea') {
                      $value = nl2br(Html::content('prepare', $value));
                  }
                  if ($field->get('type') == 'url') {
                      $parsed = parse_url($value);
                      if (empty($parsed['scheme'])) {
                          $value = 'http://' . ltrim($value, '/');
                      }
                      $value = '<a href="' . $value . '" rel="external">' . $value . '</a>';
                  }
              }
          }

          $val = $value;

          // Handle dependent field visibility
          if (isset($depFields[$field->get('name')])) {
              foreach ($depFields[$field->get('name')] as $opt => $deps) {
                  $depHide = array_merge($deps, $depHide);
              }
          }

          if (is_array($value)) {
              foreach (array_keys($value) as $k => $v) {
                  if (isset($depFields[$field->get('name')]) && isset($depFields[$field->get('name')][$v])) {
                      $depShow = array_merge($depShow, $depFields[$field->get('name')][$v]);
                  }
              }
              $displayVal = [];
              foreach ($value as $k => $v) {
                  $displayVal[$k] = renderIfJson($v);
              }
              $val = implode('<br />', $displayVal);
          } else {
              if (isset($depFields[$field->get('name')]) && isset($depFields[$field->get('name')][$value])) {
                  $depShow = array_merge($depShow, $depFields[$field->get('name')][$value]);
              }
              $val = renderIfJson($value);
          }

          $depHide = array_diff($depHide, $depShow);

          if (in_array($field->get('name'), $depHide)) {
              if (!$isUser) continue;
              $cls[] = 'hide';
          }

          if (empty($value)) {
              $cls[] = $isUser ? 'hidden' : 'hide';
          }
        @endphp

        <li class="{{ implode(' ', $cls) }} section"
            id="input-section-{{ e($field->get('name')) }}">
          <div class="section-content">
            <div class="key">{{ $field->get('label') }}</div>
            <div class="value">
              @if (!empty($val))
                {!! is_array($val) ? implode(', ', $val) : $val !!}
              @else
                {{ Lang::txt('PLG_MEMBERS_PROFILE_NOT_SET') }}
              @endif
            </div>
            <br class="clear" />

            @if ($isUser)
              @php
                if ($field->get('type') == 'url') {
                    $value = strip_tags($val ?: '');
                }
                if ($field->get('type') == 'tags') {
                    $value = $profile->tags('string');
                }
                if ($field->get('type') == 'address') {
                    $value = $fieldProfile->get('profile_value');
                    $value = $value ?: $profile->get($field->get('name'));
                }
                if (is_array($value)) {
                    $value = array_keys($value);
                }
                $formfield = $form->getField($field->get('name'));
              @endphp

              @if ($formfield)
                @php
                  $formfield->setValue($fieldProfile->get('profile_value'));

                  if ($profile->get('access') > 2) {
                      $fieldAccess  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . '</label>';
                      $fieldAccess .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
                      $fieldAccess .= '<input type="hidden" name="access[' . $field->get('name')
                          . ']" value="' . $fieldProfile->get('access', $field->get('access')) . '" />';
                  } else {
                      $accessName = 'access[' . $field->get('name') . ']';
                      $accessVal = $value == ''
                          ? $field->get('access')
                          : $fieldProfile->get('access', $field->get('access'));
                      $selectHtml = \Components\Members\Helpers\Html::selectAccess(
                          $accessName, $accessVal, 'input-select'
                      );
                      $fieldAccess = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY')
                          . '</label>' . $selectHtml;
                  }
                @endphp
                {!! $__view->view('default', 'edit')
                     ->set('registration_field', $field->get('name'))
                     ->set('profile_field', $field->get('name'))
                     ->set('registration', $field->get('action_edit'))
                     ->set('title', $field->get('label'))
                     ->set('profile', $profile)
                     ->set('isUser', $isUser)
                     ->set('inputs', $formfield->label . $formfield->input)
                     ->set('access', $fieldAccess)
                     ->loadTemplate() !!}
              @endif
            @endif
          </div>
          @if ($isUser)
            <div class="section-edit">
              <a class="edit-profile-section" href="#">{{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}</a>
            </div>
          @endif
        </li>
      @endif
    @endforeach

    {{-- ── Opt-in / Email updates ────────────────────── --}}
    @if (
      $params->get('access_optin') == 0
      || ($params->get('access_optin') == 1 && $loggedin)
      || ($params->get('access_optin') == 2 && $isUser)
    )
      @php
        $optinCls = '';
        if ($params->get('access_optin') == 2) $optinCls .= 'private';
        if ($profile->get('sendEmail') == '' || is_null($profile->get('sendEmail'))) {
            $optinCls .= $isUser ? ' hidden' : ' hide';
        }
        if (isset($update_missing) && in_array('optin', array_keys($update_missing))) {
            $optinCls = str_replace(' hide', '', $optinCls) . ' missing';
        }
        if (!$isUser) $optinCls .= ' hide';

        switch ($profile->get('sendEmail')) {
            case '1':  $mailPrefValue = 'Yes, send me emails'; break;
            case '0':  $mailPrefValue = 'No, don\'t send me emails'; break;
            default:   $mailPrefValue = 'Unanswered'; break;
        }

        $optinSelect  = '<select name="access[optin]" class="input-select">' . "\n";
        foreach ($legacy as $k => $v) {
            $selected = ($k == $params->get('access_optin')) ? ' selected=1' : '';
            $optinSelect .= ' <option value="' . $k . '"' . $selected . '>' . $v . '</option>' . "\n";
        }
        $optinSelect .= '</select>' . "\n";

        $optInState = Field::state('registrationOptIn', 'RRRR', 'edit');
      @endphp

      @if ($isUser && $optInState != $stateHidden)
        <li class="profile-optin section {{ $optinCls }}">
          <div class="section-content">
            <div class="key">{{ Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES') }}</div>
            <div class="value">{{ $mailPrefValue }}</div>
            @if ($isUser)
              <br class="clear" />
              @php
                $options = [
                    '-1' => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_SELECT'),
                    '1'  => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_YES'),
                    '0'  => Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_OPT_NO'),
                ];

                $optinHtml  = '<strong>' . Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES_EXPLANATION') . '</strong>';
                $optinHtml .= '<label for="sendEmail"><select name="sendEmail" id="sendEmail" class="input-select">';
                foreach ($options as $key => $val) {
                    $sel = ($key == $profile->get('sendEmail')) ? 'selected=1' : '';
                    $optinHtml .= '<option ' . $sel . ' value="' . $key . '">' . $val . '</option>';
                }
                $optinHtml .= '</select></label>';

                if ($profile->get('access') > 2) {
                    $optinAccess  = '<label>' . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . '</label>';
                    $optinAccess .= Lang::txt('PLG_MEMBERS_PROFILE_ACCESS_MUST_BE_PUBLIC');
                    $optinAccess .= '<input type="hidden" name="access[sendEmail]" value="'
                        . $params->get('access_optin') . '" />';
                } else {
                    $optinAccess = '<div class="block"><label>'
                        . Lang::txt('PLG_MEMBERS_PROFILE_PRIVACY') . $optinSelect . '</label></div>';
                }
              @endphp
              {!! $__view->view('default', 'edit')
                   ->set('registration_field', 'sendEmail')
                   ->set('profile_field', 'sendEmail')
                   ->set('registration', $profile->get('sendEmail'))
                   ->set('title', Lang::txt('PLG_MEMBERS_PROFILE_EMAILUPDATES'))
                   ->set('profile', $profile)
                   ->set('isUser', $isUser)
                   ->set('inputs', $optinHtml)
                   ->set('access', $optinAccess)
                   ->loadTemplate() !!}
            @endif
          </div>
          @if ($isUser && $optInState != $stateReadonly)
            <div class="section-edit">
              <a class="edit-profile-section" href="#">{{ Lang::txt('PLG_MEMBERS_PROFILE_EDIT') }}</a>
            </div>
          @endif
        </li>
      @endif
    @endif
  </ul>
</div>{{-- /#profile-page-content --}}

{{-- Dependent field toggle data (CSP-safe) --}}
@if (!empty($depConfig))
  <script type="application/json" id="profile-dependency-config">
    {!! json_encode($depConfig) !!}
  </script>
@endif

{{-- Native dialogs for profile picture and address editing --}}
<dialog id="profile-picture-dialog" class="profile-picture-dialog">
  <div class="dialog-header">
    <h2>{{ Lang::txt('PLG_MEMBERS_PROFILE_PICTURE') }}</h2>
    <button class="dialog-close" type="button" aria-label="{{ Lang::txt('JCANCEL') }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="2" stroke="currentColor" width="20" height="20" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
  <div class="dialog-body"></div>
</dialog>

<dialog id="address-edit-dialog" class="address-edit-dialog">
  <div class="dialog-header">
    <h2>{{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS') }}</h2>
    <button class="dialog-close" type="button" aria-label="{{ Lang::txt('JCANCEL') }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="2" stroke="currentColor" width="20" height="20" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
      </svg>
    </button>
  </div>
  <div class="dialog-body"></div>
</dialog>

