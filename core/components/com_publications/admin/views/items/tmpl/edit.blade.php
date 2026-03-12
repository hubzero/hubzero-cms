{{--
  Publications — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Publications\Helpers\Permissions::getActions('item');
  $site  = str_replace('/administrator', '', rtrim(Request::base(), DS));

  $text = ($task == 'edit'
      ? Lang::txt('JACTION_EDIT') . ' #' . $model->get('id') . ' (v.' . $model->get('version_label') . ')'
      : Lang::txt('JACTION_CREATE'));

  Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': ' . $text, 'publications');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();

  $__view->css()->js();


  // Get pub category
  $rt = $model->category();

  // Parse metadata fields
  $data = [];
  preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $model->get('metadata', ''), $matches, PREG_SET_ORDER);
  foreach ($matches as $match) {
      $data[$match[1]] = $match[2];
  }
  $customFields = $model->_curationModel->getMetaSchema();
  $elements     = new \Components\Publications\Models\Elements($data, $customFields);
  $fields       = $elements->render();

  $status  = $model->getStatusName();
  $rating  = $model->get('master_rating') == 9.9 ? 0.0 : $model->get('master_rating');
  $params  = $model->params;

  // Type panels config
  $typeParams = $model->masterType()->_params;
  $panels = [
      'authors'   => $typeParams->get('show_authors',   2),
      'audience'  => $typeParams->get('show_audience',  0),
      'gallery'   => $typeParams->get('show_gallery',   1),
      'tags'      => $typeParams->get('show_tags',      1),
      'license'   => $typeParams->get('show_license',   2),
      'notes'     => $typeParams->get('show_notes',     1),
      'metadata'  => $typeParams->get('show_metadata',  1),
      'submitter' => $typeParams->get('show_submitter', 0),
  ];

  // Publish dates
  $pubUp   = $model->version->published_up;
  $pubUpVal = ($pubUp && $pubUp != '0000-00-00 00:00:00')
      ? Date::of($pubUp)->toLocal('Y-m-d H:i:s')
      : '';
  $pubDown = $model->version->published_down;
  $downVal = (strtolower($pubDown ?? '') != strtolower(Lang::txt('COM_PUBLICATIONS_NEVER'))
      && $pubDown && $pubDown != '0000-00-00 00:00:00')
      ? Date::of($pubDown)->toLocal('Y-m-d H:i:s')
      : '';

  // Bundle
  $archiveUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=archive&pid=' . $model->get('id')
      . '&vid=' . $model->get('version_id')
      . '&version=' . $model->versionAlias(),
      false, false
  );
  $serveUrl = trim($site, DS) . '/publications/'
      . $model->get('id') . DS . 'serve' . DS
      . $model->get('version_number')
      . '/?render=archive';

  // Unpublish reason
  $reason  = $model->get('unpublished_reason');
  $naTxt   = Lang::txt('COM_PUBLICATIONS_UNPUBLISHED_NOT_AVAILABLE');
  $errTxt  = Lang::txt('COM_PUBLICATIONS_UNPUBLISHED_ERROR');
  $reasonVal = ($reason != $naTxt && $reason != $errTxt) ? $reason : '';
@endphp

