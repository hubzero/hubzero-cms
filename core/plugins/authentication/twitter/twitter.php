<?php

namespace Plugins\Authentication\Twitter;

use Hubzero\Plugin\Plugin;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

class Twitter extends \Hubzero\Plugin\OauthClient
{
    /**
     * Affects constructor behavior.
     * If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * The initialized OAuth2 provider.
     *
     * @var  \Smolblog\OAuth2\Client\Provider\Twitter
     */
    private $provider = null;

    /**
     * Session scope key for namespacing session variables.
     *
     * @var  string
     */
    private $name = 'twitter';

    /**
     * Initialize the OAuth2 provider.
     *
     * @param   object  $subject
     * @param   array   $config
     */
    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        $this->provider = new \Smolblog\OAuth2\Client\Provider\Twitter([
            'clientId'     => $this->params->get('app_id'),
            'clientSecret' => $this->params->get('app_secret'),
            'redirectUri'  => $this->getReturnUrl(),
        ]);
    }

    /**
     * Perform logout (not currently used)
     *
     * @return  void
     */
    public function logout()
    {
        // Cannot be done server side with current API
    }

    /**
     * Check login status of current user with regards to twitter
     *
     * @return  array  $status
     */
    public function status()
    {
        // No JS SDK available for server-side status check
    }

    /**
     * Method to call when redirected back from twitter after authentication.
     * Grab the return URL if set and handle denial of app privileges.
     *
     * @param   object  $credentials
     * @param   object  $options
     * @return  void
     */
    public function login(&$credentials, &$options)
    {
        $return = '';
        $b64dreturn = '';

        if ($return = Session::get('returnUrl', null, $this->name)) {
            $b64dreturn = base64_decode($return);

            if (!\Hubzero\Utility\Uri::isInternal($b64dreturn)) {
                $b64dreturn = '';
            }
        }

        $options['return'] = $b64dreturn;

        Session::clear('returnUrl', $this->name);

        // Check to make sure they didn't deny our application permissions
        if (Request::getVar('error', null)) {
            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . $return),
                Lang::txt('PLG_AUTHENTICATION_TWITTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
                'error'
            );
        }
    }

    /**
     * Method to setup twitter params and redirect to twitter auth URL
     *
     * @param   object  $view  view object
     * @param   object  $tpl   template object
     * @return  void
     */
    public function display($view, $tpl)
    {
        $params = array(
            'scope' => ['tweet.read', 'users.read', 'users.email', 'offline.access'],
            'redirect_uri' => $this->getReturnUrl()
        );

        $loginUrl = $this->provider->getAuthorizationUrl($params);

        Session::set('oauth2state', $this->provider->getState(), $this->name);
        Session::set('oauth2verifier', $this->provider->getPkceVerifier(), $this->name);
        Session::set('returnUrl', $view->return, $this->name);

        App::redirect($loginUrl);
    }

    /**
     * This method should handle any authentication and report back to the subject
     *
     * @param   array    $credentials  Array holding the user credentials
     * @param   array    $options      Array of extra options
     * @param   object   $response     Authentication response object
     * @return  boolean
     */
    public function onAuthenticate($credentials, $options, &$response)
    {
        $this->onUserAuthenticate($credentials, $options, $response);
    }

    /**
     * This method should handle any authentication and report back to the subject
     *
     * @param   array    $credentials  Array holding the user credentials
     * @param   array    $options      Array of extra options
     * @param   object   $response     Authentication response object
     * @return  boolean
     */
    public function onUserAuthenticate($credentials, $options, &$response)
    {
        $code = Request::getVar('code', null);
        $state = Request::getVar('state', null);

        if ($code == null) {
            $authUrl = $this->provider->getAuthorizationUrl(array(
                'scope' => ['tweet.read', 'users.read', 'users.email', 'offline.access']
            ));

            Session::set('oauth2state', $this->provider->getState(), $this->name);
            Session::set('oauth2verifier', $this->provider->getPkceVerifier(), $this->name);

            App::redirect($authUrl);
        } elseif ($state !== Session::get('oauth2state', null, $this->name)) {
            Session::clear('oauth2state', $this->name);
            Session::clear('oauth2verifier', $this->name);

            $response->status = \Hubzero\Auth\Status::FAILURE;
            $response->error_message = Lang::txt(
                'PLG_AUTHENTICATION_TWITTER_ERROR_RETRIEVING_PROFILE',
                'Mismatched state'
            );

            return;
        }

        $token = $this->provider->getAccessToken('authorization_code', array(
            'code' => Request::getString('code'),
            'code_verifier' => Session::get('oauth2verifier', null, $this->name),
        ));

        Session::clear('oauth2state', $this->name);
        Session::clear('oauth2verifier', $this->name);

        if (isset($token) && $token) {
            try {
                $owner = $this->provider->getResourceOwner($token);

                $id       = $owner->getId();
                $fullname = $owner->getName();
                $username = $owner->getUsername();
                $email    = $owner->getEmail();
            } catch (\Exception $e) {
                $response->status = \Hubzero\Auth\Status::FAILURE;
                $response->error_message = Lang::txt(
                    'PLG_AUTHENTICATION_TWITTER_ERROR_RETRIEVING_PROFILE',
                    $e->getMessage()
                );
                return;
            }

            // Create the hubzero auth link
            $method = (Component::params('com_members')->get('allowUserRegistration', false))
                ? 'find_or_create'
                : 'find';
            $hzal = \Hubzero\Auth\Link::$method('authentication', $this->name, null, $id);

            if ($hzal === false) {
                $response->status = \Hubzero\Auth\Status::FAILURE;
                $response->error_message = Lang::txt('PLG_AUTHENTICATION_TWITTER_UNKNOWN_USER');
                return;
            }

            // Set response variables
            $response->auth_link = $hzal;
            $response->type      = $this->name;
            $response->status    = \Hubzero\Auth\Status::SUCCESS;
            $response->fullname  = $fullname;

            if ($hzal->user_id) {
                $user = User::getInstance($hzal->user_id);

                $response->username = $user->username;
                $response->email    = $user->email;
                $response->fullname = $user->name;
            } else {
                $response->username = '-' . $hzal->id;
                $response->email    = $response->username . '@invalid';

                // Also set a suggested username for their hub account
                if ($username) {
                    Session::set('auth_link.tmp_username', $username);
                } elseif ($email) {
                    $sub_email = explode('@', $email, 2);
                    Session::set('auth_link.tmp_username', $sub_email[0]);
                }
            }

            $hzal->update();

            // If we have a real user, drop the authenticator cookie
            if (isset($user) && is_object($user)) {
                $prefs = array(
                    'user_id'       => $user->get('id'),
                    'user_img'      => $owner->getImageUrl(),
                    'authenticator' => $this->name,
                );

                $namespace = 'authenticator';
                $lifetime  = time() + 365 * 24 * 60 * 60;

                \Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
            }
        } else {
            $response->status = \Hubzero\Auth\Status::FAILURE;
            $response->error_message = Lang::txt('PLG_AUTHENTICATION_TWITTER_AUTHENTICATION_FAILED');
        }
    }

    /**
     * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
     *
     * @param   array  $options
     * @return  void
     */
    public function link($options = array())
    {
        $code = Request::getVar('code', null);
        $state = Request::getVar('state', null);

        if ($code == null) {
            $authUrl = $this->provider->getAuthorizationUrl(array(
                'scope' => ['tweet.read', 'users.read', 'users.email', 'offline.access']
            ));

            Session::set('oauth2state', $this->provider->getState(), $this->name);
            Session::set('oauth2verifier', $this->provider->getPkceVerifier(), $this->name);

            App::redirect($authUrl);
        } elseif ($state !== Session::get('oauth2state', null, $this->name)) {
            Session::clear('oauth2state', $this->name);
            Session::clear('oauth2verifier', $this->name);

            App::redirect(
                Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                Lang::txt('PLG_AUTHENTICATION_TWITTER_ERROR'),
                'error'
            );
            return;
        }

        $token = $this->provider->getAccessToken('authorization_code', array(
            'code' => Request::getString('code'),
            'code_verifier' => Session::get('oauth2verifier', null, $this->name),
        ));

        Session::clear('oauth2state', $this->name);
        Session::clear('oauth2verifier', $this->name);

        if (isset($token) && $token) {
            try {
                $owner = $this->provider->getResourceOwner($token);
                $id    = $owner->getId();
            } catch (\Exception $e) {
                App::redirect(
                    Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                    Lang::txt('PLG_AUTHENTICATION_TWITTER_ERROR'),
                    'error'
                );
                return;
            }

            $hzad = \Hubzero\Auth\Domain::getInstance('authentication', $this->name, '');

            // Create the link
            if (\Hubzero\Auth\Link::getInstance($hzad->id, $id)) {
                // This account is already linked to another hub account
                App::redirect(
                    Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                    Lang::txt('PLG_AUTHENTICATION_TWITTER_ACCOUNT_ALREADY_LINKED'),
                    'error'
                );
            } else {
                $hzal = \Hubzero\Auth\Link::find_or_create('authentication', $this->name, null, $id);

                if ($hzal) {
                    $hzal->set('user_id', User::get('id'));
                    $hzal->update();
                } else {
                    Log::error(sprintf(
                        'Hubzero\Auth\Link::find_or_create("authentication", "twitter", null, %s) returned false',
                        $id
                    ));
                }
            }
        } else {
            // User didn't authorize our app, or, clicked cancel
            App::redirect(
                Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                Lang::txt('PLG_AUTHENTICATION_TWITTER_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
                'error'
            );
        }
    }

    /**
     * Generate return url
     *
     * @param   string  $return  url
     * @param   bool    $encode  whether or not to encode return before using
     * @return  string  url
     */
    private function getReturnUrl($return = null, $encode = false)
    {
        $service = trim(Request::base(), '/');

        if (empty($service)) {
            $service = $_SERVER['HTTP_HOST'];
        }

        $rtrn = '';
        if (isset($return) && !empty($return)) {
            if ($encode) {
                $return = base64_encode($return);
            }
            $rtrn = '&return=' . $return;
        }

        return self::getRedirectUri($this->name) . $rtrn;
    }

    /**
     * Display login button
     *
     * @param   string  $return
     * @return  string
     */
    public static function onRenderOption($return = null)
    {
        Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/twitter/assets/css/twitter.css');

        $html = '<a class="twitter account" href="'
            . Route::url('index.php?option=com_users&view=login&authenticator=twitter'
            . $return)
            . '">';

            $html .= '<div class="signin">';
                $html .= Lang::txt('PLG_AUTHENTICATION_TWITTER_SIGN_IN');
            $html .= '</div>';
        $html .= '</a>';

        return $html;
    }
}
