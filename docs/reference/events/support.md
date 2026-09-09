<!--
status: generated
source: Event::trigger('support.*') call sites and core/plugins/support/
-->

# Support events

Events in the `support` group. A plugin in `core/plugins/support/` receives an event by defining a public method with the event's name; the arguments are those the call site passes, in order.

## `support.onCommentPrepare`

Fired from:

- [`core/components/com_support/models/comment.php:208`](../../../core/components/com_support/models/comment.php#L208) with `['com_support.comment', &$this]`

Listeners:

- `plg_support_markdown` — [`onCommentPrepare($context, &$comment)`](../../../core/plugins/support/markdown/markdown.php)

## `support.onGetCaptcha`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_support_captcha` — [`onGetCaptcha($ext='com')`](../../../core/plugins/support/captcha/captcha.php)

## `support.onGetComponentCaptcha`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_support_captcha` — [`onGetComponentCaptcha()`](../../../core/plugins/support/captcha/captcha.php)

## `support.onGetModuleCaptcha`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_support_captcha` — [`onGetModuleCaptcha()`](../../../core/plugins/support/captcha/captcha.php)

## `support.onPreTicketSubmission`

Fired from:

- [`core/components/com_support/site/controllers/tickets.php:861`](../../../core/components/com_support/site/controllers/tickets.php#L861) with `[]`

No plugin in the source tree listens for this event.

## `support.onReportItem`

Fired from:

- [`core/components/com_support/site/controllers/abuse.php:196`](../../../core/components/com_support/site/controllers/abuse.php#L196) with `[ $refid, $cat ]`

Listeners:

- `plg_support_answers` — [`onReportItem($refid, $category)`](../../../core/plugins/support/answers/answers.php)
- `plg_support_blog` — [`onReportItem($refid, $category)`](../../../core/plugins/support/blog/blog.php)
- `plg_support_comments` — [`onReportItem($refid, $category)`](../../../core/plugins/support/comments/comments.php)
- `plg_support_forum` — [`onReportItem($refid, $category)`](../../../core/plugins/support/forum/forum.php)
- `plg_support_kb` — [`onReportItem($refid, $category)`](../../../core/plugins/support/kb/kb.php)
- `plg_support_resources` — [`onReportItem($refid, $category)`](../../../core/plugins/support/resources/resources.php)
- `plg_support_wiki` — [`onReportItem($refid, $category)`](../../../core/plugins/support/wiki/wiki.php)
- `plg_support_wishlist` — [`onReportItem($refid, $category)`](../../../core/plugins/support/wishlist/wishlist.php)

## `support.onTicketComment`

Fired from:

- [`core/components/com_support/admin/views/tickets/tmpl/edit.php:392`](../../../core/components/com_support/admin/views/tickets/tmpl/edit.php#L392) with `[$this->row]`
- [`core/components/com_support/site/views/tickets/tmpl/ticket.php:575`](../../../core/components/com_support/site/views/tickets/tmpl/ticket.php#L575) with `[$this->row]`

No plugin in the source tree listens for this event.

## `support.onTicketSubmission`

Fired from:

- [`core/components/com_support/site/controllers/tickets.php:1409`](../../../core/components/com_support/site/controllers/tickets.php#L1409) with `[$row]`

Listeners:

- `plg_support_slack` — [`onTicketSubmission($ticket)`](../../../core/plugins/support/slack/slack.php)

## `support.onTicketUpdate`

Fired from:

- [`core/components/com_support/admin/controllers/tickets.php:578`](../../../core/components/com_support/admin/controllers/tickets.php#L578) with `[$ticket, $comment]`
- [`core/components/com_support/site/controllers/tickets.php:1793`](../../../core/components/com_support/site/controllers/tickets.php#L1793) with `[$row, $rowc]`

Listeners:

- `plg_support_slack` — [`onTicketUpdate($ticket, $comment)`](../../../core/plugins/support/slack/slack.php)

## `support.onValidateCaptcha`

No call site in the CMS fires this event; the listener may answer an event fired by another package or by an older plugin.

Listeners:

- `plg_support_captcha` — [`onValidateCaptcha()`](../../../core/plugins/support/captcha/captcha.php)

## `support.onValidateTicketSubmission`

Fired from:

- [`core/components/com_support/site/controllers/tickets.php:910`](../../../core/components/com_support/site/controllers/tickets.php#L910) with `[$reporter, $problem]`

No plugin in the source tree listens for this event.
