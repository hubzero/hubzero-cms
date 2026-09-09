<!--
status: generated
source: Event::trigger('answers.*') call sites and core/plugins/answers/
-->

# Answers events

Events in the `answers` group. A plugin in `core/plugins/answers/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `answers.onQuestionNotify`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:756`](../../../core/components/com_answers/site/controllers/questions.php#L756) with `array($row)) as $results) { $recipients = array_merge($recipients, $results`

Listeners:

- `plg_answers_tools` — [`onQuestionNotify($row)`](../../../core/plugins/answers/tools/tools.php)

## `answers.onQuestionsFilters`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_answers_members` — [`onQuestionsFilters()`](../../../core/plugins/answers/members/members.php)

## `answers.onQuestionsPrepareFilters`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:432`](../../../core/components/com_answers/site/controllers/questions.php#L432) with `[$filters]`

Listeners:

- `plg_answers_members` — [`onQuestionsPrepareFilters($filters)`](../../../core/plugins/answers/members/members.php)
- `plg_answers_tools` — [`onQuestionsPrepareFilters($filters)`](../../../core/plugins/answers/tools/tools.php)
