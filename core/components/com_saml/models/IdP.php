<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Models;

/**
 * SAML 2.0 Identity Provider protocol engine
 *
 * Stateless wrapper around LightSAML: builds and verifies protocol
 * messages. Identity, keys, and lifetimes come from the component
 * params; per-SP trust comes from the ServiceProvider model. Nothing
 * here is hub- or SP-specific.
 */
class IdP
{
	/**
	 * Component params (\Hubzero\Config\Registry)
	 *
	 * @var  object
	 */
	protected $params;

	/**
	 * Constructor
	 *
	 * @param   object  $params  Component params; defaults to com_saml's
	 * @return  void
	 */
	public function __construct($params = null)
	{
		$this->params = $params ?: \Component::params('com_saml');
	}

	/**
	 * The base URL every published identity and endpoint is built from
	 *
	 * Prefers the hub's configured canonical URL. `Request::root()` is only a
	 * fallback: it is derived from the request's Host header, which a caller
	 * controls — an IdP whose Issuer shifts per request has its assertions
	 * rejected, and metadata fetched with a forged Host would advertise
	 * endpoints pointing somewhere else. See isIdentityRequestDerived().
	 *
	 * @return  string  No trailing slash
	 */
	public function getBaseUrl()
	{
		$base = trim((string) \Config::get('live_site', ''));

		return rtrim($base ?: \Request::root(), '/');
	}

	/**
	 * Is the published identity derived from the request rather than config?
	 *
	 * True when neither IdP_entityID nor the hub's live_site is set, which
	 * the admin dashboard warns about.
	 *
	 * @return  boolean
	 */
	public function isIdentityRequestDerived()
	{
		return !trim((string) $this->params->get('IdP_entityID', ''))
			&& !trim((string) \Config::get('live_site', ''));
	}

	/**
	 * The IdP entity ID
	 *
	 * Configured via IdP_entityID; defaults to the hub's canonical URL.
	 *
	 * @return  string
	 */
	public function getEntityID()
	{
		$entityId = trim((string) $this->params->get('IdP_entityID', ''));

		return $entityId ?: $this->getBaseUrl();
	}

	/**
	 * The IdP single sign-on URL
	 *
	 * @return  string
	 */
	public function getLoginUrl()
	{
		return $this->getBaseUrl() . '/saml/idp/login';
	}

	/**
	 * The IdP single logout URL
	 *
	 * @return  string
	 */
	public function getLogoutUrl()
	{
		return $this->getBaseUrl() . '/saml/idp/logout';
	}

	/**
	 * The IdP metadata URL
	 *
	 * @return  string
	 */
	public function getMetadataUrl()
	{
		return $this->getBaseUrl() . '/saml/idp/metadata';
	}

	/**
	 * Path to the IdP signing certificate
	 *
	 * @return  string
	 */
	public function getCertificateFile()
	{
		return (string) $this->params->get('IdPCertificateFile', '/etc/saml/cert/saml.crt');
	}

	/**
	 * Path to the IdP private key
	 *
	 * @return  string
	 */
	public function getKeyFile()
	{
		return (string) $this->params->get('IdPKeyFile', '/etc/saml/cert/saml.pem');
	}

	/**
	 * User accessors an attribute map may name
	 *
	 * @var  array
	 */
	protected static $attributeSources = array(
		'email', 'name', 'givenName', 'middleName', 'surname', 'username', 'id'
	);

	/**
	 * Assertion lifetime (NotOnOrAfter) in seconds
	 *
	 * @return  integer
	 */
	public function getAssertionLifetime()
	{
		return max(30, (int) $this->params->get('assertion_lifetime', 180));
	}

	/**
	 * Allowed clock skew (NotBefore) in seconds
	 *
	 * @return  integer
	 */
	public function getClockSkew()
	{
		return max(0, (int) $this->params->get('clock_skew', 60));
	}

	/**
	 * Is the signing certificate readable?
	 *
	 * Publishing metadata needs only this — the certificate is the public
	 * half — so a missing key must not take the metadata endpoint down and
	 * break every SP's periodic refresh.
	 *
	 * @return  boolean
	 */
	public function hasUsableCertificate()
	{
		return is_readable($this->getCertificateFile());
	}

	/**
	 * Are the signing certificate and key both usable?
	 *
	 * Required by anything that signs — SSO and logout.
	 *
	 * @return  boolean
	 */
	public function hasUsableKeypair()
	{
		return $this->hasUsableCertificate() && is_readable($this->getKeyFile());
	}

	/**
	 * Retrieves the certificate for the IdP
	 *
	 * @return  \LightSaml\Credential\X509Certificate
	 */
	public function getCertificate()
	{
		return \LightSaml\Credential\X509Certificate::fromFile($this->getCertificateFile());
	}

