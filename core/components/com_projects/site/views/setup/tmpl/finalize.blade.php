{{--
 * Setup wizard step 3: Finalize (terms, restricted data, grants)
 *
 * Variables:
 *   $model   - Project model object
 *   $option  - Component option string
 *   $title   - Page title
 *   $step    - Current step number
 *   $section - Active section string
 *   $msg     - Status message string
 *   $config  - Component config Registry
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()->js()->js('setup')->css('jquery.fancybox.css', 'system');

    $privacylink = $config->get('privacylink', '/legal/privacy');
    $hipaa       = $config->get('HIPAAlink', 'http://www.hhs.gov/ocr/privacy/');
    $ferpa       = $config->get('FERPAlink', 'http://www2.ed.gov/policy/gen/reg/ferpa/index.html');

    $formAction = Route::url('index.php?option=' . $option);
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

    <form id="hubForm" method="post" action="{{ $formAction }}">
        @include('setup::_form', [
            'model'      => $model,
            'step'       => $step,
            'option'     => $option,
            'controller' => 'setup',
            'section'    => $section,
        ])

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8">
                <fieldset>
                    <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_SETUP_BEFORE_COMPLETE') }}</legend>

                    <div class="alert alert-info">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_WHY_ASK') }}</div>

                    @if ($config->get('restricted_data', 0) == 1)
                        <h4 class="text-lg font-medium mt-4">
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI') }}
                            <span class="badge badge-error badge-sm">{{ Lang::txt('REQUIRED') }}</span>
                        </h4>
                        <div class="form-group form-check">
                            <label for="restricted-yes" class="terms-label font-semibold form-check-label">
                                <input
                                    class="radio radio-primary restricted-answer"
                                    name="restricted"
                                    id="restricted-yes"
                                    type="radio"
                                    value="yes"
                                    @checked($model->params->get('restricted_data') == 'yes')
                                />
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES') }}
                            </label>
                        </div>

                        <div class="pl-6 mt-2" id="restricted-choice">
                            <p class="text-sm text-base-content/60 font-semibold">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_MAKE_CHOICE') }}
                            </p>

                            <div class="form-group form-check">
                                <label for="export" class="terms-label form-check-label">
                                    <input
                                        class="checkbox checkbox-primary restricted-opt"
                                        name="export"
                                        id="export"
                                        type="checkbox"
                                        value="yes"
                                        @checked($model->params->get('export_data') == 'yes')
                                    />
                                    {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_EXPORT_CONTROLLED') }}
                                </label>
                            </div>
                            <div id="stop-export" class="alert alert-error mt-2 hidden">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_EXPORT') }}
                            </div>

                            <div class="form-group form-check">
                                <label for="irb" class="terms-label form-check-label">
                                    <input
                                        class="checkbox checkbox-primary restricted-opt"
                                        name="irb"
                                        id="irb"
                                        type="checkbox"
                                        value="yes"
                                        @checked($model->params->get('irb_data') == 'yes')
                                    />
                                    {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_IRB') }}
                                </label>
                            </div>
                            <div id="stop-irb" class="alert alert-warning mt-2 hidden">
                                <h5>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE') }}</h5>
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB') }}
                                <div class="form-group form-check">
                                    <label for="agree_irb" class="form-check-label">
                                        <input
                                            class="checkbox checkbox-primary"
                                            name="agree_irb"
                                            id="agree_irb"
                                            type="checkbox"
                                            value="1"
                                        />
                                        {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_IRB_AGREE') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group form-check">
                                <label for="hipaa" class="form-check-label terms-label">
                                    <input
                                        class="checkbox checkbox-primary restricted-opt"
                                        name="hipaa"
                                        id="hipaa"
                                        type="checkbox"
                                        value="yes"
                                        @checked($model->params->get('hipaa_data') == 'yes')
                                    />
                                    {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_HIPAA') }}
                                </label>
                            </div>
                            <div id="stop-hipaa" class="alert alert-error mt-2 hidden">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_HIPAA') }}
                            </div>

                            <div class="form-group form-check">
                                <label for="ferpa" class="form-check-label terms-label">
                                    <input
                                        class="checkbox checkbox-primary restricted-opt"
                                        name="ferpa"
                                        id="ferpa"
                                        type="checkbox"
                                        value="yes"
                                        @checked($model->params->get('ferpa_data') == 'yes')
                                    />
                                    {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PROJECT_WILL_INVOLVE_FERPA') }}
                                </label>
                            </div>
                            <div id="stop-ferpa" class="alert alert-warning mt-2 hidden">
                                <h5>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_MUST_ACKNOWLEDGE') }}</h5>
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA') }}
                                <div class="form-group form-check">
                                    <label for="agree_ferpa" class="form-check-label">
                                        <input
                                            class="checkbox checkbox-primary"
                                            name="agree_ferpa"
                                            id="agree_ferpa"
                                            type="checkbox"
                                            value="1"
                                        />
                                        {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_STOP_FERPA_AGREE') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <label class="terms-label font-semibold form-check-label">
                                <input
                                    class="radio radio-primary restricted-answer"
                                    name="restricted"
                                    id="restricted-no"
                                    type="radio"
                                    value="no"
                                    @checked($model->params->get('restricted_data') == 'no')
                                />
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO') }}
                            </label>
                        </div>
                    @endif

                    @if ($config->get('restricted_data', 0) == 2)
                        <h4 class="text-lg font-medium mt-4">
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI') }}
                            <span class="badge badge-error badge-sm">{{ Lang::txt('REQUIRED') }}</span>
                        </h4>
                        <div class="form-group form-check">
                            <label for="restricted" class="terms-label font-semibold form-check-label">
                                <input
                                    class="checkbox checkbox-primary"
                                    name="restricted"
                                    id="restricted"
                                    type="checkbox"
                                    value="no"
                                />
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_SENSITIVE_DATA_AGREE') }}
                            </label>
                        </div>
                    @endif
                </fieldset>

                @if ($config->get('grantinfo', 0))
                    <fieldset>
                        <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO') }}</legend>

                        <div class="mb-4">
                            <div class="form-group form-check">
                                <label for="grant_info-no" class="form-check-label">
                                    <input
                                        class="radio radio-primary"
                                        name="grant_info"
                                        id="grant_info-no"
                                        type="radio"
                                        value="0"
                                        checked="checked"
                                    /> {{ Lang::txt('JNO') }}
                                </label>
                            </div>
                            <div class="form-group form-check">
                                <label for="grant_info-yes" class="form-check-label">
                                    <input
                                        class="radio radio-primary"
                                        name="grant_info"
                                        id="grant_info-yes"
                                        type="radio"
                                        value="1"
                                    /> {{ Lang::txt('JYES') }}
                                </label>
                            </div>
                        </div>
                        <div class="hidden" id="grant_info_block">
                            <div class="alert alert-info">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY') }}</div>
                            @foreach (['grant_title' => 'COM_PROJECTS_SETUP_TERMS_GRANT_TITLE', 'grant_PI' => 'COM_PROJECTS_SETUP_TERMS_GRANT_PI', 'award_number' => 'COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER', 'grant_agency' => 'COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY', 'grant_budget' => 'COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'] as $paramKey => $langKey)
                                <div class="mb-4">
                                    <label class="terms-label">
                                        {{ Lang::txt($langKey) }}:
                                        <input
                                            name="{{ $paramKey }}"
                                            maxlength="250"
                                            type="text"
                                            class="input input-bordered w-full"
                                            value="{{ $model->params->get($paramKey) }}"
                                        />
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <fieldset>
                    <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS') }}</legend>

                    <div class="form-group form-check">
                        <label class="form-check-label terms-label">
                            <h4>
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE') }}
                                <a href="{{ $privacylink }}" rel="external">
                                    {{ Lang::txt('COM_PROJECTS_SETUP_TERMS') }}
                                </a>?
                                <span class="badge badge-error badge-sm">{{ Lang::txt('REQUIRED') }}</span>
                            </h4>
                            <input
                                class="checkbox checkbox-primary"
                                name="agree"
                                type="checkbox"
                                value="1"
                            />
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_AGREE') }}
                            <a href="{{ $privacylink }}" rel="external">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS') }}
                            </a>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_AGREE_PROJECT') }}
                            <span class="font-semibold">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_ALL_MEMBERS') }}</span>.
                        </label>
                    </div>
                </fieldset>

                <div class="flex gap-2 mt-6">
                    <input
                        type="submit"
                        value="{{ Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE') }}"
                        class="btn btn-success"
                        id="btn-finalize"
                    />
                </div>
            </div>
            <div class="md:col-span-4">
                <div class="card bg-base-200/50 p-4">
                    @if ($config->get('restricted_data', 0))
                        <h4>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE') }}</h4>
                        <p>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_RULE_EXPLAIN') }}</p>
                        <p>
                            {{ Lang::txt('COM_PROJECTS_SETUP_MORE_ON') }}
                            <a href="{{ $hipaa }}" rel="external">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_HIPAA') }}</a>.
                            {{ Lang::txt('COM_PROJECTS_SETUP_MORE_ON') }}
                            <a href="{{ $ferpa }}" rel="external nofollow">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_FERPA') }}</a>.
                        </p>
                        <div class="alert alert-info">{{ Lang::txt('COM_PROJECTS_ERROR_SETUP_TERMS_NOTE') }}</div>
                    @else
                        <h4>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_WHY') }}</h4>
                        <p>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_PRIVACY_BECAUSE') }}
                            <a href="{{ $privacylink }}" rel="external nofollow">
                                {{ Lang::txt('COM_PROJECTS_SETUP_TERMS') }}
                            </a>.
                        </p>
                    @endif
                </div>
                @if ($config->get('grantinfo', 0))
                    <div class="card bg-base-200/50 p-4 mt-6">
                        <h4>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_WHY') }}</h4>
                        <p>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANTINFO_BECAUSE') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>
