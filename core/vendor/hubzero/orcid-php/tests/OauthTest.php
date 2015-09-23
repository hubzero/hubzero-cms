<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Curl.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Oauth.php';

use Orcid\Oauth;

/**
 * Base ORCID oauth tests
 */
class OauthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Gets a sample oauth object
     *
     * @return  object
     **/
    public function oauth()
    {
        // Mock the Oauth class to return an ORCID iD
        $http = $this->getMockBuilder('Curl')
                     ->setMethods(['execute'])
                     ->getMock();

        // Tell the curl method to return an empty ORCID iD
        $http->method('execute')
              ->willReturn(1);

        return new Oauth($http);
    }

    /**
     * Test to make sure we can get a basic authorization url
     *
     * @return  void
     **/
    public function testGetBasicAuthorizationUrl()
    {
        $oauth = $this->oauth()
                      ->setClientId('1234')
                      ->setScope('/authorize')
                      ->setRedirectUri('here');

        $this->assertEquals(
            'https://orcid.org/oauth/authorize?client_id=1234&scope=/authorize&redirect_uri=here&response_type=code',
            $oauth->getAuthorizationUrl(),
            'Failed to fetch a properly formatted authorization URL'
        );
    }

    /**
     * Test to make sure we throw an exception for a missing client id
     *
     * @expectedException  Exception
     * @return  void
     **/
    public function testGetAuthorizationUrlThrowsExceptionForMissingClientId()
    {
        $oauth = $this->oauth()
                      ->setScope('/authorize')
                      ->setRedirectUri('here')
                      ->getAuthorizationUrl();
    }

    /**
     * Test to make sure we throw an exception for a missing scope
     *
     * @expectedException  Exception
     * @return  void
     **/
    public function testGetAuthorizationUrlThrowsExceptionForMissingScope()
    {
        $oauth = $this->oauth()
                      ->setClientId('1234')
                      ->setRedirectUri('here')
                      ->getAuthorizationUrl();
    }

    /**
     * Test to make sure we throw an exception for a missing redirect uri
     *
     * @expectedException  Exception
     * @return  void
     **/
    public function testGetAuthorizationUrlThrowsExceptionForMissingRedirectUri()
    {
        $oauth = $this->oauth()
                      ->setClientId('1234')
                      ->setScope('/authorize')
                      ->getAuthorizationUrl();
    }

    /**
     * Test to make sure we can get an authorization url with the showLogin option enabled
     *
     * @return  void
     **/
    public function testGetAuthorizationUrlHasAdditionalParameter()
    {
        $url = $this->oauth()
                    ->setClientId('1234')
                    ->setScope('/authorize')
                    ->setRedirectUri('here')
                    ->showLogin()
                    ->setState('foobar')
                    ->setFamilyNames('Smith')
                    ->setGivenNames('John')
                    ->setEmail('me@gmail.com')
                    ->getAuthorizationUrl();

        $this->assertRegExp('/&show_login=true/', $url, 'Failed to fetch an authorization URL with the show_login parameters set');
        $this->assertRegExp('/&state=foobar/', $url, 'Failed to fetch an authorization URL with the state parameters set');
        $this->assertRegExp('/&family_names=Smith/', $url, 'Failed to fetch an authorization URL with the family_names parameters set');
        $this->assertRegExp('/&given_names=John/', $url, 'Failed to fetch an authorization URL with the given_names parameters set');
        $this->assertRegExp('/&email=me%40gmail.com/', $url, 'Failed to fetch an authorization URL with the email parameters set');
    }
}