<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content;

use Hubzero\Facades\App;

/**
 * Content negotiator for files application
 */
class Moderator
{
    /**
     * The path to the content being served
     *
     * @var  string
     **/
    private $path;

    /**
     * The token identifier
     *
     * @var  string
     **/
    private $token;

    /**
     * The session identifier
     *
     * @var  string
     **/
    private $session_id;

    /**
     * The site secret key
     *
     * @var  string
     **/
    private $secret;

    /**
     * Constructs the moderator
     *
     * @param   string|null  $identifier  The entity identifier, either a path or a token hash
     * @param   string|null  $session_id  The PHP session id to use when creating the token
     * @param   string|null  $secret      The site secret used when creating the token
     * @return  void
     **/
    public function __construct($identifier = null, $session_id = null, #[\SensitiveParameter] $secret = null)
    {
        if (is_file($identifier == null ? '' : $identifier)) {
            $this->path = (string) $identifier;
        } else {
            $this->decompose((string) $identifier);
        }

        // We assume that if session_id and secret aren't included, that we're
        // in an environement where we can easily grab them.
        if (!$session_id && App::has('session')) {
            $session_id = App::get('session')->getId();
        }
        $this->session_id = $session_id;

        if (!$secret && App::has('config')) {
            $secret = App::get('config')->get('secret');
        }
        $this->secret = $secret;
    }

    /**
     * Builds the url identifier to the content
     *
     * @return  string
     **/
    public function getUrl()
    {
        return App::get('request')->root(true) . 'files/' . $this->getIdentifier();
    }

    /**
     * Gets the file path
     *
     * @return  string
     **/
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Validates the given token against the session date
     *
     * @return  bool
     **/
    public function validateToken()
    {
        if (!hash_equals($this->getToken(), (string) $this->token)) {
            // Using 'public' as the session ID allows for shareable URLs
            // not tied to a specific user session. Usage would be for
            // files that do not need access control.
            $this->session_id = 'public';
        }
        return hash_equals($this->getToken(), (string) $this->token);
    }

    /**
     * Generates the url string identifier
     *
     * @return  string
     **/
    private function getIdentifier()
    {
        return base64_encode($this->getToken() . ':' . $this->path);
    }

    /**
     * Generates the request token
     *
     * @return  string
     **/
    private function getToken()
    {
        return hash('sha256', $this->session_id . ':' . $this->secret . ':' . $this->path);
    }

    /**
     * Unpacks the token into meaninful bits
     *
     * @param   string  $identifier  The identifier to process
     * @return  void
     **/
    private function decompose($identifier)
    {
        $identifier = base64_decode($identifier == null ? '' : $identifier);

        if (strstr($identifier, ':')) {
            list($token, $path) = explode(':', $identifier, 2);

            $this->token = $token;
            $this->path  = $path;
        }
    }
}
