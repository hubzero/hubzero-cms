{{--
  Resources contribution compose step — title, abstract, custom fields.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $task       — current task
    $row        — resource model
    $step       — current step number
    $steps      — array of step names
    $next_step  — next step number
    $id         — resource id
    $progress   — progress array

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css('create.css');
  $__view->js('create.js');

  $row->fulltxt = ($row->fulltxt)
      ? stripslashes($row->fulltxt ?: '')
      : stripslashes($row->introtext ?: '');

  $type = $row->type;

  $data = [];
  preg_match_all('#<nb:(.*?)>(.*?)</nb:(.*?)>#s', $row->fulltxt, $matches, PREG_SET_ORDER);
  if (count($matches) > 0) {
      foreach ($matches as $match) {
          $data[$match[1]] = trim($match[2]);
      }
  }

  $row->fulltxt = preg_replace('#<nb:(.*?)>(.*?)</nb:(.*?)>#s', '', $row->fulltxt);
  $row->fulltxt = trim($row->fulltxt);

  $elements = new \Components\Resources\Models\Elements($data, $type->get('customFields'));
  $fields = $elements->render();

  $group_cn = Request::getString('group', '');
  $draftUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url(
      'index.php?option=' . $option
      . '&task=draft&step=' . $next_step
      . '&group=' . $group_cn
      . '&id=' . $id
  );
  $filerSrc = Request::base(true)
      . '/index.php?option=' . $option
      . '&controller=media&tmpl=component'
      . '&resource=' . $row->id;
  $fulltxt = e(stripslashes($row->fulltxt));
  $titleValue = e(stripslashes($row->title ?: ''));
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
      'group_cn' => $group_cn,
      'resource' => $row,
      'progress' => $progress,
  ])

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm" accept-charset="utf-8">

    {{-- About section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2">
        <x-form-section :heading="Lang::txt('COM_CONTRIBUTE_COMPOSE_ABOUT')">

          <x-form-field
              name="fields[title]"
              inputId="field-title"
              :label="Lang::txt('COM_CONTRIBUTE_COMPOSE_TITLE')"
              :required="true">
            <input type="text"
                   name="fields[title]"
                   id="field-title"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ $titleValue }}"
                   aria-required="true" />
          </x-form-field>

          <x-form-field
              name="fields[fulltxt]"
              inputId="field-fulltxt"
              :label="Lang::txt('COM_CONTRIBUTE_COMPOSE_ABSTRACT')"
              :required="true">
            {!! $__view->editor('fields[fulltxt]', $fulltxt, 50, 20, 'field-fulltxt') !!}
          </x-form-field>

          <x-form-section :heading="Lang::txt('COM_CONTRIBUTE_MEDIA_MANAGER')">
            <p class="text-sm text-base-content/70 mb-2">
              {{ Lang::txt('COM_CONTRIBUTE_MEDIA_EXPLANATION') }}
            </p>
            <iframe width="100%"
                    height="160"
                    name="filer"
                    id="filer"
                    title="{{ Lang::txt('COM_CONTRIBUTE_MEDIA_MANAGER') }}"
                    src="{{ $filerSrc }}"></iframe>
          </x-form-section>

        </x-form-section>
      </div>

      <div>
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body text-sm text-base-content/70">
            <p>{{ Lang::txt('COM_CONTRIBUTE_COMPOSE_EXPLANATION') }}</p>
            <p>{{ Lang::txt('COM_CONTRIBUTE_COMPOSE_ABSTRACT_HINT') }}</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Custom fields --}}
    @if($fields)
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
          <x-form-section :heading="Lang::txt('COM_CONTRIBUTE_COMPOSE_DETAILS')">
            {!! $fields !!}
          </x-form-section>
        </div>

        <div>
          <div class="card bg-base-100 shadow-sm">
            <div class="card-body text-sm text-base-content/70">
              <p>{{ Lang::txt('COM_CONTRIBUTE_COMPOSE_CUSTOM_FIELDS_EXPLANATION') }}</p>
            </div>
          </div>
        </div>
      </div>
    @endif

    <input type="hidden" name="fields[published]" value="{{ $row->get('published') }}" />
    <input type="hidden" name="fields[standalone]" value="1" />
    <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
    <input type="hidden" name="id" value="{{ $row->get('id') }}" />
    <input type="hidden" name="fields[type]" value="{{ $row->get('type') }}" />
    <input type="hidden" name="fields[created]" value="{{ $row->get('created') }}" />
    <input type="hidden" name="fields[created_by]" value="{{ $row->get('created_by') }}" />
    <input type="hidden" name="fields[publish_up]" value="{{ $row->get('publish_up') }}" />
    <input type="hidden" name="fields[publish_down]" value="{{ $row->get('publish_down') }}" />
    <input type="hidden" name="fields[group_owner]" value="{{ $row->get('group_owner') }}" />

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="{{ $task }}" />
    <input type="hidden" name="step" value="{{ $next_step }}" />

    {!! Html::input('token') !!}

    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_CONTRIBUTE_NEXT') }}
      </button>
    </div>
  </form>

</x-page-container>
