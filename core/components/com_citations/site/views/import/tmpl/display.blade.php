{{--
  Citations — import step 1: upload file.

  Variables from controller (displayTask):
    $title          — Page title string
    $accepted_files — Array of accepted file type descriptions
    $messages       — Array of notification messages
    $gid            — Optional group ID

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_CITATIONS_IMPORT'),
      'index.php?option=' . $option . '&task=import'
  );

  Document::setTitle($title);

  $uploadUrl = Route::url(
      'index.php?option=' . $option . '&task=import_upload', false
  );
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS_IMPORT')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option, false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
      </svg>
      {{ Lang::txt('COM_CITATIONS_BACK') }}
    </a>
  @endslot

  <x-alert-list :notifications="$messages" />

  <x-step-nav :steps="[
      Lang::txt('COM_CITATIONS_IMPORT_STEP1_NAME'),
      Lang::txt('COM_CITATIONS_IMPORT_STEP2_NAME'),
      Lang::txt('COM_CITATIONS_IMPORT_STEP3_NAME'),
  ]" :current="0" />

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Upload form --}}
    <div class="md:col-span-2">
      <form enctype="multipart/form-data"
            method="post"
            action="{{ $uploadUrl }}">
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body">
            <h2 class="card-title text-base">
              {{ Lang::txt('COM_CITATIONS_IMPORT_UPLOAD') }}
            </h2>
            <div class="form-control w-full">
              <label class="label" for="citations-file">
                <span class="label-text">
                  {{ Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_FILE') }}
                  <span class="text-error">*</span>
                </span>
              </label>
              <input type="file"
                     id="citations-file"
                     name="citations_file"
                     class="file-input file-input-bordered w-full"
                     required />
              <label class="label">
                <span class="label-text-alt text-base-content/50">
                  {!! Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_MAX') !!}
                </span>
              </label>
            </div>

            <div class="card-actions justify-end mt-4">
              <button type="submit"
                      name="submit"
                      class="btn btn-primary">
                {{ Lang::txt('COM_CITATIONS_IMPORT_UPLOAD') }}
              </button>
            </div>
          </div>
        </div>

        {!! Html::input('token') !!}
        <input type="hidden" name="option" value="{{ $option }}" />
        @if(isset($gid))
          <input type="hidden" name="group" value="{{ $gid }}" />
        @endif
        <input type="hidden" name="task" value="import_upload" />
      </form>
    </div>

    {{-- Accepted file types --}}
    <div>
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-sm">
            {{ Lang::txt('COM_CITATIONS_IMPORT_ACCEPTABLE') }}
          </h3>
          @if(!empty($accepted_files))
            <ul class="text-sm space-y-1 text-base-content/70">
              @foreach($accepted_files as $file)
                <li>{!! $file !!}</li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>
  </div>

</x-page-container>
