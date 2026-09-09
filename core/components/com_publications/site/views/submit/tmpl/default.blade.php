{{--
  Publication submit page — project-driven publication creation workflow.

  Variables from controller:
    $title   — page title
    $option  — component option string
    $pid     — publication ID (or null)
    $project — project model (or null)
    $msg     — status message
    $content — rendered HTML from projects plugin

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css()
      ->css('jquery.fancybox.css', 'system')
      ->js();

  // Add projects stylesheet and scripts
  \Hubzero\Document\Assets::addComponentStylesheet('com_projects');
  \Hubzero\Document\Assets::addComponentScript('com_projects');
  \Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'uploader');
  \Hubzero\Document\Assets::addPluginScript('projects', 'files', 'jquery.fileuploader.js');
  \Hubzero\Document\Assets::addPluginScript('projects', 'files', 'jquery.queueuploader.js');
@endphp

<x-page-container :title="$title">

  @if ($pid && !empty($project) && $project->get('created_by_user') == User::get('id'))
    <div class="alert alert-info mb-4">
      {{ Lang::txt('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT') }}
      @php
        $projectUrl = Route::url(
            'index.php?option=com_projects'
            . '&alias=' . $project->get('alias')
            . '&action=activate'
        );
      @endphp
      <a href="{{ $projectUrl }}" class="link">
        {{ Lang::txt('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE') }} &raquo;
      </a>
    </div>
  @endif

  @php
    // Display status message via com_projects view
    $statusView = new \Hubzero\Component\View([
        'base_path' => Component::path('com_projects') . DS . 'site',
        'name'      => 'projects',
        'layout'    => '_statusmsg',
    ]);
    $statusView->error = $__view->getError();
    $statusView->msg   = $msg;
  @endphp
  {!! $statusView->loadTemplate() !!}

  {!! $content !!}

</x-page-container>
