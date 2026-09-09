{{--
  Questions metadata — displays question count + "ask a question" link.

  Variables (from plugin):
    $resource — object: resource model
    $count    — int: number of questions

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $questionsUrl = Route::url($resource->link() . '&active=questions');
  $newQuestionUrl = Route::url($resource->link() . '&active=questions&action=new');
  $countKey = ($count == 1)
      ? 'PLG_RESOURCES_QUESTIONS_NUM_QUESTION'
      : 'PLG_RESOURCES_QUESTIONS_NUM_QUESTIONS';
@endphp

<p class="answer">
  <a href="{{ $questionsUrl }}">{{ Lang::txt($countKey, $count) }}</a>
  (<a href="{{ $newQuestionUrl }}">{{ Lang::txt('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION') }}</a>)
</p>
