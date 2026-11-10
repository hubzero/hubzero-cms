<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\Registry;
use Hubzero\Facades\Facade;
use Components\Saml\Models\IdP;
use Components\Saml\Models\ServiceProvider;
use GuzzleHttp\Psr7\ServerRequest;
use LightSaml\Context\Profile\MessageContext;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Certificate;
use LightSaml\Helper;
use LightSaml\Model\Assertion\Issuer;
use LightSaml\Model\Protocol\AuthnRequest;
use LightSaml\Model\Protocol\LogoutResponse;
use LightSaml\Model\XmlDSig\SignatureWriter;
use LightSaml\SamlConstants;

require_once dirname(__DIR__) . '/models/IdP.php';
require_once dirname(__DIR__) . '/models/ServiceProvider.php';
require_once __DIR__ . '/StubParams.php';
require_once __DIR__ . '/StubSamlUser.php';
require_once __DIR__ . '/StubSamlSp.php';

/**
 * SAML messages crossing HTTP: what the IdP reads from a browser's request,
 * whether it trusts the signature on it, and what it sends back
 *
 * Requests are built from PHP's globals, as the controller builds them, so
 * the signed redirect query reaches the binding in the encoding the SP
 * signed.
 */
class IdPBindingTest extends Basic
{
    /**
     * Temp dir holding the generated keypairs
     *
     * @var  string
     */
    private $dir;

    /**
     * @var  IdP
     */
    private $idp;

    /**
     * The globals each test replaces, put back afterwards
     *
     * @var  array
     */
    private $globals;

    /**
     * The configuration the test bootstrap registered, if any
     *
     * @var  mixed
     */
    private $config = null;

    /**
     * @return  void
     */
    public function setUp(): void
    {
        if (!function_exists('openssl_pkey_new')) {
            $this->markTestSkipped('ext/openssl is required to generate the test keypairs');
        }

        $this->globals = [$_SERVER, $_GET, $_POST];

        $this->dir = sys_get_temp_dir() . '/com_saml_binding_' . bin2hex(random_bytes(4));
        mkdir($this->dir, 0700, true);

        $this->keypair('idp');
        $this->keypair('sp');
        $this->keypair('other');

        $this->idp = new IdP(new StubParams([
            'IdP_entityID'        => 'https://idp.example.org',
            'IdPCertificateFile'  => $this->dir . '/idp.crt',
            'IdPKeyFile'          => $this->dir . '/idp.pem',
            'signature_algorithm' => 'rsa-sha256',
        ]));

        // The metadata's endpoints are built from the hub's canonical URL
        $app = Facade::getApplication();
        if ($app->has('config')) {
            $this->config = $app->raw('config');
            $app->forget('config');
        }
        $app['config'] = fn () => new Registry(['live_site' => 'https://idp.example.org']);
    }

    /**
     * @return  void
     */
    public function tearDown(): void
    {
        list($_SERVER, $_GET, $_POST) = $this->globals;

        $app = Facade::getApplication();
        $app->forget('config');
        if ($this->config !== null) {
            $app['config'] = $this->config;
        }

        if ($this->dir && is_dir($this->dir)) {
            array_map('unlink', glob($this->dir . '/*'));
            rmdir($this->dir);
        }
    }

    /**
     * An SP's signed AuthnRequest on the redirect binding is read, with its
     * RelayState, and its signature checks out against the SP's certificate
     *
     * @return  void
     */
    public function testSignedRedirectRequestIsReadAndVerified()
    {
        $location = $this->signedRedirect($this->authnRequest(), 'sp');

        $message = $this->idp->readSAMLRequest($this->arrive('GET', $location))->getMessage();

        $this->assertInstanceOf(AuthnRequest::class, $message);
        $this->assertSame('https://sp.example.com/saml2', $message->getIssuer()->getValue());
        $this->assertSame('state-123', $message->getRelayState());
        $this->assertTrue($this->idp->validateRequestSignature($message, $this->sp('sp')));
    }

    /**
     * Changing a signed parameter in transit breaks the signature
     *
     * @return  void
     */
    public function testTamperedRedirectRequestIsNotTrusted()
    {
        $location = str_replace('RelayState=state-123', 'RelayState=state-999', $this->signedRedirect($this->authnRequest(), 'sp'));

        $message = $this->idp->readSAMLRequest($this->arrive('GET', $location))->getMessage();

        $this->assertSame('state-999', $message->getRelayState());
        $this->assertFalse($this->idp->validateRequestSignature($message, $this->sp('sp')));
    }

