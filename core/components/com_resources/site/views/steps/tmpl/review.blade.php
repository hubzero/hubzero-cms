{{--
  Resource contribution step — Review & submit (or post-submit licensing).

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $task       — current task
    $step       — current step number
    $steps      — array of step names
    $id         — resource ID
    $resource   — resource model
    $config     — component config (Registry)
    $licenses   — collection of license records
    $progress   — array of step completion flags

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css('create.css');
  $__view->js('create.js');

  // Merge resource params into component config
  $rparams = new \Hubzero\Config\Registry($resource->params);
  $params = $config;
  $params->merge($rparams);

  $draftUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url('index.php?option=' . $option . '&task=' . $task);
  $viewUrl = Route::url('index.php?option=com_resources&id=' . $id);
  $previewUrl = Route::url(
      'index.php?option=com_resources&id=' . $id . '&tmpl=component&mode=preview'
  );

  // Build license data for JS-driven preview
  $licenseHiddenInputs = [];
  $customLicenseText = false;
  $licensePreview = Lang::txt('COM_CONTRIBUTE_LICENSE_PREVIEW');

  foreach ($licenses as $license) {
      $escapedText = e(nl2br($license->text));
      if (substr($license->name, 0, 6) === 'custom') {
          $licenseHiddenInputs[] = '<input type="hidden" id="license-custom" value="'
              . $escapedText . '" />';
          $customLicenseText = $escapedText;
      } else {
          $licenseHiddenInputs[] = '<input type="hidden" id="license-'
              . e($license->name) . '" value="' . $escapedText . '" />';
          if ($params->get('license') === $license->name) {
              $licensePreview = nl2br(e($license->text));
          }
      }
  }

  if (!$customLicenseText && $config->get('cc_license_custom')) {
      $placeholderText = e(Lang::txt('COM_CONTRIBUTE_ENTER_LICENSE_HERE'));
      $customLicenseText = $placeholderText;
      $licenseHiddenInputs[] = '<input type="hidden" id="license-custom" value="'
          . $placeholderText . '" />';
  }
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $draftUrl }}">
      {{ Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION') }}
    </a>
  @endslot

  @include('steps::steps', [
      'option'   => $option,
      'step'     => $step,
      'steps'    => $steps,
      'id'       => $id,
      'resource' => $resource,
      'progress' => $progress,
  ])

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm" class="space-y-8">
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="{{ $task }}" />
    <input type="hidden" name="id" value="{{ $id }}" />
    <input type="hidden" name="step" value="{{ $step }}" />

    @if($progress['submitted'] == 1)
      {{-- Post-submission: licensing only --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend text-lg font-semibold">
              {{ Lang::txt('COM_CONTRIBUTE_LICENSING_LEGEND') }}
            </legend>

            <div>
              <label for="license" class="label">
                <span class="label-text">
                  {{ Lang::txt('COM_CONTRIBUTE_LICENSE_LABEL') }}
                </span>
              </label>
              <select name="license"
                      id="license"
                      class="select select-bordered w-full">
                <option value="">{{ Lang::txt('COM_CONTRIBUTE_SELECT_LICENSE') }}</option>
                @foreach($licenses as $license)
                  @if(substr($license->name, 0, 6) === 'custom')
                    <option value="custom"
                            @selected($params->get('license') === $license->name)>
                      {{ Lang::txt('Custom') }}
                    </option>
                  @endif
                @endforeach
                @if(!$customLicenseText && $config->get('cc_license_custom'))
                  <option value="custom">
                    {{ Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE') }}
                  </option>
                @endif
                @foreach($licenses as $license)
                  @if(substr($license->name, 0, 6) === 'custom')
                    @continue
                  @endif
                  <option value="{{ e($license->name) }}"
                          @selected($params->get('license') === $license->name)>
                    {{ e($license->title) }}
                  </option>
                @endforeach
              </select>

              <div id="license-preview"
                   class="mt-3 p-3 bg-base-200 rounded-box text-sm hidden">
                {!! $licensePreview !!}
              </div>
              {!! implode("\n", $licenseHiddenInputs) !!}
            </div>

            @if($config->get('cc_license_custom'))
              <div class="mt-4">
                <label for="license-text" class="label">
                  <span class="label-text">
                    {{ Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE') }}
                  </span>
                </label>
                <textarea name="license-text"
                          id="license-text"
                          rows="10"
                          class="textarea textarea-bordered w-full hidden"
                >{{ $customLicenseText }}</textarea>
              </div>
            @endif

            <input type="hidden" name="published" value="1" />
            <input type="hidden" name="authorization" value="1" />
          </fieldset>
        </div>

        <div class="space-y-4">
          <div class="card bg-base-200 shadow-sm">
            <div class="card-body">
              <p class="text-sm text-base-content/70">
                {{ Lang::txt('COM_CONTRIBUTE_PASSED_REVIEW') }}
                <a class="link link-primary" href="{{ $viewUrl }}">
                  {{ Lang::txt('COM_CONTRIBUTE_VIEW_HERE') }}
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('COM_CONTRIBUTE_SAVE') }}
        </button>
      </div>
    </form>

    @else
      {{-- Pre-submission: authorization + optional licensing --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
          <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend text-lg font-semibold">
              {{ Lang::txt('COM_CONTRIBUTE_AUTHORIZATION_LEGEND') }}
            </legend>

            @php $sitename = Config::get('sitename'); @endphp

            <label for="authorization"
                   class="flex items-start gap-3 cursor-pointer py-2">
              <input class="checkbox checkbox-primary mt-1"
                     type="checkbox"
                     name="authorization"
                     id="authorization"
                     value="1"
                     aria-required="true" />
              <span class="text-sm">
                <span class="text-error font-semibold">
                  {{ Lang::txt('COM_CONTRIBUTE_REQUIRED') }}
                </span>
                <br />
                {!! Lang::txt(
                    'COM_CONTRIBUTE_AUTHORIZATION_LABEL',
                    e($sitename),
                    e($sitename),
                    e($sitename)
                ) !!}
                <br /><br />
                {{ Lang::txt('COM_CONTRIBUTE_AUTHORIZATION_LINKS_LABEL') }}
                <br /><br />
                @php
                  $licenseLink = '<a class="link link-primary" href="'
                      . Request::base(true)
                      . '/legal/license" target="_blank" rel="noopener">'
                      . Lang::txt('COM_CONTRIBUTE_THE_FULL_LICENSE')
                      . '</a>';
                @endphp
                {!! Lang::txt(
                    'COM_CONTRIBUTE_AUTHORIZATION_MUST_ATTRIBUTE',
                    e($sitename),
                    $licenseLink
                ) !!}
              </span>
            </label>

            @if($config->get('cc_license'))
              <div class="mt-6">
                <label for="license" class="label">
                  <span class="label-text">
                    {{ Lang::txt('COM_CONTRIBUTE_LICENSE_LABEL') }}
                  </span>
                </label>
                <select name="license"
                        id="license"
                        class="select select-bordered w-full">
                  <option value="">
                    {{ Lang::txt('COM_CONTRIBUTE_SELECT_LICENSE') }}
                  </option>
                  @foreach($licenses as $license)
                    @if(substr($license->name, 0, 6) === 'custom')
                      <option value="custom"
                              @selected($params->get('license') === $license->name)>
                        {{ Lang::txt('Custom') }}
                      </option>
                    @endif
                  @endforeach
                  @if(!$customLicenseText && $config->get('cc_license_custom'))
                    <option value="custom">
                      {{ Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE') }}
                    </option>
                  @endif
                  @foreach($licenses as $license)
                    @if(substr($license->name, 0, 6) === 'custom')
                      @continue
                    @endif
                    <option value="{{ e($license->name) }}"
                            @selected($params->get('license') === $license->name)>
                      {{ e($license->title) }}
                    </option>
                  @endforeach
                </select>

                <div id="license-preview"
                     class="mt-3 p-3 bg-base-200 rounded-box text-sm hidden">
                  {!! $licensePreview !!}
                </div>
                {!! implode("\n", $licenseHiddenInputs) !!}
              </div>

              @if($config->get('cc_license_custom'))
                <div class="mt-4">
                  <label for="license-text" class="label">
                    <span class="label-text">
                      {{ Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE') }}
                    </span>
                  </label>
                  <textarea name="license-text"
                            id="license-text"
                            rows="10"
                            class="textarea textarea-bordered w-full hidden"
                  >{{ $customLicenseText }}</textarea>
                </div>
              @endif
            @endif

            <input type="hidden" name="published" value="0" />
          </fieldset>
        </div>

        <div class="space-y-4">
          <div class="card bg-base-200 shadow-sm">
            <div class="card-body">
              <h3 class="card-title text-base">
                {{ Lang::txt('COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT') }}
              </h3>
              @php
                $resourcesLink = '<a class="link link-primary" href="'
                    . Route::url('index.php?option=' . $option) . '">'
                    . Lang::txt('resources') . '</a>';
                $whatsnewLink = '<a class="link link-primary" href="'
                    . Route::url('index.php?option=com_whatsnew') . '">'
                    . Lang::txt('What\'s New') . '</a>';
              @endphp
              @if($config->get('autoapprove', 0) != 1)
                <p class="text-sm text-base-content/70">
                  {!! Lang::txt(
                      'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_ANSWER',
                      $resourcesLink,
                      $whatsnewLink
                  ) !!}
                </p>
              @else
                <p class="text-sm text-base-content/70">
                  {!! Lang::txt(
                      'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_AUTOAPPROVED_ANSWER',
                      $resourcesLink,
                      $whatsnewLink
                  ) !!}
                </p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('COM_CONTRIBUTE_SUBMIT_CONTRIBUTION') }}
        </button>
      </div>
  </form>

  {{-- Preview section --}}
  <div class="mt-8">
    <h2 class="text-xl font-semibold mb-4">
      {{ Lang::txt('COM_CONTRIBUTE_REVIEW_PREVIEW') }}
    </h2>
    <div class="border border-base-300 rounded-box overflow-hidden">
      <iframe id="preview-frame"
              name="preview-frame"
              width="100%"
              height="600"
              title="{{ Lang::txt('COM_CONTRIBUTE_REVIEW_PREVIEW') }}"
              class="w-full"
              src="{{ $previewUrl }}"></iframe>
    </div>
  </div>
    @endif

</x-page-container>
