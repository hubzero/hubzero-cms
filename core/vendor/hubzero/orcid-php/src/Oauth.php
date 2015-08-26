<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid;

use Orcid\Http\Curl;
use Exception;

/**
 * Orcid api oauth class
 **/
class Oauth
{
    /**
     * API endpoint constants
     **/
    const HOSTNAME  = 'orcid.org';
    const AUTHORIZE = 'oauth/authorize';
    const TOKEN     = 'oauth/token';

    /**
     * The http tranport object
     *
     * @var  object
     **/
    private $http = null;

    /**
     * The ORCID api access level
     *
     * @var  string
     **/
    private $level = 'pub';

    /**
     * The oauth client ID
     *
     * @var  string
     **/
    private $clientId = null;

    /**
     * The oauth client secret
     *
     * @var  string
     **/
    private $clientSecret = null;

    /**
     * The oauth request scope
     *
     * @var  string
     **/
    private $scope = null;

    /**
     * The oauth request state
     *
     * @var  string
     **/
    private $state = null;

    /**
     * The oauth redirect URI
     *
     * @var  string
     **/
    private $redirectUri = null;

    /**
     * The login/registration page email address
     *
     * @var  string
     **/
    private $email = null;

    /**
     * The login/registration page orcid
     *
     * @var  string
     **/
    private $orcid = null;

    /**
     * The login/registration page family name
     *
     * @var  string
     **/
    private $familyNames = null;

    /**
     * The login/registration page given name
     *
     * @var  string
     **/
    private $givenNames = null;

    /**
     * Whether or not to show the login page as opposed to the registration page
     *
     * @var  bool
     **/
    private $showLogin = false;

    /**
     * The oauth access token
     *
     * @var  string
     **/
    private $accessToken = null;

    /**
     * Constructs a new instance
     *
     * @param   object  $http  a request tranport object to inject
     * @return  void
     * @uses    Orcid\Http\Curl
     **/
    public function __construct($http=null)
    {
        $this->http = $http ?: new Curl;
    }

    /**
     * Sets the oauth instance to use the public api (when needed)
     *
     * @return  $this
     **/
    public function usePublicApi()
    {
        $this->level = 'pub';

        return $this;
    }

    /**
     * Sets the oauth instance to use the members api (when needed)
     *
     * @return  $this
     **/
    public function useMembersApi()
    {
        $this->level = 'api';

        return $this;
    }

    /**
     * Sets the client ID for future use
     *
     * @param   string  $id  the client id
     * @return  $this
     **/
    public function setClientId($id)
    {
        $this->clientId = trim($id);

        return $this;
    }

    /**
     * Sets the client secret for future use
     *
     * @param   string  $secret  the client secret
     * @return  $this
     **/
    public function setClientSecret($secret)
    {
        $this->clientSecret = trim($secret);

        return $this;
    }

    /**
     * Sets the oauth scope
     *
     * This is the scope of the permissions you'll be requesting from the user.
     * See ORCID documentation for options and more details.  Though the doc
     * is somewhat unclear, I don't think you can request more than '/authorize'
     * if you intend to use the public api.
     *
     * @param   string  $scope  the request scope
     * @return  $this
     **/
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Sets the oauth state
     *
     * This isn't necessarily required, but serves as a CSRF check,
     * as well as an easy way to retain information between the inital
     * login redirect and the user coming back to your site.
     *
     * In theory, you should set this and then verify it after it comes back.
     *
     * @param   string  $state  the request state
     * @return  $this
     **/
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Sets the oauth redirect URI
     *
     * This is where the user will come back to after their interaction
     * with the ORCID login/registration page
     *
     * @param   string  $redirectUri  the redirect uri
     * @return  $this
     **/
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * Sets the oauth transaction email address
     *
     * Use this to pre-fill the email address on the login/registration form
     * that ORCID will present when the user is taken to their site for
     * authentication/registration.
     *
     * @param   string  $email  the user's email address (not required)
     * @return  $this
     **/
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Sets the oauth transaction ORCID iD
     *
     * Use this to pre-fill the sign in page shown to the user.
     *
     * @param   string  $orcid  the user's ORCID iD
     * @return  $this
     **/
    public function setOrcid($orcid)
    {
        $this->orcid = $orcid;

        return $this;
    }

    /**
     * Gets the ORCID iD
     *
     * @return  string
     **/
    public function getOrcid()
    {
        return $this->orcid;
    }

    /**
     * Sets the registration page family names
     *
     * @param   string  $familyNames  the registration page family names
     * @return  $this
     **/
    public function setFamilyNames($familyNames)
    {
        $this->familyNames = $familyNames;

        return $this;
    }

