<?php

namespace Components\Saml\Models;

class IdP 
{
	// Defining some trusted Service Providers.
	private $trusted_acs = [
    	'http://nanohub.instructure.com/saml2' => 'https://nanohub.instructure.com/login/saml'
	];
	private $trusted_slo = [
    	'http://nanohub.instructure.com/saml2' => 'https://nanohub.instructure.com/login/saml/logout'
	];
	private $trusted_signingcert = [
    	'http://nanohub.instructure.com/saml2' => 
		'MIIEMDCCAxigAwIBAgIJAPBXgeztn8U2MA0GCSqGSIb3DQEBCwUAMIGsMQswCQYD'.
		'VQQGEwJVUzENMAsGA1UECAwEVXRhaDEXMBUGA1UEBwwOU2FsdCBMYWtlIENpdHkx'.
		'GjAYBgNVBAoMEUluc3RydWN0dXJlLCBJbmMuMRMwEQYDVQQLDApPcGVyYXRpb25z'.
		'MSAwHgYDVQQDDBdDYW52YXMgU0FNTCBDZXJ0aWZpY2F0ZTEiMCAGCSqGSIb3DQEJ'.
		'ARYTb3BzQGluc3RydWN0dXJlLmNvbTAeFw0xOTAzMjExNTM5MDRaFw0yOTAzMTgx'.
		'NTM5MDRaMIGsMQswCQYDVQQGEwJVUzENMAsGA1UECAwEVXRhaDEXMBUGA1UEBwwO'.
		'U2FsdCBMYWtlIENpdHkxGjAYBgNVBAoMEUluc3RydWN0dXJlLCBJbmMuMRMwEQYD'.
		'VQQLDApPcGVyYXRpb25zMSAwHgYDVQQDDBdDYW52YXMgU0FNTCBDZXJ0aWZpY2F0'.
		'ZTEiMCAGCSqGSIb3DQEJARYTb3BzQGluc3RydWN0dXJlLmNvbTCCASIwDQYJKoZI'.
		'hvcNAQEBBQADggEPADCCAQoCggEBAPXoYCW9QPrtfn0+WLX43YtM89gLHrnSM0rR'.
		'Tc+0DQ9TUZKKrma80XvwOS3K0hjf7k+mAlarYptwXuPOaS6+LMRgxBRx/iWdugKr'.
		'yWKpwbzZ13v1TnLZ1rc6ThyRuilvKIPD7dP3rv+A1EzYYk9ZGtd5gFSBUtUqFwj1'.
		'76CUaEjCIN8FaogbbppWi/C1kWtPvPY+UeZ4IBJUpj+ect8rbhdVq5FxDErRdAzH'.
		'CIi6xSqlLqmV13rqD4srMtE98d+9Ki2hat3yNz3mmb5aZdiLQk6DosfQmHfNy6JS'.
		'GyVwmAZOPB5ssFuNfQZFK9o6WG5umS/aEN/ssfW/7uM9TDkkKvsCAwEAAaNTMFEw'.
		'HQYDVR0OBBYEFAraQ0414RyifBPG9LflNTiVFF7fMB8GA1UdIwQYMBaAFAraQ041'.
		'4RyifBPG9LflNTiVFF7fMA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcNAQELBQAD'.
		'ggEBAA+AaM/dPLidoPNJlKj9zQ9aTvJIF7MQhfrNkeNkMpGmE0igyZFq6z2WuA5u'.
		'U2cF/f7jNTBqaaFEbnA8BiRlE/FrTLHIIgP5JX2+n1WmakI/aVbnXICvrVRn84Yt'.
		'5SHdVacI5Whv3RKgRzkpBOb9jgZ+E4keAvtxHUuIMMEtxT/fySwFaRfG0Wit6fxX'.
		'buDiucMWZ+vEY243lO6ORPTiMeMcZGRqA5prwAWyfLzkXW1X5U3GXhWW7ZRmHxkT'.
		'8EwppSeosigJnYIjhrXFsiLU7wplnDD9yfe+ho70ZG3mb2MgfG59ZLTzv3l+Anau'.
		'BN+f6kyYZ9ztdvueX8SUp5T4s40='
	];

	/**
	 * Retrieves the Assertion Consumer Service.
	 *
   	 * @param string
	 *   The Service Provider Entity Id
	 * @return
	 *   The Assertion Consumer Service Url.
	 */
	public function getTrustedServiceProviderSigningCert($entityId)
	{
		return $this->trusted_signingcert[$entityId];
	}

