<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Components\Saml\Models\IdP;
use PHPUnit\Framework\Attributes\CoversMethod;

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
#[CoversMethod(IdP::class, 'createSAMLResponse')]
class IdPResponseTest extends Basic
{
    /**
     * Temp dir holding the generated keypair
     *
     * @var  string
     */
    private $dir;

    /**
     * @var  \Components\Saml\Models\IdP|null
     */
    private $idp;

    /**
     * @var  \Components\Saml\Tests\StubSamlSp|null
     */
    private $sp;

    /**
     * @var  \Components\Saml\Tests\StubSamlUser|null
     */
    private $user;

    /**
     * Generate a throwaway keypair rather than committing one
     *
     * @return  void
     */
    public function setUp(): void
    {
        if (!function_exists('openssl_pkey_new')) {
            $this->markTestSkipped('ext/openssl is required to generate the test keypair');
        }

        $this->dir = sys_get_temp_dir() . '/com_saml_test_' . bin2hex(random_bytes(4));
        mkdir($this->dir, 0700, true);

        $key = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        $csr  = openssl_csr_new(['commonName' => 'test-idp.example.org'], $key);
        $this->assertInstanceOf(\OpenSSLCertificateSigningRequest::class, $csr);
        $cert = openssl_csr_sign($csr, null, $key, 1);
        $this->assertNotFalse($cert);

        openssl_x509_export_to_file($cert, $this->dir . '/saml.crt');
        openssl_pkey_export_to_file($key, $this->dir . '/saml.pem');

        $this->idp = new IdP(new \Hubzero\Config\Registry([
            'IdP_entityID'        => 'https://idp.example.org',
            'IdPCertificateFile'  => $this->dir . '/saml.crt',
            'IdPKeyFile'          => $this->dir . '/saml.pem',
            'signature_algorithm' => 'rsa-sha256',
            'assertion_lifetime'  => 180,
            'clock_skew'          => 60,
        ]));

        $this->sp = new StubSamlSp([
            'entity_id'     => 'https://sp.example.com/saml2',
            'acs_url'       => 'https://sp.example.com/acs',
            'nameid_source' => 'username',
            'nameid_format' => \LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED,
        ]);

        $this->user = new StubSamlUser([
            'id'        => 42,
            'username'  => 'jdoe',
            'email'     => 'jdoe@example.org',
            'name'      => 'Jane Doe',
            'givenName' => 'Jane',
            'surname'   => 'Doe',
        ]);
    }

    /**
     * @return  void
     */
    public function tearDown(): void
    {
        if ($this->dir && is_dir($this->dir)) {
            array_map('unlink', (glob($this->dir . '/*') ?: []));
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
        $context = new \LightSaml\Context\Model\SerializationContext();
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
     * @return  void
     **/
    public function testResponseIsAddressedToRegisteredAcs()
    {
        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_req1', '_sess1'));

        $this->assertEquals(1, $this->countNodes($x, '/samlp:Response[@Destination="https://sp.example.com/acs"]'));
        $this->assertEquals(1, $this->countNodes($x, '/samlp:Response[@InResponseTo="_req1"]'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:SubjectConfirmationData[@InResponseTo="_req1"]'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:Audience[text()="https://sp.example.com/saml2"]'));
        $this->assertEquals(
            1,
            $this->countNodes($x, '//samlp:StatusCode[@Value="urn:oasis:names:tc:SAML:2.0:status:Success"]'),
        );
    }

    /**
     * The SessionIndex is the opaque value handed in, never a hub session id
     *
     * @return  void
     **/
    public function testSessionIndexIsTheOpaqueValueSupplied()
    {
        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_req1', '_opaque99'));

        $this->assertEquals(1, $this->countNodes($x, '//saml:AuthnStatement[@SessionIndex="_opaque99"]'));
    }

    /**
     * NameID follows the SP's configured source
     *
     * @return  void
     **/
    public function testNameIdFollowsPerSpPolicy()
    {
        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:NameID[text()="jdoe"]'));

        $byEmail = new StubSamlSp([
            'entity_id'     => 'https://sp.example.com/saml2',
            'acs_url'       => 'https://sp.example.com/acs',
            'nameid_source' => 'email',
        ]);

        $x = $this->xpath($this->idp->createSAMLResponse($byEmail, $this->user, '_r', '_s'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:NameID[text()="jdoe@example.org"]'));
    }

    /**
     * With no map configured, the default attribute set is released
     *
     * @return  void
     **/
    public function testDefaultAttributeSetIsReleased()
    {
        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));

        $this->assertEquals(4, $this->countNodes($x, '//saml:Attribute'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:AttributeValue[text()="jdoe@example.org"]'));
    }

    /**
     * A configured map replaces the default set entirely
     *
     * @return  void
     **/
    public function testConfiguredMapReplacesDefaultSet()
    {
        $mapped = new StubSamlSp([
            'entity_id'     => 'https://sp.example.com/saml2',
            'acs_url'       => 'https://sp.example.com/acs',
            'nameid_source' => 'username',
        ], [['name' => 'eppn', 'friendly' => 'eppn', 'source' => 'username']]);

        $x = $this->xpath($this->idp->createSAMLResponse($mapped, $this->user, '_r', '_s'));

        $this->assertEquals(1, $this->countNodes($x, '//saml:Attribute'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:Attribute[@Name="eppn"]'));
    }

    /**
     * An attribute whose source resolves to nothing is omitted, and an
     * assertion that would carry no attributes at all omits the statement —
     * a childless AttributeStatement is schema-invalid
     *
     * @return  void
     **/
    public function testEmptyValuesAreOmittedAndNoEmptyStatementIsEmitted()
    {
        $bare = new StubSamlUser(['id' => 7, 'username' => 'ghost']);

        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $bare, '_r', '_s'));

        $this->assertEquals(0, $this->countNodes($x, '//saml:AttributeStatement'));
        $this->assertEquals(1, $this->countNodes($x, '//saml:NameID[text()="ghost"]'));
    }

    /**
     * The response is signed with SHA-256, and the assertion is signed only
     * when the SP's registration asks for it
     *
     * @return  void
     **/
    public function testSigningFollowsPerSpPolicy()
    {
        $x = $this->xpath($this->idp->createSAMLResponse($this->sp, $this->user, '_r', '_s'));

        $this->assertEquals(1, $this->countNodes($x, '/samlp:Response/ds:Signature'));
        $this->assertEquals(0, $this->countNodes($x, '//saml:Assertion/ds:Signature'));
        $this->assertEquals(
            1,
            $this->countNodes(
                $x,
                '//ds:SignatureMethod[@Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"]',
            ),
        );

        $signing = new StubSamlSp([
            'entity_id'      => 'https://sp.example.com/saml2',
            'acs_url'        => 'https://sp.example.com/acs',
            'nameid_source'  => 'username',
            'sign_assertion' => 1,
        ]);

        $x = $this->xpath($this->idp->createSAMLResponse($signing, $this->user, '_r', '_s'));

        $this->assertEquals(1, $this->countNodes($x, '//saml:Assertion/ds:Signature'));
    }

    /**
     * How many nodes an XPath expression finds (none when it does not evaluate)
     *
     * @param   \DOMXPath  $x
     * @param   string     $expression
     * @return  int
     */
    private function countNodes(\DOMXPath $x, $expression)
    {
        $list = $x->query($expression);

        return $list ? $list->length : 0;
    }
}
