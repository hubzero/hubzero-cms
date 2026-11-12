<!--
status: generated
source: Event::trigger('xmessage.*') call sites and core/plugins/xmessage/
-->

# Xmessage events

Events in the `xmessage` group. A plugin in `core/plugins/xmessage/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `xmessage.onMessage`

Fired from:

- [`core/libraries/Hubzero/Message/Helper.php:161`](../../../core/libraries/Hubzero/Message/Helper.php#L161) with `[$from, $xmessage, $user, $action]`

Listeners:

- `plg_xmessage_email` — [`onMessage($from, $xmessage, $user, $action)`](../../../core/plugins/xmessage/email/email.php)
- `plg_xmessage_internal` — [`onMessage($from, $xmessage, $user, $action)`](../../../core/plugins/xmessage/internal/internal.php)

## `xmessage.onMessageMethods`

Fired from:

- [`core/components/com_members/admin/controllers/messages.php:243`](../../../core/components/com_members/admin/controllers/messages.php#L243) with `[]`
- [`core/plugins/members/messages/messages.php:443`](../../../core/plugins/members/messages/messages.php#L443) with `[]`

Listeners:

- `plg_xmessage_email` — [`onMessageMethods()`](../../../core/plugins/xmessage/email/email.php)
- `plg_xmessage_internal` — [`onMessageMethods()`](../../../core/plugins/xmessage/internal/internal.php)

## `xmessage.onSendMessage`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:226`](../../../core/components/com_answers/site/controllers/questions.php#L226) with `['answers_reply_comment', $subject, $message, $from, [$authorid], $this->option]`
- [`core/components/com_answers/site/controllers/questions.php:239`](../../../core/components/com_answers/site/controllers/questions.php#L239) with `['new_answer_admin', $subject, $message, $from, $receivers, $this->option]`
- [`core/components/com_answers/site/controllers/questions.php:900`](../../../core/components/com_answers/site/controllers/questions.php#L900) with `['new_question_admin', $subject, $message, $from, $recipients, $this->option]`
- [`core/components/com_answers/site/controllers/questions.php:1053`](../../../core/components/com_answers/site/controllers/questions.php#L1053) with `['answers_question_deleted', $subject, $message, $from, $users, $this->_option]`
- [`core/components/com_answers/site/controllers/questions.php:1249`](../../../core/components/com_answers/site/controllers/questions.php#L1249) with `[$messageType , $subject, $message, $from, [$authorid], $this->option]`
- [`core/components/com_answers/site/controllers/questions.php:1261`](../../../core/components/com_answers/site/controllers/questions.php#L1261) with `['new_answer_admin', $subject, $message, $from, $receivers, $this->option]`
- [`core/components/com_cart/lib/cartmessenger/CartMessenger.php:465`](../../../core/components/com_cart/lib/cartmessenger/CartMessenger.php#L465) with `['store_notifications', $mailSubject, $mailMessage, $from, $adminId, '', null, '', 0, true]`
- [`core/components/com_courses/site/controllers/course.php:634`](../../../core/components/com_courses/site/controllers/course.php#L634) with `['courses_deleted', $subject, $message, $from, $managers, $this->option]`
- [`core/components/com_groups/site/controllers/membership.php:872`](../../../core/components/com_groups/site/controllers/membership.php#L872) with `['groups_cancelled_me', $subject, $message, $from, $this->view->group->get('managers'), $this->option]`
- [`core/components/com_jobs/admin/controllers/jobs.php:321`](../../../core/components/com_jobs/admin/controllers/jobs.php#L321) with `$messageParams`
- [`core/components/com_projects/admin/controllers/projects.php:572`](../../../core/components/com_projects/admin/controllers/projects.php#L572) with `['projects_admin_notice', $subject, $body, $from, $managers, $this->option]`
- [`core/components/com_projects/helpers/html.php:1234`](../../../core/components/com_projects/helpers/html.php#L1234) with `[ $component, $subject, $body, $from, $addressees, $option, ]`
- and 26 more call sites

Listeners:

- `plg_xmessage_handler` — [`onSendMessage($type, $subject, $message, $from = [], $to = [], $component = '', $element = null, $description = '', $group_id = 0, $bypassGroupsCheck = false, $anonymous = 0)`](../../../core/plugins/xmessage/handler/handler.php)

## `xmessage.onTakeAction`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_xmessage_handler` — [`onTakeAction($type, $uids = [], $component = '', $element = null)`](../../../core/plugins/xmessage/handler/handler.php)
