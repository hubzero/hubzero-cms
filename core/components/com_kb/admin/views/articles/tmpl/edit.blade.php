{{--
  KB Article — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Kb\Admin\Helpers\Permissions::getActions('article');
  $text  = ($task == 'edit') ? Lang::txt('COM_KB_EDIT') : Lang::txt('COM_KB_NEW');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_KB') }}: {{ Lang::txt('COM_KB_ARTICLE') }}: {{ $text }}"
    icon="kb"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_KB_DETAILS') }}">

      <div class="admin-field">
        <label for="field-category" class="label">
          {{ Lang::txt('COM_KB_CATEGORY') }}
          <span class="text-error">*</span>
        </label>
        {!! \Components\Kb\Admin\Helpers\Html::categories(
            $categories,
            $row->get('category'),
            'fields[category]',
            'field-category',
            'class="select select-bordered w-full" required'
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_KB_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="255"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_KB_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->get('alias', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_KB_ALIAS_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-fulltxt" class="label">
          {{ Lang::txt('COM_KB_BODY') }}
          <span class="text-error">*</span>
        </label>
        {!! $__view->editor(
            'fields[fulltxt]',
            e($row->get('fulltxt', '')),
            60,
            30,
            'field-fulltxt',
            ['class' => 'required', 'buttons' => ['pagebreak', 'readmore', 'article']]
        ) !!}
      </div>

      <div class="admin-field">
        <label for="field-tags" class="label">{{ Lang::txt('COM_KB_TAGS') }}</label>
        <textarea name="tags"
                  id="field-tags"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ $row->tags('string') }}</textarea>
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_KB_FIELD_TAGS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Metadata table --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_KB_DETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_KB_ID') }}</td>
              <td>
                {{ $row->get('id', 0) }}
                <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_KB_CREATED') }}</td>
              <td>
                <time datetime="{{ $row->get('created') }}">
                  {{ Date::of($row->get('created'))->toSql() }}
                </time>
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_KB_CREATOR') }}</td>
              <td>{{ $row->creator->get('name', Lang::txt('COM_KB_UNKNOWN')) }}</td>
            </tr>
            @php
              $modified   = $row->get('modified');
              $isModified = !$row->isNew() && $modified && $modified != '0000-00-00 00:00:00';
            @endphp
            @if($isModified)
              <tr>
                <td>{{ Lang::txt('COM_KB_LAST_MODIFIED') }}</td>
                <td>
                  <time datetime="{{ $modified }}">
                    {{ Date::of($modified)->toSql() }}
                  </time>
                </td>
              </tr>
              @php $modifier = User::getInstance($row->get('modified_by')); @endphp
              @if(is_object($modifier))
                <tr>
                  <td>{{ Lang::txt('COM_KB_MODIFIER') }}</td>
                  <td>{{ $modifier->get('name', Lang::txt('COM_KB_UNKNOWN')) }}</td>
                </tr>
              @endif
            @endif
            <tr>
              <td>{{ Lang::txt('COM_KB_HITS') }}</td>
              <td>{{ $row->get('hits', 0) }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_KB_VOTES') }}</td>
              <td>
                <span class="text-success-dark">+{{ $row->get('helpful', 0) }}</span>
                <span class="text-error">-{{ $row->get('nothelpful', 0) }}</span>
              </td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing state --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_KB_STATE') }}">
        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_KB_PUBLISH') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_KB_ACCESS_LEVEL') }}</label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $row->get('access')) !!}
          </select>
      </x-admin-fieldset>
    </div>

    {{-- Parameters --}}
    @if(isset($params) && $params)
      <x-admin-fieldset legend="{{ Lang::txt('COM_KB_PARAMETERS') }}">
          {!! $params->render() !!}
      </x-admin-fieldset>
    @endif
  @endslot
</x-admin-edit>