@foreach ($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform form-validate"
      data-confirmreset="{{ Lang::txt('COM_PUBLICATIONS_CONFIRM_RATINGS_RESET') }}">

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_22rem] gap-6">

    {{-- ─── Left column ─────────────────────────────────────── --}}
    <div class="space-y-6">

      {{-- Details --}}
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

        <div class="admin-field">
          <label for="field-title" class="label text-base-content">
            {{ Lang::txt('COM_PUBLICATIONS_FIELD_TITLE') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="title"
                 id="field-title"
                 class="input input-bordered w-full"
                 maxlength="250"
                 required
                 value="{{ $model->get('title') }}" />
        </div>

        <div class="admin-field">
          <label for="category" class="label text-base-content">
            {{ Lang::txt('COM_PUBLICATIONS_FIELD_CATEGORY') }}
            <span class="text-error">*</span>
          </label>
          @php
            $__view->view('_selectcategory')
                ->set('categories', $model->category()->getContribCategories())
                ->set('value', $model->get('category'))
                ->set('name', 'category')
                ->set('showNone', '')
                ->display();
          @endphp
        </div>

        <div class="admin-field">
          <label for="field-alias" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') }}</label>
          <input type="text"
                 name="alias"
                 id="field-alias"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="{{ $model->get('alias') }}" />
        </div>

        <div class="admin-field">
          <label for="pub-abstract" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_SYNOPSIS') }}</label>
          <textarea name="abstract"
                    id="pub-abstract"
                    class="textarea textarea-bordered w-full"
                    rows="3">{{ preg_replace("/\r\n/", "\r", trim($model->get('abstract'))) }}</textarea>
        </div>

        <div class="admin-field">
          <label for="pub_description" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DESCRIPTION') }}</label>
          {!! $__view->editor(
              'description',
              e($model->get('description')),
              '40', '10', 'pub_description'
          ) !!}
        </div>

      </x-admin-fieldset>

      {{-- Metadata --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_METADATA') }}">
        @if($fields)
          {!! $fields !!}
        @else
          <p class="text-muted-foreground text-sm">{{ Lang::txt('COM_PUBLICATIONS_NO_METADATA_FIELDS') }}</p>
        @endif
      </x-admin-fieldset>

      {{-- Notes --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_NOTES') }}">
        <div class="admin-field">
          <label for="notes" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_NOTES') }}</label>
          {!! $__view->editor(
              'release_notes',
              e($model->get('release_notes', '')),
              '20', '10', 'notes',
              ['class' => 'minimal no-footer']
          ) !!}
        </div>
      </x-admin-fieldset>

      {{-- Authors --}}
      @php
        $addAuthorUrl = Route::url(
            'index.php?option=' . $option
            . '&controller=' . $controller
            . '&task=addauthor&pid=' . $model->get('id')
            . '&vid=' . $model->get('version_id'), false
        );
      @endphp
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_AUTHORS') }}">
        <div class="flex justify-between items-center mb-2">
          <span></span>
          <a href="{!! $addAuthorUrl !!}" class="btn btn-sm btn-ghost">
            {{ Lang::txt('COM_PUBLICATIONS_ADD_AUTHOR') }}
          </a>
        </div>
        <div id="publication-authors">
          @php
            $__view->view('_selectauthors')
                ->set('authNames', $model->authors())
                ->set('option', $option)
                ->display();
          @endphp
        </div>
      </x-admin-fieldset>

      {{-- Tags --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_TAGS') }}">
        <div class="admin-field">
          @php
            $tf = Event::trigger(
                'hubzero.onGetMultiEntry',
                [['tags', 'tags', 'actags', '', $model->getTagsForEditing(0, 0, true)]]
            );
          @endphp
          @if(count($tf) > 0)
            {!! $tf[0] !!}
          @else
            <input type="text"
                   name="tags"
                   id="actags"
                   class="input input-bordered w-full"
                   value="{{ $model->getTagsForEditing() }}" />
          @endif
        </div>
      </x-admin-fieldset>

      {{-- License --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_LICENSE') }}">
        <div class="admin-field">
          <label for="license_type" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_LICENSE_TYPE') }}</label>
          @php
            $__view->view('_selectlicense')
                ->set('licenses', $licenses)
                ->set('selected', $model->license())
                ->display();
          @endphp
        </div>
        <div class="admin-field">
          <label for="license_text" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_LICENSE_TEXT') }}</label>
          <textarea name="license_text"
                    id="license_text"
                    class="textarea textarea-bordered w-full font-mono text-sm"
                    rows="5">{{ preg_replace("/\r\n/", "\r", trim($model->get('license_text', ''))) }}</textarea>
        </div>
      </x-admin-fieldset>

      {{-- Disable download --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_DISABLE_DOWNLOAD_LINK') }}">
        <div class="admin-field">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox"
                   name="disabledownloadlink"
                   id="disabledownloadlink"
                   class="checkbox checkbox-sm"
                   {{ $model->version->downloadDisabled ? 'checked' : '' }} />
            {{ Lang::txt('COM_PUBLICATIONS_FIELD_DISABLE_DOWNLOAD_DESCRIPTION') }}
          </label>
        </div>
      </x-admin-fieldset>

    </div>

    {{-- ─── Right column ────────────────────────────────────── --}}
    <div class="space-y-6">

      {{-- Meta table --}}
      <table class="admin-meta">
        <tbody>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_ID') }}</td>
            <td>{{ $model->get('id') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_CREATED') }}</td>
            <td>{{ $model->created('date') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_CREATOR') }}</td>
            <td>{{ $model->creator()->get('name', Lang::txt('(unknown)')) }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_PROJECT') }}</td>
            <td>{{ $model->project()->get('title') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_TYPE') }}</td>
            <td>{{ $model->_type->type }}</td>
          </tr>
          @if($model->isPublished() || $model->isUnpublished())
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_RANKING') }}</td>
              <td>
                {{ $model->get('master_ranking') }}/10
                @if($model->get('master_ranking') != '0')
                  <button type="button" name="reset_ranking" id="reset_ranking" class="btn btn-xs btn-ghost ml-1">
                    {{ Lang::txt('Reset ranking') }}
                  </button>
                @endif
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_RATING') }}</td>
              <td>
                {{ $rating }}/5.0 ({{ $model->get('master_times_rated') }} reviews)
                @if($rating != '0.0')
                  <button type="button" name="reset_rating" id="reset_rating" class="btn btn-xs btn-ghost ml-1">
                    {{ Lang::txt('Reset rating') }}
                  </button>
                @endif
              </td>
            </tr>
          @endif
        </tbody>
      </table>

      {{-- Version --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_VERSION') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_VERSION_ID') }}</td>
              <td>{{ $model->get('version_id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_VERSION') }}</td>
              <td>
                <input type="text"
                       name="version_label"
                       id="field-version_label"
                       class="input input-bordered input-sm w-24"
                       maxlength="250"
                       aria-label="{{ Lang::txt('COM_PUBLICATIONS_FIELD_VERSION') }}"
                       value="{{ $model->get('version_label') }}" />
                <span class="text-sm text-muted-foreground">({{ $status }})</span>
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_URL') }}</td>
              <td>
                @php
                  $pubUrl = trim($site, '/') . '/publications/'
                      . $model->get('id') . '/'
                      . $model->get('version_number');
                @endphp
                <a href="{{ $pubUrl }}" class="link link-hover text-primary text-xs break-all">
                  {{ $pubUrl }}
                </a>
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_MODIFIED') }}</td>
              <td>{{ $model->modified('date') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_MODIFIED_BY') }}</td>
              <td>{{ $model->modifier()->get('name', Lang::txt('(unknown)')) }}</td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>

      {{-- Publishing --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label for="field-published" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_STATUS') }}</label>
          <select name="state" id="field-published" class="select select-bordered select-sm w-full">
            <option value="3" {{ $model->get('state') == 3 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT') }}
            </option>
            <option value="4" {{ $model->get('state') == 4 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_READY') }}
            </option>
            <option value="5" {{ $model->get('state') == 5 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_PENDING') }}
            </option>
            <option value="7" {{ $model->get('state') == 7 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_WIP') }}
            </option>
            <option value="1" {{ $model->get('state') == 1 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED') }}
            </option>
            <option value="0" {{ $model->get('state') == 0 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED') }}
            </option>
            <option value="2" {{ $model->get('state') == 2 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_VERSION_DELETED') }}
            </option>
          </select>
        </div>

        <div class="admin-field" id="unPubReasonDiv">
          <label for="field-unPubReason" class="label text-base-content">
            {{ Lang::txt('COM_PUBLICATIONS_FIELD_UNPUBLISHED_REASON') }}
          </label>
          <select name="unPubReasonDropdownList" id="field-unPubReason"
                  class="select select-bordered select-sm w-full" disabled>
            <option value="0" {{ ($reason != $naTxt && $reason != $errTxt) ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_UNPUBLISHED_OTHERS') }}
            </option>
            <option value="1" {{ ($reason == null || $reason == $naTxt) ? 'selected' : '' }}>
              {{ $naTxt }}
            </option>
            <option value="2" {{ $reason == $errTxt ? 'selected' : '' }}>
              {{ $errTxt }}
            </option>
          </select>
        </div>

        <div class="admin-field" id="reasonDiv">
          <label for="reason" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ASK_REASON') }}</label>
          <textarea name="reason" id="reason"
                    class="textarea textarea-bordered w-full text-sm"
                    rows="4"
                    disabled>{{ $reasonVal }}</textarea>
        </div>

        <div class="admin-field">
          <label for="field-featured" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_FEATURED') }}</label>
          <select name="featured" id="field-featured" class="select select-bordered select-sm w-full">
            <option value="0" {{ $model->get('featured') == 0 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_NO') }}
            </option>
            <option value="1" {{ $model->get('featured') == 1 ? 'selected' : '' }}>
              {{ Lang::txt('COM_PUBLICATIONS_YES') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="access" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ACCESS') }}</label>
          @php
            $__view->view('_selectaccess')
                ->set('as', 'Public,Registered,Private')
                ->set('value', $model->get('master_access'))
                ->display();
          @endphp
        </div>

        @if(isset($groups))
          <div class="admin-field">
            <label for="group_owner" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_GROUP_OWNER') }}</label>
            @php
              $__view->view('_selectgroup')
                  ->set('groups', $groups)
                  ->set('groupOwner', $model->project()->groupOwner())
                  ->set('value', ($model->groupOwner() ? $model->groupOwner()->gidNumber : 0))
                  ->display();
            @endphp
          </div>
        @endif

        <div class="admin-field">
          <label for="published_up" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_PUBLISH_DATE') }}</label>
          {!! Html::input('calendar', 'published_up', $pubUpVal) !!}
        </div>

        <div class="admin-field">
          <label for="publish_down" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_UNPUBLISH_DATE') }}</label>
          {!! Html::input('calendar', 'published_down', $downVal, ['placeholder' => Lang::txt('COM_PUBLICATIONS_NEVER')]) !!}
        </div>

        <div class="admin-field">
          <label for="doi" class="label text-base-content">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DOI') }}</label>
          <input type="text"
                 id="doi"
                 name="doi"
                 class="input input-bordered input-sm w-full"
                 value="{{ $model->doi ?? '' }}" />
        </div>

        {{-- Submitter/dates/bundle info --}}
        <table class="admin-meta mt-2">
          <tbody>
            @if($model->submitter())
              <tr>
                <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTER') }}</td>
                <td>{{ $model->submitter()->name }}</td>
              </tr>
            @endif
            @if($model->isPending())
              <tr>
                <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTED') }}</td>
                <td>{{ $model->submitted }}</td>
              </tr>
            @elseif($model->isPublished() || $model->isUnpublished())
              @if($model->submitted())
                <tr>
                  <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_SUBMITTED') }}</td>
                  <td>{!! $model->submitted('datetime') !!}</td>
                </tr>
              @endif
              @if($model->accepted())
                <tr>
                  <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_ACCEPTED') }}</td>
                  <td>{!! $model->accepted('datetime') !!}</td>
                </tr>
              @endif
            @endif
            <tr>
              <td>{{ Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE') }}</td>
              <td>
                @if(file_exists($model->bundlePath()))
                  <a href="{{ $serveUrl }}" class="link link-hover text-primary text-sm archival">
                    {{ Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE') }}
                  </a>
                  &nbsp;
                  <a href="{!! $archiveUrl !!}" class="link link-hover text-sm text-muted-foreground">
                    [{{ Lang::txt('COM_PUBLICATIONS_REPACKAGE') }}]
                  </a>
                @else
                  <a href="{!! $archiveUrl !!}" class="link link-hover text-primary text-sm archival">
                    {{ Lang::txt('COM_PUBLICATIONS_PRODUCE_ARCHIVAL') }}
                  </a>
                @endif
              </td>
            </tr>
          </tbody>
        </table>

      </x-admin-fieldset>

      {{-- Management Options --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELD_MANAGEMENT_OPTIONS') }}">
        <div class="admin-field">
          <textarea name="message" id="message"
                    class="textarea textarea-bordered w-full text-sm"
                    aria-label="{{ Lang::txt('COM_PUBLICATIONS_ACTION_SEND_MESSAGE') }}"
                    rows="4"></textarea>
          <input type="hidden" name="admin_action" id="admin_action" value="" />
          <div class="flex flex-wrap gap-2 mt-2">
            <button type="submit" class="btn btn-sm btn-ghost" id="do-message">
              {{ Lang::txt('COM_PUBLICATIONS_ACTION_SEND_MESSAGE') }}
            </button>
            @if($model->isPublished())
              <button type="submit" class="btn btn-sm btn-ghost" id="do-unpublish">
                {{ Lang::txt('COM_PUBLICATIONS_ACTION_UNPUBLISH_VERSION') }}
              </button>
            @elseif($model->isUnpublished())
              <button type="submit" class="btn btn-sm btn-ghost" id="do-republish">
                {{ Lang::txt('COM_PUBLICATIONS_ACTION_REPUBLISH_VERSION') }}
              </button>
            @elseif($model->isPending())
              <button type="submit" class="btn btn-sm btn-success" id="do-publish">
                {{ Lang::txt('COM_PUBLICATIONS_ACTION_APPROVE_AND_PUBLISH') }}
              </button>
              <button type="submit" class="btn btn-sm btn-ghost" id="do-revert">
                {{ Lang::txt('COM_PUBLICATIONS_ACTION_REVERT_TO_DRAFT') }}
              </button>
            @endif
          </div>
        </div>
      </x-admin-fieldset>

      {{-- Parameters accordion --}}
      <details class="collapse collapse-arrow bg-base-200 border border-base-300 rounded-box">
        <summary class="collapse-title text-sm font-semibold">
          {{ Lang::txt('COM_PUBLICATIONS_FIELDSET_PARAMETERS') }}
        </summary>
        <div class="collapse-content">
          <table class="table table-sm w-full mt-2">
            <tbody>
              @foreach($panels as $panel => $defaultVal)
                <tr>
                  <td class="font-medium text-sm">{{ ucfirst($panel) }}</td>
                  <td>
                    <select name="params[show_{{ $panel }}]"
                            class="select select-bordered select-xs">
                      <option value="0"
                              {{ $params->get('show_' . $panel, $defaultVal) == 0 ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_HIDE') }}
                      </option>
                      <option value="1"
                              {{ $params->get('show_' . $panel, $defaultVal) > 0 ? 'selected' : '' }}>
                        {{ Lang::txt('COM_PUBLICATIONS_SHOW') }}
                      </option>
                    </select>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </details>

      {{-- Content --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_PUBLICATIONS_FIELDSET_CONTENT') }}">
        @php
          $__view->view('_selectcontent')
              ->set('pub', $model)
              ->set('option', $option)
              ->display();
        @endphp
      </x-admin-fieldset>

    </div>
  </div>

  <input type="hidden" name="id" value="{{ $model->get('id') }}" />
  <input type="hidden" name="version" value="{{ $model->versionAlias() }}" />
  <input type="hidden" name="isnew" value="{{ $isnew ?? 0 }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  {!! Html::input('token') !!}
</form>
