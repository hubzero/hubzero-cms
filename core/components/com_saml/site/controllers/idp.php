<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Site\Controllers;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'IdP.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ServiceProvider.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'SamlSession.php';

use Hubzero\Component\SiteController;
use Components\Saml\Models\IdP as IdPModel;
use Components\Saml\Models\ServiceProvider;
use Components\Saml\Models\SamlSession;
use Request;
use Session;
use Route;
use User;
use Lang;
use App;

/**
 * Controller for the hub's SAML IdP endpoints
 *
 * /saml/idp/metadata — IdP metadata document
 * /saml/idp/login    — SP-initiated single sign-on
 * /saml/idp/logout   — SP-initiated single logout
 */
class Idp extends SiteController
{
	/**
	 * How many login round trips may be pending in one session at once
	 */
	const MAX_PENDING_REQUESTS = 5;

	/**
	 * The protocol engine
	 *
	 * @var  object
	 */
	protected $idp = null;

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (!$this->config->get('IdP_enable', 1))
		{
			App::abort(404);
		}

		$this->idp = new IdPModel($this->config);

		// Fail cleanly here rather than with an exception mid-protocol.
		// Metadata needs only the public certificate; signing needs both.
		$task = Request::getCmd('task', 'display');

		$usable = $task == 'metadata'
			? $this->idp->hasUsableCertificate()
			: $this->idp->hasUsableKeypair();

		if (!$usable)
		{
			$this->log('endpoint unavailable: signing certificate or key is missing or unreadable');
			App::abort(503, Lang::txt('COM_SAML_ERROR_NO_KEYPAIR'));
		}

