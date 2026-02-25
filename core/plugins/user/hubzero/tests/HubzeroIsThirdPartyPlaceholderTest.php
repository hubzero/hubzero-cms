<?php

/**
 * Unit tests for Hubzero::isThirdPartyPlaceholder().
 *
 * Third-party-auth users (ORCID, Google, Shibboleth, etc.) are inserted
 * with a placeholder username of the form "-<hzal_id>" and an email of
 * "-<hzal_id>@invalid" before the user has completed the registration-
 * completion form. The admin-new-user notification is deferred for these
 * accounts so it fires with the user's real chosen values instead.
 * This predicate identifies the placeholder state.
 */

use PHPUnit\Framework\TestCase;
use Plugins\User\Hubzero\Hubzero;

final class HubzeroIsThirdPartyPlaceholderTest extends TestCase
{
    public function testPlaceholderUsernameIsDetected()
    {
        $this->assertTrue(Hubzero::isThirdPartyPlaceholder([
            'username' => '-2843',
            'email'    => '-2843@invalid',
        ]));
    }

    public function testNormalUsernameIsNotPlaceholder()
    {
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'username' => 'u0000_0002_6885_6310',
            'email'    => 'nkissebe+rhys@gmail.com',
        ]));
    }

    public function testDashPrefixedButNonNumericIsNotPlaceholder()
    {
        // Only "-<digits>" is a placeholder; a leading dash with letters is
        // something else (unlikely, but should not falsely match).
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'username' => '-abc',
        ]));
    }

    public function testEmptyOrMissingUsernameIsNotPlaceholder()
    {
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'username' => '',
        ]));
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([]));
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'email' => '-2843@invalid',
        ]));
    }

    public function testUsernameStartingWithDigitIsNotPlaceholder()
    {
        // A username that happens to be all digits (unusual but valid on
        // some hubs) is not a placeholder — the dash prefix is required.
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'username' => '12345',
        ]));
    }

    public function testStringifiedNonPlaceholderInputIsHandled()
    {
        // Callers pass whatever User::get('username') returned. Coerce to
        // string internally — an integer-shaped username shouldn't crash.
        $this->assertFalse(Hubzero::isThirdPartyPlaceholder([
            'username' => 12345,
        ]));
    }
}
