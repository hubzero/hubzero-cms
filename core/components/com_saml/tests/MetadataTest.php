<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Tests;

use Hubzero\Test\Basic;
use Components\Saml\Helpers\Metadata;

require_once dirname(__DIR__) . '/helpers/Metadata.php';

/**
 * Metadata import helper test
 */
class MetadataTest extends Basic
{
	/**
	 * A representative SP EntityDescriptor (Canvas-style)
	 *
	 * @var  string
	 */
	private $xml = '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="http://sp.example.com/saml2">
  <md:SPSSODescriptor AuthnRequestsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
    <md:KeyDescriptor use="signing">
      <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
        <ds:X509Data>
          <ds:X509Certificate>
            MIIEMDCCAxigAwIBAgIJAPBXgeztn8U2MA0GCSqGSIb3DQEBCwUAMIGsMQswCQYD
            VQQGEwJVUzENMAsGA1UECAwEVXRhaDEXMBUGA1UEBwwOU2FsdCBMYWtlIENpdHkx
          </ds:X509Certificate>
        </ds:X509Data>
      </ds:KeyInfo>
    </md:KeyDescriptor>
    <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://sp.example.com/slo_post"/>
    <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="https://sp.example.com/slo"/>
    <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="https://sp.example.com/acs_redirect" index="1"/>
    <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://sp.example.com/acs" index="0" isDefault="true"/>
  </md:SPSSODescriptor>
</md:EntityDescriptor>';

