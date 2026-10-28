<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Helpers;

use Components\Support\Models\Ticket;
use Components\Support\Models\Comment;
use Hubzero\Mail\View as MailView;
use Hubzero\Mail\Token;
use Exception;
use Component;
use Config;
use Event;
use Route;
use Lang;
use User;

include_once __DIR__ . DS . 'acl.php';
include_once __DIR__ . DS . 'utilities.php';

/**
 * Tells everyone who should know that a ticket was updated
 *
 * One place for what used to be copied between the site form, the admin
 * form and the API: the ticket-update event (which the Slack plugin
 * listens to), who gets e-mailed, the e-mail itself, the record of who
 * was notified on the comment, and the activity log entry.
 */
class Notifier
{
    /**
     * Notify about a saved ticket update
     *
     * The comment and ticket must already be saved. Options:
     *   email_submitter  (bool)  e-mail the ticket's submitter
     *   email_owner      (bool)  e-mail the owner (and a prior owner, or the
     *                            group's managers when there is no owner)
     *   email_terse      (bool)  short e-mail, no attachments, no reply token
     *   option           (string) component name for the message system
     *   controller       (string) controller name for the e-mail view
     *   acl              (ACL)    who may see private comments; default: the
     *                             current user's ACL
     *
     * @param   Ticket   $ticket   The ticket as saved
     * @param   Comment  $comment  The saved comment carrying the changelog
     * @param   Ticket   $before   The ticket as it was before this update
     * @param   array    $options
     * @return  array    Error messages, empty when everything went out
     */
    public static function ticketUpdated(Ticket $ticket, Comment $comment, Ticket $before, array $options = array())
    {
        $options = array_merge(array(
            'email_submitter' => false,
            'email_owner'     => false,
            'email_terse'     => false,
            'option'          => 'com_support',
            'controller'      => 'tickets',
            'acl'             => null,
        ), $options);

        $errors = array();
        $config = Component::params('com_support');

        // The API and the command line do not load a component's strings
        Lang::load('com_support', dirname(__DIR__) . DS . 'site');
        $access = $comment->get('access');

        // Listeners first (Slack and the like), so they hear about every
        // update, whether or not anyone is e-mailed
        Event::trigger('support.onTicketUpdate', array($ticket, $comment));

        // Nothing anyone needs to hear about
        if (
            !$comment->get('comment')
            && $ticket->get('owner') == $before->get('owner')
            && $ticket->get('group_id') == $before->get('group_id')
            && $comment->attachments->count() <= 0
        ) {
            return $errors;
        }

        if ($options['email_submitter'] && !$comment->isPrivate()) {
            // A ticket can be submitted without a (valid) account, so fall
            // back to the address stored on the ticket itself
            if ($ticket->submitter->get('id')) {
                $comment->addTo(array(
                    'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER'),
                    'name'  => $ticket->submitter->get('name', $ticket->get('name')),
                    'email' => $ticket->submitter->get('email', $ticket->get('email')),
                    'id'    => $ticket->submitter->get('id')
                ));
            } elseif ($ticket->get('email')) {
                $comment->addTo($ticket->get('email'));
            }
        }

        if ($options['email_owner']) {
            if ($before->get('owner') && $ticket->get('owner') != $before->get('owner')) {
                $comment->addTo(array(
                    'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_PRIOR_OWNER'),
                    'name'  => $before->assignee->get('name'),
                    'email' => $before->assignee->get('email'),
                    'id'    => $before->assignee->get('id')
                ));
            }

