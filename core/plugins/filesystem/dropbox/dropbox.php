<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Filesystem\Dropbox;

use Hubzero\Plugin\Plugin;
use Plugins\Filesystem\Dropbox\DropboxOauthClient;
use Srmklive\Dropbox\Adapter\DropboxAdapter;
use Srmklive\Dropbox\Client\DropboxClient;
use Hubzero\Utility\Arr;
use Hubzero\Session;

/**
 * Plugin class for dropbox filesystem connectivity
 */
class Dropbox extends Plugin
{
    /**
     * Initializes the Dropbox connection
     *
     * @param   array   $params  Client application data
     * @return  DropboxAdapter
     **/
    public static function init($params = [])
    {
        if (isset($params['app_token'])) {
            $accessToken = $params['app_token']->access_token;
        } else {
            self::_getAccessToken($params);
        }

        // Create the client
        $client = new DropboxClient($accessToken);

        // Return the adapter
        $path = Arr::getValue($params, 'path', '');
        $path = urldecode($path);
        return new DropboxAdapter($client, $path);
    }

    /**
     * Retrieves Dropbox code used to get access token
     *
     * @param   array   $params  Client application data
     * @return  void
     **/
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected static function _getAccessToken($params)
    {
        $oauthClient = new DropboxOauthClient();
        $authUrl = $oauthClient->getAuthorizationUrl();
        $oauthState = $oauthClient->getState();

        self::_setLocalOauthData($oauthState);

        $oauthClient->getAuthorizationCode($authUrl);
    }

    /**
     * Sets OAuth-relevant data in local user session
     *
     * @param   array   $state  OAuth state
     * @return  void
     **/
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected static function _setLocalOauthData($state)
    {
        $connectionId = Request::getInt('connection', 0);
        $connection = \Components\Projects\Models\Orm\Connection::one($connectionId);
        $project = $connection->project;
        $projectsFilesUrl = \Route::url($project->link('files') . '/browse?connection=' . $connectionId);

        Session::set('dropbox.connection_to_set_up', $connectionId);
        Session::set('dropbox.local_origin_url', $projectsFilesUrl);
        Session::set('dropbox.state', $state);
    }
}
