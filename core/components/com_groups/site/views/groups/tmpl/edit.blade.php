{{--
  Group create/edit form — multi-section editor.

  Sections: details, logo, membership settings, privacy/access, email
  settings, page settings.

  Variables from controller:
    $title              — string: page title
    $option             — string: component option
    $task               — string: current task ('new' or 'edit')
    $group              — Group object
    $tags               — string: comma-separated tags
    $logos              — array: available logo file paths
    $customFields       — collection of custom Field models
    $customAnswers      — array: field name => value
    $hub_group_plugins  — array: plugin config
    $group_plugin_access— array: plugin name => access level
    $config             — Registry: component config
    $notifications      — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css()
         ->js()
         ->js('jquery.cycle2', 'system');

  // Tag editor
  $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', $tags]]);

  // Email settings
  $params = Component::params('com_groups');
  $autoEmailResponses = $params->get('email_member_groupsidcussionemail_autosignup', 0);

  // Default logo
  $default_logo = $__view->img('group_default_logo.png');
  $group->set('logo', ltrim($group->get('logo') ?: '', DS));

  // Access levels
  $levels = [
      'anyone'     => Lang::txt('COM_GROUPS_PLUGIN_ANYONE'),
      'registered' => Lang::txt('COM_GROUPS_PLUGIN_REGISTERED'),
      'members'    => Lang::txt('COM_GROUPS_PLUGIN_MEMBERS'),
      'nobody'     => Lang::txt('COM_GROUPS_PLUGIN_DISABLED'),
  ];

  // Back link
  $host     = Request::getString('HTTP_HOST', '', 'SERVER');
  $referrer = Request::getString('HTTP_REFERER', '', 'SERVER');
  if (
      filter_var($referrer, FILTER_VALIDATE_URL) === false
      || $referrer === ''
      || strpos($referrer, $host) === false
  ) {
      $backLink = Route::url('index.php?option=' . $option);
  } else {
      $backLink = $referrer;
  }

  if ($task === 'edit') {
      $backLink  = Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn'));
      $backTitle = Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP');
  } else {
      $backTitle = Lang::txt('COM_GROUPS_ACTION_BACK');
  }

  $actionUrl = Route::url('index.php?option=' . $option);
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $backLink }}" title="{{ $backTitle }}">
      {{ $backTitle }}
    </a>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  @if($task !== 'new' && !$group->get('published'))
    <div class="alert alert-warning" role="alert">
      {{ Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING') }}
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm" class="stepper">
    <div class="flex flex-col lg:flex-row gap-6">
      <div class="flex-1 min-w-0">

        {{-- Details --}}
        <x-form-section :heading="Lang::txt('COM_GROUPS_DETAILS_FIELD_TITLE')">
          @if($task !== 'new')
            <input name="cn" type="hidden" value="{{ $group->get('cn') }}" />
          @else
            <x-form-field name="cn" inputId="group_cn_field"
                          :label="Lang::txt('COM_GROUPS_DETAILS_FIELD_CN')"
                          :required="true">
              <input name="cn" id="group_cn_field" type="text"
                     class="input input-bordered w-full"
                     value="{{ $group->get('cn') }}" autocomplete="off" />
              <span class="text-sm text-base-content/60">
                {!! Lang::txt('COM_GROUPS_DETAILS_FIELD_CN_HINT') !!}
              </span>
            </x-form-field>
          @endif

          <x-form-field name="description" inputId="field-description"
                        :label="Lang::txt('COM_GROUPS_DETAILS_FIELD_DESCRIPTION')"
                        :required="true">
            <input type="text" name="description" id="field-description"
                   class="input input-bordered w-full"
                   value="{{ e(stripslashes($group->get('description') ?: '')) }}" />
          </x-form-field>

          <x-form-field name="tags" inputId="actags"
                        :label="Lang::txt('COM_GROUPS_DETAILS_FIELD_TAGS')">
            @if(count($tf) > 0)
              {!! $tf[0] !!}
            @else
              <input type="text" name="tags" id="actags"
                     class="input input-bordered w-full"
                     value="{{ $tags }}" />
            @endif
            <span class="text-sm text-base-content/60">
              {{ Lang::txt('COM_GROUPS_DETAILS_FIELD_TAGS_HINT') }}
            </span>
          </x-form-field>

          {!! $__view->view('_customfields')
               ->set('customFields', $customFields)
               ->set('customAnswers', $customAnswers)
               ->display() !!}
        </x-form-section>

        {{-- Logo --}}
        @if($task !== 'new')
          <x-form-section :heading="Lang::txt('COM_GROUPS_LOGO_FIELD_TITLE')">
            <p class="mb-3">{{ Lang::txt('COM_GROUPS_LOGO_FIELD_DESC') }}</p>

            @if($group->isSuperGroup())
              <div class="alert alert-info mb-3" role="alert">
                {{ Lang::txt('COM_GROUPS_LOGO_FIELD_DESC_SUPER_GROUP') }}
              </div>
            @endif

            <x-form-field name="group[logo]" inputId="group_logo"
                          :label="Lang::txt('COM_GROUPS_LOGO_FIELD_TITLE')">
              <select name="group[logo]" id="group_logo"
                      class="select select-bordered w-full"
                      rel="{{ $group->get('gidNumber') }}">
                <option value="">{{ Lang::txt('COM_GROUPS_LOGO_FIELD_OPTION_NULL') }}</option>
                @foreach($logos as $logo)
                  @php
                    $remove = PATH_APP . DS . 'site' . DS . 'groups' . DS
                        . $group->get('gidNumber') . DS . 'uploads' . DS;
                    $sel = (str_replace($remove, '', $logo) === $group->get('logo'));
                    $displayPath = rtrim(Request::root(true), '/') . str_replace(PATH_ROOT, '', $logo);
                    $displayName = str_replace($remove, '', $logo);
                  @endphp
                  <option value="{{ $displayPath }}" @if($sel) selected @endif>
                    {{ $displayName }}
                  </option>
                @endforeach
              </select>
            </x-form-field>

            <div class="preview" id="logo">
              <div id="logo_picked">
                @if($group->get('logo'))
                  @php
                    $selectedPath = substr(
                        PATH_APP . DS . 'site' . DS . 'groups' . DS
                        . $group->get('gidNumber') . DS . 'uploads' . DS . $group->get('logo'),
                        strlen(PATH_ROOT)
                    );
                  @endphp
                  <img src="{{ rtrim(Request::root(true), '/') . $selectedPath }}"
                       alt="{{ $group->get('cn') }}" />
                @else
                  <img src="{{ rtrim(Request::root(true), '/') . $default_logo }}"
                       alt="{{ $group->get('cn') }}" />
                @endif
              </div>
            </div>
          </x-form-section>
        @endif

        {{-- Membership settings --}}
        <x-form-section :heading="Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_TITLE')">
          <p class="mb-3">{{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_DESC') }}</p>

          <fieldset class="space-y-3">
            <legend class="font-semibold">
              {{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_LEGEND') }}
              <span class="text-error text-sm">{{ Lang::txt('COM_GROUPS_REQUIRED') }}</span>
            </legend>

            @foreach([
              ['value' => 0, 'label' => 'OPEN', 'desc' => 'OPEN'],
              ['value' => 1, 'label' => 'RESTRICTED', 'desc' => 'RESTRICTED'],
              ['value' => 2, 'label' => 'INVITE', 'desc' => 'INVITE'],
              ['value' => 3, 'label' => 'CLOSED', 'desc' => 'CLOSED'],
            ] as $jp)
              <label class="flex items-start gap-2 cursor-pointer">
                <input type="radio" class="radio radio-sm mt-1"
                       name="join_policy" id="join_policy{{ $jp['value'] }}"
                       value="{{ $jp['value'] }}"
                       @if($group->get('join_policy') == $jp['value']) checked @endif />
                <div>
                  <strong>{{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_' . $jp['label'] . '_SETTING') }}</strong>
                  <br />
                  <span class="text-sm text-base-content/60">
                    {{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_' . $jp['desc'] . '_SETTING_DESC') }}
                  </span>
                </div>
              </label>

              @if($jp['value'] === 1)
                <div class="ml-8">
                  <label for="restrict_msg" class="text-sm">
                    <strong>{{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS') }}</strong>
                    ({{ Lang::txt('COM_GROUPS_MEMBERSHIP_SETTINGS_RESTRICTED_SETTING_CREDENTIALS_DESC') }})
                  </label>
                  <textarea name="restrict_msg" id="restrict_msg" rows="5"
                            class="textarea textarea-bordered w-full mt-1"
                  >{{ e(stripslashes($group->get('restrict_msg') ?: '')) }}</textarea>
                </div>
              @endif
            @endforeach
          </fieldset>
        </x-form-section>

        {{-- Privacy / Discoverability --}}
        <x-form-section :heading="Lang::txt('COM_GROUPS_PRIVACY_SETTINGS_TITLE')">
          <p class="mb-3">{{ Lang::txt('COM_GROUPS_PRIVACY_SETTINGS_DESC') }}</p>

          <fieldset class="space-y-3 mb-6">
            <legend class="font-semibold">
              {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_LEGEND') }}
              <span class="text-error text-sm">{{ Lang::txt('COM_GROUPS_REQUIRED') }}</span>
            </legend>

            @foreach([
              ['value' => 0, 'key' => 'VISIBLE'],
              ['value' => 1, 'key' => 'HIDDEN'],
            ] as $disc)
              <label class="flex items-start gap-2 cursor-pointer">
                <input type="radio" class="radio radio-sm mt-1"
                       name="discoverability" id="discoverability{{ $disc['value'] }}"
                       value="{{ $disc['value'] }}"
                       @if($group->get('discoverability') == $disc['value']) checked @endif />
                <div>
                  <strong>{{ Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_' . $disc['key'] . '_SETTING') }}</strong>
                  <br />
                  <span class="text-sm text-base-content/60">
                    {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_SETTINGS_' . $disc['key'] . '_SETTING_DESC') }}
                  </span>
                </div>
              </label>
            @endforeach
          </fieldset>

          {{-- Plugin access --}}
          <fieldset>
            <legend class="font-semibold">{{ Lang::txt('COM_GROUPS_ACCESS_SETTINGS_TITLE') }}</legend>
            <p class="text-sm text-base-content/60 mb-3">
              {{ Lang::txt('COM_GROUPS_ACCESS_SETTINGS_DESC') }}
            </p>

            <ul id="access" class="space-y-2">
              @for($i = 0; $i < count($hub_group_plugins); $i++)
                @if($hub_group_plugins[$i]['display_menu_tab'])
                  @php $pluginName = $hub_group_plugins[$i]['name']; @endphp
                  <li class="flex items-center gap-3">
                    <input type="hidden"
                           name="group_plugin[{{ $i }}][name]"
                           value="{{ $pluginName }}" />
                    <span class="font-medium w-32">{{ $hub_group_plugins[$i]['title'] }}</span>
                    <select name="group_plugin[{{ $i }}][access]"
                            class="select select-bordered select-sm">
                      @foreach($levels as $level => $name)
                        @if($pluginName !== 'overview' || $level !== 'nobody')
                          <option value="{{ $level }}"
                                  @if(($group_plugin_access[$pluginName] ?? '') === $level) selected @endif>
                            {{ $name }}
                          </option>
                        @endif
                      @endforeach
                    </select>
                  </li>
                @endif
              @endfor
            </ul>
          </fieldset>
        </x-form-section>

        {{-- Email settings --}}
        <x-form-section :heading="Lang::txt('COM_GROUPS_EMAIL_SETTINGS_TITLE')">
          <p class="mb-3">{{ Lang::txt('COM_GROUPS_EMAIL_SETTINGS_DESC') }}</p>

          <fieldset>
            <legend class="font-semibold">
              {{ Lang::txt('COM_GROUPS_EMAIL_SETTING_FORUM_SECTION_LEGEND') }}
            </legend>

            <label class="flex items-start gap-2 cursor-pointer">
              @php
                $autoSub = $group->get('discussion_email_autosubscribe', null);
                $checked = ($autoSub == 1 || ($autoSub === null && $autoEmailResponses));
              @endphp
              <input type="checkbox" class="checkbox checkbox-sm mt-1"
                     name="discussion_email_autosubscribe"
                     id="discussion_email_autosubscribe"
                     value="1"
                     @if($checked) checked @endif />
              <div>
                <strong>{{ Lang::txt('COM_GROUPS_EMAIL_SETTING_FORUM_AUTO_SUBSCRIBE') }}</strong>
                <br />
                <span class="text-sm text-base-content/60">
                  {{ Lang::txt('COM_GROUPS_EMAIL_SETTINGS_FORUM_AUTO_SUBSCRIBE_NOTE') }}
                </span>
              </div>
            </label>
          </fieldset>
        </x-form-section>

        {{-- Page settings --}}
        <x-form-section :heading="Lang::txt('COM_GROUPS_PAGES_SETTINGS_TITLE')">
          <p class="mb-3">{{ Lang::txt('COM_GROUPS_PAGES_SETTINGS_DESC') }}</p>

          @php
            $gParams  = new \Hubzero\Config\Registry($group->get('params'));
            $comments = $gParams->get('page_comments', $config->get('page_comments', 0));
            $author   = $gParams->get('page_author', $config->get('page_author', 0));
          @endphp

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-field name="params[page_comments]" inputId="param_page_comments"
                          :label="Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS')">
              <select name="params[page_comments]" id="param_page_comments"
                      class="select select-bordered w-full">
                <option value="0" @if($comments == 0) selected @endif>
                  {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO') }}
                </option>
                <option value="1" @if($comments == 1) selected @endif>
                  {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES') }}
                </option>
                <option value="2" @if($comments == 2) selected @endif>
                  {{ Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK') }}
                </option>
              </select>
              <span class="text-sm text-base-content/60">
                {{ Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS_HINT') }}
              </span>
            </x-form-field>

            <x-form-field name="params[page_author]" inputId="param_page_author"
                          :label="Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR')">
              <select name="params[page_author]" id="param_page_author"
                      class="select select-bordered w-full">
                <option value="0" @if($author == 0) selected @endif>
                  {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO') }}
                </option>
                <option value="1" @if($author == 1) selected @endif>
                  {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES') }}
                </option>
              </select>
              <span class="text-sm text-base-content/60">
                {{ Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_HINT') }}
              </span>
            </x-form-field>
          </div>
        </x-form-section>
      </div>

      {{-- Sidebar: file browser --}}
      <div class="lg:w-80 shrink-0">
        @if($group->get('gidNumber'))
          <div class="sticky top-4">
            @php
              $browserUrl = Route::url('index.php?option=com_groups&cn='
                  . $group->get('gidNumber')
                  . '&controller=media&task=filebrowser&tmpl=component');
            @endphp
            <iframe class="w-full h-96 border rounded" src="{{ $browserUrl }}"></iframe>
          </div>
        @else
          <p class="text-sm text-base-content/60 italic">
            {{ Lang::txt('COM_GROUPS_EDIT_MUST_SAVE_TO_UPLOAD_IMAGES') }}
          </p>
        @endif
      </div>
    </div>

    <div class="mt-6">
      <button type="submit" class="btn btn-success">
        {{ Lang::txt('COM_GROUPS_EDIT_SUBMIT_BTN_TEXT') }}
      </button>
    </div>

    {!! Html::input('token') !!}
    <input type="hidden" name="published"
           value="{{ $group->get('published') }}" />
    <input type="hidden" name="gidNumber"
           value="{{ $group->get('gidNumber') ?: 0 }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="task" value="save" />
  </form>
</x-page-container>