	/**
	 * Retrieves the Assertion Consumer Service.
	 *
   	 * @param string
	 *   The Service Provider Entity Id
	 * @return
	 *   The Assertion Consumer Service Url.
	 */
	public function getTrustedServiceProviderAcs($entityId)
	{
		return $this->trusted_acs[$entityId];
	}

	/**
	 * Retrieves the Assertion Consumer Service.
	 *
   	 * @param string
	 *   The Service Provider Entity Id
	 * @return
	 *   The Assertion Consumer Service Url.
	 */
	public function getTrustedServiceProviderSlo($entityId)
	{
		return $this->trusted_slo[$entityId];
	}

	/**
	 * Returning a dummy IdP identifier.
	 *
	 * @return string
	 */
	public function getEntityID()
	{
		return "https://nanohub.org";
	}

	/**
	 * Returning the IdP login url.
	 *
	 * @return string
	 */
	public function getLoginUrl()
	{
		return "https://nanohub.org/saml/idp/login";
	}

	/**
	 * Returning the IdP login url.
	 *
	 * @return string
	 */
	public function getLogoutUrl()
	{
		return "https://nanohub.org/saml/idp/logout";
	}

	/**
	 * Retrieves the certificate from the IdP.
	 *
	 * @return \LightSaml\Credential\X509Certificate
	 */
	public function getCertificate()
	{
		return \LightSaml\Credential\X509Certificate::fromFile("/etc/saml/cert/saml.crt");
  	}

	/**
	 * Retrieves the private key from the Idp.
	 *
	 * @return \RobRichards\XMLSecLibs\XMLSecurityKey
	 */
	public function getPrivateKey()
	{
		return \LightSaml\Credential\KeyHelper::createPrivateKey("/etc/saml/cert/saml.pem", '', true);
	}

	/**
	 * Reads a SAMLRequest from the HTTP request and returns a messageContext.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 *   The HTTP request.
	 *
	 * @return \LightSaml\Context\Profile\MessageContext
	 *   The MessageContext that contains the SAML message.
	 */
	public function readSAMLRequest($request)
	{
		// We use the Binding Factory to construct a new SAML Binding based on the
		// request.
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$binding = $bindingFactory->getBindingByRequest($request);
		// We prepare a message context to receive our SAML Request message.
		$messageContext = new \LightSaml\Context\Profile\MessageContext();

		// The receive method fills in the messageContext with the SAML Request data.
		/** @var \LightSaml\Model\Protocol\Response $response */
		$binding->receive($request, $messageContext);

		return $messageContext;
	}

