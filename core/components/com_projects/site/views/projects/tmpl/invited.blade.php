{{--
 * Project invitation confirmation page (for guests/non-members)
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *   $task   - Current task string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $__view->css()->js();

    $defaultUrl = Route::url('index.php?option=' . $option . '&task=' . $task);
    $rtrn = Request::getString('REQUEST_URI', $defaultUrl, 'server');

    $loginUrl = Route::url(
        'index.php?option=com_users&view=login&return=' . base64_encode($rtrn)
    );
    $registerUrl = Route::url(
        'index.php?option=com_members&controller=register&return=' . base64_encode($rtrn)
    );
@endphp

<div id="project-wrap">
    <section class="main section">
        @include('projects::_header', [
            'model'         => $model,
            'showPic'       => 1,
            'showPrivacy'   => 0,
            'goBack'        => 0,
            'showUnderline' => 1,
            'option'        => $option,
        ])

        <h3 class="text-xl font-semibold mb-4">{{ Lang::txt('COM_PROJECTS_INVITED_CONFIRM') }}</h3>

        <div id="confirm-invite" class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p>
                            {{ Lang::txt('COM_PROJECTS_INVITED_CONFIRM_SCREEN') }}
                            "<span class="font-semibold">{{ $model->get('title') }}</span>".
                            {{ Lang::txt('COM_PROJECTS_INVITED_NEED_ACCOUNT_TO_JOIN') }}
                        </p>
                    </div>
                    <div class="flex flex-col gap-3">
                        <p>
                            {{ Lang::txt('COM_PROJECTS_INVITED_HAVE_ACCOUNT') }}
                            <a href="{{ $loginUrl }}" class="link link-primary">{!! Lang::txt('COM_PROJECTS_INVITED_PLEASE_LOGIN') !!}</a>
                        </p>
                        <p>
                            {{ Lang::txt('COM_PROJECTS_INVITED_DO_NOT_HAVE_ACCOUNT') }}
                            <a href="{{ $registerUrl }}" class="link link-primary">{!! Lang::txt('COM_PROJECTS_INVITED_PLEASE_REGISTER') !!}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