	/**
	 * Parsing a full SP descriptor extracts every field
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseExtractsFields()
	{
		$fields = Metadata::parse($this->xml);

		$this->assertIsArray($fields);
		$this->assertEquals('http://sp.example.com/saml2', $fields['entity_id']);
		$this->assertEquals(1, $fields['want_requests_signed']);
		$this->assertStringStartsWith('MIIEMDCCAxig', $fields['signing_cert']);
		$this->assertStringNotContainsString(' ', $fields['signing_cert']);
	}

	/**
	 * The HTTP-POST ACS is preferred over other bindings regardless of order
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParsePrefersPostAcs()
	{
		$fields = Metadata::parse($this->xml);

		$this->assertEquals('https://sp.example.com/acs', $fields['acs_url']);
	}

	/**
	 * The HTTP-Redirect SLO is the only one taken — it is the binding we
	 * answer over, so storing a SOAP or POST endpoint would look configured
	 * while every logout response went nowhere
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseTakesOnlyRedirectSlo()
	{
		$fields = Metadata::parse($this->xml);

		$this->assertEquals('https://sp.example.com/slo', $fields['slo_url']);

		// Only a non-answerable binding on offer — no SLO is registered
		$xml = str_replace(
			'Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="https://sp.example.com/slo"',
			'Binding="urn:oasis:names:tc:SAML:2.0:bindings:SOAP" Location="https://sp.example.com/slo_soap"',
			$this->xml
		);

		$fields = Metadata::parse($xml);

		$this->assertArrayNotHasKey('slo_url', $fields);
	}

	/**
	 * A published NameID format we can issue selects both the format and the
	 * matching value source — an SP that only accepts emailAddress must not
	 * be left on the username default
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseAdoptsPublishedNameIdFormat()
	{
		$xml = str_replace(
			'<md:KeyDescriptor use="signing">',
			'<md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</md:NameIDFormat>'
			. '<md:KeyDescriptor use="signing">',
			$this->xml
		);

		$fields = Metadata::parse($xml);

		$this->assertEquals('urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress', $fields['nameid_format']);
		$this->assertEquals('email', $fields['nameid_source']);
	}

	/**
	 * A format we cannot issue is ignored rather than stored, and a
	 * descriptor that publishes none leaves the keys out entirely so an
	 * existing registration is not overwritten
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseIgnoresUnsupportedAndAbsentNameIdFormats()
	{
		$xml = str_replace(
			'<md:KeyDescriptor use="signing">',
			'<md:NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:transient</md:NameIDFormat>'
			. '<md:KeyDescriptor use="signing">',
			$this->xml
		);

		$fields = Metadata::parse($xml);
		$this->assertArrayNotHasKey('nameid_format', $fields);

		// The base fixture publishes no NameIDFormat at all
		$fields = Metadata::parse($this->xml);
		$this->assertArrayNotHasKey('nameid_format', $fields);
		$this->assertArrayNotHasKey('nameid_source', $fields);
	}

	/**
	 * An SP with no HTTP-POST ACS cannot be delivered to, so the import
	 * fails rather than registering an unreachable address
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseRequiresPostAcs()
	{
		$xml = str_replace(
			'Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://sp.example.com/acs"',
			'Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact" Location="https://sp.example.com/acs_artifact"',
			$this->xml
		);

		$this->assertFalse(Metadata::parse($xml));
	}

	/**
	 * An explicit AuthnRequestsSigned="false" is reported
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseExplicitlyUnsignedRequests()
	{
		$xml = str_replace('AuthnRequestsSigned="true"', 'AuthnRequestsSigned="false"', $this->xml);

		$fields = Metadata::parse($xml);

		$this->assertEquals(0, $fields['want_requests_signed']);
	}

	/**
	 * An absent AuthnRequestsSigned leaves the key out entirely, so
	 * re-importing cannot silently disable signature verification on an SP
	 * that already requires it
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseOmitsAbsentSignedFlag()
	{
		$xml = str_replace('AuthnRequestsSigned="true"', '', $this->xml);

		$fields = Metadata::parse($xml);

		$this->assertArrayNotHasKey('want_requests_signed', $fields);
	}

	/**
	 * Invalid or non-SP documents fail cleanly with false
	 *
	 * @covers  Components\Saml\Helpers\Metadata::parse
	 * @return  void
	 **/
	public function testParseRejectsBadDocuments()
	{
		$this->assertFalse(Metadata::parse('not xml at all'));
		$this->assertFalse(Metadata::parse('<foo/>'));

		// IdP metadata is not SP metadata
		$this->assertFalse(Metadata::parse(
			'<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="x"><md:IDPSSODescriptor/></md:EntityDescriptor>'
		));

		// An SP descriptor without an ACS is unusable
		$this->assertFalse(Metadata::parse(
			'<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="x"><md:SPSSODescriptor/></md:EntityDescriptor>'
		));
	}

	/**
	 * Non-http(s) URLs are refused before any fetch
	 *
	 * @covers  Components\Saml\Helpers\Metadata::fetch
	 * @return  void
	 **/
	public function testFetchRejectsNonHttpsUrls()
	{
		$this->assertFalse(Metadata::fetch('file:///etc/passwd'));
		$this->assertFalse(Metadata::fetch('ftp://example.com/metadata.xml'));

		// The document is trusted for ACS URL and signing cert, and nothing
		// verifies a signature over it, so plain http is refused too
		$this->assertFalse(Metadata::fetch('http://example.com/metadata.xml'));
	}

	/**
	 * Internal addresses are refused, so the fetch cannot be used to probe
	 * the hub's own network
	 *
	 * @covers  Components\Saml\Helpers\Metadata::fetch
	 * @return  void
	 **/
	public function testFetchRejectsInternalAddresses()
	{
		$this->assertFalse(Metadata::fetch('https://127.0.0.1/metadata'));
		$this->assertFalse(Metadata::fetch('https://localhost/metadata'));
		$this->assertFalse(Metadata::fetch('https://10.0.0.5/metadata'));
		$this->assertFalse(Metadata::fetch('https://192.168.1.1/metadata'));
		$this->assertFalse(Metadata::fetch('https://169.254.169.254/latest/meta-data/'));
		$this->assertFalse(Metadata::fetch('https://[::1]/metadata'));
	}
}
