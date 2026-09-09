{{--
 * Setup wizard step 1: Describe project (name, alias, about, privacy)
 *
 * Variables:
 *   $model   - Project model object
 *   $option  - Component option string
 *   $title   - Page title
 *   $step    - Current step number
 *   $section - Active section string
 *   $msg     - Status message string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()->js()->js('setup')->css('jquery.fancybox.css', 'system');

    $formAction = Route::url('index.php?option=' . $option);
    $verifyUrl = Route::url('index.php?option=com_projects&task=verify&no_html=1&ajax=1&text=');
    $suggestUrl = Route::url('index.php?option=com_projects&task=suggestalias&no_html=1&ajax=1&text=');
    $saveUrl = Route::url('index.php?option=' . $option . '&task=save&id=' . $model->get('id'));
@endphp

@include('setup::_title', [
    'model'  => $model,
    'step'   => $step,
    'option' => $option,
    'title'  => $title,
])

<section class="main section" id="setup">
    @include('projects::_statusmsg', [
        'error' => $__view->getError(),
        'msg'   => $msg,
    ])

    @include('setup::_metadata', [
        'model'  => $model,
        'step'   => $step,
        'option' => $option,
    ])

    @include('setup::_steps', [
        'model'  => $model,
        'step'   => $step,
        'option' => $option,
    ])

    <div>
        <form id="hubForm" method="post" action="{{ $formAction }}" enctype="multipart/form-data">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_PROJECTS_PICK_NAME') }}</h3>

                    @include('setup::_form', [
                        'model'      => $model,
                        'step'       => $step,
                        'option'     => $option,
                        'controller' => 'setup',
                        'section'    => $section,
                    ])
                    <input type="hidden" name="extended" id="extended" value="0" />
                    <input type="hidden" name="verified" id="verified" value="0" />

                    <div class="form-group">
                        <label class="label" for="field-title">
                            <span class="label-text">
                                {{ Lang::txt('COM_PROJECTS_TITLE') }}
                                <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
                            </span>
                        </label>
                        <span class="verification"></span>
                        <input
                            name="title"
                            maxlength="250"
                            id="field-title"
                            type="text"
                            value="{{ e($model->get('title')) }}"
                            class="input input-bordered w-full verifyme"
                        />
                        <div class="label">
                            <span class="label-text-alt">{!! Lang::txt('COM_PROJECTS_HINTS_TITLE') !!}</span>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="label" for="field-alias">
                            <span class="label-text">
                                {{ Lang::txt('COM_PROJECTS_ALIAS_NAME') }}
                                <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
                            </span>
                        </label>
                        <span class="verification"></span>
                        <input
                            name="name"
                            maxlength="30"
                            id="field-alias"
                            type="text"
                            value="{{ $model->get('alias') }}"
                            @if ($model->get('id')) disabled="disabled" @endif
                            class="input input-bordered w-full verifyme"
                            data-verify="{{ $verifyUrl }}"
                            data-suggest="{{ $suggestUrl }}"
                        />
                        <div class="label">
                            <span class="label-text-alt">{{ Lang::txt('COM_PROJECTS_HINTS_NAME') }}</span>
                        </div>
                    </div>

                    <div id="moveon" class="nogo">
                        <div class="mt-6">
                            <input
                                type="submit"
                                value="{{ Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE') }}"
                                class="btn disabled"
                                disabled="disabled"
                            />
                        </div>
                    </div>

                    <div id="describe">
                        <h2 class="text-xl font-semibold mt-6 mb-2">{{ Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT') }}</h2>
                        <p class="mb-4">{{ Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NOW_OR_LATER') }}</p>
                        <div class="flex gap-2">
                            <a href="{{ $saveUrl }}" id="next_desc" class="btn btn-success">
                                {{ Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_YES') }}
                            </a>
                            <a href="{{ $saveUrl }}" id="next_step" class="btn">
                                {{ Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NO') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="card bg-base-200/50">
                        <div class="card-body">
                            <h4 class="card-title text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_TITLE_NAME_PROJECT') }}</h4>
                            <p class="text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_NAME_PROJECT') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="describearea" class="mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT') }}</h3>

                        <div class="form-group">
                            <label class="label" for="field-about">
                                <span class="label-text">
                                    {{ Lang::txt('COM_PROJECTS_ABOUT') }}
                                    <span class="badge badge-ghost badge-sm">{{ Lang::txt('OPTIONAL') }}</span>
                                </span>
                            </label>
                            {!! $__view->editor(
                                'about',
                                e($model->about('raw')),
                                35,
                                25,
                                'field-about',
                                ['class' => 'form-control minimal no-footer']
                            ) !!}
                        </div>

                        <h3 class="text-lg font-semibold mt-8 mb-4">{{ Lang::txt('COM_PROJECTS_SETTING_APPEAR_IN_SEARCH') }}</h3>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input
                                    class="radio radio-primary"
                                    name="access"
                                    type="radio"
                                    value="5"
                                    @checked($model->get('access') == 5)
                                />
                                <span class="label-text">{{ Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE') }}</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input
                                    class="radio radio-primary"
                                    name="access"
                                    type="radio"
                                    value="1"
                                    @checked($model->get('access') != 5)
                                />
                                <span class="label-text">{!! Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC') !!}</span>
                            </label>
                        </div>

                        @if ($model->get('id'))
                            <div class="js mt-8">
                                <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_PROJECTS_ADD_PICTURE') }}</h3>

                                @include('setup::_picture', [
                                    'model'  => $model,
                                    'step'   => $step,
                                    'option' => $option,
                                ])
                            </div>
                        @endif

                        <div class="mt-6">
                            <input
                                type="submit"
                                value="{{ Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE') }}"
                                class="btn btn-success"
                                id="gonext"
                            />
                        </div>
                    </div>

                    <div class="lg:col-span-1 space-y-4">
                        <div class="card bg-base-200/50">
                            <div class="card-body">
                                <h4 class="card-title text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_TITLE_DESC') }}</h4>
                                <p class="text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_DESC_PROJECT') }}</p>
                            </div>
                        </div>

                        @if ($model->get('id'))
                            <div class="card bg-base-200/50">
                                <div class="card-body">
                                    <h4 class="card-title text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_TITLE_THUMB') }}</h4>
                                    <p class="text-sm">{{ Lang::txt('COM_PROJECTS_HOWTO_THUMB') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
