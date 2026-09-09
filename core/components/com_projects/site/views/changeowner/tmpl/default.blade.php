{{--
 * Change project owner — keep or delete form
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $formAction = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
    );
@endphp

<div id="project-wrap">
    <section class="main section">
        @include('projects::_header', [
            'model'         => $model,
            'showPic'       => 0,
            'showPrivacy'   => 0,
            'goBack'        => 0,
            'showUnderline' => 1,
            'option'        => $option,
        ])

        <p class="alert alert-warning">{{ Lang::txt('COM_PROJECTS_INFO_OWNER_DELETED') }}</p>

        <form method="post" action="{{ $formAction }}" id="hubForm">
            <fieldset>
                <legend class="text-lg font-semibold">
                    {{ Lang::txt('COM_PROJECTS_OWNER_DELETED_OPTIONS') }}
                </legend>

                <input type="hidden" name="id" value="{{ $model->get('id') }}" />
                <input type="hidden" name="task" value="fixownership" />
                <input type="hidden" name="option" value="{{ $option }}" />

                <div class="form-group form-check">
                    <label for="keep1" class="form-check-label flex items-center gap-2">
                        <input
                            class="radio radio-primary"
                            name="keep"
                            type="radio"
                            id="keep1"
                            value="1"
                            checked="checked"
                        />
                        {{ Lang::txt('COM_PROJECTS_OWNER_KEEP_PROJECT') }}
                    </label>
                </div>

                <div class="form-group form-check">
                    <label for="keep0" class="form-check-label flex items-center gap-2">
                        <input
                            class="radio radio-primary"
                            name="keep"
                            type="radio"
                            id="keep0"
                            value="0"
                        />
                        {{ Lang::txt('COM_PROJECTS_OWNER_DELETE_PROJECT') }}
                    </label>
                </div>

                <div class="flex gap-2 mt-6">
                    <input
                        type="submit"
                        class="btn btn-primary"
                        value="{{ Lang::txt('COM_PROJECTS_SAVE_MY_CHOICE') }}"
                    />
                </div>
            </fieldset>
        </form>
    </section>
</div>