	/**
	 * Retrieves the private key for the IdP, typed for the configured
	 * signature algorithm (SHA-256 or better only)
	 *
	 * @return  \RobRichards\XMLSecLibs\XMLSecurityKey
	 */
	public function getPrivateKey()
	{
		return \LightSaml\Credential\KeyHelper::createPrivateKey(
			$this->getKeyFile(),
			(string) $this->params->get('IdPKeyPassphrase', ''),
			true,
			$this->getSignatureKeyType()
		);
	}

	/**
	 * XMLSecurityKey type for the configured signature algorithm
	 *
	 * @return  string
	 */
	protected function getSignatureKeyType()
	{
		switch ($this->params->get('signature_algorithm', 'rsa-sha256'))
		{
			case 'rsa-sha384':
				return \RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA384;
			case 'rsa-sha512':
				return \RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA512;
			case 'rsa-sha256':
			default:
				return \RobRichards\XMLSecLibs\XMLSecurityKey::RSA_SHA256;
		}
	}

	/**
	 * Digest algorithm matching the configured signature algorithm
	 *
	 * @return  string
	 */
	protected function getDigestAlgorithm()
	{
		switch ($this->params->get('signature_algorithm', 'rsa-sha256'))
		{
			case 'rsa-sha384':
				return \RobRichards\XMLSecLibs\XMLSecurityDSig::SHA384;
			case 'rsa-sha512':
				return \RobRichards\XMLSecLibs\XMLSecurityDSig::SHA512;
			case 'rsa-sha256':
			default:
				return \RobRichards\XMLSecLibs\XMLSecurityDSig::SHA256;
		}
	}

	/**
	 * A signature writer for outgoing messages
	 *
	 * @return  \LightSaml\Model\XmlDSig\SignatureWriter
	 */
	protected function getSignatureWriter()
	{
		return new \LightSaml\Model\XmlDSig\SignatureWriter(
			$this->getCertificate(),
			$this->getPrivateKey(),
			$this->getDigestAlgorithm()
		);
	}

	/**
	 * Reads a SAML message from the HTTP request and returns a messageContext.
	 *
	 * @param   \Symfony\Component\HttpFoundation\Request  $request  The HTTP request.
	 * @return  \LightSaml\Context\Profile\MessageContext
	 */
	public function readSAMLRequest($request)
	{
		// We use the Binding Factory to construct a new SAML Binding based on the
		// request. HTTP-Redirect is the supported binding (DESIGN.md §13.2);
		// HTTP-POST is accepted because the factory provides it.
		$bindingFactory = new \LightSaml\Binding\BindingFactory();
		$binding = $bindingFactory->getBindingByRequest($request);

		// We prepare a message context to receive our SAML Request message.
		$messageContext = new \LightSaml\Context\Profile\MessageContext();

		// The receive method fills in the messageContext with the SAML Request data.
		$binding->receive($request, $messageContext);

		return $messageContext;
	}

