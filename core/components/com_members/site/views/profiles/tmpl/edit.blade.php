{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $__view->css();

    $profiles = $profile->profiles()->ordered()->rows();

    // Convert to XML so we can use the Form processor
    $xml = \Components\Members\Models\Profile\Field::toXml($fields, 'edit');

    // Gather data to pass to the form processor
    $data = new Hubzero\Config\Registry(
        \Components\Members\Models\Profile::collect($profiles)
    );

    // Create a new form
    Hubzero\Form\Form::addFieldPath(Hubzero\Facades\Component::path('com_members') . DS . 'models' . DS . 'fields');

    $form = new Hubzero\Form\Form('profile', array('control' => 'profile'));
    $form->load($xml);
    $form->bind($data);

    $profileFields = array();
    foreach ($profiles as $p) {
        if (isset($profileFields[$p->get('profile_key')])) {
            $values = $profileFields[$p->get('profile_key')]->get('profile_value');
            if (!is_array($values)) {
                $values = array($values);
            }
            $values[] = $p->get('profile_value');
            $profileFields[$p->get('profile_key')]->set('profile_value', $values);
        } else {
            $profileFields[$p->get('profile_key')] = $p;
        }
    }
@endphp

<x-page-container :title="$title">
    <form
        id="hubForm"
        class="edit-profile"
        method="post"
        action="{{ Route::url('index.php?option=' . $option) }}"
        enctype="multipart/form-data">

        <fieldset>
            <legend>{{ Lang::txt('Contact Information') }}</legend>
            <input type="hidden" name="id" value="{{ $profile->get('id') }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="task" value="save" />

            <label>
                {{ Lang::txt('Visibility (who has access to my profile)') }}
                {!! \Components\Members\Helpers\Html::selectAccess(
                    'access',
                    $profile->get('access'),
                    'select select-bordered'
                ) !!}
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label>
                        {{ Lang::txt('FIRST_NAME') }}:
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            name="name[first]"
                            value="{{ e($profile->get('givenName')) }}" />
                    </label>
                </div>
                <div>
                    <label>
                        {{ Lang::txt('MIDDLE_NAME') }}:
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            name="name[middle]"
                            value="{{ e($profile->get('middleName')) }}" />
                    </label>
                </div>
                <div>
                    <label>
                        {{ Lang::txt('LAST_NAME') }}:
                        <input
                            type="text"
                            class="input input-bordered w-full"
                            name="name[last]"
                            value="{{ e($profile->get('surname')) }}" />
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label>
                        {{ Lang::txt('Valid E-mail') }}:
                        <input
                            name="email"
                            id="email"
                            type="text"
                            class="input input-bordered w-full"
                            value="{{ e($profile->get('email')) }}" />
                    </label>
                </div>
                <div>
                    <label>
                        {{ Lang::txt('Confirm E-mail') }}:
                        <input
                            name="email2"
                            id="email2"
                            type="text"
                            class="input input-bordered w-full"
                            value="{{ e($profile->get('email')) }}" />
                    </label>
                </div>
            </div>
            <div class="alert alert-warning">Important! If you change your E-Mail address you <strong>must</strong>
                confirm receipt of the confirmation e-mail in order to re-activate your account.</div>
        </fieldset>

        <fieldset>
            <legend>{{ Lang::txt('Profile') }}</legend>

            @foreach ($fields as $field)
                @php
                    if (!isset($profileFields[$field->get('name')])) {
                        $profileFields[$field->get('name')] = \Components\Members\Models\Profile::blank();
                        $profileFields[$field->get('name')]->set('access', 1);
                    }

                    $fieldProfile = $profileFields[$field->get('name')];

                    $value = $fieldProfile->get('profile_value');
                    $value = $value ?: $profile->get($field->get('name'));
                    if ($field->get('type') == 'tags') {
                        $value = $profile->tags('string');
                    }

                    $formfield = $form->getField($field->get('name'));
                    $formfield->setValue($value);
                @endphp
                <div class="input-wrap">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-3">
                            {!! $formfield->label !!}
                            {!! $formfield->input !!}
                        </div>
                        <div class="md:col-span-1">
                            <label>{{ Lang::txt('COM_MEMBERS_FIELD_ACCESS') }}</label>
                            {!! \Components\Members\Helpers\Html::selectAccess(
                                'access[' . $field->get('name') . ']',
                                $field->get('access'),
                                'select select-bordered'
                            ) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </fieldset>

        <fieldset>
            <legend>{{ Lang::txt('Updates') }}</legend>

            <label for="sendEmail">
                {{ Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES') }}
                <select name="sendEmail" id="sendEmail" class="select select-bordered">
                    @php
                        $emailOptions = array(
                            '-1' => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_SELECT'),
                            '1'  => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_YES'),
                            '0'  => Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_OPT_NO')
                        );
                    @endphp
                    @foreach ($emailOptions as $key => $val)
                        <option value="{{ $key }}" @if($key == $profile->get('sendEmail')) selected @endif>{{ $val }}</option>
                    @endforeach
                </select>
                <span class="hint">{{ Lang::txt('COM_MEMBERS_PROFILE_EMAILUPDATES_EXPLANATION') }}</span>
            </label>
        </fieldset>

        <fieldset id="memberpicture">
            <legend>{{ Lang::txt('MEMBER_PICTURE') }}</legend>
            @php
                $iframeSrc = Route::url(
                    'index.php?option=' . $option
                    . '&controller=media&tmpl=component&file='
                    . stripslashes($profile->get('picture'))
                    . '&amp;id=' . $profile->get('id')
                );
            @endphp
            <iframe
                width="100%"
                height="350"
                border="0"
                name="filer"
                id="filer"
                src="{{ $iframeSrc }}"></iframe>
        </fieldset>

        {!! Html::input('token') !!}
        <p class="submit">
            <input class="btn btn-success" type="submit" name="submit" value="{{ Lang::txt('SAVE') }}" />
            <a class="btn btn-secondary" href="{{ Route::url('index.php?option=' . $option . '&task=cancel&id=' . $profile->get('id')) }}">{{ Lang::txt('CANCEL') }}</a>
        </p>
    </form>
</x-page-container>