	/**
	 * Constructs a SAML Response.
	 *
	 * @param \IdpProvider $idpProvider
	 * @param $user_id
	 * @param $user_email
	 * @param $issuer
	 * @param $id
	 */
	public function createSAMLResponse($user, $issuer, $acsUrl, $id, $session_id)
  	{
		$destination =  $this->getTrustedServiceProviderAcs($issuer);

		if (!$destination)
		{
			$response = (new \LightSaml\Model\Protocol\Response())
        		->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode( \LightSaml\SamlConstants::STATUS_RESPONDER)))
				->setID(\LightSaml\Helper::generateID())
				->setIssueInstant(new \DateTime())
				->setIssuer(new \LightSaml\Model\Assertion\Issuer($this->getEntityId()))
				->setDestination( $acsUrl )
				->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($this->getCertificate(), $this->getPrivateKey()));
		}
		else
		{
			$response = (new \LightSaml\Model\Protocol\Response())
				->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode( \LightSaml\SamlConstants::STATUS_SUCCESS)))
				->setID(\LightSaml\Helper::generateID())
				->setIssueInstant(new \DateTime())
				->setIssuer(new \LightSaml\Model\Assertion\Issuer($this->getEntityId()))
				->setDestination( $acsUrl )
				->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($this->getCertificate(), $this->getPrivateKey()))
				->addAssertion((new \LightSaml\Model\Assertion\Assertion())
					->setId(\LightSaml\Helper::generateID())
					->setIssueInstant(new \DateTime())
					->setIssuer((new \LightSaml\Model\Assertion\Issuer())
						->setValue($this->getEntityId()))
					->setSubject((new \LightSaml\Model\Assertion\Subject())
					->setNameID((new \LightSaml\Model\Assertion\NameID())
						->setValue($user->get('username'))
						->setFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED))
					->addSubjectConfirmation((new \LightSaml\Model\Assertion\SubjectConfirmation())
						->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
						->setSubjectConfirmationData((new \LightSaml\Model\Assertion\SubjectConfirmationData())
						->setInResponseTo($id)
						->setNotOnOrAfter(new \DateTime('+180 SECONDS'))
						->setRecipient($acsUrl))))
					->setConditions((new \LightSaml\Model\Assertion\Conditions())
					->setNotBefore(new \DateTime('-180 SECONDS'))
					->setNotOnOrAfter(new \DateTime('+180 SECONDS'))
					->addItem((new \LightSaml\Model\Assertion\AudienceRestriction())
						->addAudience($issuer)))
					->addItem((new \LightSaml\Model\Assertion\AttributeStatement())
					->addAttribute((new \LightSaml\Model\Assertion\Attribute())
						->setName(\LightSaml\ClaimTypes::EMAIL_ADDRESS)
						->setFriendlyName('E-Mail')
						->addAttributeValue($user->get('email')))
						->addAttribute((new \LightSaml\Model\Assertion\Attribute())
							->setName(\LightSaml\ClaimTypes::COMMON_NAME)
							->setFriendlyName('CommonName')
							->addAttributeValue($user->get('name')))
						->addAttribute((new \LightSaml\Model\Assertion\Attribute())
							->setName(\LightSaml\ClaimTypes::GIVEN_NAME)
							->setFriendlyName('GivenName')
							->addAttributeValue($user->get('givenName')))
						->addAttribute((new \LightSaml\Model\Assertion\Attribute())
							->setName(\LightSaml\ClaimTypes::SURNAME)
							->setFriendlyName('Surname')
							->addAttributeValue($user->get('surname'))))      
					->addItem((new \LightSaml\Model\Assertion\AuthnStatement())
						->setAuthnInstant(new \DateTime())
						->setSessionIndex($session_id)
						->setAuthnContext((new \LightSaml\Model\Assertion\AuthnContext())
							->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT))
					)
				);
    	}

        // Serialize to XML.
        $serializationContext = new \LightSaml\Model\Context\SerializationContext();

        $response->serialize($serializationContext->getDocument(), $serializationContext);
        
		return $response;
    }

	/**
	 * Constructs a SAML Logout Response.
	 *
	 * @param \IdpProvider $idpProvider
	 * @param $sloUrl
	 * @param $request_id
	 * @param $status
	 */
	public function createSAMLLogoutResponse($sloUrl, $request_id, $status)
  	{
		$response = (new \LightSaml\Model\Protocol\LogoutResponse())
			->setID(\LightSaml\Helper::generateID())
			->setIssueInstant(new \DateTime())
			->setDestination( $sloUrl )
			->setInResponseTo($request_id)
			->setIssuer(new \LightSaml\Model\Assertion\Issuer($this->getEntityId()))
			->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode( $status )))
			->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($this->getCertificate(), $this->getPrivateKey()));

        $serializationContext = new \LightSaml\Model\Context\SerializationContext();

        $response->serialize($serializationContext->getDocument(), $serializationContext);
        
		return $response;
    }

	public function metadataXML()
    {
		$entityDescriptor = (new \LightSaml\Model\Metadata\EntityDescriptor())
			->setEntityID($this->getEntityID())
			->setValidUntil( new \DateTime('+1 DAY') )
			->addItem( (new \LightSaml\Model\Metadata\IdpSsoDescriptor())
				->setWantAuthnRequestsSigned(true)
				->addKeyDescriptor( (new \LightSaml\Model\Metadata\KeyDescriptor())
                    ->setUse(\LightSaml\Model\Metadata\KeyDescriptor::USE_SIGNING)
                    ->setCertificate($this->getCertificate())
                	)
               	->addNameIdFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL)
               	->addSingleSignOnService( (new \LightSaml\Model\Metadata\SingleSignOnService())
                    ->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation($this->getLoginUrl()))
				->addSingleLogoutService( (new \LightSaml\Model\Metadata\SingleLogoutService())
                    ->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation($this->getLogoutUrl())));

        $serializationContext = new \LightSaml\Model\Context\SerializationContext();

        $entityDescriptor->serialize($serializationContext->getDocument(), $serializationContext);

        $serializationContext->getDocument()->formatOutput = true;

        $xml = $serializationContext->getDocument()->saveXML();

		return $xml;
	}
}