    /**
     * A request signed with a key other than the registered one is refused
     *
     * @return  void
     */
    public function testRequestSignedByAnotherKeyIsNotTrusted()
    {
        $location = $this->signedRedirect($this->authnRequest(), 'other');

        $message = $this->idp->readSAMLRequest($this->arrive('GET', $location))->getMessage();

        $this->assertFalse($this->idp->validateRequestSignature($message, $this->sp('sp')));
    }

    /**
     * A registered certificate that does not parse refuses the request; it
     * does not fail it with an error
     *
     * @return  void
     */
    public function testUnparseableRegisteredCertificateIsRefused()
    {
        $location = $this->signedRedirect($this->authnRequest(), 'sp');
        $message  = $this->idp->readSAMLRequest($this->arrive('GET', $location))->getMessage();

        $sp = ServiceProvider::blank()->set('signing_cert', base64_encode('not a certificate'));

        $this->assertNull($sp->certificate());
        $this->assertFalse($this->idp->validateRequestSignature($message, $sp));
    }

    /**
     * An AuthnRequest on the POST binding is read, with its RelayState
     *
     * @return  void
     */
    public function testPostRequestIsRead()
    {
        $request = $this->authnRequest();
        $context = new MessageContext();
        $request->serialize($context->getSerializationContext()->getDocument(), $context->getSerializationContext());

        $message = $this->idp->readSAMLRequest($this->arrive('POST', 'https://idp.example.org/saml/idp/login', [
            'SAMLRequest' => base64_encode($context->getSerializationContext()->getDocument()->saveXML()),
            'RelayState'  => 'state-123',
        ]))->getMessage();

        $this->assertInstanceOf(AuthnRequest::class, $message);
        $this->assertSame($request->getID(), $message->getID());
        $this->assertSame('state-123', $message->getRelayState());
    }

    /**
     * The login response goes back as a self-posting form to the ACS, with
     * the signed Response and the RelayState in it
     *
     * @return  void
     */
    public function testLoginResponseIsPostedToTheAcs()
    {
        $sp = new StubSamlSp([
            'entity_id'     => 'https://sp.example.com/saml2',
            'acs_url'       => 'https://sp.example.com/acs',
            'nameid_source' => 'username',
        ]);
        $user = new StubSamlUser(['id' => 42, 'username' => 'jdoe', 'email' => 'jdoe@example.org']);

        $response = $this->idp->createSAMLResponse($sp, $user, 'request-1', 'session-1');
        $response->setRelayState('state-123');

        $context = (new MessageContext())->setMessage($response);
        $http = $this->idp->bindingFactory()->create(SamlConstants::BINDING_SAML2_HTTP_POST)->send($context);

        $this->assertSame(200, $http->getStatusCode());
        $this->assertStringStartsWith('text/html', $http->getHeaderLine('Content-Type'));

        $html = (string) $http->getBody();
        $this->assertStringContainsString('action="https://sp.example.com/acs"', $html);

        $this->assertMatchesRegularExpression('/name="SAMLResponse" value="([^"]+)"/', $html);
        preg_match('/name="SAMLResponse" value="([^"]+)"/', $html, $field);
        $xml = base64_decode(html_entity_decode($field[1]));
        $this->assertStringContainsString('InResponseTo="request-1"', $xml);
        $this->assertStringContainsString('ds:SignatureValue', $xml);

        $this->assertMatchesRegularExpression('/name="RelayState" value="state-123"/', $html);
    }

    /**
     * The logout response is a redirect to the SP's SLO URL whose query the
     * IdP has signed, verifiable with the IdP's certificate
     *
     * @return  void
     */
    public function testLogoutResponseIsASignedRedirectToTheSlo()
    {
        $sp = new StubSamlSp(['slo_url' => 'https://sp.example.com/slo']);

        $response = $this->idp->createSAMLLogoutResponse($sp, 'logout-1', SamlConstants::STATUS_SUCCESS);
        $response->setRelayState('state-123');

        $context = (new MessageContext())->setMessage($response);
        $http = $this->idp->bindingFactory()->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)->send($context);

        $this->assertSame(302, $http->getStatusCode());

        $location = $http->getHeaderLine('Location');
        $this->assertStringStartsWith('https://sp.example.com/slo?', $location);

