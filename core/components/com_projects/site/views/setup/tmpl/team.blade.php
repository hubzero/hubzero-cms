{{--
 * Setup wizard step 2: Add team members
 *
 * Variables:
 *   $model   - Project model object
 *   $option  - Component option string
 *   $title   - Page title
 *   $step    - Current step number
 *   $section - Active section string
 *   $msg     - Status message string
 *   $content - Team plugin content HTML
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
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8">
                <fieldset>
                    <legend class="text-lg font-semibold">{{ Lang::txt('COM_PROJECTS_ADD_TEAM') }}</legend>
                    @include('setup::_form', [
                        'model'      => $model,
                        'step'       => $step,
                        'option'     => $option,
                        'controller' => 'setup',
                        'section'    => $section,
                    ])
                    <div id="cbody">
                        {!! $content !!}
                    </div>
                </fieldset>
                <div class="flex gap-2 mt-6">
                    <input
                        type="submit"
                        value="{{ Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE') }}"
                        class="btn btn-success"
                        id="gonext"
                    />
                </div>
            </div>
            <div class="md:col-span-4">
                <div class="card bg-base-200/50 p-4">
                    <h4>{{ Lang::txt('COM_PROJECTS_HOWTO_TITLE_ROLES') }}</h4>
                    <p>
                        <span class="italic font-semibold">{{ ucfirst(Lang::txt('COM_PROJECTS_LABEL_OWNERS')) }}</span>
                        {{ Lang::txt('COM_PROJECTS_CAN') }}:
                    </p>
                    <ul>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE') }}</li>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO') }}</li>
                        <li><strong>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE') }}</strong></li>
                    </ul>
                    <p>
                        <span class="italic font-semibold">{{ ucfirst(Lang::txt('COM_PROJECTS_LABEL_COLLABORATORS')) }}</span>
                        {{ Lang::txt('COM_PROJECTS_CAN') }}:
                    </p>
                    <ul>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE') }}</li>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO') }}</li>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE') }}</li>
                    </ul>
                    <p>
                        <span class="italic font-semibold">{{ ucfirst(Lang::txt('COM_PROJECTS_LABEL_REVIEWER')) }}</span>
                        {{ Lang::txt('COM_PROJECTS_CAN') }}:
                    </p>
                    <ul>
                        <li>{{ Lang::txt('COM_PROJECTS_HOWTO_ROLES_REVIEWER_CAN_ONE') }}</li>
                    </ul>
                    @if ($model->get('owned_by_group'))
                        <h4>{{ Lang::txt('COM_PROJECTS_HOWTO_GROUP_PROJECT') }}</h4>
                        <p>{{ Lang::txt('COM_PROJECTS_HOWTO_GROUP_EXPLAIN') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </form>
</section>
