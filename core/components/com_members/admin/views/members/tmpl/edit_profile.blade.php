{{--
  Member edit — Profile fields tab

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $fields = \Components\Members\Models\Profile\Field::all()
      ->including(['options', function ($option) {
          $option->select('*');
      }])
      ->ordered()
      ->rows();

  $access = array();
  foreach ($fields as $field) {
      $access[$field->get('name')] = $field->get('access');
  }

  // Convert to XML so we can use the Form processor
  $xml = \Components\Members\Models\Profile\Field::toXml($fields);

  $profiles = $profile->profiles()->ordered()->rows();

  // Gather data to pass to the form processor
  $data = new Hubzero\Config\Registry(
      \Components\Members\Models\Profile::collect($profiles)
  );
  $data->set('tags', $profile->tags('string'));

  foreach ($profiles as $p) {
      $d = (isset($access[$p->get('profile_key')]) ? $access[$p->get('profile_key')] : 1);
      $access[$p->get('profile_key')] = $p->get('access', $d);
  }

  // Create a new form
  Hubzero\Form\Form::addFieldPath(Component::path('com_members') . DS . 'models' . DS . 'fields');

  $form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
  $form->load($xml);
  $form->bind($data);

  $formFields = $form->getFieldset('basic');

  $rorApiBoolean = \Hubzero\Facades\Component::params('com_members')->get('rorApi');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
  {{-- Left column: Profile fields --}}
  <div class="lg:col-span-7">
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_PROFILE') }}">
      @foreach($formFields as $field)
        @php
          if (!isset($access[$field->fieldname])) {
              $access[$field->fieldname] = 1;
          }
        @endphp
        <div class="admin-field border-b border-base-200 last:border-0 pb-3 mb-3"
             id="input-{{ $field->fieldname }}"
             @if($field->description) data-hint="{{ $field->description }}" @endif>
          <div class="flex items-baseline justify-between gap-2 mb-1">
            <div class="flex-1 min-w-0">
              @if($field->hidden)
                <label for="profile_{{ $field->fieldname }}" class="label">{{ $field->fieldname }}</label>
              @else
                {!! $field->label !!}
              @endif
            </div>
            <div class="shrink-0 flex items-center gap-1">
              <label for="field-access-{{ $field->fieldname }}"
                     class="text-xs text-muted-foreground whitespace-nowrap">{{ Lang::txt('Access') }}:</label>
              <select name="profileaccess[{{ $field->fieldname }}]"
                      id="field-access-{{ $field->fieldname }}"
                      class="select select-bordered select-sm">
                {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $access[$field->fieldname]) !!}
              </select>
            </div>
          </div>
          @if($field->hidden)
            <input type="text"
                   name="{{ $field->name }}"
                   id="profile_{{ $field->fieldname }}"
                   class="input input-bordered w-full"
                   value="{{ $field->value }}" />
          @else
            {!! $field->input !!}
            @if($field->description && strtolower($field->type) !== 'orcid')
              <span class="label-text-alt text-muted-foreground text-xs">{{ $field->description }}</span>
            @endif
            @if(strtolower($field->fieldname) == 'organization' && strtolower($field->type) == 'text' && $rorApiBoolean)
              <span class="hidden rorApiAvailable"></span>
            @endif
          @endif
        </div>
      @endforeach
    </x-admin-fieldset>
  </div>

  {{-- Right column: Picture + authenticator data --}}
  <div class="lg:col-span-5">
    <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_MEDIA_PICTURE') }}">
      @if($profile->get('id'))
        @php
          $iframeSrc = Route::url(
              'index.php?option=' . $option
              . '&controller=media&tmpl=component&id='
              . $profile->get('id') . '&t=' . time(), false
          );
        @endphp
        <iframe class="w-full border-0 rounded"
                height="420"
                name="filer"
                id="filer"
                src="{!! $iframeSrc !!}"></iframe>
      @else
        <div class="alert alert-warning">
          <span>{{ Lang::txt('COM_MEMBERS_PICTURE_ADDED_LATER') }}</span>
        </div>
      @endif
    </x-admin-fieldset>

    @php
      $lnks = Hubzero\Auth\Link::find_by_user_id($profile->get('id'));
    @endphp
    @if($lnks)
      <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_AUTHENTICATOR_DATA') }}">
        @foreach($lnks as $lnk)
          @php
            $extrafields = Hubzero\Auth\Link\Data::all()
                ->whereEquals('link_id', $lnk['id'])
                ->rows();
          @endphp
          @if($extrafields->count() > 0)
            <div class="mb-4">
              <h4 class="font-medium text-sm mb-2">{{ $lnk['auth_domain_name'] }}</h4>
              @foreach($extrafields as $extrafield)
                @php
                  $fieldId = $extrafield->get('link_id')
                      . '_' . $extrafield->get('domain_key')
                      . '_' . $extrafield->get('id');
                @endphp
                <div class="admin-field">
                  <label for="{{ $fieldId }}" class="label text-xs">{{ $extrafield->get('domain_key') }}</label>
                  <input type="text"
                         name="{{ $fieldId }}"
                         id="{{ $fieldId }}"
                         class="input input-bordered input-sm w-full"
                         value="{{ $extrafield->get('domain_value') }}"
                         readonly />
                </div>
              @endforeach
            </div>
          @endif
        @endforeach
      </x-admin-fieldset>
    @endif
  </div>
</div>
