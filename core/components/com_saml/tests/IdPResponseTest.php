<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Components\Saml\Models\IdP;

require_once dirname(__DIR__) . '/models/IdP.php';

/**
 * Minimal stand-in for the component params registry
 */
class StubParams
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}
}

/**
 * Stand-in for a user record
 */
class StubSamlUser
{
	private $data;

	public function __construct($data)
	{
		$this->data = $data;
	}

	public function get($key, $default = '')
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}
}

/**
 * Stand-in for a registered Service Provider
 */
class StubSamlSp
{
	private $data;
	private $map;

	public function __construct($data, $map = null)
	{
		$this->data = $data;
		$this->map  = $map;
	}

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	public function attributeMap()
	{
		return $this->map;
	}
}

/**
 * SAML Response construction test
 *
 * Exercises the assertion the IdP actually hands to a Service Provider: that
 * it is addressed to the registered ACS, names the request it answers,
 * carries the per-SP NameID and attribute policy, and is signed.
 *
 * IdP_entityID is set explicitly so the model never has to resolve the hub's
 * base URL, which keeps the test free of framework facades.
 */
class IdPResponseTest extends Basic
{
	/**
	 * Temp dir holding the generated keypair
	 *
	 * @var  string
	 */
	private $dir;

	/**
	 * @var  object
	 */
	private $idp;

	/**
	 * @var  object
	 */
	private $sp;

	/**
	 * @var  object
	 */
	private $user;

	/**
	 * Generate a throwaway keypair rather than committing one
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		if (!function_exists('openssl_pkey_new'))
		{
			$this->markTestSkipped('ext/openssl is required to generate the test keypair');
		}

		$this->dir = sys_get_temp_dir() . '/com_saml_test_' . bin2hex(random_bytes(4));
		mkdir($this->dir, 0700, true);

		$key = openssl_pkey_new(array(
			'private_key_bits' => 2048,
			'private_key_type' => OPENSSL_KEYTYPE_RSA
		));

		$csr  = openssl_csr_new(array('commonName' => 'test-idp.example.org'), $key);
		$cert = openssl_csr_sign($csr, null, $key, 1);

		openssl_x509_export_to_file($cert, $this->dir . '/saml.crt');
		openssl_pkey_export_to_file($key, $this->dir . '/saml.pem');

		$this->idp = new IdP(new StubParams(array(
			'IdP_entityID'        => 'https://idp.example.org',
			'IdPCertificateFile'  => $this->dir . '/saml.crt',
			'IdPKeyFile'          => $this->dir . '/saml.pem',
			'signature_algorithm' => 'rsa-sha256',
			'assertion_lifetime'  => 180,
			'clock_skew'          => 60
		)));

		$this->sp = new StubSamlSp(array(
			'entity_id'     => 'https://sp.example.com/saml2',
			'acs_url'       => 'https://sp.example.com/acs',
			'nameid_source' => 'username',
			'nameid_format' => \LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED
		));

		$this->user = new StubSamlUser(array(
			'id'        => 42,
			'username'  => 'jdoe',
			'email'     => 'jdoe@example.org',
			'name'      => 'Jane Doe',
			'givenName' => 'Jane',
			'surname'   => 'Doe'
		));
	}

	/**
	 * @return  void
	 */
	public function tearDown(): void
	{
		if ($this->dir && is_dir($this->dir))
		{
			array_map('unlink', glob($this->dir . '/*'));
			rmdir($this->dir);
		}
	}

	/**
	 * Serialize a response and return an XPath over it
	 *
	 * @param   object  $response
	 * @return  \DOMXPath
	 */
	private function xpath($response)
	{
		$context = new \LightSaml\Model\Context\SerializationContext();
		$response->serialize($context->getDocument(), $context);

		$xpath = new \DOMXPath($context->getDocument());
		$xpath->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
		$xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
		$xpath->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

		return $xpath;
	}

