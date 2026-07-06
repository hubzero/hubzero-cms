<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once __DIR__ . '/src/GithubAdapter.php';

use Hubzero\Plugin\Filesystem\Github\GithubAdapter;

/**
 * Plugin class for github filesystem connectivity
 */
class plgFilesystemGithub extends \Hubzero\Plugin\Plugin
{
	/**
	 * Initializes the github connection
	 *
	 * Builds the read-only adapter directly. Public repositories are readable
	 * anonymously, so we deliberately do NOT force an OAuth handshake here: it
	 * is wrong to ask a user to grant account-wide access just to read a public
	 * repo that may not even be theirs. A token is used only if one has already
	 * been stored (for private repos). Establishing that token is an explicit,
	 * manager-initiated step -- see authorize().
	 *
	 * @param   array   $params  Any connection params needed
	 * @return  object
	 **/
	public static function init($params = [])
	{
		$repository = isset($params['repository']) ? $params['repository'] : '';
		$token      = isset($params['access_token']) ? $params['access_token'] : null;

		return new GithubAdapter($repository, $token);
	}

	/**
	 * Begin the GitHub OAuth handshake for a connection (redirects to GitHub).
	 *
	 * This must only ever be reached from a manager-gated action: it mints a
	 * token bound to the authorizing user's own GitHub account, so it is never
	 * triggered implicitly by browsing. Public repositories never reach this
	 * path -- they are read anonymously by init().
	 *
	 * @param   array  $params  The stored connection params
	 * @return  void
	 **/
	public static function authorize($params = [])
	{
		$pparams = Plugin::params('filesystem', 'github');
		$app_key = $pparams['app_key'];

		$repository = isset($params['repository']) ? $params['repository'] : '';

		$base  = 'https://github.com/login/oauth/authorize';
		$query = '?client_id=' . $app_key;

		// This is a read-only connector, and this path is reached only for
		// private repositories. In classic GitHub OAuth, 'repo' is the sole
		// scope that grants read access to private repos (it unavoidably also
		// covers write). We drop the previous 'user' scope, which was never used.
		$scope = '&scope=repo';

		$return = (Request::getString('return')) ? Request::getString('return') : Request::current(true);
		$return = base64_encode($return);
		$state  = '&state=' . $return;

		Session::set('github.state', $return);
		Session::set('github.connection_to_set_up', Request::getInt('connection', 0));
		Session::set('github.repo', $repository);

		App::redirect($base . $query . $scope . $state);
	}
}