		parent::execute();
	}

	/**
	 * Default task — nothing to display
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		App::abort(404);
	}

	/**
	 * Serve the IdP metadata document
	 *
	 * @return  void
	 */
	public function metadataTask()
	{
		if (!$this->config->get('enableIdPMetadataEndpoint', 1))
		{
			App::abort(404);
		}

		if (Request::method() != 'GET')
		{
			App::abort(405);
		}

		header('Content-Type: application/samlmetadata+xml');

		echo $this->idp->metadataXML();

		exit();
	}

	/**
	 * Decode an AuthnRequest and check everything that does not depend on
	 * who the user is.
	 *
	 * Anything untrusted is rejected with a plain 400 and a log line — an
	 * untrusted issuer never receives signed XML (DESIGN.md §7.2).
	 *
	 * @param   object  $request  \Symfony\Component\HttpFoundation\Request
	 * @return  array   [AuthnRequest, ServiceProvider]
	 */
	protected function validateAuthnRequest($request)
	{
		try
		{
			$messageContext = $this->idp->readSAMLRequest($request);
			$message = $messageContext->getMessage();
		}
		catch (\Exception $e)
		{
			$message = null;
		}

		if (!$message instanceof \LightSaml\Model\Protocol\AuthnRequest)
		{
			$this->log('login rejected: no AuthnRequest in request');
			App::abort(400, Lang::txt('COM_SAML_ERROR_BAD_REQUEST'));
		}

		$issuer = $message->getIssuer() ? $message->getIssuer()->getValue() : '';

		// Resolve the issuer against the trust store
		$sp = ServiceProvider::oneByEntityId($issuer);

		if (!$sp || !$sp->get('id') || !$sp->isEnabled())
		{
			$this->log('login rejected: untrusted issuer "' . $issuer . '"');
			App::abort(400, Lang::txt('COM_SAML_ERROR_UNTRUSTED_ISSUER'));
		}

		// Verify the request signature with the SP's registered certificate
		if ($sp->get('want_requests_signed', 1) && !$this->idp->validateRequestSignature($message, $sp))
		{
			$this->log('login rejected: bad or missing signature from ' . $issuer);
			App::abort(400, Lang::txt('COM_SAML_ERROR_BAD_SIGNATURE'));
		}

		// A request-supplied ACS URL must exactly match the registration;
		// the registered URL is always the one we respond to
		$acsUrl = $message->getAssertionConsumerServiceURL();

		if ($acsUrl && $acsUrl !== $sp->get('acs_url'))
		{
			$this->log('login rejected: ACS mismatch from ' . $issuer . ' (' . $acsUrl . ')');
			App::abort(400, Lang::txt('COM_SAML_ERROR_ACS_MISMATCH'));
		}

		return array($message, $sp);
	}

	/**
	 * Stash the current request across the login round trip
	 *
	 * @return  string  The key to carry in the return URL
	 */
	protected function stashRequest()
	{
		// Keyed per stash so two SP logins in two tabs cannot answer each
		// other's AuthnRequest; the key travels in the return URL
		$key = bin2hex(random_bytes(8));

		// Bounded ring of pending stashes, so repeated round trips cannot
		// grow the session without limit
		$pending = (array) Session::get('saml.pending', array());
		$pending[] = $key;

		while (count($pending) > self::MAX_PENDING_REQUESTS)
		{
			Session::clear('saml.request.' . array_shift($pending));
		}

		Session::set('saml.pending', $pending);

		// Read the superglobals, not the framework Request: restoring a stash
		// writes there, and the framework's copy was snapshotted at bootstrap
		// — reading it would stash an empty request on a second pass.
		Session::set('saml.request.' . $key, array(
			'qs'   => isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '',
			'post' => array(
				'SAMLRequest' => isset($_POST['SAMLRequest']) ? (string) $_POST['SAMLRequest'] : '',
				'RelayState'  => isset($_POST['RelayState']) ? (string) $_POST['RelayState'] : ''
			)
		));

		return $key;
	}

	/**
	 * Put a stashed request back into the superglobals so the binding layer
	 * sees the message exactly as the SP sent it
	 *
	 * @return  void
	 */
	protected function restoreStashedRequest()
	{
		$key = preg_replace('/[^a-f0-9]/', '', (string) Request::getVar('rk', '', 'get', 'none'));

		if (!$key
		 || Request::getVar('SAMLRequest', '', 'get', 'none')
		 || Request::getVar('SAMLRequest', '', 'post', 'none'))
		{
			return;
		}

		$stashed = Session::get('saml.request.' . $key, null);

		Session::clear('saml.request.' . $key);

		Session::set(
			'saml.pending',
			array_values(array_diff((array) Session::get('saml.pending', array()), array($key)))
		);

		if (!is_array($stashed))
		{
			return;
		}

		if (!empty($stashed['post']['SAMLRequest']))
		{
			// strlen, not array_filter: a RelayState of "0" is a real deep
			// link the SP expects back
			foreach ($stashed['post'] as $field => $value)
			{
				if (strlen((string) $value))
				{
					$_POST[$field] = $value;
				}
			}

			$_SERVER['REQUEST_METHOD'] = 'POST';
		}
		elseif (!empty($stashed['qs']))
		{
			$_SERVER['QUERY_STRING'] = $stashed['qs'];
			parse_str($stashed['qs'], $_GET);
		}
	}

	/**
	 * SP-initiated single sign-on
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		if (!in_array(Request::method(), array('GET', 'POST')))
		{
			App::abort(405);
		}

		// Restore a request stashed before the login round trip
		$this->restoreStashedRequest();

		// Receive the HTTP request and extract the SAMLRequest
		$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

		// Everything about the request is checked before the user is asked to
		// do anything: nobody is sent through a login they were going to be
		// refused after, and an untrusted caller cannot write session state.
		list($message, $sp) = $this->validateAuthnRequest($request);

		$issuer = $sp->get('entity_id');
		$id     = $message->getID();

		// Replay protection: an AuthnRequest ID may be answered once. The
		// session row written when it is answered is what records that.
		if (SamlSession::wasRequestAnswered($sp, $id))
		{
			$this->log('login rejected: replayed request ' . $id . ' from ' . $issuer);
			App::abort(400, Lang::txt('COM_SAML_ERROR_REPLAY'));
		}

		// Guests authenticate against the hub first, then re-enter this task.
		// The whole request has to survive that round trip: the raw query
		// string for the redirect binding (its signature covers the exact
		// encoding) and the posted fields for the POST binding.
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode('/saml/idp/login?rk=' . $this->stashRequest()), false)
			);
		}

		// Per-SP authorization: denial renders a hub page, not a SAML error
		// (DESIGN.md §13.1)
		if (!$sp->userCanAccess(User::getInstance()))
		{
			$this->log('login denied: ' . User::get('username') . ' not authorized for ' . $issuer);

			$this->view
				->set('sp', $sp)
				->setLayout('denied')
				->display();
			return;
		}

		// Record the SSO session under an opaque index. Writing the request
		// ID here is what consumes it — a rejected or denied attempt never
		// gets this far, so it stays legitimately retryable.
		$samlSession = SamlSession::open(User::getInstance(), $sp, $id);

		if (!$samlSession->get('id'))
		{
			$this->log('login failed: could not record session for ' . User::get('username'));
			App::abort(500, Lang::txt('COM_SAML_ERROR_INTERNAL'));
		}

		// Construct the signed SAML Response
		$response = $this->idp->createSAMLResponse(
			$sp,
			User::getInstance(),
			$id,
			$samlSession->get('session_index')
		);

		$this->log('login issued: ' . User::get('username') . ' -> ' . $issuer . ' (session ' . $samlSession->get('session_index') . ')');

		// Return it to the registered ACS via the HTTP-POST binding
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);

		$outContext = new \LightSaml\Context\Profile\MessageContext();
		$response->setRelayState($request->get('RelayState'));
		$outContext->setMessage($response);

		// send(), not echo: the page carries a signed bearer assertion and
		// needs the binding's Content-Type and no-store headers with it
		$httpResponse = $postBinding->send($outContext);
		$httpResponse->send();

		exit();
	}

	/**
	 * SP-initiated single logout
	 *
	 * @return  void
	 */
	public function logoutTask()
	{
		if (!in_array(Request::method(), array('GET', 'POST')))
		{
			App::abort(405);
		}

		$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

		try
		{
			$messageContext = $this->idp->readSAMLRequest($request);
			$message = $messageContext->getMessage();
		}
		catch (\Exception $e)
		{
			$message = null;
		}

		if (!$message instanceof \LightSaml\Model\Protocol\LogoutRequest)
		{
			$this->log('logout rejected: no LogoutRequest in request');
			App::abort(400, Lang::txt('COM_SAML_ERROR_BAD_REQUEST'));
		}

		$issuer = $message->getIssuer() ? $message->getIssuer()->getValue() : '';

		$sp = ServiceProvider::oneByEntityId($issuer);

		if (!$sp || !$sp->get('id') || !$sp->isEnabled())
		{
			$this->log('logout rejected: untrusted issuer "' . $issuer . '"');
			App::abort(400, Lang::txt('COM_SAML_ERROR_UNTRUSTED_ISSUER'));
		}

		if (!$sp->get('slo_url'))
		{
			$this->log('logout rejected: no SLO URL registered for ' . $issuer);
			App::abort(400, Lang::txt('COM_SAML_ERROR_BAD_REQUEST'));
		}

		if ($sp->get('want_requests_signed', 1) && !$this->idp->validateRequestSignature($message, $sp))
		{
			$this->log('logout rejected: bad or missing signature from ' . $issuer);
			App::abort(400, Lang::txt('COM_SAML_ERROR_BAD_SIGNATURE'));
		}

		// A replayed LogoutRequest is harmless rather than blocked: the
		// session it names is already ended, so the "already ended" branch
		// below answers Success without logging anyone out a second time.
		$status = \LightSaml\SamlConstants::STATUS_RESPONDER;

		$sessionIndex = (string) $message->getSessionIndex();
		$nameId = $message->getNameID() ? $message->getNameID()->getValue() : '';

		if ($sessionIndex)
		{
			$samlSession = SamlSession::oneByIndex($sessionIndex);
		}
		else
		{
			// SessionIndex is optional; resolve the subject-only form by
			// matching the NameID this SP would have been issued
			$samlSession = $this->sessionForNameId($sp, $nameId);
		}

		if ($samlSession && $samlSession->get('id') && (int) $samlSession->get('sp_id') == (int) $sp->get('id'))
		{
			// When the request names a subject, it must match the session's user
			$expected = $this->idp->getNameIdValue($sp, User::getInstance($samlSession->get('user_id')));

			if ($nameId && $nameId !== $expected)
			{
				$this->log('logout rejected: NameID mismatch for session ' . $samlSession->get('session_index'));
			}
			elseif (!$samlSession->isActive())
			{
				// Already logged out — SLO is idempotent
				$status = \LightSaml\SamlConstants::STATUS_SUCCESS;

				$this->log('logout: session ' . $samlSession->get('session_index') . ' was already ended');
			}
			elseif ($samlSession->get('hub_session_id') == \App::get('session')->getId())
			{
				// Front-channel SLO: the browser carries the hub session being
				// logged out, so it is ours to destroy. A plugin can veto
				// that, in which case the session is still live and saying
				// Success would be a lie.
				if (App::get('auth')->logout() === false)
				{
					$status = \LightSaml\SamlConstants::STATUS_PARTIAL_LOGOUT;

					$this->log('logout refused: hub logout vetoed for session ' . $samlSession->get('session_index'));
				}
				else
				{
					SamlSession::endAllForUser($samlSession->get('user_id'));

					$status = \LightSaml\SamlConstants::STATUS_SUCCESS;

					$this->log('logout completed: session ' . $samlSession->get('session_index') . ' for ' . $issuer);
				}
			}
			else
			{
				// The request did not arrive with the hub session it names, so
				// that session survives. Say so — claiming Success would tell
				// the SP the user is signed out of the hub when they are not.
				$status = \LightSaml\SamlConstants::STATUS_PARTIAL_LOGOUT;

				$this->log('logout partial: hub session for ' . $samlSession->get('session_index') . ' not present in this request');
			}
		}
		else
		{
			$this->log('logout: unknown session index from ' . $issuer);
		}

		// Respond to the registered SLO endpoint via the HTTP-Redirect binding
		$response = $this->idp->createSAMLLogoutResponse($sp, $message->getID(), $status);

		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$redirectBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);

		$outContext = new \LightSaml\Context\Profile\MessageContext();
		$response->setRelayState($request->get('RelayState'));
		$outContext->setMessage($response);

		$httpResponse = $redirectBinding->send($outContext);
		$httpResponse->send();

		exit();
	}

	/**
	 * Resolve a LogoutRequest that names only a subject
	 *
	 * The NameID is interpreted under the SP's own policy — the same rule
	 * that produced it at SSO time — so a persistent-ID SP cannot log out a
	 * session by guessing a username.
	 *
	 * @param   object  $sp      ServiceProvider
	 * @param   string  $nameId
	 * @return  mixed   SamlSession or null
	 */
	protected function sessionForNameId($sp, $nameId)
	{
		if (!$nameId)
		{
			return null;
		}

		switch ($sp->get('nameid_source', 'username'))
		{
			case 'email':
				$user = \Hubzero\User\User::oneByEmail($nameId);
			break;

			case 'id':
				$user = \Hubzero\User\User::oneOrNew((int) $nameId);
			break;

			case 'username':
			default:
				$user = \Hubzero\User\User::oneByUsername($nameId);
			break;
		}

		if (!$user || !$user->get('id'))
		{
			return null;
		}

		return SamlSession::oneActiveForUser($user->get('id'), $sp->get('id'));
	}

	/**
	 * Write to the saml log — logging must never break SSO
	 *
	 * Messages carry attacker-supplied values (issuer, request IDs), and the
	 * log is one record per line, so control characters are stripped to keep
	 * forged entries out of the audit trail.
	 *
	 * @param   string  $message
	 * @return  void
	 */
	protected function log($message)
	{
		// Matched bytewise, not with /u: a malformed UTF-8 issuer would make a
		// unicode pattern return null and swallow the entry entirely. UTF-8
		// continuation bytes are all >= 0x80, so multibyte text is untouched.
		$message = preg_replace('/[\x00-\x1F\x7F]+/', ' ', (string) $message);

		if (strlen($message) > 512)
		{
			$message = substr($message, 0, 512) . '…';
		}

		try
		{
			$log = App::get('log');

			if (!$log->has('saml'))
			{
				$log->register('saml', array(
					'file'   => 'saml.log',
					'level'  => 'info',
					'format' => "%datetime% %message%\n"
				));
			}

			$log->logger('saml')->info(
				Request::ip() . ' ' . (User::isGuest() ? '-' : User::get('username')) . ' ' . $message
			);
		}
		catch (\Exception $e)
		{
			// Never fatal
		}
	}
}