	/**
	 * The response is addressed to the registered ACS and names the request
	 * it answers — never an address taken from the request itself
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testResponseIsAddressedToRegisteredAcs()
	{
		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_req1', '_sess1'));

		$this->assertEquals(1, $x->query('/samlp:Response[@Destination="https://sp.example.com/acs"]')->length);
		$this->assertEquals(1, $x->query('/samlp:Response[@InResponseTo="_req1"]')->length);
		$this->assertEquals(1, $x->query('//saml:SubjectConfirmationData[@InResponseTo="_req1"]')->length);
		$this->assertEquals(1, $x->query('//saml:Audience[text()="https://sp.example.com/saml2"]')->length);
		$this->assertEquals(1, $x->query('//samlp:StatusCode[@Value="urn:oasis:names:tc:SAML:2.0:status:Success"]')->length);
	}

	/**
	 * The SessionIndex is the opaque value handed in, never a hub session id
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testSessionIndexIsTheOpaqueValueSupplied()
	{
		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_req1', '_opaque99'));

		$this->assertEquals(1, $x->query('//saml:AuthnStatement[@SessionIndex="_opaque99"]')->length);
	}

	/**
	 * NameID follows the SP's configured source
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testNameIdFollowsPerSpPolicy()
	{
		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));
		$this->assertEquals(1, $x->query('//saml:NameID[text()="jdoe"]')->length);

		$byEmail = new StubSamlSp(array(
			'entity_id'     => 'https://sp.example.com/saml2',
			'acs_url'       => 'https://sp.example.com/acs',
			'nameid_source' => 'email'
		));

		$x = $this->xpath($this->idp->createSAMLResponse($byEmail, $this->user, '_r', '_s'));
		$this->assertEquals(1, $x->query('//saml:NameID[text()="jdoe@example.org"]')->length);
	}

	/**
	 * With no map configured, the default attribute set is released
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testDefaultAttributeSetIsReleased()
	{
		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));

		$this->assertEquals(4, $x->query('//saml:Attribute')->length);
		$this->assertEquals(1, $x->query('//saml:AttributeValue[text()="jdoe@example.org"]')->length);
	}

	/**
	 * A configured map replaces the default set entirely
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testConfiguredMapReplacesDefaultSet()
	{
		$mapped = new StubSamlSp(
			array(
				'entity_id'     => 'https://sp.example.com/saml2',
				'acs_url'       => 'https://sp.example.com/acs',
				'nameid_source' => 'username'
			),
			array(array('name' => 'eppn', 'friendly' => 'eppn', 'source' => 'username'))
		);

		$x = $this->xpath($this->idp->createSAMLResponse($mapped, $this->user, '_r', '_s'));

		$this->assertEquals(1, $x->query('//saml:Attribute')->length);
		$this->assertEquals(1, $x->query('//saml:Attribute[@Name="eppn"]')->length);
	}

	/**
	 * An attribute whose source resolves to nothing is omitted, and an
	 * assertion that would carry no attributes at all omits the statement —
	 * a childless AttributeStatement is schema-invalid
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testEmptyValuesAreOmittedAndNoEmptyStatementIsEmitted()
	{
		$bare = new StubSamlUser(array('id' => 7, 'username' => 'ghost'));

		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $bare, '_r', '_s'));

		$this->assertEquals(0, $x->query('//saml:AttributeStatement')->length);
		$this->assertEquals(1, $x->query('//saml:NameID[text()="ghost"]')->length);
	}

	/**
	 * The response is signed with SHA-256, and the assertion is signed only
	 * when the SP's registration asks for it
	 *
	 * @covers  Components\Saml\Models\IdP::createSAMLResponse
	 * @return  void
	 **/
	public function testSigningFollowsPerSpPolicy()
	{
		$x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));

		$this->assertEquals(1, $x->query('/samlp:Response/ds:Signature')->length);
		$this->assertEquals(0, $x->query('//saml:Assertion/ds:Signature')->length);
		$this->assertEquals(
			1,
			$x->query('//ds:SignatureMethod[@Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"]')->length
		);

		$signing = new StubSamlSp(array(
			'entity_id'      => 'https://sp.example.com/saml2',
			'acs_url'        => 'https://sp.example.com/acs',
			'nameid_source'  => 'username',
			'sign_assertion' => 1
		));

		$x = $this->xpath($this->idp->createSAMLResponse($signing, $this->user, '_r', '_s'));

		$this->assertEquals(1, $x->query('//saml:Assertion/ds:Signature')->length);
	}
}
