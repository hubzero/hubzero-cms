<?php

/**
 * @author Antonio Duran
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package joomdle
 *
 * Authentication Plugin: Joomdle XMLRPC auth
 *
 * SSO with XMLRPC used to connect with Joomla
 *
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/joomdle/auth.php');

if (array_key_exists ('MoodleSession', $_COOKIE))
{
                $old_session = session_id ();
                session_name ("MoodleSession");
                session_id("");
                //session_destroy();
                session_unregister("USER");
                session_unregister("SESSION");
                setcookie('MoodleSession', '',  time() - 3600, '/','','',0);
                unset($_SESSION);

}
$redirect_url = get_config (NULL, 'joomla_url');
redirect($redirect_url);
