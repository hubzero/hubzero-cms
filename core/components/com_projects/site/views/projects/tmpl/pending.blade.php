{{--
 * Project pending approval page
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

    $__view->css()->js();
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
        <p class="alert alert-warning">{{ Lang::txt('COM_PROJECTS_PROJECT_PENDING_APPROVAL') }}</p>
    </section>
</div>