            if ($ticket->get('owner')) {
                $comment->addTo(array(
                    'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER'),
                    'name'  => $ticket->assignee->get('name'),
                    'email' => $ticket->assignee->get('email'),
                    'id'    => $ticket->assignee->get('id')
                ));
            } elseif ($ticket->get('group_id')) {
                $group = \Hubzero\User\Group::getInstance($ticket->get('group_id'));

                if ($group) {
                    foreach ($group->get('managers') as $manager) {
                        $manager = User::getInstance($manager);

                        if (!$manager || !$manager->get('id')) {
                            continue;
                        }

                        $comment->addTo(array(
                            'role'  => Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_GROUPMANAGER'),
                            'name'  => $manager->get('name'),
                            'email' => $manager->get('email'),
                            'id'    => $manager->get('id')
                        ));
                    }
                }
            }
        }

        foreach ($comment->changelog()->get('cc') as $cc) {
            $comment->addTo($cc, Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC'));
        }

        // Watchers see a private comment only if they may read private comments
        $acl = $options['acl'] ?: ACL::getACL();
        foreach ($ticket->watchers as $watcher) {
            $acl->setUser($watcher->user_id);

            if (!$comment->isPrivate() || $acl->check('read', 'private_comments')) {
                $comment->addTo($watcher->user_id, 'watcher');
            }
        }
        $acl->setUser(User::get('id'));

        $recipients = array(
            array('support.tickets', 1)
        );

        if (count($comment->to())) {
            $allowEmailResponses = $config->get('email_processing') && !$options['email_terse'];

            if ($allowEmailResponses) {
                try {
                    $encryptor = new Token();
                } catch (Exception $e) {
                    $allowEmailResponses = false;
                }
            }

            $subject = Lang::txt('COM_SUPPORT_EMAIL_SUBJECT_TICKET_COMMENT', $ticket->get('id'));

            $from = array(
                'name'      => Lang::txt('COM_SUPPORT_EMAIL_FROM', Config::get('sitename')),
                'email'     => Config::get('mailfrom'),
                'multipart' => md5(date('U'))  // Html email
            );

            // The e-mail layouts live with the site views, wherever the
            // caller is running (site, admin or API)
            $config->set('email_terse', (int) $options['email_terse']);

            $eview = new MailView(array(
                'base_path' => dirname(__DIR__) . DS . 'site',
                'name'      => 'emails',
                'layout'    => 'comment_plain'
            ));
            $eview->option     = $options['option'];
            $eview->controller = $options['controller'];
            $eview->comment    = $comment;
            $eview->ticket     = $ticket;
            $eview->config     = $config;
            $eview->delimiter  = ($allowEmailResponses ? '~!~!~!~!~!~!~!~!~!~!' : '');

            $message = array();
            $message['plaintext'] = $eview->loadTemplate(false);
            $message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

            $eview->setLayout('comment_html');
            $message['multipart'] = $eview->loadTemplate();
            $message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

            $message['attachments'] = array();
            if (!$options['email_terse']) {
                foreach ($comment->attachments as $attachment) {
                    if ($attachment->size() < 2097152) {
                        $message['attachments'][] = $attachment->path();
                    }
                }
            }

            // Hub accounts get a message through the message system
            foreach ($comment->to('ids') as $to) {
                $recipients[] = array('user', $to['id']);

                if ($allowEmailResponses) {
                    $token = $encryptor->buildEmailToken(1, 1, $to['id'], $ticket->get('id'));
                    $from['replytoemail'] = 'htc-' . $token . strstr(Config::get('mailfrom'), '@');
                }

                // The handler answers true when sent, false or an error string
                // otherwise; a non-empty array of failures is not success
                $sent = Event::trigger('xmessage.onSendMessage', array('support_reply_submitted', $subject, $message, $from, array($to['id']), $options['option']));
                $failed = empty($sent);
                foreach ((array) $sent as $result) {
                    if ($result !== true) {
                        $failed = true;
                    }
                }

                if ($failed) {
                    $errors[] = Lang::txt('COM_SUPPORT_ERROR_FAILED_TO_MESSAGE', $to['name'] . '(' . $to['role'] . ')');
                    continue;
                }

                if ($to['role'] == 'watcher') {
                    continue;
                }

                $comment->changelog()->notified($to['role'], $to['name'], $to['email']);
            }

            // Bare addresses get plain e-mail
            foreach ($comment->to('emails') as $to) {
                if ($allowEmailResponses) {
                    $token = $encryptor->buildEmailToken(1, 1, -9999, $ticket->get('id'));
                    Utilities::sendEmail($to['email'], $subject, $message, $from, 'htc-' . $token . strstr(Config::get('mailfrom'), '@'));
                } else {
                    Utilities::sendEmail($to['email'], $subject, $message, $from);
                }

                if ($to['role'] == 'watcher') {
                    continue;
                }

                $comment->changelog()->notified($to['role'], $to['name'], $to['email']);
            }
        } elseif (!$comment->get('comment') && $comment->attachments->count() <= 0) {
            // A bare change of fields nobody is told about stays internal
            $comment->set('access', 1);
        }

        if (count($comment->changelog()->get('notifications')) > 0 || $access != $comment->get('access')) {
            if (!$comment->save()) {
                $errors[] = $comment->getError();
            }
        }

        $link = Route::url($ticket->link());
        $label = '<a href="' . $link . '">#' . htmlspecialchars((string) ($ticket->get('id') . ' - ' . $ticket->get('summary')), ENT_QUOTES, 'UTF-8') . '</a>';

        $desc = $comment->get('comment')
            ? Lang::txt('COM_SUPPORT_ACTIVITY_COMMENT_CREATED', $comment->get('id'), $label)
            : Lang::txt('COM_SUPPORT_ACTIVITY_TICKET_UPDATED', $label);

        Event::trigger('system.logActivity', array(
            'activity' => array(
                'action'      => 'created',
                'scope'       => 'support.ticket.comment',
                'scope_id'    => $comment->get('id'),
                'description' => $desc,
                'details'     => array(
                    'id'      => $ticket->get('id'),
                    'summary' => $ticket->get('summary'),
                    'url'     => $link,
                    'comment' => $comment->get('id')
                )
            ),
            'recipients' => $recipients
        ));

        return $errors;
    }
}
