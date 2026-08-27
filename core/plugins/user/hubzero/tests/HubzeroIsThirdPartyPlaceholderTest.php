<?php
/**
 * Unit tests for plgUserHubzero::isThirdPartyPlaceholder().
 *
 * Third-party-auth users (ORCID, Google, Shibboleth, etc.) are inserted
 * with a placeholder username of the form "-<hzal_id>" and an email of
 * "-<hzal_id>@invalid" before the user has completed the registration-
 * completion form. The admin-new-user notification is deferred for these
 * accounts so it fires with the user's real chosen values instead.
 * This predicate identifies the placeholder state.
 */

use PHPUnit\Framework\TestCase;

final class HubzeroIsThirdPartyPlaceholderTest extends TestCase
{
	public function testPlaceholderUsernameIsDetected()
	{
		$this->assertTrue(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => '-2843',
			'email'    => '-2843@invalid',
		]));
	}

	public function testNormalUsernameIsNotPlaceholder()
	{
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => 'u0000_0002_6885_6310',
			'email'    => 'nkissebe+rhys@gmail.com',
		]));
	}

	public function testDashPrefixedButNonNumericIsNotPlaceholder()
	{
		// Only "-<digits>" is a placeholder; a leading dash with letters is
		// something else (unlikely, but should not falsely match).
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => '-abc',
		]));
	}

	public function testEmptyOrMissingUsernameIsNotPlaceholder()
	{
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => '',
		]));
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([]));
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'email' => '-2843@invalid',
		]));
	}

	public function testUsernameStartingWithDigitIsNotPlaceholder()
	{
		// A username that happens to be all digits (unusual but valid on
		// some hubs) is not a placeholder — the dash prefix is required.
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => '12345',
		]));
	}

	public function testStringifiedNonPlaceholderInputIsHandled()
	{
		// Callers pass whatever User::get('username') returned. Coerce to
		// string internally — an integer-shaped username shouldn't crash.
		$this->assertFalse(plgUserHubzero::isThirdPartyPlaceholder([
			'username' => 12345,
		]));
	}
}