	/**
	 * Verify the signature on an incoming message against the SP's
	 * registered signing certificate.
	 *
	 * @param   object  $message  \LightSaml\Model\Protocol\SamlMessage
	 * @param   object  $sp       ServiceProvider
	 * @return  boolean
	 */
	public function validateRequestSignature($message, $sp)
	{
		$signature = $message->getSignature();

		if (!$signature)
		{
			return false;
		}

		$certificate = $sp->certificate();

		if (!$certificate)
		{
			return false;
		}

		try
		{
			$key = \LightSaml\Credential\KeyHelper::createPublicKey($certificate);

			return (bool) $signature->validate($key);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * The NameID value for a user under an SP's policy
	 *
	 * @param   object  $sp    ServiceProvider
	 * @param   object  $user  \Hubzero\User\User
	 * @return  string
	 */
	public function getNameIdValue($sp, $user)
	{
		switch ($sp->get('nameid_source', 'username'))
		{
			case 'email':
				return (string) $user->get('email');
			case 'id':
				return (string) $user->get('id');
			case 'username':
			default:
				return (string) $user->get('username');
		}
	}

	/**
	 * Can this attribute-map source be resolved?
	 *
	 * @param   string  $source
	 * @return  boolean
	 */
	public static function isKnownAttributeSource($source)
	{
		if (!is_string($source))
		{
			return false;
		}

		return in_array($source, self::$attributeSources)
			|| $source == 'organization'
			|| (strpos($source, 'profile:') === 0 && strlen($source) > strlen('profile:'));
	}

	/**
	 * Resolve an attribute-map source to a user value
	 *
	 * Only whitelisted accessors resolve — the map is data, not code.
	 * Returns null for unknown sources so the attribute is skipped.
	 *
	 * @param   object  $user    \Hubzero\User\User
	 * @param   string  $source
	 * @return  string|null
	 */
	protected function resolveAttributeSource($user, $source)
	{
		if (in_array($source, self::$attributeSources))
		{
			return (string) $user->get($source, '');
		}

		// Organization lives in the profile table, not on the user record
		if ($source == 'organization')
		{
			$source = 'profile:organization';
		}

		if (strpos($source, 'profile:') === 0)
		{
			$key = substr($source, strlen('profile:'));

			require_once \Component::path('com_members') . DS . 'models' . DS . 'profile.php';

			$profile = \Components\Members\Models\Profile::oneByKeyAndUser($key, $user->get('id'));

			return $profile ? (string) $profile->get('profile_value', '') : '';
		}

		return null;
	}

	/**
	 * Build the attribute statement for a user under an SP's release policy
	 *
	 * Returns null when nothing resolves to a value: the SAML schema requires
	 * an AttributeStatement to have at least one attribute, and validating
	 * SPs reject an assertion carrying an empty one.
	 *
	 * @param   object  $sp    ServiceProvider
	 * @param   object  $user  \Hubzero\User\User
	 * @return  \LightSaml\Model\Assertion\AttributeStatement|null
	 */
	protected function buildAttributeStatement($sp, $user)
	{
		$map = $sp->attributeMap();

		// An empty (but configured) map releases nothing; only an absent one
		// falls back to the default set
		if (is_array($map) && !$map)
		{
			return null;
		}

		if (!$map)
		{
			// Default release set — matches the original hardcoded behavior
			$map = array(
				array('name' => \LightSaml\ClaimTypes::EMAIL_ADDRESS, 'friendly' => 'E-Mail',     'source' => 'email'),
				array('name' => \LightSaml\ClaimTypes::COMMON_NAME,   'friendly' => 'CommonName', 'source' => 'name'),
				array('name' => \LightSaml\ClaimTypes::GIVEN_NAME,    'friendly' => 'GivenName',  'source' => 'givenName'),
				array('name' => \LightSaml\ClaimTypes::SURNAME,       'friendly' => 'Surname',    'source' => 'surname')
			);
		}

		$statement = new \LightSaml\Model\Assertion\AttributeStatement();
		$released = 0;

		foreach ($map as $entry)
		{
			if (empty($entry['name']) || empty($entry['source']))
			{
				continue;
			}

			$value = $this->resolveAttributeSource($user, $entry['source']);

			// Unknown sources resolve to null; empty values are omitted
			// rather than released, since an empty attribute can blank out
			// the corresponding field in the SP's user record
			if ($value === null || $value === '')
			{
				continue;
			}

			$attribute = (new \LightSaml\Model\Assertion\Attribute())
				->setName($entry['name'])
				->addAttributeValue($value);

			if (!empty($entry['friendly']))
			{
				$attribute->setFriendlyName($entry['friendly']);
			}

			$statement->addAttribute($attribute);
			$released++;
		}

		return $released ? $statement : null;
	}

	/**
	 * Constructs a signed SAML Response for a trusted SP.
	 *
	 * The destination is always the SP's registered ACS URL — never a
	 * request-supplied one (DESIGN.md §7.2). Callers must have already
	 * resolved and authorized the SP.
	 *
	 * @param   object  $sp            ServiceProvider
	 * @param   object  $user          \Hubzero\User\User
	 * @param   string  $inResponseTo  The AuthnRequest ID
	 * @param   string  $sessionIndex  Opaque session index (SamlSession)
	 * @return  \LightSaml\Model\Protocol\Response
	 */
	public function createSAMLResponse($sp, $user, $inResponseTo, $sessionIndex)
	{
		$acsUrl   = $sp->get('acs_url');
		$issuer   = $sp->get('entity_id');
		$lifetime = $this->getAssertionLifetime();
		$skew     = $this->getClockSkew();

		$assertion = (new \LightSaml\Model\Assertion\Assertion())
			->setId(\LightSaml\Helper::generateID())
			->setIssueInstant(new \DateTime())
			->setIssuer((new \LightSaml\Model\Assertion\Issuer())
				->setValue($this->getEntityID()))
			->setSubject((new \LightSaml\Model\Assertion\Subject())
			->setNameID((new \LightSaml\Model\Assertion\NameID())
				->setValue($this->getNameIdValue($sp, $user))
				->setFormat($sp->get('nameid_format', \LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED)))
			->addSubjectConfirmation((new \LightSaml\Model\Assertion\SubjectConfirmation())
				->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
				->setSubjectConfirmationData((new \LightSaml\Model\Assertion\SubjectConfirmationData())
				->setInResponseTo($inResponseTo)
				->setNotOnOrAfter(new \DateTime('+' . $lifetime . ' SECONDS'))
				->setRecipient($acsUrl))))
			->setConditions((new \LightSaml\Model\Assertion\Conditions())
			->setNotBefore(new \DateTime('-' . $skew . ' SECONDS'))
			->setNotOnOrAfter(new \DateTime('+' . $lifetime . ' SECONDS'))
			->addItem((new \LightSaml\Model\Assertion\AudienceRestriction())
				->addAudience($issuer)))
			->addItem((new \LightSaml\Model\Assertion\AuthnStatement())
				->setAuthnInstant(new \DateTime())
				->setSessionIndex($sessionIndex)
				->setAuthnContext((new \LightSaml\Model\Assertion\AuthnContext())
					->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)));

		// Only carried when something actually resolved to a value
		if ($attributes = $this->buildAttributeStatement($sp, $user))
		{
			$assertion->addItem($attributes);
		}

		// The response signature already covers the assertion, which is what
		// SPs have historically been given here. SPs that specifically
		// require an assertion-level signature (WantAssertionsSigned) opt in
		// per registration.
		if ($sp->get('sign_assertion'))
		{
			$assertion->setSignature($this->getSignatureWriter());
		}

		$response = (new \LightSaml\Model\Protocol\Response())
			->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode(\LightSaml\SamlConstants::STATUS_SUCCESS)))
			->setID(\LightSaml\Helper::generateID())
			->setIssueInstant(new \DateTime())
			->setIssuer(new \LightSaml\Model\Assertion\Issuer($this->getEntityID()))
			->setDestination($acsUrl)
			->setInResponseTo($inResponseTo)
			->setSignature($this->getSignatureWriter())
			->addAssertion($assertion);

