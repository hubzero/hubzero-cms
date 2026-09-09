{{--
  Ask a new question form.

  Variables from controller (newTask):
    $question — Question model instance (new or existing for editing)
    $config   — Component params (Registry)
    $funds    — Available user points (int)
    $tag      — Pre-filled tag string

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
          Lang::txt(strtoupper($option)),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_ANSWERS_NEW'),
      'index.php?option=' . $option . '&task=new'
  );

  Document::setTitle(
      Lang::txt('COM_ANSWERS') . ': ' . Lang::txt('COM_ANSWERS_NEW')
  );

  $cancelUrl = $question->get('id')
      ? Route::url($question->link(), false)
      : Route::url('index.php?option=' . $option, false);
@endphp

<x-page-container :title="Lang::txt('COM_ANSWERS') . ': ' . Lang::txt('COM_ANSWERS_NEW')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option, false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
      </svg>
      {{ Lang::txt('COM_ANSWERS_ALL_QUESTIONS') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_ANSWERS_TIPS')">
        <p class="text-sm text-base-content/60">
          {{ Lang::txt('COM_ANSWERS_BE_POLITE') }}
        </p>
    </x-sidebar-card>

    @if($config->get('banking'))
      <x-sidebar-card :title="Lang::txt('COM_ANSWERS_WHAT_IS_REWARD')">
          <p class="text-sm text-base-content/60">
            {{ Lang::txt('COM_ANSWERS_EXPLAINED_MARKET_VALUE') }}
            <a class="link" href="{{ $config->get('infolink') }}">
              {{ Lang::txt('COM_ANSWERS_LEARN_MORE') }}
            </a>
            {{ Lang::txt('COM_ANSWERS_ABOUT_POINTS') }}
          </p>
      </x-sidebar-card>
    @endif
  @endslot

  <form action="{{ Route::url('index.php?option=' . $option, false) }}"
        method="post"
        id="hubForm">

    <x-form-section :heading="Lang::txt('COM_ANSWERS_YOUR_QUESTION')">
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="controller" value="questions" />
      <input type="hidden" name="task" value="saveq" />
      <input type="hidden" name="fields[id]" value="{{ $question->get('id', 0) }}" />
      <input type="hidden" name="fields[funds]" value="{{ $funds }}" />
      <input type="hidden" name="fields[email]" value="1" />
      <input type="hidden" name="fields[state]" value="0" />
      {!! Html::input('token') !!}
      {!! Html::input('honeypot') !!}

      <x-form-field name="actags"
                    :label="Lang::txt('COM_ANSWERS_TAGS')"
                    :required="true">
        {!! $__view->autocompleter('tags', 'tags', e($tag), 'actags') !!}
      </x-form-field>

      <x-form-field name="field-subject"
                    :label="Lang::txt('COM_ANSWERS_ASK_ONE_LINER')"
                    :required="true">
        <input type="text"
               class="input input-bordered w-full"
               name="fields[subject]"
               id="field-subject"
               value="{{ $question->get('subject', '') }}"
               required />
      </x-form-field>

      <x-form-field name="field-question"
                    :label="Lang::txt('COM_ANSWERS_ASK_DETAILS')">
        <textarea class="textarea textarea-bordered w-full"
                  name="fields[question]"
                  id="field-question"
                  rows="8">{{ $question->get('question', '') }}</textarea>
      </x-form-field>

      @if($config->get('banking'))
        <x-form-field name="field-reward"
                      :label="Lang::txt('COM_ANSWERS_ASSIGN_REWARD')"
                      :hint="Lang::txt('COM_ANSWERS_YOU_HAVE') . ' ' . e($funds) . ' ' . Lang::txt('COM_ANSWERS_POINTS_TO_SPEND')">
          <input type="number"
                 class="input input-bordered w-24"
                 name="fields[reward]"
                 id="field-reward"
                 value=""
                 min="0"
                 max="{{ $funds }}"
                 {{ $funds <= 0 ? 'disabled' : '' }} />
        </x-form-field>
      @else
        <input type="hidden" name="fields[reward]" value="0" />
      @endif

      <x-form-field name="fields-anonymous"
                    :label="Lang::txt('COM_ANSWERS_POST_QUESTION_ANON')"
                    type="checkbox">
        <input type="checkbox"
               class="checkbox checkbox-sm"
               name="fields[anonymous]"
               id="fields-anonymous"
               value="1" />
      </x-form-field>

      {!! Html::input('honeypot') !!}
    </x-form-section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">
        {{ Lang::txt('COM_ANSWERS_SUBMIT') }}
      </button>
      <a class="btn btn-ghost" href="{{ $cancelUrl }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>
  </form>

</x-page-container>
