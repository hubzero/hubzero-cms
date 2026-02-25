<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oauth\Api\Controllers;

use Hubzero\Component\ApiController;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\User;
use OAuthProvider;

/**
 * Short description for 'OauthApiController'
 *
 * Long description (if any) ...
 */
class OauthControllerApi extends ApiController
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_provider;
    protected $segments;
    protected $view;

    /**
     * Short description for 'execute'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    public function execute()
    {
        switch ($this->segments[0]) {
            case 'request_token':
                $this->requestToken();
                break;
            case 'authorize':
                $this->authorize();
                break;
            case 'access_token':
                $this->accessToken();
                break;
            case 'token_info':
                $this->tokenInfo();
                break;
            case 'consumer_request_test':
                $this->consumerRequestTest();
                break;
            case 'unsigned_request_test':
                $this->unsignedRequestTest();
                break;
            default:
                $this->notFound();
                break;
        }
    }

    private function consumerRequestTest()
    {
        if (empty($this->_request->validApiKey)) {
            $this->send('No API Key', 401);
            return;
        }

        $this->send('Consumer Request Test OK', 200);
    }

    private function unsignedRequestTest()
    {
        $this->send('Unsigned Request Test OK', 200);
    }

    /**
     * Short description for 'tokenInfo'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    private function tokenInfo()
    {
        $this->send($this->_provider->getTokenData(), 200);
    }

    /**
     * Short description for 'notFound'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    private function notFound()
    {
        $this->send('Not Found', 404);
    }

    /**
     *  Token Request Endpoint
     *
     *  The client obtains a set of token credentials from the server by
     *  making an authenticated (RFC5849 Section 3) HTTP "POST" request to
     *  the Token Request endpoint
     *
     * @return     void
     */
    private function requestToken()
    {
        if (empty($this->_provider)) {
            $this->send('Bad Request', 400);
            return;
        }

        $callback_url = Request::getString('oauth_callback', '');

        $token = sha1(OAuthProvider::generateToken(20, false));
        $token_secret = sha1(OAuthProvider::generateToken(20, false));
        $verifier = sha1(OAuthProvider::generateToken(20, false));

        $db = App::get('db');

        $consumer_data = $this->_provider->getConsumerData();

        if (empty($consumer_data)) {
            $this->send('Internal Server Error', 500);
            return;
        }

        if ((empty($callback_url)) || ($callback_url == 'oob')) {
            $callback_url = $consumer_data->callback_url;
        }

        $sql = "INSERT INTO `#__oauthp_tokens` "
            . "(consumer_id,user_id,state,token,token_secret,callback_url,verifier,created) VALUES ("
            . $db->Quote($consumer_data->id) . ", '0', '1', "
            . $db->Quote($token) . "," . $db->Quote($token_secret) . ", "
            . $db->Quote($callback_url) . ", " . $db->Quote($verifier) . ", UTC_TIMESTAMP());";
        $db->setQuery($sql);

        if (!$db->query()) {
            $this->send('Internal Server Error', 500);
        } else {
            $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
            $msg = "oauth_token=" . $token . "&oauth_token_secret=" . $token_secret
                . "&oauth_callback_confirmed=true";
            $this->send($msg, 200);
        }
    }

    /**
     * Short description for 'authorize'
     *
     * Long description (if any) ...
     *
     * @return     boolean Return description (if any) ...
     */
    private function authorize()
    {
        $oauth_token = Request::getString('oauth_token');

        if (empty($oauth_token)) {
            $this->view->setLayout('notoken');
        }

        $db = App::get('db');

        $sql = "SELECT * FROM `#__oauthp_tokens` WHERE token=" . $db->Quote($oauth_token)
            . " AND user_id=0 LIMIT 1;";
        $db->setQuery($sql);

        $result = $db->loadObject();

        if ($result === false) {
            $this->view->setLayout('internalerror');
        }

        if (empty($result)) {
            $this->view->setLayout('invalidtoken');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->view = new \Hubzero\Component\View(array(
                'base_path' => dirname(dirname(__DIR__)) . '/site',
                'name'      => 'authorize',
                'layout'    => 'authorize'
            ));


            $this->view->oauth_token = $oauth_token;
            $this->view->form_action = '/api/oauth/authorize';
            $this->view->display();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = Request::get('username');
            $password = Request::get('password');

            if (true) {
                // user grants application 'consumer_key' permission to act on their behalf
                // so record: user_id consumer_key accesstoken #acl

                // $db->setQuery("SELECT access_token FROM `#__user_accesstokens` "
                //     . "WHERE user_id=" . $db->Quote($useraccount->getUserId())
                //     . " consumer_key=" . $db->Quote($this->_provider->consumer_key));

                if (!empty($result->callback_url)) {
                    $redirectUrl = $result->callback_url . "?oauth_token="
                        . $_REQUEST['oauth_token'] . "&oauth_verifier=" . $result->verifier;
                    $this->response->headers->set('Location', $redirectUrl);
                    $this->send('Redirect', 302);
                }

                return true;
            }

            $this->send("Invalid Request", 400);
            return;
        }

        $this->send("Internal Server Error", 500);
        return false;
    }

    /**
     * Short description for 'accessToken'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    private function accessToken()
    {
        if (empty($this->_provider)) {
            $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
            $this->send('oauth_problem=bad oauth provider', 501);
            return;
        }

        $xauth_request = false;

        $header = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        }

        // @FIXME: header check is inexact and could give false positives
        // @FIXME: pecl oauth provider doesn't handle x_auth in header
        // @FIXME: api application should convert xauth variables in
        //         header to form/query data as workaround
        // @FIXME: this code is here for future use if/when pecl oauth
        //         provider is fixed
        //
        if (
            isset($_GET['x_auth_mode'])
            || isset($_GET['x_auth_username'])
            || isset($_GET['x_auth_password'])
            || isset($_POST['x_auth_mode'])
            || isset($_POST['x_auth_username'])
            || isset($_POST['x_auth_password'])
            || (strpos($header, 'x_auth_mode') !== false)
            || (strpos($header, 'x_auth_username') !== false)
            || (strpos($header, 'x_auth_mode') !== false)
        ) {
            $xauth_request = true;
        }

        if ($xauth_request) {
            if ($this->_provider->getConsumerData()->xauth == '0') {
                $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
                $this->send('oauth_problem=permission_denied', 401);
                return;
            }

            if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
                $this->send('SSL Required', 403);
                return;
            }

            if (isset($this->_provider->x_auth_mode)) {
                $x_auth_mode = $this->_provider->x_auth_mode;
            } elseif (isset($_POST['x_auth_mode'])) {
                $x_auth_mode = $_POST['x_auth_mode'];
            } elseif (isset($_GET['x_auth_mode'])) {
                $x_auth_mode = $_GET['x_auth_mode'];
            } else {
                $x_auth_mode = '';
            }

            if (isset($this->_provider->x_auth_username)) {
                $x_auth_username = $this->_provider->x_auth_username;
            } elseif (isset($_POST['x_auth_username'])) {
                $x_auth_username = $_POST['x_auth_username'];
            } elseif (isset($_GET['x_auth_username'])) {
                $x_auth_username = $_GET['x_auth_username'];
            } else {
                $x_auth_username = '';
            }

            if (isset($this->_provider->x_auth_password)) {
                $x_auth_password = $this->_provider->x_auth_password;
            } elseif (isset($_POST['x_auth_password'])) {
                $x_auth_password = $_POST['x_auth_password'];
            } elseif (isset($_GET['x_auth_password'])) {
                $x_auth_password = $_GET['x_auth_password'];
            } else {
                $x_auth_password = '';
            }

            if ($x_auth_mode != 'client_auth') {
                $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
                $this->send('oauth_problem=permission_denied', 400);
                return;
            }

            $match = \Hubzero\User\Password::passwordMatches($x_auth_username, $x_auth_password, true);

            if (!$match) {
                $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
                $this->send('oauth_problem=permission_denied', 401);
                return;
            }

            $useraccount = User::getInstance($x_auth_username);

            $db = App::get('db');

            $db->setQuery("SELECT token,token_secret FROM `#__oauthp_tokens` WHERE consumer_id="
                . $db->Quote($this->_provider->getConsumerData()->id) . " AND user_id ="
                . $db->Quote($useraccount->get('id')) . " LIMIT 1;");

            $result = $db->loadObject();

            if ($result === false) {
                $this->send('Internal Server Error', 500);
                return;
            }

            if (!is_object($result)) {
                if ($this->_provider->getConsumerData()->xauth_grant < 1) {
                    $this->send('Internal Server Error', 501);
                    return;
                }

                $token = sha1(OAuthProvider::generateToken(20, false));
                $token_secret = sha1(OAuthProvider::generateToken(20, false));

                $db = App::get('db');

                $sql = "INSERT INTO #__oauthp_tokens "
                    . "(consumer_id,user_id,state,token,token_secret,callback_url) VALUE ("
                    . $db->Quote($this->_provider->getConsumerData()->id) . ","
                    . $db->Quote($useraccount->get('id')) . ",'1',"
                    . $db->Quote($token) . "," . $db->Quote($token_secret) . ","
                    . $db->Quote($this->_provider->getConsumerData()->callback_url) . ");";
                $db->setQuery($sql);

                if (!$db->query()) {
                    $this->send('Internal Server Error', 502);
                    return;
                }

                if ($db->getAffectedRows() < 1) {
                    $this->send('Internal Server Error', 503);
                    return;
                }

                $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
                $msg = "oauth_token=" . $token . "&oauth_token_secret=" . $token_secret;
                $this->send($msg, 200);
            } else {
                $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
                $msg = "oauth_token=" . $result->token . "&oauth_token_secret=" . $result->token_secret;
                $this->send($msg, 200);
            }

            return;
        } else {
            $this->send('Internal Server Error', 503);
            return;

            // @FIXME: we don't support 3-legged auth yet
            // lookup request token to access token, give out access token
            // check verifier
            // check used flag
            $this->response->headers->set('Content-Type', 'application/x-www-form-urlencoded');
            $this->send("oauth_token=" . $token . "&oauth_token_secret=" . $token_secret, 200);
            return;
        }
    }
}
