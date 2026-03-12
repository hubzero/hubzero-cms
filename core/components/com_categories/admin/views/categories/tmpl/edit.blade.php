{{--
  Categories — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $ext = Request::getCmd('extension', 'com_content');

  Toolbar::title($title, 'content');
  if ($canDo->get('core.edit')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::save2copy();
      Toolbar::save2new();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('category');

  $formAction = Route::url(
      'index.php?option=com_categories&extension=' . $ext
      . '&layout=edit&id=' . (int) $item->get('id', 0),
      false, false
  );
@endphp

<form action="{{ $formAction }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="form-validate"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Main content column --}}
    <div class="lg:col-span-7">
      <x-admin-fieldset legend="{{ Lang::txt('COM_CATEGORIES_FIELDSET_DETAILS') }}">

          <div class="admin-field">
            {!! $form->getLabel('title') !!}
            {!! $form->getInput('title') !!}
          </div>

          <div class="admin-field">
            {!! $form->getLabel('alias') !!}
            {!! $form->getInput('alias') !!}
          </div>

          <div class="admin-field">
            {!! $form->getLabel('parent_id') !!}
            {!! $form->getInput('parent_id') !!}
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="admin-field">
              {!! $form->getLabel('published') !!}
              {!! $form->getInput('published') !!}
            </div>
            <div class="admin-field">
              {!! $form->getLabel('access') !!}
              {!! $form->getInput('access') !!}
            </div>
          </div>

          <div class="admin-field">
            {!! $form->getLabel('language') !!}
            {!! $form->getInput('language') !!}
          </div>

          <div class="admin-field">
            {!! $form->getLabel('description') !!}
            {!! $form->getInput('description') !!}
          </div>

      </x-admin-fieldset>
    </div>

    {{-- Sidebar --}}
    <div class="lg:col-span-5 space-y-6">
      {{-- Meta info --}}
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_CATEGORIES_FIELD_EXTENSION') }}</td>
                <td>
                  {{ $item->get('extension') }}
                  {!! $form->getInput('extension') !!}
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_CATEGORIES_FIELD_ID') }}</td>
                <td>
                  {{ $item->get('id', 0) }}
                  <input type="hidden" name="fields[id]"
                         value="{{ $item->get('id', 0) }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_CATEGORIES_FIELD_CREATOR') }}</td>
                @php
                  $creatorId = $item->get('created_user_id');
                  $creatorName = $creatorId
                      ? User::getInstance($creatorId)->get('name', Lang::txt('(unknown)'))
                      : Lang::txt('(unknown)');
                @endphp
                <td>
                  {{ $creatorName }}
                  <input type="hidden" name="fields[created_user_id]"
                         value="{{ $creatorId }}" />
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_CATEGORIES_FIELD_CREATED') }}</td>
                <td>
                  @if($item->get('created_time'))
                    {{ Date::of($item->get('created_time'))->toLocal() }}
                  @else
                    —
                  @endif
                </td>
              </tr>
              @if($item->get('modified_time'))
                @php
                  $modifierId = $item->get('modified_user_id');
                  $modifierName = $modifierId
                      ? User::getInstance($modifierId)->get('name', Lang::txt('(unknown)'))
                      : Lang::txt('(unknown)');
                @endphp
                <tr>
                  <td>{{ Lang::txt('COM_CATEGORIES_FIELD_MODIFIER') }}</td>
                  <td>
                    {{ $modifierName }}
                    <input type="hidden" name="fields[modified_user_id]"
                           value="{{ $modifierId }}" />
                  </td>
                </tr>
                <tr>
                  <td>{{ Lang::txt('COM_CATEGORIES_FIELD_MODIFIED') }}</td>
                  <td>{{ Date::of($item->get('modified_time'))->toLocal() }}</td>
                </tr>
              @endif
            </tbody>
          </table>
      </x-admin-fieldset>

      {{-- Options (params fieldsets) --}}
      @php
        $paramFieldSets = $form->getFieldsets('params');
      @endphp
      @if($paramFieldSets && count($paramFieldSets) > 0)
        <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_BASIC') }}">
            @foreach($paramFieldSets as $fsName => $fieldSet)
              @if(isset($fieldSet->description) && trim($fieldSet->description))
                <p class="text-sm text-muted-foreground">
                  {{ Lang::txt($fieldSet->description) }}
                </p>
              @endif
              @foreach($form->getFieldset($fsName) as $field)
                <div class="admin-field">
                  {!! $field->label !!}
                  {!! $field->input !!}
                </div>
              @endforeach
              @if($fsName == 'basic')
                <div class="admin-field">
                  {!! $form->getLabel('note') !!}
                  {!! $form->getInput('note') !!}
                </div>
              @endif
            @endforeach
        </x-admin-fieldset>
      @endif

      {{-- Metadata --}}
      <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS') }}">
          <div class="admin-field">
            {!! $form->getLabel('metadesc') !!}
            {!! $form->getInput('metadesc') !!}
          </div>
          <div class="admin-field">
            {!! $form->getLabel('metakey') !!}
            {!! $form->getInput('metakey') !!}
          </div>
          @php
            $metaGroup = $form->getGroup('metadata');
          @endphp
          @if($metaGroup)
            @foreach($metaGroup as $field)
              @if($field->hidden)
                {!! $field->input !!}
              @else
                <div class="admin-field">
                  {!! $field->label !!}
                  {!! $field->input !!}
                </div>
              @endif
            @endforeach
          @endif
      </x-admin-fieldset>

      {{-- Attribs fieldsets (dynamic, from extension) --}}
      @php
        $attribFieldSets = $form->getFieldsets('attribs');
      @endphp
      @if($attribFieldSets)
        @foreach($attribFieldSets as $fsName => $fieldSet)
          @if($fsName != 'editorConfig' && $fsName != 'basic-limited')
            @php
              $fsLabel = !empty($fieldSet->label)
                  ? $fieldSet->label
                  : 'COM_CATEGORIES_' . $fsName . '_FIELDSET_LABEL';
            @endphp
            <x-admin-fieldset legend="{{ Lang::txt($fsLabel) }}">
                @if(isset($fieldSet->description) && trim($fieldSet->description))
                  <p class="text-sm text-muted-foreground">
                    {{ Lang::txt($fieldSet->description) }}
                  </p>
                @endif
                @foreach($form->getFieldset($fsName) as $field)
                  <div class="admin-field">
                    {!! $field->label !!}
                    {!! $field->input !!}
                  </div>
                @endforeach
            </x-admin-fieldset>
          @endif
        @endforeach
      @endif
    </div>
  </div>

  {{-- Permissions (admin only) --}}
  @if($canDo->get('core.admin'))
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_ACTION_PERMISSIONS_LABEL') }}" class="mt-6" body-class="">
      {!! $form->getInput('rules') !!}
    </x-admin-fieldset>
  @endif

  <input type="hidden" name="task" value="" />
  {!! Html::input('token') !!}
</form>
