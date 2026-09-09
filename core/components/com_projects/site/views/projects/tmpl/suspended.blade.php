{{--
 * Project suspended notice with optional reinstate form
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

    $__view->css()->js();

    // Check who suspended the project
    $log = \Hubzero\Activity\Log::all();
    $l = $log->getTableName();
    $r = \Hubzero\Activity\Recipient::blank()->getTableName();

    $result = $log
        ->join($r, $r . '.log_id', $l . '.id', 'inner')
        ->whereEquals($r . '.scope', 'project')
        ->whereEquals($r . '.scope_id', $model->get('id'))
        ->whereEquals($l . '.description', Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT_SUSPENDED'))
        ->order($l . '.created', 'desc')
        ->row();

    $suspended = null;
    if ($result) {
        $suspended = $result->details->get('admin');
    }

    $formAction = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
    );
    $supportUrl = Route::url('index.php?option=com_support&controller=tickets&task=new');
@endphp

<div id="project-wrap">
    <section class="main section">
        <form method="post" action="{{ $formAction }}">
            <fieldset>
                <input type="hidden" name="id" value="{{ $model->get('id') }}" />
                <input type="hidden" name="task" value="reinstate" />
                <input type="hidden" name="option" value="{{ $option }}" />

                @include('projects::_header', [
                    'model'         => $model,
                    'showPic'       => 1,
                    'showPrivacy'   => 0,
                    'goBack'        => 0,
                    'showUnderline' => 1,
                    'option'        => $option,
                ])

                <p class="alert alert-warning">
                    @if ($suspended == 2)
                        {{ Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT') }}
                    @else
                        {{ Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_ADMIN') }}
                    @endif
                    @if (!$model->access('manager') && $suspended == 2)
                        {{ Lang::txt('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_NO_MANAGER') }}
                    @endif
                </p>

                @if ($model->access('manager') && $suspended == 2)
                    <h4 class="mt-4">{{ Lang::txt('COM_PROJECTS_CANCEL_WANT_TO_REINSTATE') }}</h4>
                    <p class="mt-2">
                        <input
                            type="submit"
                            class="btn btn-primary"
                            value="{{ Lang::txt('COM_PROJECTS_CANCEL_YES_REINSTATE') }}"
                        />
                    </p>
                    <p class="mt-2">
                        {{ ucfirst(Lang::txt('COM_PROJECTS_CANCEL_PERMANENTLY')) }},
                        {{ Lang::txt('COM_PROJECTS_CANCEL_YOU_CAN_ALSO') }}
                        <a href="{{ $supportUrl }}" class="link link-primary">{{ Lang::txt('COM_PROJECTS_CANCEL_CONTACT_ADMIN') }}</a>
                    </p>
                @endif
            </fieldset>
        </form>
    </section>
</div>
