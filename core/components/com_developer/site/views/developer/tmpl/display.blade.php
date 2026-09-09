{{--
  Developer portal intro page — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$apiUrl   = Route::url('index.php?option=com_developer&controller=api');
$toolsUrl = Route::url('index.php?option=com_tools');
@endphp

<x-page-container :title="Lang::txt('COM_DEVELOPER')">

  <div class="grid grid-cols-1 gap-6">
    {{-- API Development --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <div class="flex-1">
            <h3 class="card-title text-xl">
              {{ Lang::txt('COM_DEVELOPER_API_DEVELOPMENT') }}
            </h3>
            <p class="mt-2">
              {!! Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_DESC') !!}
            </p>
          </div>
          <div class="card-actions">
            <a href="{{ $apiUrl }}" class="btn btn-primary">
              {{ Lang::txt('COM_DEVELOPER_API_DEVELOPMENT_HOME') }}
            </a>
          </div>
        </div>
      </div>
    </div>

    {{-- Tool Development --}}
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <div class="flex-1">
            <h3 class="card-title text-xl">
              {{ Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT') }}
            </h3>
            <p class="mt-2">
              {!! Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_DESC') !!}
            </p>
          </div>
          <div class="card-actions">
            <a href="{{ $toolsUrl }}" class="btn btn-primary">
              {{ Lang::txt('COM_DEVELOPER_TOOL_DEVELOPMENT_HOME') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-page-container>
