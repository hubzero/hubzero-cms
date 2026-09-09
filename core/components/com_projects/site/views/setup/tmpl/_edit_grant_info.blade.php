{{--
 * Grant information fieldset partial
 *
 * Variables:
 *   $model - Project model object
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;

    $approved = ($model->params->get('grant_status') == 1) ? 1 : 0;
    $hasGrantInfo = false;

    if (
        $model->params->get('grant_PI')
        || $model->params->get('grant_title')
        || $model->params->get('grant_agency')
        || $model->params->get('grant_budget')
        || $approved
    ) {
        $hasGrantInfo = true;
    }
@endphp

<fieldset>
    <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO') }}</legend>

    <div class="mb-4">
        <div class="mb-4 form-check">
            <label for="grant_info-no" class="form-check-label">
                <input
                    class="radio radio-primary form-check-input"
                    name="grant_info"
                    id="grant_info-no"
                    type="radio"
                    value="0"
                    @checked(!$hasGrantInfo)
                /> {{ Lang::txt('JNO') }}
            </label>
        </div>
        <div class="mb-4 form-check">
            <label for="grant_info-yes" class="form-check-label">
                <input
                    class="radio radio-primary form-check-input"
                    name="grant_info"
                    id="grant_info-yes"
                    type="radio"
                    value="1"
                    @checked($hasGrantInfo)
                /> {{ Lang::txt('JYES') }}
            </label>
        </div>
    </div>

    <div class="grant_info @if (!$hasGrantInfo) hidden @endif" id="grant_info_block">
        @if ($approved)
            <p class="alert alert-success">
                {{ Lang::txt('COM_PROJECTS_GRANT_APPROVED_WITH_CODE') }}
                <span class="font-semibold">
                    {{ htmlentities(html_entity_decode($model->params->get('grant_approval', 'N/A'))) }}
                </span>
            </p>
        @else
            <p>{{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY') }}</p>
        @endif

        @foreach (['grant_title' => 'COM_PROJECTS_SETUP_TERMS_GRANT_TITLE', 'grant_PI' => 'COM_PROJECTS_SETUP_TERMS_GRANT_PI', 'award_number' => 'COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER', 'grant_agency' => 'COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY', 'grant_budget' => 'COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'] as $paramKey => $langKey)
            <div class="mb-4">
                <label for="param-{{ $paramKey }}" class="terms-label">
                    {{ Lang::txt($langKey) }}:
                    @if ($approved)
                        <span class="font-semibold">
                            {{ htmlentities(html_entity_decode($model->params->get($paramKey, 'N/A'))) }}
                        </span>
                    @else
                        @php
                            $val = $model->params->get($paramKey);
                            $val = htmlentities(html_entity_decode($val == null ? '' : $val));
                        @endphp
                        <input
                            name="params[{{ $paramKey }}]"
                            id="param-{{ $paramKey }}"
                            class="input input-bordered w-full"
                            maxlength="250"
                            type="text"
                            value="{{ $val }}"
                        />
                    @endif
                </label>
            </div>
        @endforeach

        @if (!$approved)
            <div class="mb-4">
                <div class="mb-4 form-check">
                    <label for="param-grant_status" class="form-check-label">
                        <input
                            class="checkbox checkbox-primary form-check-input"
                            name="params[grant_status]"
                            id="param-grant_status"
                            type="checkbox"
                            value="0"
                            @checked($model->params->get('grant_status') == 2)
                        />
                        @if ($model->params->get('grant_status') == 2)
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_RESUBMIT_FOR_APPROVAL') }}
                        @else
                            {{ Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_NOTIFY_ADMIN') }}
                        @endif
                    </label>
                </div>
            </div>
        @endif
    </div>
</fieldset>
