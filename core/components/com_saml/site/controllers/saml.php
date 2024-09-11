<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Site\Controllers;

include_once \Component::path('com_saml') . DS . 'models' . DS . 'IdP.php';

use Hubzero\Component\SiteController;
use Components\Saml\Models\Idp;

/**
 * Controller for Authorizing OAuth
 */
class Saml extends SiteController
{
	public function metadataTask()
	{
		$idp = new IdP();
		
		if (Request::method() != 'GET')
		{
			App::abort(405);
		}

		$metadata = $idp->metadataXML();

		header('Content-Type: text/xml');

		echo $metadata;

		exit();
	}

	public function loginTask()
	{
		$idp = new IdP();

		if (Request::method() != 'GET')
		{
			App::abort(405);
		}

		if (User::isGuest())
		{
			Session::set("saml.qs", Request::getString('QUERY_STRING','','server') );

			App::redirect("/users/login?return=" . base64_encode('/saml/login') );
		}
		
		if (Request::getString('QUERY_STRING','','server') == '')
		{
			$qs =  Session::get("saml.qs", null);

			if ($qs !== null)
			{
				$_SERVER['QUERY_STRING'] = $qs;
				parse_str($qs, $_GET);
			}
		}

		Session::clear("saml.qs");

		// Receive the HTTP Request and extract the SAMLRequest.
		$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

		$saml_request = $idp->readSAMLRequest($request);

		// Getting a few details from the message like ID and Issuer.
		$issuer = $saml_request->getMessage()->getIssuer()->getValue();
		$id     = $saml_request->getMessage()->getID();
		$acsUrl = $saml_request->getMessage()->getAssertionConsumerServiceURL();

		$session_id = Session::getId();

		// Construct a SAML Response.
		$response = $idp->createSAMLResponse(User::getInstance(), $issuer, $acsUrl, $id, $session_id);

		// Prepare the POST binding (form).
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
		$messageContext = new \LightSaml\Context\Profile\MessageContext();
		$messageContext->setMessage($response);

		// Ensure we include the RelayState.
		$message = $messageContext->getMessage();
		$message->setRelayState($request->get('RelayState'));
		$messageContext->setMessage($message);

		// Return the Response.
		$httpResponse = $postBinding->send($messageContext);

		echo $httpResponse->getContent();

		exit();
	}
}
