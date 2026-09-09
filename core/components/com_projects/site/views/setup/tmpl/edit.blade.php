{{--
 * Project settings/edit view (bespoke layout)
 *
 * Variables:
 *   $option     - Component option string
 *   $model      - Project model object
 *   $config     - Component config Registry
 *   $section    - Active section string (info|info_custom|team|settings)
 *   $sections   - Available sections array
 *   $msg        - Status message string
 *   $content    - Plugin content HTML (for team section)
 *   $publishing - Whether publishing is enabled (bool)
 *   $fields     - Custom fields (optional)
 *   $data       - Custom field data (optional)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css()->js()
        ->css('jquery.fancybox.css', 'system')
        ->css('edit')
        ->js('setup');

    $privacy = !$model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

    $layout = $model->params->get('layout', $config->get('layout', 'standard'));
    $theme  = $model->params->get('theme', $config->get('theme', 'light'));

    if ($layout == 'extended') {
        $__view->css('extended.css')->css('theme' . $theme . '.css');
    } else {
        $__view->css('standard.css');
    }

    $formAction = Route::url($model->link() . '&task=save');
    $deleteUrl  = Route::url('index.php?option=' . $option . '&alias=' . $model->get('alias') . '&task=delete');
@endphp

<div id="project-wrap" class="edit-project">
    @if ($layout == 'extended')
        @include('projects::_topheader', [
            'model'      => $model,
            'publicView' => false,
            'option'     => $option,
        ])
        @include('projects::_topmenu', [
            'model'      => $model,
            'active'     => 'edit',
            'tabs'       => [],
            'option'     => $option,
            'guest'      => false,
            'publicView' => false,
        ])
        <div class="project-inner-wrap">
    @else
        @include('projects::_header', [
            'model'         => $model,
            'showPic'       => 1,
            'showPrivacy'   => 0,
            'goBack'        => 1,
            'showUnderline' => 1,
            'option'        => $option,
        ])
    @endif

    @include('projects::_statusmsg', [
        'error' => $__view->getError(),
        'msg'   => $msg,
    ])

    <section class="main section" id="edit-project-content">
        <h3 class="text-xl font-semibold mb-4">{{ ucwords(Lang::txt('COM_PROJECTS_EDIT_PROJECT')) }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-3">
                @include('setup::_sections', [
                    'sections' => $sections,
                    'section'  => $section,
                    'option'   => $option,
                    'model'    => $model,
                ])

                <div class="card bg-base-200/50 p-4 mt-4">
                    <h3>{{ Lang::txt('COM_PROJECTS_TIPS') }}</h3>

                    @if ($section == 'team')
                        <h4>{{ Lang::txt('PLG_PROJECTS_TEAM_HOWTO_ROLES_TIPS') }}</h4>
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
                    @endif

                    @if ($section == 'settings')
                        <h4>{{ Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE') }}</h4>
                        <p>{{ Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE_EXPLAIN') }}</p>
                        @if ($config->get('grantinfo', 0))
                            <h5>{{ Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_WHY') }}</h5>
                            <p>{{ Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_BECAUSE') }}</p>
                        @endif
                    @endif

                    @if ($section == 'info' || $section == 'info_custom')
                        <p>{{ Lang::txt('COM_PROJECTS_CANCEL_PROJECT_NEED') }}</p>
                        <p>
                            <a class="btn btn-error" href="{{ $deleteUrl }}" id="delproject">
                                {{ Lang::txt('JACTION_DELETE') }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>
            <div id="edit-project" class="md:col-span-9">
                <form id="hubForm" class="full" method="post" action="{{ $formAction }}">
                    <input type="hidden" id="pid" name="id" value="{{ $model->get('id') }}" />
                    <input type="hidden" name="task" value="save" />
                    <input type="hidden" name="active" value="{{ $section }}" />
                    <input type="hidden" name="name" value="{{ $model->get('alias') }}" />
                    {!! Html::input('token') !!}
                    {!! Html::input('honeypot') !!}

                    @if ($section == 'team')
                        @include('setup::_edit_team', [
                            'content' => $content,
                            'model'   => $model,
                        ])
                    @else
                        @include('setup::_edit_info', [
                            'config'     => $config,
                            'model'      => $model,
                            'option'     => $option,
                            'privacy'    => $privacy,
                            'publishing' => $publishing,
                            'fields'     => $fields ?? null,
                            'data'       => $data ?? [],
                        ])
                    @endif
                </form>
            </div>
        </div>
    </section>

    @if ($layout == 'extended')
        </div>
    @endif
</div>
