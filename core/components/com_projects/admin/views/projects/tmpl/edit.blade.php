{{--
  Projects — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $__view->css()->js();


  // Compute quota values
  $quota = $params->get('quota');
  $quota = $quota
      ? $quota
      : \Components\Projects\Helpers\Html::convertSize(
          floatval($config->get('defaultQuota', '1')), 'GB', 'b'
      );
  $pubQuota = $params->get('pubQuota');
  $pubQuota = $pubQuota
      ? $pubQuota
      : \Components\Projects\Helpers\Html::convertSize(
          floatval($config->get('pubQuota', '1')), 'GB', 'b'
      );
  $quotaGB    = \Components\Projects\Helpers\Html::convertSize($quota, 'b', 'GB', 2);
  $pubQuotaGB = \Components\Projects\Helpers\Html::convertSize($pubQuota, 'b', 'GB', 2);
  $sysgroup   = $config->get('group_prefix', 'pr-') . $model->get('alias');

  // Compute status text
  if ($model->isActive()) {
      $statusCls  = 'badge-success';
      $statusText = Lang::txt('COM_PROJECTS_ACTIVE')
          . ' ' . Lang::txt('COM_PROJECTS_SINCE')
          . ' ' . Date::of($model->get('created'))->toLocal('M d, Y');
  } elseif ($model->isDeleted()) {
      $statusCls  = 'badge-error';
      $statusText = Lang::txt('COM_PROJECTS_DELETED');
  } elseif ($model->inSetup()) {
      $statusCls  = 'badge-info';
      $statusText = Lang::txt('Setup') . ' ' . Lang::txt('in progress');
  } elseif ($model->isInactive()) {
      $statusCls  = 'badge-ghost';
      $text = $suspended ? Lang::txt('COM_PROJECTS_SUSPENDED') : Lang::txt('COM_PROJECTS_INACTIVE');
      $statusText = $text;
      if ($suspended) {
          $statusText .= ' (' . ($suspended == 1
              ? Lang::txt('COM_PROJECTS_BY_ADMIN')
              : Lang::txt('COM_PROJECTS_BY_PROJECT_MANAGER')) . ')';
      }
  } elseif ($model->isPending()) {
      $statusCls  = 'badge-warning';
      $statusText = Lang::txt('COM_PROJECTS_PENDING_APPROVAL');
  } elseif ($model->isArchived()) {
      $statusCls  = 'badge-warning';
      $statusText = Lang::txt('Archived');
  } else {
      $statusCls  = 'badge-ghost';
      $statusText = '';
  }

  // Groups for ownership
  $groups = \Hubzero\User\Helper::getGroups($model->get('owned_by_user'), 'members', 1);
  if ($model->groupOwner()) {
      $groups[] = $model->groupOwner();
  }

  $titleStr = Lang::txt('Projects')
      . ': ' . $model->get('title')
      . ' (' . $model->get('alias') . ', #' . $model->get('id') . ')';

  Toolbar::title($titleStr, 'projects');
  if (User::authorise('core.edit', $option)) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();

  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
  $gitgcUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=gitgc&id=' . $model->get('id'), false
  );
  $eraseUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=erase&id=' . $model->get('id'), false
  );
@endphp