        // Read it back as the SP would, and check the IdP's signature
        $message = $this->idp->readSAMLRequest($this->arrive('GET', $location))->getMessage();

        $this->assertInstanceOf(LogoutResponse::class, $message);
        $this->assertSame('logout-1', $message->getInResponseTo());
        $this->assertSame('state-123', $message->getRelayState());
        $this->assertTrue($message->getSignature()->validate(
            KeyHelper::createPublicKey(X509Certificate::fromFile($this->dir . '/idp.crt')),
        ));
    }

    /**
     * The metadata document names the IdP, its endpoints and its certificate
     *
     * @return  void
     */
    public function testMetadataDescribesTheIdp()
    {
        $document = new \DOMDocument();
        $document->loadXML($this->idp->metadataXML());

        $xml = new \DOMXPath($document);
        $xml->registerNamespace('md', SamlConstants::NS_METADATA);
        $xml->registerNamespace('ds', SamlConstants::NS_XMLDSIG);

        $this->assertSame('https://idp.example.org', $xml->evaluate('string(/md:EntityDescriptor/@entityID)'));
        $this->assertSame(
            'https://idp.example.org/saml/idp/login',
            $xml->evaluate('string(//md:SingleSignOnService/@Location)'),
        );
        $this->assertSame(
            'https://idp.example.org/saml/idp/logout',
            $xml->evaluate('string(//md:SingleLogoutService/@Location)'),
        );
        $this->assertSame(
            X509Certificate::fromFile($this->dir . '/idp.crt')->getData(),
            preg_replace('/\s+/', '', $xml->evaluate('string(//md:KeyDescriptor//ds:X509Certificate)')),
        );
    }

    /**
     * An AuthnRequest from the test SP
     *
     * @return  AuthnRequest
     */
    private function authnRequest()
    {
        return (new AuthnRequest())
            ->setID(Helper::generateID())
            ->setIssueInstant(new \DateTime())
            ->setDestination('https://idp.example.org/saml/idp/login')
            ->setIssuer(new Issuer('https://sp.example.com/saml2'))
            ->setRelayState('state-123');
    }

    /**
     * The redirect URL an SP sends a browser to, signed with one of the keys
     *
     * @param   AuthnRequest  $request
     * @param   string        $signer   idp, sp or other
     * @return  string
     */
    private function signedRedirect($request, $signer)
    {
        $request->setSignature(new SignatureWriter(
            X509Certificate::fromFile($this->dir . '/' . $signer . '.crt'),
            KeyHelper::createPrivateKey($this->dir . '/' . $signer . '.pem', '', true),
        ));

        $context = (new MessageContext())->setMessage($request);

        return $this->idp->bindingFactory()
            ->create(SamlConstants::BINDING_SAML2_HTTP_REDIRECT)
            ->send($context)
            ->getHeaderLine('Location');
    }

    /**
     * A registered SP whose signing certificate is one of the test keys
     *
     * @param   string  $name
     * @return  ServiceProvider
     */
    private function sp($name)
    {
        return ServiceProvider::blank()
            ->set('entity_id', 'https://sp.example.com/saml2')
            ->set('signing_cert', file_get_contents($this->dir . '/' . $name . '.crt'));
    }

    /**
     * The request the controller would see for this browser request
     *
     * @param   string  $method
     * @param   string  $url
     * @param   array   $post
     * @return  ServerRequest
     */
    private function arrive($method, $url, array $post = [])
    {
        $parts = parse_url($url);
        $query = isset($parts['query']) ? $parts['query'] : '';

        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['HTTP_HOST']      = $parts['host'];
        $_SERVER['HTTPS']          = 'on';
        $_SERVER['REQUEST_URI']    = $parts['path'] . ($query !== '' ? '?' . $query : '');
        $_SERVER['QUERY_STRING']   = $query;
        parse_str($query, $_GET);
        $_POST = $post;

        return ServerRequest::fromGlobals();
    }

    /**
     * Generate a throwaway keypair rather than committing one
     *
     * @param   string  $name
     * @return  void
     */
    private function keypair($name)
    {
        $key  = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $csr  = openssl_csr_new(['commonName' => $name . '.example.org'], $key);
        $cert = openssl_csr_sign($csr, null, $key, 1, ['digest_alg' => 'sha256']);

        openssl_x509_export_to_file($cert, $this->dir . '/' . $name . '.crt');
        openssl_pkey_export_to_file($key, $this->dir . '/' . $name . '.pem');
    }
}
