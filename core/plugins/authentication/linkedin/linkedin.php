<?php

use Hubzero\Plugin\Plugin;

// phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

class plgAuthenticationLinkedIn extends \Hubzero\Plugin\OauthClient
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
     * @var  \League\OAuth2\Client\Provider\LinkedIn
     */
    private $provider = null;

    /**
     * Session scope key for namespacing session variables.
     *
     * @var  string
     */
    private $name = 'linkedin';

    /**
     * Initialize the OAuth2 provider.
     *
     * @param   object  $subject
     * @param   array   $config
     */
    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);

        $this->provider = new \League\OAuth2\Client\Provider\LinkedIn([
            'clientId'     => $this->params->get('api_key'),
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
        // Cannot be done server side with LinkedIn's current API
    }

    /**
     * Check login status of current user with regards to LinkedIn
     *
     * @return  array  $status
     */
    public function status()
    {
        // LinkedIn deprecated their JS SDK; server-side status check
        // is not feasible with current API limitations
    }

    /**
     * Method to call when redirected back from LinkedIn after authentication.
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
                Lang::txt('PLG_AUTHENTICATION_LINKEDIN_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
                'error'
            );
        }
    }

    /**
     * Method to setup LinkedIn params and redirect to LinkedIn auth URL
     *
     * @param   object  $view  view object
     * @param   object  $tpl   template object
     * @return  void
     */
    public function display($view, $tpl)
    {
        $params = array(
            'scope' => ['r_liteprofile', 'r_emailaddress'],
            'redirect_uri' => $this->getReturnUrl()
        );

        $loginUrl = $this->provider->getAuthorizationUrl($params);

        Session::set('oauth2state', $this->provider->getState(), $this->name);
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
        return $this->onUserAuthenticate($credentials, $options, $response);
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
                'scope' => ['r_liteprofile', 'r_emailaddress']
            ));

            Session::set('oauth2state', $this->provider->getState(), $this->name);

            App::redirect($authUrl);
        } elseif (!$this->hasValidState($state)) {
            Session::clear('oauth2state', $this->name);

            $response->status = \Hubzero\Auth\Status::FAILURE;
            $response->error_message = Lang::txt(
                'PLG_AUTHENTICATION_LINKEDIN_ERROR_RETRIEVING_PROFILE',
                'Mismatched state'
            );

            return;
        }

        $token = $this->provider->getAccessToken(
            'authorization_code',
            array('code' => Request::getString('code'))
        );

        Session::clear('oauth2state', $this->name);

        if (isset($token) && $token) {
            try {
                $owner = $this->provider->getResourceOwner($token);

                $id        = $owner->getId();
                $firstname = $owner->getFirstName();
                $lastname  = $owner->getLastName();
                $fullname  = trim($firstname . ' ' . $lastname);
                $email     = $owner->getEmail();
            } catch (\Exception $e) {
                $response->status = \Hubzero\Auth\Status::FAILURE;
                $response->error_message = Lang::txt(
                    'PLG_AUTHENTICATION_LINKEDIN_ERROR_RETRIEVING_PROFILE',
                    $e->getMessage()
                );
                return;
            }

            // Adopt a pre-v2 link for this member before looking one up
            $this->migrateLegacyLink($id, $email);

            // Create the hubzero auth link
            $method = (Component::params('com_members')->get('allowUserRegistration', false))
                ? 'find_or_create'
                : 'find';
            $hzal = \Hubzero\Auth\Link::$method('authentication', $this->name, null, $id);

            if ($hzal === false) {
                $response->status = \Hubzero\Auth\Status::FAILURE;
                $response->error_message = Lang::txt('PLG_AUTHENTICATION_LINKEDIN_UNKNOWN_USER');
                return;
            }

            $hzal->set('email', $email);

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
                if ($email) {
                    $sub_email    = explode('@', $email, 2);
                    $tmp_username = $sub_email[0];
                    Session::set('auth_link.tmp_username', $tmp_username);
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
            $response->error_message = Lang::txt('PLG_AUTHENTICATION_LINKEDIN_AUTHENTICATION_FAILED');
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
                'scope' => ['r_liteprofile', 'r_emailaddress']
            ));

            Session::set('oauth2state', $this->provider->getState(), $this->name);

            App::redirect($authUrl);
        } elseif (!$this->hasValidState($state)) {
            Session::clear('oauth2state', $this->name);

            App::redirect(
                Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ERROR'),
                'error'
            );
            return;
        }

        $token = $this->provider->getAccessToken(
            'authorization_code',
            array('code' => Request::getString('code'))
        );

        Session::clear('oauth2state', $this->name);

        if (isset($token) && $token) {
            try {
                $owner = $this->provider->getResourceOwner($token);
                $id    = $owner->getId();
                $email = $owner->getEmail();
            } catch (\Exception $e) {
                App::redirect(
                    Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                    Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ERROR'),
                    'error'
                );
                return;
            }

            // Adopt a pre-v2 link for this member before looking one up
            $this->migrateLegacyLink($id, $email);

            $hzad = \Hubzero\Auth\Domain::getInstance('authentication', $this->name, '');

            // Create the link
            if (\Hubzero\Auth\Link::getInstance($hzad->id, $id)) {
                // This account is already linked to another hub account
                App::redirect(
                    Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                    Lang::txt('PLG_AUTHENTICATION_LINKEDIN_ACCOUNT_ALREADY_LINKED'),
                    'error'
                );
            } else {
                $hzal = \Hubzero\Auth\Link::find_or_create('authentication', $this->name, null, $id);

                if ($hzal) {
                    $hzal->set('user_id', User::get('id'));
                    $hzal->set('email', $email);
                    $hzal->update();
                } else {
                    Log::error(sprintf(
                        'Hubzero\Auth\Link::find_or_create("authentication", "linkedin", null, %s) returned false',
                        $id
                    ));
                }
            }
        } else {
            // User didn't authorize our app, or, clicked cancel
            App::redirect(
                Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                Lang::txt('PLG_AUTHENTICATION_LINKEDIN_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
                'error'
            );
        }
    }

    /**
     * Re-key a pre-v2 LinkedIn auth link onto the current member ID
     *
     * LinkedIn's v1 profile API and the v2 endpoint this plugin now uses issue
     * member IDs from different ID spaces, so a link stored before the v2
     * migration will never match on username alone and the member would be
     * treated as a brand new user. Where exactly one link in this domain
     * carries the same verified email, adopt that row instead.
     *
     * @param   string  $id     Current (v2) LinkedIn member ID
     * @param   string  $email  Verified email address reported by LinkedIn
     * @return  void
     */
    private function migrateLegacyLink($id, $email)
    {
        if (empty($id) || empty($email))
        {
            return;
        }

        $hzad = \Hubzero\Auth\Domain::find_or_create('authentication', $this->name, null);

        if (!is_object($hzad) || !$hzad->get('id'))
        {
            return;
        }

        // Already keyed on the current member ID - nothing to migrate
        if (\Hubzero\Auth\Link::getInstance($hzad->get('id'), $id))
        {
            return;
        }

        $rows = \Hubzero\Auth\Link::all()
            ->whereEquals('auth_domain_id', $hzad->get('id'))
            ->whereEquals('email', $email)
            ->rows();

        // Only adopt an unambiguous match
        if (!$rows || $rows->count() != 1)
        {
            return;
        }

        $row = $rows->first();

        if (!$row->get('id') || $row->get('username') == $id)
        {
            return;
        }

        $legacy = $row->get('username');
        $row->set('username', $id);

        if ($row->update())
        {
            Log::auth(sprintf(
                'Re-keyed LinkedIn auth link %s from legacy member ID "%s" to "%s"',
                $row->get('id'),
                $legacy,
                $id
            ));
        }
    }

    /**
     * Verify the OAuth state parameter against the one issued for this session
     *
     * A plain !== comparison is not sufficient: when the request carries no
     * state and the session holds none, null !== null is false and the guard
     * passes, letting an attacker feed us their own authorization code. Treat a
     * missing value on either side as a failure and compare in constant time.
     *
     * @param   mixed    $state  State parameter from the request
     * @return  bool     whether the state matches the one issued
     */
    private function hasValidState($state)
    {
        $expected = Session::get('oauth2state', null, $this->name);

        if (!is_string($state) || $state === '' || !is_string($expected) || $expected === '')
        {
            return false;
        }

        return hash_equals($expected, $state);
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
        Document::addStylesheet(Request::root(false) . 'core/plugins/authentication/linkedin/assets/css/linkedin.css');

        $html = '<a class=\"linkedin account\" href=\"'
            . Route::url('index.php?option=com_users&view=login&authenticator=linkedin' . $return) . '\">';
            $html .= '<div class="signin">';
                $html .= Lang::txt('PLG_AUTHENTICATION_LINKEDIN_SIGN_IN');
            $html .= '</div>';
        $html .= '</a>';

        return $html;
    }
}
