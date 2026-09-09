{{--
  Employer intro — 3-step subscription walkthrough.

  Variables from controller:
    $title  — Page title string
    $config — Component params (Registry)
    $option — Component option string
    $task   — Current task (addjob or resumes)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $registerUrl = Route::url('index.php?option=com_members&controller=register');
  $isPostJob = ($task ?? '') == 'addjob';
@endphp

<x-page-container :title="$title">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Step 1: Login --}}
    <div class="card bg-base-100 shadow-sm border-2 border-primary">
      <div class="card-body">
        <div class="flex items-center gap-2 mb-2">
          <span class="badge badge-primary badge-lg font-bold">1</span>
          <h3 class="card-title text-base">
            {{ Lang::txt('COM_JOBS_STEP_LOGIN') }}
            {{ Lang::txt('COM_JOBS_TO') }}
            {{ Config::get('sitename') }}
          </h3>
        </div>
        <p class="text-sm text-base-content/70">
          {{ Lang::txt('COM_JOBS_LOGIN_NO_ACCOUNT') }}
          <a href="{{ $registerUrl }}" class="link link-primary">
            {{ Lang::txt('COM_JOBS_LOGIN_REGISTER_NOW') }}
          </a>.
          {{ Lang::txt('COM_JOBS_LOGIN_IT_IS_FREE') }}
        </p>
      </div>
    </div>

    {{-- Step 2: Subscribe --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <div class="flex items-center gap-2 mb-2">
          <span class="badge badge-neutral badge-lg font-bold">2</span>
          <h3 class="card-title text-base">
            {{ Lang::txt('COM_JOBS_STEP_SUBSCRIBE') }}
          </h3>
        </div>
        <p class="text-sm text-base-content/70">
          {{ Lang::txt('COM_JOBS_INTRO_TO_ACCESS') }}
          {{ Lang::txt('COM_JOBS_EMPLOYER_SERVICES') }}
          {{ Lang::txt('COM_JOBS_INTRO_SUBSCRIPTION_REQUIRED') }}
          {{ Lang::txt('COM_JOBS_INTRO_HOW_TO_SUBSCRIBE') }}
        </p>
      </div>
    </div>

    {{-- Step 3: Browse/Post --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <div class="flex items-center gap-2 mb-2">
          <span class="badge badge-neutral badge-lg font-bold">3</span>
          <h3 class="card-title text-base">
            {{ $isPostJob
                ? Lang::txt('COM_JOBS_ACTION_POST_AND_BROWSE')
                : Lang::txt('COM_JOBS_ACTION_BROWSE_AND_POST') }}
          </h3>
        </div>
        <p class="text-sm text-base-content/70">
          @if($isPostJob)
            {{ Lang::txt('COM_JOBS_INTRO_POST_UP_TO') }}
            {{ $config->get('maxads', 3) }}
            {{ Lang::txt('COM_JOBS_INTRO_POST_DETAILS') }}
          @else
            {{ Lang::txt('COM_JOBS_INTRO_BROWSE_INFO') }}
            {{ Lang::txt('COM_JOBS_INTRO_BROWSE_DETAILS') }}
          @endif
        </p>
      </div>
    </div>
  </div>
</x-page-container>
