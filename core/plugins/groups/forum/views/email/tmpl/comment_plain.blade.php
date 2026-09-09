{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}
@php
$base = rtrim(Request::base(), '/');
$sef  = Route::url($thread->link());
$link = $base . '/' . trim($sef, '/');

// Build message
$message = '';
if ($delimiter) {
    $message .= $delimiter . "\n";

    if (Component::params('com_groups')->get('email_comment_processing')) {
        $message .= Lang::txt('PLG_GROUPS_FORUM_EMAIL_REPLY_ABOVE') . "\n";
    }

    $message .= 'Message from '
        . $base
        . ' / '
        . Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD', $thread->get('id'))
        . "\n";
}
$message .= ($post->get('anonymous')) ? Lang::txt('JANONYMOUS') : $post->creator->get('name')
    . ' ('
    . $post->creator->get('username')
    . ')';
$message .= ' wrote (in '
    . $group->get('description')
    . ': '
    . $section->get('title')
    . ' - '
    . $category->get('title')
    . ' - '
    . $thread->get('title')
    . '):';

$output = html_entity_decode(strip_tags($post->content ?? ''), ENT_COMPAT, 'UTF-8');
$output = preg_replace_callback(
    "/(&#[0-9]+;)/",
    function ($m) {
        return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
    },
    $output
);

$message .= $output;

$attachments = $post->attachments()
    ->whereIn('state', [\Components\Forum\Models\Post::STATE_PUBLISHED])
    ->rows();
if ($attachments->count() > 0) {
    $message .= "\n\n";
    foreach ($attachments as $attachment) {
        $message .= $base
            . '/'
            . trim(Route::url($thread->link()), '/')
            . '/'
            . $attachment->get('post_id')
            . '/'
            . $attachment->get('filename')
            . "\n";
    }
}

if ($unsubscribe) {
    $message .= "\n\n" . Lang::txt('PLG_GROUPS_FORUM_EMAIL_UNSUBSCRIBE') . ":\n" . $__view->get('unsubscribe');
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
@endphp
