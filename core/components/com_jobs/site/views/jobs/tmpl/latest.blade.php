{{--
  Latest job postings — minimal listing for the intro page.

  Variables from controller (displayTask):
    $option  — Component option string
    $filters — Current filter settings
    $config  — Component params (Registry)
    $task    — Current task
    $emp     — Employer privileges flag
    $jobs    — Array of job objects
    $admin   — Admin privileges flag

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
@endphp

<section class="mt-8">
  <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_JOBS_LATEST_POSTINGS') }}</h3>

  @if(count($jobs) > 0)
    {!! $__view->view('_list')
        ->set('option', $option)
        ->set('filters', $filters)
        ->set('config', $config)
        ->set('task', $task)
        ->set('emp', $emp)
        ->set('mini', 1)
        ->set('jobs', $jobs)
        ->set('admin', $admin)
        ->loadTemplate() !!}

    <div class="mt-4">
      <a class="btn btn-ghost btn-sm" href="{{ $browseUrl }}">
        {{ Lang::txt('COM_JOBS_ACTION_BROWSE_ALL_JOBS') }}
      </a>
    </div>
  @else
    <p class="text-base-content/60">{{ Lang::txt('COM_JOBS_NO_JOBS_FOUND') }}</p>
  @endif
</section>
