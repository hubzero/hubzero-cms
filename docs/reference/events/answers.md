<!--
status: generated
source: Event::trigger('answers.*') call sites and core/plugins/answers/
-->

# Answers events

Events in the `answers` group. A plugin in `core/plugins/answers/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `answers.onQuestionNotify`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:851`](../../../core/components/com_answers/site/controllers/questions.php#L851) with `[$row]`

Listeners:

- `plg_answers_tools` — [`onQuestionNotify($row)`](../../../core/plugins/answers/tools/tools.php)

## `answers.onQuestionsFilters`

Fired from:

- [`core/components/com_answers/site/views/questions/tmpl/search.php:154`](../../../core/components/com_answers/site/views/questions/tmpl/search.php#L154)

Listeners:

- `plg_answers_members` — [`onQuestionsFilters()`](../../../core/plugins/answers/members/members.php)

## `answers.onQuestionsPrepareFilters`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:520`](../../../core/components/com_answers/site/controllers/questions.php#L520) with `[$filters]`

Listeners:

- `plg_answers_members` — [`onQuestionsPrepareFilters($filters)`](../../../core/plugins/answers/members/members.php)
- `plg_answers_tools` — [`onQuestionsPrepareFilters($filters)`](../../../core/plugins/answers/tools/tools.php)
