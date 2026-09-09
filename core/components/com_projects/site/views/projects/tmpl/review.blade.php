{{--
 * Admin reviewer form (sponsored/sensitive)
 *
 * Variables:
 *   $option   - Component option string
 *   $model    - Project model object
 *   $reviewer - Reviewer type string (sponsored|sensitive)
 *   $params   - Project params Registry
 *   $filterby - Filter string
 *   $ajax     - Whether this is an AJAX request (bool)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    if ($reviewer == 'sponsored') {
        $title = Lang::txt('COM_PROJECTS_REVIEW_PROJECT_SPS');
        $approved = $params->get('grant_approval') || $params->get('grant_status') == 1 ? 1 : 0;
        $b_action = $approved
            ? Lang::txt('COM_PROJECTS_SAVE_SPS_APPROVED')
            : Lang::txt('COM_PROJECTS_SAVE_SPS');
    } else {
        $title = $model->isPending()
            ? Lang::txt('COM_PROJECTS_REVIEW_PROJECT_HIPAA')
            : Lang::txt('COM_PROJECTS_REVIEW_PROJECT_HIPAA_SAVE');
        $b_action = $model->isPending()
            ? Lang::txt('COM_PROJECTS_SAVE_HIPAA')
            : Lang::txt('COM_PROJECTS_SAVE');
        $approved = 0;
    }

    $notes = \Components\Projects\Helpers\Html::getAdminNotes($model->get('admin_notes'), $reviewer);

    $formAction = Route::url('index.php?option=' . $option . '&id=' . $model->get('id') . '&task=process')
        . '?reviewer=' . $reviewer;
    $formId = $ajax ? 'hubForm-ajax' : 'plg-form';
    $thumbUrl = Route::url($model->link('thumb'));
@endphp

@if (!$ajax)
    <div id="content-header">
        <h2>{{ $title }}</h2>
    </div>
@endif

<div id="abox-content" class="reviewer">
    @if ($ajax)
        <h3>{{ $title }}</h3>
    @endif

    @if ($__view->getError())
        <p class="alert alert-error">{{ $__view->getError() }}</p>
    @endif

    @if ($model->exists())
        <form action="{{ $formAction }}" method="post" id="{{ $formId }}">
            <fieldset>
                <input type="hidden" name="id" value="{{ $model->get('id') }}" />
                <input type="hidden" name="action" value="save" />
                <input type="hidden" name="task" value="process" />
                <input type="hidden" name="ajax" value="1" />
                <input type="hidden" name="no_html" value="1" />
                <input type="hidden" name="reviewer" value="{{ $reviewer }}" />
                <input type="hidden" name="filterby" value="{{ $filterby }}" />
                <input type="hidden" name="option" value="{{ $option }}" />
            </fieldset>

            <div class="flex items-start gap-4 mb-6">
                <div class="shrink-0">
                    <img src="{{ $thumbUrl }}" alt="" class="rounded w-16 h-16" />
                </div>
                <div>
                    <p class="font-semibold">
                        {{ $model->get('title') }}
                        (<span class="aliasname">{{ $model->get('alias') }}</span>)
                    </p>
                    <p class="italic">
                        {{ Lang::txt('COM_PROJECTS_CREATED_BY') . ': ' . $model->creator('name') }}
                    </p>
                </div>
            </div>

            @if ($reviewer == 'sponsored')
                <div id="spsinfo" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE') }}:
                            <input
                                name="grant_title"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('grant_title') }}"
                                class="input input-bordered w-full"
                            />
                        </label>
                        <label>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI') }}:
                            <input
                                name="grant_PI"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('grant_PI') }}"
                                class="input input-bordered w-full"
                            />
                        </label>
                        <label>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY') }}:
                            <input
                                name="grant_agency"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('grant_agency') }}"
                                class="input input-bordered w-full"
                            />
                        </label>
                        <label>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET') }}:
                            <input
                                name="grant_budget"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('grant_budget') }}"
                                class="input input-bordered w-full"
                            />
                        </label>
                        <label>
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER') }}:
                            <input
                                name="award_number"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('award_number') }}"
                                class="input input-bordered w-full"
                            />
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <label
                            for="grant_approval"
                            class="{{ $approved ? 'spsapproved' : 'spsapproval' }}"
                        >
                            {{ $approved
                                ? ucfirst(Lang::txt('COM_PROJECTS_APPROVAL_CODE_APPROVED'))
                                : Lang::txt('COM_PROJECTS_APPROVAL_CODE_PROVIDE') }}:
                            <input
                                name="grant_approval"
                                id="grant_approval"
                                maxlength="250"
                                type="text"
                                value="{{ $params->get('grant_approval') }}"
                                class="input input-bordered w-full"
                            />
                            @if (!$approved)
                                <p class="text-sm text-base-content/60">{{ Lang::txt('COM_PROJECTS_SPS_APPROVAL_HINT') }}</p>
                            @endif
                        </label>
                        @if (!$approved)
                            <div class="flex items-center gap-4">
                                <span>{{ Lang::txt('COM_PROJECTS_OR') }}</span>
                                <label for="rejected" class="font-semibold flex items-center gap-2">
                                    <input
                                        class="checkbox checkbox-primary"
                                        name="rejected"
                                        id="rejected"
                                        type="checkbox"
                                        value="1"
                                        @checked($params->get('grant_status') == 2)
                                    />
                                    {{ $params->get('grant_status') == 2
                                        ? Lang::txt('COM_PROJECTS_SPS_REJECTED_KEEP')
                                        : Lang::txt('COM_PROJECTS_SPS_REJECT') }}
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($model->isPending() && $reviewer == 'sensitive')
                <div class="mb-4">
                    <label id="sdata-approve" class="flex items-center gap-2">
                        <input class="checkbox checkbox-primary" name="approve" type="checkbox" value="1" />
                        {{ ucfirst(Lang::txt('COM_PROJECTS_APPROVE_PROJECT_CONFIRM')) }}
                    </label>
                </div>
            @endif

            <div id="newadmincomment" class="mb-6">
                <h4>
                    {{ ucfirst(Lang::txt('COM_PROJECTS_ADD_ADMIN_COMMENT')) }}
                    <span class="text-sm text-base-content/60">{{ Lang::txt('OPTIONAL') }}</span>
                </h4>
                <label>
                    <textarea name="comment" rows="4" cols="40" class="textarea textarea-bordered w-full"></textarea>
                </label>
                @if ($reviewer == 'sponsored' && !$approved)
                    <label class="flex items-center gap-2 mt-2">
                        <input class="checkbox checkbox-primary" name="notify" type="checkbox" value="1" />
                        {{ ucfirst(Lang::txt('COM_PROJECTS_REVIEWERS_ADD_ACTIVITY')) }}
                    </label>
                @endif
            </div>

            <div class="flex gap-2 mt-6">
                <input type="submit" value="{{ $b_action }}" class="btn btn-primary" />
                <input type="reset" id="cancel-action" class="btn btn-ghost" value="{{ Lang::txt('JCANCEL') }}" />
            </div>

            <div id="admincommentbox" class="mt-6">
                <h4>
                    {{ ucfirst(Lang::txt('COM_PROJECTS_REVIEWER_COMMENTS')) }}
                    <span class="text-sm text-base-content/60">{{ ucfirst(Lang::txt('COM_PROJECTS_REVIEWER_COMMENTS_LATEST_FIRST')) }}</span>
                </h4>
                @if ($notes)
                    {!! $notes !!}
                @else
                    <p class="text-base-content/60 italic">No comments found</p>
                @endif
            </div>
        </form>
    @endif
</div>
