{{--
  New question form — ask a question about a resource.

  Variables (from plugin):
    $option   — string: component option
    $resource — object: resource model
    $row      — object: question model (new or existing)
    $tag      — string: resource tag
    $funds    — int: user's available points
    $banking  — bool: points banking enabled

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css();
@endphp

<h3 class="section-header">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS') }}</h3>

<div class="section">
  @foreach($__view->getErrors() as $error)
    <p class="error">{{ $error }}</p>
  @endforeach

  <form action="{{ Route::url($resource->link() . '&active=questions') }}"
        method="post"
        id="hubForm"
        class="full">
    <fieldset>
      <legend>{{ Lang::txt('COM_ANSWERS_YOUR_QUESTION') }}</legend>

      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="id" value="{{ e($resource->id) }}" />
      <input type="hidden" name="active" value="questions" />
      <input type="hidden" name="action" value="save" />
      <input type="hidden" name="funds" value="{{ e($funds) }}" />

      {!! Html::input('token') !!}

      <input type="hidden" name="tag" value="{{ e($tag) }}" />
      <input type="hidden" name="question[id]" value="{{ e($row->get('id')) }}" />
      <input type="hidden" name="question[email]" value="1" />
      <input type="hidden" name="question[state]" value="0" />
      <input type="hidden" name="question[created_by]" value="{{ e(User::get('id')) }}" />

      <label for="field-anonymous">
        <input class="option" type="checkbox" name="question[anonymous]"
               id="field-anonymous" value="1" />
        {{ Lang::txt('COM_ANSWERS_POST_QUESTION_ANON') }}
      </label>

      <label>
        {{ Lang::txt('COM_ANSWERS_TAGS') }}:<br />
        @php
          $tf = Event::trigger(
              'hubzero.onGetMultiEntry',
              [['tags', 'tags', 'actags', '', $row->get('tags', '')]]
          );
          $tf = implode("\n", $tf);

          echo $tf ?: '<textarea name="tags" id="actags" rows="6" cols="35">'
              . e($row->get('tags', '')) . '</textarea>';
        @endphp
      </label>

      <label for="field-subject">
        {{ Lang::txt('COM_ANSWERS_ASK_ONE_LINER') }}:
        <span class="required">{{ Lang::txt('COM_ANSWERS_REQUIRED') }}</span><br />
        <input type="text" name="question[subject]" id="field-subject"
               value="{{ e(stripslashes($row->get('subject'))) }}" />
      </label>

      <label for="field-question">
        {{ Lang::txt('COM_ANSWERS_ASK_DETAILS') }}:<br />
        @php
          echo $__view->editor(
              'question[question]',
              e($row->get('question')),
              50, 10, 'field-question'
          );
        @endphp
      </label>

      @if($banking)
        <label for="field-reward">
          {{ Lang::txt('COM_ANSWERS_ASSIGN_REWARD') }}:<br />
          <input type="text" name="question[reward]" id="field-reward"
                 value="" size="5"
                 {{ (int) $funds <= 0 ? 'disabled' : '' }} />
          {{ Lang::txt('COM_ANSWERS_YOU_HAVE') }}
          <strong>{{ e($funds) }}</strong>
          {{ Lang::txt('COM_ANSWERS_POINTS_TO_SPEND') }}
        </label>
      @else
        <input type="hidden" name="question[reward]" value="0" />
      @endif
    </fieldset>

    <p class="submit">
      <input type="submit" class="btn btn-success" value="{{ Lang::txt('COM_ANSWERS_SUBMIT') }}" />
    </p>
  </form>
</div>
