<!--
status: generated
source: Event::trigger('xmessage.*') call sites and core/plugins/xmessage/
-->

# Xmessage events

Events in the `xmessage` group. A plugin in `core/plugins/xmessage/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `xmessage.onMessage`

Fired from:

- [`core/libraries/Hubzero/Message/Helper.php:161`](../../../core/libraries/Hubzero/Message/Helper.php#L161) with `[$from, $xmessage, $user, $action])) { $this->setError(Lang::txt('Unable to message user %s with method %s', $uid, $action)`

Listeners:

- `plg_xmessage_email` — [`onMessage($from, $xmessage, $user, $action)`](../../../core/plugins/xmessage/email/email.php)
- `plg_xmessage_internal` — [`onMessage($from, $xmessage, $user, $action)`](../../../core/plugins/xmessage/internal/internal.php)

## `xmessage.onMessageMethods`

Fired from:

- [`core/components/com_members/admin/controllers/messages.php:240`](../../../core/components/com_members/admin/controllers/messages.php#L240) with `[]`
- [`core/plugins/members/messages/messages.php:443`](../../../core/plugins/members/messages/messages.php#L443) with `[]`

Listeners:

- `plg_xmessage_email` — [`onMessageMethods()`](../../../core/plugins/xmessage/email/email.php)
- `plg_xmessage_internal` — [`onMessageMethods()`](../../../core/plugins/xmessage/internal/internal.php)

## `xmessage.onSendMessage`

Fired from:

- [`core/components/com_answers/site/controllers/questions.php:213`](../../../core/components/com_answers/site/controllers/questions.php#L213) with `['answers_reply_comment', $subject, $message, $from, [$authorid], $this->option])) { $this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED')`
- [`core/components/com_answers/site/controllers/questions.php:221`](../../../core/components/com_answers/site/controllers/questions.php#L221) with `['new_answer_admin', $subject, $message, $from, $receivers, $this->option])) { $this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED')`
- [`core/components/com_answers/site/controllers/questions.php:850`](../../../core/components/com_answers/site/controllers/questions.php#L850) with `['new_question_admin', $subject, $message, $from, $recipients, $this->option])) { Notify::error(Lang::txt('COM_ANSWERS_MESSAGE_FAILED')`
- [`core/components/com_answers/site/controllers/questions.php:990`](../../../core/components/com_answers/site/controllers/questions.php#L990) with `['answers_question_deleted', $subject, $message, $from, $users, $this->_option))) { $this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED']`
- [`core/components/com_answers/site/controllers/questions.php:1166`](../../../core/components/com_answers/site/controllers/questions.php#L1166) with `[$messageType , $subject, $message, $from, [$authorid], $this->option])) { $this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED')`
- [`core/components/com_answers/site/controllers/questions.php:1173`](../../../core/components/com_answers/site/controllers/questions.php#L1173) with `['new_answer_admin', $subject, $message, $from, $receivers, $this->option])) { $this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED')`
- [`core/components/com_cart/lib/cartmessenger/CartMessenger.php:465`](../../../core/components/com_cart/lib/cartmessenger/CartMessenger.php#L465) with `['store_notifications', $mailSubject, $mailMessage, $from, $adminId, '', null, '', 0, true],`
- [`core/components/com_courses/site/controllers/course.php:624`](../../../core/components/com_courses/site/controllers/course.php#L624) with `['courses_deleted', $subject, $message, $from, $managers, $this->option])) { Notify::error(Lang::txt('COM_COURSES_ERROR_EMAIL_MEMBERS_FAI…`
- [`core/components/com_groups/site/controllers/membership.php:816`](../../../core/components/com_groups/site/controllers/membership.php#L816) with `['groups_cancelled_me', $subject, $message, $from, $this->view->group->get('managers'), $this->option])) { $this->setError(Lang::txt('GRO…`
- [`core/components/com_jobs/admin/controllers/jobs.php:324`](../../../core/components/com_jobs/admin/controllers/jobs.php#L324) with `$messageParams)) { Notify::error(Lang::txt('COM_JOBS_ERROR_FAILED_TO_MESSAGE_USERS')`
- [`core/components/com_projects/admin/controllers/projects.php:544`](../../../core/components/com_projects/admin/controllers/projects.php#L544) with `['projects_admin_notice', $subject, $body, $from, $managers, $this->option]`
- [`core/components/com_projects/helpers/html.php:1209`](../../../core/components/com_projects/helpers/html.php#L1209) with `[ $component, $subject, $body, $from, $addressees, $option, ],`
- and 26 more call sites

Listeners:

- `plg_xmessage_handler` — [`onSendMessage($type, $subject, $message, $from = [], $to = [], $component = '', $element = null, $description = '', $group_id = 0, $bypassGroupsCheck = false, $anonymous = 0,)`](../../../core/plugins/xmessage/handler/handler.php)

## `xmessage.onTakeAction`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_xmessage_handler` — [`onTakeAction($type, $uids = [], $component = '', $element = null)`](../../../core/plugins/xmessage/handler/handler.php)
