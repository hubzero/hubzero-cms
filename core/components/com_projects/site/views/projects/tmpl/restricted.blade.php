{{--
 * Restricted data pre-setup question (PHI check)
 *
 * Variables:
 *   $option  - Component option string
 *   $title   - Page title
 *   $model   - Project model object
 *   $msg     - Status message string
 *   $gid     - Group ID
 *   $group   - Group object (optional)
 *
 * Bugs fixed from original:
 *   - Used $model instead of $this->project (was inconsistent with other views)
 *   - Removed unclosed </span> inside <h4> on original line 76
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()->js();

    $pageTitle = $model->get('title')
        ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . e($model->get('title'))
        : $title;

    $formAction = Route::url('index.php?option=' . $option);
@endphp

<h2>
    {{ $pageTitle }}
    @if ($gid && is_object($group))
        {{ Lang::txt('COM_PROJECTS_FOR') }}
        {{ ucfirst(Lang::txt('COM_PROJECTS_GROUP')) }}
        <a href="{{ Route::url('index.php?option=com_groups&cn=' . $group->get('cn')) }}">
            {{ \Hubzero\Utility\Str::truncate($group->get('description'), 50) }}
        </a>
    @endif
</h2>

<section class="main section" id="setup">
    @if ($__view->getError())
        <p class="alert alert-error">{{ $__view->getError() }}</p>
    @elseif ($msg)
        <p>{{ $msg }}</p>
    @endif

    <form id="hubForm" method="post" action="{{ $formAction }}">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8">
                <fieldset class="w-full">
                    <input type="hidden" name="task" value="setup" />
                    <input type="hidden" name="step" value="1" />
                    <input type="hidden" name="save_stage" value="0" />
                    <input type="hidden" id="option" name="option" value="{{ $option }}" />
                    <input type="hidden" id="pid" name="id" value="{{ $model->get('id') }}" />
                    <input type="hidden" id="gid" name="gid" value="{{ $gid }}" />
                    <input type="hidden" name="proceed" value="1" />

                    <h2>{{ Lang::txt('COM_PROJECTS_SETUP_BEFORE_WE_START') }}</h2>
                    <h4 class="text-lg font-medium">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI') }}</h4>

                    <label class="terms-label font-semibold">
                        <input
                            class="radio radio-primary restricted-answer"
                            name="restricted"
                            id="f-restricted-no"
                            type="radio"
                            value="no"
                            checked="checked"
                        />
                        {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO') }}
                    </label>
                    <label class="terms-label font-semibold">
                        <input
                            class="radio radio-primary restricted-answer"
                            name="restricted"
                            id="f-restricted-yes"
                            type="radio"
                            value="yes"
                        />
                        {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES_NOT_SURE') }}
                    </label>
                    <div id="f-restricted-explain" class="alert alert-warning mt-2">
                        {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_EXPLAIN') }}
                    </div>

                    <div class="flex gap-2 mt-6">
                        <input
                            type="submit"
                            value="{{ Lang::txt('COM_PROJECTS_CONTINUE') }}"
                            class="btn btn-primary"
                            id="btn-preform"
                        />
                    </div>
                </fieldset>
            </div>
            <div class="md:col-span-4">
                <div class="card bg-base-200/50 p-4">
                    <h4>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_WHY') }}</h4>
                    <p>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_BECAUSE') }}</p>
                </div>
            </div>
        </div>
    </form>
</section>
