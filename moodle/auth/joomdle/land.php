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
 * 2008-11-01  File created.
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once $CFG->dirroot . '/mnet/xmlrpc/client.php';
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/joomdle/auth.php');


if (!$site = get_site()) {
    print_error('mnet_session_prohibited', 'mnet', '', '');
}

if (!is_enabled_auth('mnet')) {
    error('mnet is disabled');
}
// grab the GET params
$token         = optional_param('token',    PARAM_TEXT);
$username = optional_param('username',      PARAM_TEXT);
$create_user = optional_param('create_user',      PARAM_TEXT);
$wantsurl      = optional_param('wantsurl', PARAM_TEXT);
$use_wrapper      = optional_param('use_wrapper', PARAM_TEXT);
$id      = optional_param('id', PARAM_TEXT);
$mtype      = optional_param('mtype', PARAM_TEXT);
$day      = optional_param('day', PARAM_TEXT);
$mon      = optional_param('mon', PARAM_TEXT);
$year      = optional_param('year', PARAM_TEXT);
$itemid      = optional_param('Itemid', PARAM_TEXT);

$override_itemid = auth_plugin_joomdle::call_method ('getDefaultItemid');

if ($override_itemid)
	$itemid = $override_itemid;


if ($username != 'guest')
{
	/* Logged user trying to access */
	$logged = auth_plugin_joomdle::call_method ("confirmJoomlaSession", $username, $token);

	if (is_array ($logged) && xmlrpc_is_fault($logged)) {
	    trigger_error("xmlrpc: $response[faultString] ($response[faultCode])");
	} else 
		if ($logged) {
			// log in
			$user = get_complete_user_data('username', $username);
			if (!$user)
			{
				if ($create_user)
					auth_plugin_joomdle::create_joomdle_user ($username); //XXX
				else
				{
					/* If the user does not exists and we don't have to create it, we are done */
					$redirect_url = get_config (NULL, 'joomla_url');
					redirect($redirect_url);
				}

			}
			$user = get_complete_user_data('username', $username);
			complete_user_login($user);

			if (!empty($localuser->mnet_foreign_host_array)) {
			    $user->mnet_foreign_host_array = $localuser->mnet_foreign_host_array;
			}
	} //logged
} //username != guest
			// redirect
			if ($use_wrapper)
			{
				$redirect_url = get_config (NULL, 'joomla_url');
				switch ($mtype) 
				{
					case "event":
						$redirect_url .= "/index.php?option=com_joomdle&view=wrapper&moodle_page_type=$mtype&id=$id&day=$day&mon=$mon&year=$year&Itemid=$itemid";
						break;
					case "course":
					case "news":
						$redirect_url .= "/index.php?option=com_joomdle&view=wrapper&moodle_page_type=$mtype&id=$id&Itemid=$itemid";
						break;
					case "user":
						$redirect_url .= "/index.php?option=com_joomdle&view=wrapper&moodle_page_type=$mtype&id=$id&Itemid=$itemid";
						break;
					default:
						if ($wantsurl)
							$redirect_url =  urldecode ($wantsurl) ;
						else
							$redirect_url = get_config (NULL, 'joomla_url');
				} 
			}
			else
			{
			
				$redirect_url = $CFG->wwwroot;
				switch ($mtype)
				{
					case "course":
						$redirect_url .= "/course/view.php?id=$id";
						break;
					case "news":
						$redirect_url .= "/mod/forum/discuss.php?id=$id";
						break;
					case "event":
						$redirect_url .= "/calendar/view.php?view=day&cal_d=$day&cal_m=$mon&cal_y=$year";
						break;
					case "user":
						$redirect_url .= "/user/view.php?id=$id";
						break;
					case "topic":
						$redirect_url .= "/mod/assignment/view.php?id=$id";
						break;
					default:
						preg_match('@^(?:https?://)?([^/]+)@i',
						    get_config (NULL, 'joomla_url'), $matches);
						$host = $matches[0];


						/* If not full URL, see if path/host is needed */
						if (($wantsurl) && (substr ($wantsurl, 0, 7) != 'http://'))
						{
							/* If no initial slash, it is a joomla relative path. We add path */
							if ($wantsurl[0] != '/')
							{
								$path = parse_url (get_config (NULL, 'joomla_url'), PHP_URL_PATH);
								$wantsurl = $path.'/'.$wantsurl;
							}


							if ($wantsurl)
								$redirect_url =  $host.urldecode ($wantsurl) ;
								//$redirect_url =  urldecode ($wantsurl) ;
							else
								$redirect_url = get_config (NULL, 'joomla_url');
								//$redirect_url = get_config (NULL, 'joomla_url');
						}
						else $redirect_url = $wantsurl;

				}
			}

redirect($redirect_url);

?>
