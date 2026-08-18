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
	}
}
