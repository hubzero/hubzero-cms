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
 * IdP protocol engine test
 *
 * Covers the attribute-source whitelist, which decides what a Service
 * Provider may be told about a user.
 */
class IdPTest extends Basic
{
	/**
	 * Documented sources resolve
	 *
	 * @covers  Components\Saml\Models\IdP::isKnownAttributeSource
	 * @return  void
	 **/
	public function testKnownAttributeSources()
	{
		foreach (array('email', 'name', 'givenName', 'middleName', 'surname', 'username', 'id') as $source)
		{
			$this->assertTrue(IdP::isKnownAttributeSource($source), $source . ' should be resolvable');
		}

		// Alias for the profile field of the same name
		$this->assertTrue(IdP::isKnownAttributeSource('organization'));

		// Arbitrary profile fields
		$this->assertTrue(IdP::isKnownAttributeSource('profile:orcid'));
		$this->assertTrue(IdP::isKnownAttributeSource('profile:organization'));

		// Group membership, which resolves to a list rather than a value
		$this->assertTrue(IdP::isKnownAttributeSource('groups'));
		$this->assertTrue(IdP::isKnownAttributeSource('groups:allowed'));
	}

	/**
	 * Anything else is refused, so a typo cannot silently release nothing
	 *
	 * @covers  Components\Saml\Models\IdP::isKnownAttributeSource
	 * @return  void
	 **/
	public function testUnknownAttributeSourcesRejected()
	{
		$this->assertFalse(IdP::isKnownAttributeSource('password'));
		$this->assertFalse(IdP::isKnownAttributeSource('emailAddress'));
		$this->assertFalse(IdP::isKnownAttributeSource('profile:'));
		$this->assertFalse(IdP::isKnownAttributeSource(''));
		$this->assertFalse(IdP::isKnownAttributeSource('../../etc/passwd'));

		// Only the two documented group policies, so a plausible-looking
		// typo cannot be saved as a source that releases nothing
		$this->assertFalse(IdP::isKnownAttributeSource('groups:'));
		$this->assertFalse(IdP::isKnownAttributeSource('groups:all'));
		$this->assertFalse(IdP::isKnownAttributeSource('groups:managed'));
	}

	/**
	 * URI attribute names are declared as URIs; others stay unspecified
	 *
	 * @covers  Components\Saml\Models\IdP::attributeNameFormat
	 * @return  void
	 **/
	public function testAttributeNameFormat()
	{
		$uri = 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri';

		$this->assertEquals($uri, IdP::attributeNameFormat('urn:oid:1.3.6.1.4.1.5923.1.5.1.1'));
		$this->assertEquals($uri, IdP::attributeNameFormat('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress'));
		$this->assertEquals('urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified', IdP::attributeNameFormat('isMemberOf'));
	}
}
