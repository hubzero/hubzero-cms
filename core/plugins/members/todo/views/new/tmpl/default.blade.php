{{--
  Member Todo — new todo item modal form.

  Variables from plugin (onMembers):
    $option   — component option
    $member   — member profile object
    $projects — array of project objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $url = 'index.php?option=com_members&id=' . $member->get('id') . '&active=todo';
@endphp

<div id="abox-content">
  <h3 class="text-lg font-semibold mb-4">{{ Lang::txt('PLG_MEMBERS_TODO_ADD_TODO') }}</h3>

  <form action="{{ Route::url($url . '&action=save') }}" method="post" id="plg-form" class="space-y-4">
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="uid" id="uid" value="{{ $member->get('id') }}" />
    <input type="hidden" name="active" value="todo" />
    <input type="hidden" name="action" value="save" />
    {!! Html::input('token') !!}

    <div class="form-control w-full">
      <label class="label" for="todo-content">
        <span class="label-text">{{ Lang::txt('PLG_MEMBERS_TODO_TYPEIT') }}</span>
      </label>
      <textarea id="todo-content" name="content" rows="6"
                class="textarea textarea-bordered w-full"
                placeholder="{{ Lang::txt('PLG_MEMBERS_TODO_TYPEIT') }}"></textarea>
    </div>

    @if (count($projects) > 0)
      <div class="form-control w-full">
        <label class="label" for="todo-project">
          <span class="label-text">{{ ucfirst(Lang::txt('PLG_MEMBERS_TODO_CHOOSE_PROJECT')) }}</span>
        </label>
        <select id="todo-project" name="projectid" class="select select-bordered w-full">
          @foreach ($projects as $project)
            <option value="{{ $project->get('id') }}">
              {{ stripslashes($project->get('title')) }} ({{ $project->get('alias') }})
            </option>
          @endforeach
        </select>
      </div>
    @endif

    <div class="form-control w-full">
      <label class="label" for="dued">
        <span class="label-text">{{ ucfirst(Lang::txt('PLG_MEMBERS_TODO_DUE')) }}</span>
      </label>
      <input type="text" id="dued" name="due" class="input input-bordered w-full duebox"
             placeholder="mm/dd/yyyy" value="" />
    </div>

    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('PLG_MEMBERS_TODO_SAVE') }}
      </button>
    </div>
  </form>
</div>