@foreach($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<form action="{!! $formAction !!}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform form-validate"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_20rem] gap-6">

    {{-- Left column --}}
    <div class="space-y-6">

      {{-- Basic Info --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_BASIC_INFO') }}">

        <div class="admin-field">
          <label for="title" class="label label-required">
            {{ Lang::txt('COM_PROJECTS_TITLE') }}
          </label>
          <input type="text"
                 name="title"
                 id="title"
                 class="input input-bordered w-full required"
                 maxlength="250"
                 value="{{ $model->get('title') }}" />
        </div>

        <div class="admin-field">
          <label for="alias" class="label">{{ Lang::txt('COM_PROJECTS_ALIAS') }}</label>
          <input type="text"
                 name="alias"
                 id="alias"
                 class="input input-bordered w-full bg-base-200"
                 maxlength="250"
                 value="{{ $model->get('alias') }}"
                 readonly disabled />
        </div>

        <div class="admin-field">
          <label for="about" class="label">{{ Lang::txt('COM_PROJECTS_ABOUT') }}</label>
          {!! $__view->editor('about', $model->about('raw'), 35, 25, 'about') !!}
        </div>

        <div class="admin-field">
          <label for="tags" class="label">{{ Lang::txt('COM_PROJECTS_TAGS') }}</label>
          @php
            $tf = Event::trigger(
                'hubzero.onGetMultiEntry',
                [['tags', 'tags', 'actags', '', $tags]]
            );
          @endphp
          @if(count($tf) > 0)
            {!! $tf[0] !!}
          @else
            <input type="text"
                   name="tags"
                   id="tags"
                   class="input input-bordered w-full"
                   value="{{ $tags }}" />
          @endif
        </div>

        @if(\Hubzero\Facades\Plugin::isEnabled('projects', 'tools') || $publishing)
          <div class="admin-field">
            <label for="field-type" class="label">{{ Lang::txt('COM_PROJECTS_TYPE') }}</label>
            <select name="type" id="field-type" class="select select-bordered w-full">
              @foreach($types as $type)
                @if(($type->id == 3 && !$publishing) || ($type->id == 2 && !\Hubzero\Facades\Plugin::isEnabled('projects', 'tools')))
                  @continue
                @endif
                <option value="{{ $type->id }}"
                        @selected($type->id == $model->get('type'))>
                  {{ $type->type }}
                </option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="admin-field">
          <label for="owned_by_user" class="label">
            {{ Lang::txt('COM_PROJECTS_OWNER_LEAD') }}
          </label>
          <select name="owned_by_user" id="owned_by_user" class="select select-bordered w-full">
            @php $ownerId = $model->get('owned_by_user'); @endphp
            @foreach($model->team(['status' => 1], true) as $member)
              <option value="{{ $member->userid }}"
                      @selected($member->userid == $ownerId)>
                {{ $member->fullname }}
                @if($member->userid == $ownerId)
                  ({{ Lang::txt('PLG_PROJECTS_TEAM_CURRENT_OWNER') }})
                @endif
              </option>
            @endforeach
          </select>
        </div>

        @if(!empty($groups))
          <div class="admin-field">
            <label for="owned_by_group" class="label">
              {{ Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOOSE_GROUP') }}
            </label>
            <select name="owned_by_group" id="owned_by_group" class="select select-bordered w-full">
              <option value="0" @selected(!$model->groupOwner())>
                {{ Lang::txt('PLG_PROJECTS_TEAM_NO_GROUP') }}
              </option>
              @php $usedGroups = []; @endphp
              @foreach($groups as $g)
                @if(in_array($g->gidNumber, $usedGroups))
                  @continue
                @endif
                @php $usedGroups[] = $g->gidNumber; @endphp
                <option value="{{ $g->gidNumber }}"
                        @selected($g->gidNumber == $model->get('owned_by_group'))>
                  {{ \Hubzero\Utility\Str::truncate($g->description, 30) }} ({{ $g->cn }})
                </option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="admin-field">
          <label class="label">{{ Lang::txt('COM_PROJECTS_SYS_GROUP') }}</label>
          <p class="text-sm text-muted-foreground mt-1">{{ $sysgroup }}</p>
        </div>

      </x-admin-fieldset>

      {{-- Parameters --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_PARAMETERS') }}">

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_PROJECTS_PRIVACY') }}</label>
          <select name="access" id="field-access" class="select select-bordered w-full">
            {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $model->get('access')) !!}
          </select>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer gap-2 justify-start">
            <input type="hidden" name="params[team_public]" value="0" />
            <input type="checkbox"
                   name="params[team_public]"
                   id="param-team_public"
                   value="1"
                   class="checkbox checkbox-sm"
                   @checked($params->get('team_public')) />
            <span>{{ Lang::txt('COM_PROJECTS_TEAM_PUBLIC') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label class="label cursor-pointer gap-2 justify-start">
            <input type="hidden" name="params[publications_public]" value="0" />
            <input type="checkbox"
                   name="params[publications_public]"
                   id="param-publications_public"
                   value="1"
                   class="checkbox checkbox-sm"
                   @checked($params->get('publications_public')) />
            <span>{{ Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC') }}</span>
          </label>
        </div>

        <div class="admin-field">
          <label for="param-layout" class="label">{{ Lang::txt('COM_PROJECTS_LAYOUT') }}</label>
          <select name="params[layout]" id="param-layout" class="select select-bordered w-full">
            <option value="standard" @selected($params->get('layout', 'standard') === 'standard')>
              {{ Lang::txt('COM_PROJECTS_LAYOUT_STANDARD') }}
            </option>
            <option value="extended" @selected($params->get('layout') === 'extended')>
              {{ Lang::txt('COM_PROJECTS_LAYOUT_EXTENDED') }}
            </option>
          </select>
        </div>

        @if($config->get('restricted_data', 0))
          <div class="admin-field">
            <label class="label">{{ Lang::txt('COM_PROJECTS_SENSITIVE_DATA') }}</label>
            <p class="text-sm mt-1">
              <span class="font-medium">{{ strtoupper($params->get('restricted_data', 'no')) }}</span>
              @if($params->get('restricted_data') === 'yes')
                <span class="text-muted-foreground">
                  (
                  @if($params->get('hipaa_data') === 'yes') HIPAA @endif
                  @if($params->get('ferpa_data') === 'yes') FERPA @endif
                  @if($params->get('export_data') === 'yes') Export Controlled @endif
                  @if($params->get('irb_data') === 'yes') IRB @endif
                  )
                </span>
              @endif
            </p>
          </div>
        @endif

        @if($config->get('grantinfo', 0))
          <div class="admin-field">
            <label for="param-grant_title" class="label">
              {{ Lang::txt('COM_PROJECTS_TERMS_GRANT_TITLE') }}
            </label>
            <input type="text"
                   name="params[grant_title]"
                   id="param-grant_title"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ html_entity_decode($params->get('grant_title', '')) }}" />
          </div>
          <div class="admin-field">
            <label for="param-grant_PI" class="label">
              {{ Lang::txt('COM_PROJECTS_TERMS_GRANT_PI') }}
            </label>
            <input type="text"
                   name="params[grant_PI]"
                   id="param-grant_PI"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ html_entity_decode($params->get('grant_PI', '')) }}" />
          </div>
          <div class="admin-field">
            <label for="param-award_number" class="label">
              {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER') }}
            </label>
            <input type="text"
                   name="params[award_number]"
                   id="param-award_number"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ html_entity_decode($params->get('award_number', '')) }}" />
          </div>
          <div class="admin-field">
            <label for="param-grant_agency" class="label">
              {{ Lang::txt('COM_PROJECTS_TERMS_GRANT_AGENCY') }}
            </label>
            <input type="text"
                   name="params[grant_agency]"
                   id="param-grant_agency"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ html_entity_decode($params->get('grant_agency', '')) }}" />
          </div>
          <div class="admin-field">
            <label for="param-grant_budget" class="label">
              {{ Lang::txt('COM_PROJECTS_TERMS_GRANT_BUDGET') }}
            </label>
            <input type="text"
                   name="params[grant_budget]"
                   id="param-grant_budget"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ html_entity_decode($params->get('grant_budget', '')) }}" />
          </div>
          <div class="admin-field">
            <label class="label">{{ Lang::txt('COM_PROJECTS_TERMS_GRANT_APPROVAL_CODE') }}</label>
            <p class="text-sm mt-1">
              @php $approval = html_entity_decode($params->get('grant_approval', '')); @endphp
              {{ $approval ?: Lang::txt('COM_PROJECTS_NA') }}
            </p>
          </div>
        @endif

      </x-admin-fieldset>

      {{-- Files (only if project is past setup) --}}
      @if(!$model->inSetup())
        <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_FILES') }}">

          <div class="admin-field">
            <label for="param-quota" class="label">
              {{ Lang::txt('Files Quota') }}
              <span class="text-muted-foreground">({{ Lang::txt('COM_PROJECTS_FILES_GBYTES') }})</span>
            </label>
            <input type="text"
                   name="params[quota]"
                   id="param-quota"
                   class="input input-bordered w-32"
                   maxlength="100"
                   value="{{ $quotaGB }}" />
          </div>

          <div class="admin-field">
            <label for="param-pubQuota" class="label">
              {{ Lang::txt('Publications Quota') }}
              <span class="text-muted-foreground">({{ Lang::txt('COM_PROJECTS_FILES_GBYTES') }})</span>
            </label>
            <input type="text"
                   name="params[pubQuota]"
                   id="param-pubQuota"
                   class="input input-bordered w-32"
                   maxlength="100"
                   value="{{ $pubQuotaGB }}" />
          </div>

          @if($diskusage)
            <div class="admin-field">
              {!! $diskusage !!}
            </div>
          @endif

          <div class="admin-field text-sm">
            {{ Lang::txt('Maintenance options:') }}
            <a href="{{ $gitgcUrl }}" class="link link-primary ml-2">git gc --aggressive</a>
            <span class="text-muted-foreground ml-1">[{{ Lang::txt('Takes minutes to run') }}]</span>
          </div>

        </x-admin-fieldset>
      @endif

      {{-- Erase notice --}}
      <div class="alert alert-warning text-sm flex items-center gap-3">
        <a href="{{ $eraseUrl }}" class="btn btn-sm btn-warning">
          {{ Lang::txt('COM_PROJECTS_ERASE_PROJECT') }}
        </a>
        {{ Lang::txt('COM_PROJECTS_ERASE_NOTICE') }}
      </div>

    </div>

    {{-- Right column --}}
    <div class="space-y-6">

      {{-- Meta table --}}
      @if($model->get('id'))
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_PROJECTS_CREATED') }}</td>
              <td>
                {{ $model->get('created') }}<br />
                <span class="text-xs text-muted-foreground">
                  {{ Lang::txt('COM_PROJECTS_BY') }}
                  {{ $model->creator('name') }}
                  ({{ $model->creator('username') }})
                </span>
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PROJECTS_STATUS') }}</td>
              <td>
                <span class="badge {{ $statusCls }} badge-sm">{{ $statusText }}</span>
              </td>
            </tr>
            @if(isset($counts['files']))
              <tr>
                <td>{{ Lang::txt('COM_PROJECTS_FILES') }}</td>
                <td>{{ $counts['files'] }}</td>
              </tr>
            @endif
            @if(isset($counts['publications']))
              <tr>
                <td>{{ Lang::txt('COM_PROJECTS_PUBLICATIONS') }}</td>
                <td>{{ $counts['publications'] }}</td>
              </tr>
            @endif
            @if(isset($counts['todo']))
              <tr>
                <td>{{ Lang::txt('COM_PROJECTS_TODOS') }}</td>
                <td>
                  {{ $counts['todo'] }}
                  @if(!empty($counts['todos_completed']) && $counts['todos_completed'] > 0)
                    <span class="text-muted-foreground text-xs">
                      (+{{ $counts['todos_completed'] }} {{ Lang::txt('COM_PROJECTS_TODOS_COMPLETED') }})
                    </span>
                  @endif
                </td>
              </tr>
            @endif
            @if(isset($counts['notes']))
              <tr>
                <td>{{ Lang::txt('COM_PROJECTS_NOTES') }}</td>
                <td>{{ $counts['notes'] }}</td>
              </tr>
            @endif
            @if(isset($counts['activity']))
              <tr>
                <td>{{ Lang::txt('COM_PROJECTS_ACTIVITIES_IN_FEED') }}</td>
                <td>{{ $counts['activity'] }}</td>
              </tr>
            @endif
            <tr>
              <td>{{ Lang::txt('COM_PROJECTS_LAST_ACTIVITY') }}</td>
              <td>
                @if($last_activity)
                  @php
                    $activity = preg_replace('/said/', 'posted an update', $last_activity->description);
                    $activity = preg_replace('/&#58;/', '', $activity);
                    $timeAgo  = \Components\Projects\Helpers\Html::timeAgo($last_activity->created);
                  @endphp
                  {{ $last_activity->created }}
                  <span class="text-xs text-muted-foreground">
                    ({{ $timeAgo }} {{ Lang::txt('COM_PROJECTS_AGO') }})
                  </span>
                  <br />
                  <span class="text-xs font-medium">{{ $last_activity->creator->name }}</span>
                  {{ $activity }}
                @else
                  {{ Lang::txt('COM_PROJECTS_NA') }}
                @endif
              </td>
            </tr>
          </tbody>
        </table>
      @endif

      {{-- Status actions --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_STATUS') }}">

        <div class="admin-field">
          <label for="message" class="label">{{ Lang::txt('COM_PROJECTS_MESSAGE') }}</label>
          <textarea name="message"
                    id="message"
                    rows="4"
                    class="textarea textarea-bordered w-full text-sm">
          </textarea>
        </div>

        <div class="admin-field">
          <p class="label mb-2 text-base-content">{{ Lang::txt('COM_PROJECTS_OPTIONS') }}</p>
          <input type="hidden" name="admin_action" value="" />
          <div class="flex flex-wrap gap-2">
            <button type="submit"
                    id="do-message"
                    class="btn btn-sm btn-outline">
              {{ Lang::txt('COM_PROJECTS_OPTION_SEND_MESSAGE') }}
            </button>
            @if($model->isActive())
              <button type="submit"
                      id="do-suspend"
                      class="btn btn-sm btn-warning btn-outline">
                {{ Lang::txt('COM_PROJECTS_OPTION_SUSPEND') }}
              </button>
            @elseif($model->isInactive() || $model->isDeleted())
              <button type="submit"
                      id="do-reinstate"
                      class="btn btn-sm btn-success btn-outline">
                {{ $suspended
                    ? Lang::txt('COM_PROJECTS_OPTION_REINSTATE')
                    : Lang::txt('COM_PROJECTS_OPTION_ACTIVATE') }}
              </button>
            @endif
            @if(!$model->isDeleted())
              <button type="submit"
                      id="do-delete"
                      class="btn btn-sm btn-error btn-outline">
                {{ Lang::txt('COM_PROJECTS_OPTION_DELETE') }}
              </button>
            @endif
            @if($model->isArchived())
              <button type="submit"
                      id="do-unarchive"
                      class="btn btn-sm btn-ghost btn-outline">
                {{ Lang::txt('COM_PROJECTS_OPTION_UNARCHIVE') }}
              </button>
            @else
              <button type="submit"
                      id="do-archive"
                      class="btn btn-sm btn-ghost btn-outline">
                {{ Lang::txt('COM_PROJECTS_OPTION_ARCHIVE') }}
              </button>
            @endif
          </div>
        </div>

      </x-admin-fieldset>

      {{-- Team summary --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_TEAM') . ' (' . ($counts['team'] ?? 0) . ')' }}">

        <table class="w-full text-sm">
          <tbody>
            <tr>
              <th class="text-left font-medium text-muted-foreground py-1 pr-3 w-1/2">
                {{ Lang::txt('COM_PROJECTS_MANAGERS') }}
              </th>
              <td class="py-1">{{ $managers ?: Lang::txt('COM_PROJECTS_NA') }}</td>
            </tr>
            <tr>
              <th class="text-left font-medium text-muted-foreground py-1 pr-3">
                {{ Lang::txt('COM_PROJECTS_COLLABORATORS') }}
              </th>
              <td class="py-1">{{ $members ?: Lang::txt('COM_PROJECTS_NA') }}</td>
            </tr>
            <tr>
              <th class="text-left font-medium text-muted-foreground py-1 pr-3">
                {{ Lang::txt('COM_PROJECTS_AUTHORS') }}
              </th>
              <td class="py-1">{{ $authors ?: Lang::txt('COM_PROJECTS_NA') }}</td>
            </tr>
            <tr>
              <th class="text-left font-medium text-muted-foreground py-1 pr-3">
                {{ Lang::txt('COM_PROJECTS_REVIEWERS') }}
              </th>
              <td class="py-1">{{ $reviewers ?: Lang::txt('COM_PROJECTS_NA') }}</td>
            </tr>
          </tbody>
        </table>

        <div class="border-t border-base-300 pt-3 mt-3">
          <p class="text-sm font-medium mb-2">{{ Lang::txt('COM_PROJECTS_ADD_MEMBER') }}</p>
          <div class="admin-field">
            <label for="newmember" class="label">
              {{ Lang::txt('COM_PROJECTS_ADD_MEMBER_USERNAME') }}
            </label>
            <input type="text"
                   name="newmember"
                   id="newmember"
                   class="input input-bordered input-sm w-full" />
          </div>
          <div class="admin-field">
            <label for="field-role" class="label">
              {{ Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE') }}
            </label>
            <select name="role" id="field-role" class="select select-bordered select-sm w-full">
              <option value="1">{{ Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_MANAGER') }}</option>
              <option value="0">{{ Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_COLLABORATOR') }}</option>
            </select>
          </div>
        </div>

      </x-admin-fieldset>

      {{-- Project image (if set) --}}
      @if($model->get('picture'))
        <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_IMAGE') }}">
          <div class="flex flex-col items-center gap-2">
            <img src="{{ $model->picture('thumb') }}"
                 width="50"
                 alt="{{ Lang::txt('COM_PROJECTS_IMAGE_THUMB') }}"
                 class="rounded" />
            <p class="text-xs text-muted-foreground">
              {{ Lang::txt('COM_PROJECTS_IMAGE_THUMB') }}
            </p>
          </div>
        </x-admin-fieldset>
      @endif

    </div>
  </div>

  <input type="hidden" name="id" value="{{ $model->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="apply" />
  {!! Html::input('token') !!}
</form>
