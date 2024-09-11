<?php

namespace Components\Saml\Models;

class IdP 
{
	// Defining some trusted Service Providers.
	private $trusted_sps = [
    	'http://nanohub.instructure.com/saml2' => 'https://nanohub.instructure.com/login/saml'
	];

	/**
	 * Retrieves the Assertion Consumer Service.
	 *
   	 * @param string
	 *   The Service Provider Entity Id
	 * @return
	 *   The Assertion Consumer Service Url.
	 */
	public function getServiceProviderAcs($entityId)
	{
		return $this->trusted_sps[$entityId];
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
	 * Retrieves the certificate from the IdP.
	 *
	 * @return \LightSaml\Credential\X509Certificate
	 */
	public function getCertificate()
	{
		return \LightSaml\Credential\X509Certificate::fromFile( \Component::path('com_saml') . DS . 'config' . DS . 'certificate.crt');
  	}

	/**
	 * Retrieves the private key from the Idp.
	 *
	 * @return \RobRichards\XMLSecLibs\XMLSecurityKey
	 */
	public function getPrivateKey()
	{
		return \LightSaml\Credential\KeyHelper::createPrivateKey( \Component::path('com_saml') . DS . 'config' . DS . 'certificate.key', '', true);
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
		$destination =  self::getServiceProviderAcs($issuer);

		if ($destination != $acsUrl)
		{
			$response = (new \LightSaml\Model\Protocol\Response())
        		->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode( \LightSaml\SamlConstants::STATUS_RESPONDER)))
				->setID(\LightSaml\Helper::generateID())
				->setIssueInstant(new \DateTime())
				->setIssuer(new \LightSaml\Model\Assertion\Issuer(self::getEntityId()))
				->setDestination( $acsUrl )
				->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter(self::getCertificate(), self::getPrivateKey()));
		}
		else
		{
			$response = (new \LightSaml\Model\Protocol\Response())
				->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode( \LightSaml\SamlConstants::STATUS_SUCCESS)))
				->setID(\LightSaml\Helper::generateID())
				->setIssueInstant(new \DateTime())
				->setIssuer(new \LightSaml\Model\Assertion\Issuer(self::getEntityId()))
				->setDestination( $acsUrl )
				->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter(self::getCertificate(), self::getPrivateKey()))
				->addAssertion((new \LightSaml\Model\Assertion\Assertion())
					->setId(\LightSaml\Helper::generateID())
					->setIssueInstant(new \DateTime())
					->setIssuer((new \LightSaml\Model\Assertion\Issuer())
						->setValue(self::getEntityId()))
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

	public function metadataXML()
    {
		$entityDescriptor = (new \LightSaml\Model\Metadata\EntityDescriptor())
			->setEntityID(self::getEntityID())
			->setValidUntil( new \DateTime('+1 DAY') )
			->addItem( (new \LightSaml\Model\Metadata\IdpSsoDescriptor())
				->setWantAuthnRequestsSigned(true)
				->addKeyDescriptor( (new \LightSaml\Model\Metadata\KeyDescriptor())
                    ->setUse(\LightSaml\Model\Metadata\KeyDescriptor::USE_SIGNING)
                    ->setCertificate(\LightSaml\Credential\X509Certificate::fromFile('/var/www/nanohub/core/components/com_saml/config/certificate.crt'))
                	)
               	->addNameIdFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL)
               	->addSingleSignOnService( (new \LightSaml\Model\Metadata\SingleSignOnService())
                    ->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
                    ->setLocation('https://nanohub.org/saml/login')));
             

        $serializationContext = new \LightSaml\Model\Context\SerializationContext();

        $entityDescriptor->serialize($serializationContext->getDocument(), $serializationContext);

        $serializationContext->getDocument()->formatOutput = true;

        $xml = $serializationContext->getDocument()->saveXML();

		return $xml;
	}
}
