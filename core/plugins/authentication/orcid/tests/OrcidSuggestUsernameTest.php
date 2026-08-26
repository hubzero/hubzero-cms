<?php
/**
 * Unit tests for plgAuthenticationOrcid::suggestUsername().
 *
 * Historical bug: the plugin used the ORCID email local-part as the
 * suggested username, which fails the login regex whenever the email
 * contains '+', '.', or other legal-in-email-but-not-in-login chars.
 * The helper under test derives the hint from the ORCID iD instead,
 * guaranteeing a valid login-safe string.
 */

use PHPUnit\Framework\TestCase;

final class OrcidSuggestUsernameTest extends TestCase
{
	public function testCanonicalOrcidIdProducesExpectedHint()
	{
		$this->assertSame(
			'u0000_0002_6885_6310',
			plgAuthenticationOrcid::suggestUsername('0000-0002-6885-6310')
		);
	}

	public function testUppercaseCheckDigitXIsLowercased()
	{
		// ORCID check digit "10" is written as 'X'; the hint must be lowercase.
		$this->assertSame(
			'u0009_0002_3538_094x',
			plgAuthenticationOrcid::suggestUsername('0009-0002-3538-094X')
		);
	}

	public function testDifferentOrcidsProduceDifferentHints()
	{
		$a = plgAuthenticationOrcid::suggestUsername('0000-0001-7697-7422');
		$b = plgAuthenticationOrcid::suggestUsername('0000-0002-6885-6310');
		$this->assertNotSame($a, $b);
	}

	/**
	 * The whole point of the helper: whatever the ORCID iD, the result must
	 * be accepted by the login-name regex ([a-z0-9_]+, length >= 2). Any
	 * regression that reintroduces '+' or '.' from an email local-part would
	 * be caught here.
	 */
	public function testResultAlwaysMatchesLoginRegex()
	{
		$ids = [
			'0000-0002-6885-6310',
			'0009-0002-3538-094X',
			'0000-0001-7697-7422',
			'0009-0009-4015-5457',
			'0000-0003-2074-2178',
		];
		foreach ($ids as $id)
		{
			$out = plgAuthenticationOrcid::suggestUsername($id);
			$this->assertMatchesRegularExpression('/^[a-z0-9_]+$/', $out, "hint for $id must be login-safe");
			$this->assertGreaterThanOrEqual(2, strlen($out), "hint for $id must be at least 2 chars");
		}
	}

	public function testHintIsDeterministic()
	{
		// Same ORCID iD must always produce the same hint — a user whose
		// account is deleted and reregisters gets the same username back.
		$a = plgAuthenticationOrcid::suggestUsername('0000-0001-7697-7422');
		$b = plgAuthenticationOrcid::suggestUsername('0000-0001-7697-7422');
		$this->assertSame($a, $b);
	}
}