		// Returned unserialized: the binding serializes (and therefore signs)
		// it on send, so doing it here too would just sign everything twice.
		return $response;
	}

	/**
	 * Constructs a signed SAML Logout Response for a trusted SP.
	 *
	 * The destination is always the SP's registered SLO URL.
	 *
	 * @param   object  $sp            ServiceProvider
	 * @param   string  $inResponseTo  The LogoutRequest ID
	 * @param   string  $status        A \LightSaml\SamlConstants::STATUS_* value
	 * @return  \LightSaml\Model\Protocol\LogoutResponse
	 */
	public function createSAMLLogoutResponse($sp, $inResponseTo, $status)
	{
		$response = (new \LightSaml\Model\Protocol\LogoutResponse())
			->setID(\LightSaml\Helper::generateID())
			->setIssueInstant(new \DateTime())
			->setDestination($sp->get('slo_url'))
			->setInResponseTo($inResponseTo)
			->setIssuer(new \LightSaml\Model\Assertion\Issuer($this->getEntityID()))
			->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode($status)))
			->setSignature($this->getSignatureWriter());

		// Serialized by the binding on send — see createSAMLResponse()
		return $response;
	}

	/**
	 * The IdP metadata document
	 *
	 * @return  string  XML
	 */
	public function metadataXML()
	{
		$entityDescriptor = (new \LightSaml\Model\Metadata\EntityDescriptor())
			->setEntityID($this->getEntityID())
			->setValidUntil(new \DateTime('+1 DAY'))
			->addItem((new \LightSaml\Model\Metadata\IdpSsoDescriptor())
				->setWantAuthnRequestsSigned(true)
				->addKeyDescriptor((new \LightSaml\Model\Metadata\KeyDescriptor())
					->setUse(\LightSaml\Model\Metadata\KeyDescriptor::USE_SIGNING)
					->setCertificate($this->getCertificate())
					)
				// Advertise every format an SP may actually be issued
				// (per-SP `nameid_format`), not just one
				->addNameIdFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED)
				->addNameIdFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL)
				->addNameIdFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_PERSISTENT)
				->addSingleSignOnService((new \LightSaml\Model\Metadata\SingleSignOnService())
					->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
					->setLocation($this->getLoginUrl()))
				->addSingleLogoutService((new \LightSaml\Model\Metadata\SingleLogoutService())
					->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
					->setLocation($this->getLogoutUrl())));

		$serializationContext = new \LightSaml\Model\Context\SerializationContext();

		$entityDescriptor->serialize($serializationContext->getDocument(), $serializationContext);

		$serializationContext->getDocument()->formatOutput = true;

		return $serializationContext->getDocument()->saveXML();
	}
}
