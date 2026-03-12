{{--
  Article — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Content\Admin\Helpers\Permissions::getActions('article', $item->get('id'));

  $titleKey = ($task == 'edit' || $task == 'apply')
      ? 'COM_CONTENT_PAGE_EDIT_ARTICLE'
      : 'COM_CONTENT_PAGE_ADD_ARTICLE';

  Toolbar::title(Lang::txt($titleKey), 'content');
  if (
      $canDo->get('core.edit')
      || ($canDo->get('core.edit.own') && User::getInstance()->get('id') == $item->get('created_by'))
      || ($canDo->get('core.create'))
  ) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::save2copy();
      Toolbar::save2new();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('article');

  // Timezone display
  $UTC    = new DateTimeZone('UTC');
  $userTZ = new DateTimeZone(
      App::get('user')->getParam('timezone', App::get('config')->get('offset', 'UTC'))
  );

  $dateCreated = new DateTime($item->created ?: 'now', $UTC);
  $dateCreated->setTimezone($userTZ);
  $abbrTZ = $dateCreated->format('T');
  $tzLabel = $abbrTZ ? "($abbrTZ)" : '';

  $dateModified = new DateTime($item->modified ?: 'now', $UTC);
  $dateModified->setTimezone($userTZ);
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    formId="item-form"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ $item->isNew() ? Lang::txt('COM_CONTENT_NEW_ARTICLE') : Lang::txt('COM_CONTENT_EDIT_ARTICLE', $item->id) }}">

    <div class="admin-field">
      {!! $form->getLabel('title') !!}
      {!! $form->getInput('title') !!}
    </div>

    <div class="admin-field">
      {!! $form->getLabel('alias') !!}
      {!! $form->getInput('alias') !!}
    </div>

    <div class="admin-field">
      <label for="categories-field" class="label">{{ Lang::txt('COM_CONTENT_CHOOSE_CATEGORY_LABEL') }}</label>
      <select id="categories-field" name="fields[catid]"
              class="select select-bordered w-full">
        @foreach($item->categories as $category)
          @if(User::authorise('core.create', $category->asset_id))
            @php
              $isSelected = false;
              if (!$item->catid) {
                  $isSelected = (strtolower($category->alias) === 'uncategorised');
              } else {
                  $isSelected = ($category->id == $item->catid);
              }
            @endphp
            <option value="{{ $category->id }}" @selected($isSelected)>
              {{ $category->nestedTitle() }}
            </option>
          @endif
        @endforeach
      </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="admin-field">
        {!! $form->getLabel('state') !!}
        @if($canDo->get('core.edit.state'))
          {!! $form->getInput('state') !!}
        @else
          <select name="fields[state]" disabled class="select select-bordered w-full">
            <option value="{{ $item->get('state') }}">{{ $item->state }}</option>
          </select>
        @endif
      </div>
      <div class="admin-field">
        {!! $form->getLabel('access') !!}
        {!! $form->getInput('access') !!}
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <div class="admin-field">
        {!! $form->getLabel('featured') !!}
        {!! $form->getInput('featured') !!}
      </div>
      <div class="admin-field">
        {!! $form->getLabel('language') !!}
        {!! $form->getInput('language') !!}
      </div>
    </div>

    <div class="admin-field">
      {!! $form->getLabel('introtext') !!}
      {!! $form->getInput('introtext') !!}
    </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta info --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
      <table class="admin-meta">
        <tbody>
          <tr>
            <td>{{ Lang::txt('COM_CONTENT_FIELD_ID_LABEL') }}</td>
            <td>
              {{ $item->get('id', 0) }}
              <input type="hidden" name="id" value="{{ $item->get('id') }}" />
            </td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_CONTENT_FIELD_CREATED_BY_LABEL') }}</td>
            <td>
              @php
                $creatorName = $item->created_by
                    ? User::getInstance($item->created_by)->get('name')
                    : Lang::txt('JUNKNOWN');
              @endphp
              {{ $creatorName }}
              <input type="hidden" name="fields[created_by]"
                     value="{{ $item->created_by }}" />
            </td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_CONTENT_FIELD_CREATED_LABEL') }} {{ $tzLabel }}</td>
            <td>
              <time datetime="{{ $dateCreated->format('Y-m-d H:i:s') }}">
                {{ $dateCreated->format('Y-m-d H:i:s') }}
              </time>
            </td>
          </tr>
          @if($item->get('modified_by'))
            <tr>
              <td>{{ Lang::txt('COM_CONTENT_FIELD_MODIFIER_LABEL') }}</td>
              <td>
                {{ User::getInstance($item->modified_by)->get('name', Lang::txt('JUNKNOWN')) }}
                <input type="hidden" name="fields[modified_by]"
                       value="{{ $item->modified_by }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_CONTENT_FIELD_MODIFIED_LABEL') }} {{ $tzLabel }}</td>
              <td>
                <time datetime="{{ $dateModified->format('Y-m-d H:i:s') }}">
                  {{ $dateModified->format('Y-m-d H:i:s') }}
                </time>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </x-admin-fieldset>

    {{-- Publishing options --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_CONTENT_FIELDSET_PUBLISHING') }}">

      <div class="admin-field">
        <label for="fields_publish_up" class="label">{{ Lang::txt('COM_CONTENT_FIELD_PUBLISH_UP_LABEL') }} {{ $tzLabel }}</label>
        {!! $form->getInput('publish_up') !!}
      </div>

      <div class="admin-field">
        <label for="fields_publish_down" class="label">{{ Lang::txt('COM_CONTENT_FIELD_PUBLISH_DOWN_LABEL') }} {{ $tzLabel }}</label>
        {!! $form->getInput('publish_down') !!}
      </div>

      @if($item->version)
        <div class="admin-field">
          {!! $form->getLabel('version') !!}
          {!! $form->getInput('version') !!}
        </div>
      @endif

      @if($item->hits)
        <div class="admin-field">
          {!! $form->getLabel('hits') !!}
          {!! $form->getInput('hits') !!}
        </div>
      @endif

    </x-admin-fieldset>

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

      @foreach($form->getGroup('metadata') as $field)
        <div class="admin-field">
          @if(!$field->hidden)
            {!! $field->label !!}
          @endif
          {!! $field->input !!}
        </div>
      @endforeach

      <div class="admin-field">
        {!! $form->getLabel('xreference') !!}
        {!! $form->getInput('xreference') !!}
      </div>

    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="return" value="{{ Request::getCmd('return') }}" />
</x-admin-edit>