    /**
     * Sets the registration page given names
     *
     * @param   string  $givenNames  the registration page given names
     * @return  $this
     **/
    public function setGivenNames($givenNames)
    {
        $this->givenNames = $givenNames;

        return $this;
    }

    /**
     * Sets the show_login flag to tell ORCID to show the login page, rather than
     * the registration page when the user initially arrives
     *
     * @return  $this
     **/
    public function showLogin()
    {
        $this->showLogin = true;

        return $this;
    }

    /**
     * Sets the oauth access token
     *
     * @param   string  $token  the access token to set
     * @return  $this
     **/
    public function setAccessToken($token)
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * Grabs the oauth access token
     *
     * @return  string
     **/
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Gets the authorization URL based on the instance parameters
     *
     * @return  string
     **/
    public function getAuthorizationUrl()
    {
        // Check for required items
        if (!$this->clientId)    throw new Exception('Client ID is required');
        if (!$this->scope)       throw new Exception('Scope is required');
        if (!$this->redirectUri) throw new Exception('Redirect URI is required');

        // Start building url (enpoint is the same for public and member APIs)
        $url  = 'https://' . self::HOSTNAME . '/' . self::AUTHORIZE;
        $url .= '?client_id='    . $this->clientId;
        $url .= '&scope='        . $this->scope;
        $url .= '&redirect_uri=' . urlencode($this->redirectUri);
        $url .= '&response_type=code';

        // Process non-required fields
        if ($this->showLogin)          $url .= '&show_login=true';
        if (isset($this->state))       $url .= '&state=' . $this->state;
        if (isset($this->familyNames)) $url .= '&family_names=' . $this->familyNames;
        if (isset($this->givenNames))  $url .= '&given_names=' . $this->givenNames;
        if (isset($this->email))       $url .= '&email=' . urlencode($this->email);

        return $url;
    }

    /**
     * Takes the given code and requests an auth token
     *
     * @param   string  $code  the oauth code needed to request the access token
     * @return  $this
     * @throws  Exception
     **/
    public function authenticate($code)
    {
        // Validate code
        if (!$code || strlen($code) != 6) throw new Exception('Invalid authorization code');

        // Check for required items
        if (!$this->clientId)     throw new Exception('Client ID is required');
        if (!$this->clientSecret) throw new Exception('Client secret is required');
        if (!$this->redirectUri)  throw new Exception('Redirect URI is required');

        $fields = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code,
            'redirect_uri'  => urlencode($this->redirectUri),
            'grant_type'    => 'authorization_code'
        ];

        $this->http->setUrl('https://' . $this->level . '.' . self::HOSTNAME . '/' . self::TOKEN)
                   ->setPostFields($fields)
                   ->setHeader(['Accept' => 'application/json']);

        $data = json_decode($this->http->execute());

        if (isset($data->access_token))
        {
            $this->setAccessToken($data->access_token);
            $this->setOrcid($data->orcid);
        }
        else
        {
            throw new Exception($data->error_description);
        }

        return $this;
    }

    /**
     * Checks for access token to indicate authentication
     *
     * @return  bool
     **/
    public function isAuthenticated()
    {
        return ($this->getAccessToken()) ? true : false;
    }

    /**
     * Grabs the user's profile
     *
     * You'll probably call this method after completing the proper oauth exchange.
     * But, in theory, you could call this without oauth and pass in a ORCID iD,
     * assuming you use the public API endpoint.
     *
     * @param   string  $orcid  the orcid to look up, if not already set as class prop
     * @return  object
     * @throws  Exception
     **/
    public function getProfile($orcid=null)
    {
        $this->http->setUrl($this->getApiEndpoint('orcid-profile', $orcid));

        if ($this->level == 'api')
        {
            // If using the members api, we have to have an access token set
            if (!$this->getAccessToken()) throw new Exception('You must first set an access token or authenticate');

            $this->http->setHeader([
                'Content-Type'  => 'application/vdn.orcid+json',
                'Authorization' => 'Bearer ' . $this->getAccessToken()
            ]);
        }
        else
        {
            $this->http->setHeader('Accept: application/orcid+json');
        }

        return json_decode($this->http->execute());
    }

    /**
     * Creates the qualified api endpoint for retrieving the desired data
     *
     * @param   string  $endpoint  the shortname of the endpoint
     * @param   string  $orcid     the orcid to look up, if not already specified
     * @return  string
     **/
    private function getApiEndpoint($endpoint, $orcid=null)
    {
        $url  = ($this->level == 'pub') ? 'http://' : 'https://';
        $url .= $this->level . '.';
        $url .= self::HOSTNAME;
        $url .= '/v1.2/';
        $url .= $orcid ?: $this->getOrcid();
        $url .= '/' . $endpoint;

        return $url;
    }
}
