{{--
 * Provisioned project activation form
 *
 * Variables:
 *   $option    - Component option string
 *   $title     - Page title
 *   $model     - Project model object
 *   $pub       - Publication object
 *   $team      - Team members string
 *   $suggested - Suggested alias string
 *   $verified  - Verification value
 *   $msg       - Status message string
 *
 * Bugs fixed from original:
 *   - Lang::txt(JCANCEL) → Lang::txt('JCANCEL') (missing quotes)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $route = 'index.php?option=com_publications&task=submit';
    $url   = Route::url($route . '&pid=' . $pub->id);

    $__view->css()->js()->css('provisioned')->js('setup');

    $mySubmissions = ucfirst(Lang::txt('COM_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS'));
    $truncTitle    = \Hubzero\Utility\Str::truncate($pub->title, 65);
    $featuresUrl   = Route::url('index.php?option=' . $option . '&task=features');
    $activateUrl   = Route::url('index.php?option=com_projects&alias=' . $model->get('alias') . '&task=activate');

    $tooltipTitle = Lang::txt('COM_PROJECTS_PROJECT_TITLE') . ' :: ' . Lang::txt('COM_PROJECTS_HINTS_TITLE');
    $tooltipAlias = Lang::txt('COM_PROJECTS_CHOOSE_ALIAS') . '::' . Lang::txt('COM_PROJECTS_HINTS_NAME');
@endphp

<div id="project-wrap">
    <section class="main section">
        <h2>{{ $title }}</h2>

        <h3 class="text-lg breadcrumbs">
            <a href="{{ $route }}">{{ $mySubmissions }}</a>
            &raquo;
            <a href="{{ $url }}">"{{ $truncTitle }}"</a>
            &raquo;
            {{ Lang::txt('COM_PROJECTS_PROVISIONED_PROJECT') }}
        </h3>

        @include('projects::_statusmsg', [
            'error' => $__view->getError(),
            'msg'   => $msg,
        ])

        <div id="activate-intro">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3>{{ Lang::txt('COM_PROJECTS_ACTIVATE_WHAT_YOU_GET') }}</h3>
                    <ul id="activate-features">
                        <li id="feature-files" class="flex items-center gap-2 mb-2">
                            {{ Lang::txt('COM_PROJECTS_ACTIVATE_GET_REPOSITORY') }}
                        </li>
                        <li id="feature-todo" class="flex items-center gap-2 mb-2">
                            {{ Lang::txt('COM_PROJECTS_ACTIVATE_GET_TODO') }}
                        </li>
                        <li id="feature-wiki" class="flex items-center gap-2 mb-2">
                            {{ Lang::txt('COM_PROJECTS_ACTIVATE_GET_WIKI') }}
                        </li>
                        <li id="andmore" class="flex items-center gap-2 mb-2">
                            <a href="{{ $featuresUrl }}">{!! Lang::txt('COM_PROJECTS_ACTIVATE_AND_MORE') !!}</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div id="activate-body" class="card bg-base-100 shadow-sm p-6">
                        <h3>{{ Lang::txt('COM_PROJECTS_ACTIVATE_YOUR_NEW_PROJECT') }}</h3>
                        <form
                            action="{{ $activateUrl }}"
                            method="post"
                            id="activate-form"
                            enctype="multipart/form-data"
                        >
                            <fieldset>
                                <input type="hidden" name="id" value="{{ $model->get('id') }}" id="projectid" />
                                <input type="hidden" name="task" value="activate" />
                                <input type="hidden" name="confirm" value="1" />
                                <input type="hidden" name="option" value="{{ $option }}" />
                                <input type="hidden" name="verified" id="verified" value="{{ $verified }}" />
                                <input type="hidden" name="pubid" value="{{ $pub->id }}" />
                            </fieldset>
                            <div id="activate-summary" class="mb-4">
                                <p>
                                    <span class="font-medium">Publication:</span>
                                    <span class="font-semibold">{{ $pub->title }}</span>
                                </p>
                                <p>
                                    <span class="font-medium">{{ Lang::txt('COM_PROJECTS_TEAM') }}:</span>
                                    {{ $team }}
                                </p>
                            </div>
                            <fieldset>
                                <label for="field-title">
                                    <span
                                        class="tooltip"
                                        title="{{ $tooltipTitle }}"
                                    >&nbsp;</span>
                                    {{ Lang::txt('COM_PROJECTS_PROJECT_TITLE') }}
                                    <input
                                        name="title"
                                        id="field-title"
                                        maxlength="250"
                                        type="text"
                                        value="{{ $pub->title }}"
                                        class="input input-bordered w-full"
                                    />
                                </label>

                                <label for="field-alias">
                                    <span
                                        class="tooltip"
                                        title="{{ $tooltipAlias }}"
                                    >&nbsp;</span>
                                    {{ Lang::txt('COM_PROJECTS_ALIAS_NAME') }}
                                    <span class="verification"></span>
                                    <input
                                        name="new-alias"
                                        id="field-alias"
                                        maxlength="30"
                                        type="text"
                                        value="{{ $suggested }}"
                                        class="input input-bordered w-full"
                                    />
                                </label>

                                <div class="flex gap-2 mt-6">
                                    <input
                                        type="submit"
                                        id="b-continue"
                                        class="btn btn-primary"
                                        value="{{ Lang::txt('COM_PROJECTS_ACTIVATE_CREATE_A_PROJECT') }}"
                                    />
                                    <a href="{{ $url }}" class="btn btn-ghost">{{ Lang::txt('JCANCEL') }}</a>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
