<?php
/**
 * @package   orcid-php
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Profile.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Oauth.php';

use Orcid\Profile;
use Orcid\Oauth;

/**
 * Base ORCID profile tests
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * The complete profile path
	 *
	 * @var  string
	 **/
	private $complete = '';

	/**
	 * The basic profile path
	 *
	 * @var  string
	 **/
	private $basic = '';

	/**
	 * Sets up tests
	 *
	 * @return  void
	 **/
	public function setup()
	{
		$this->complete = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-complete.json';
		$this->basic    = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'profile-basic.json';
	}

	/**
	 * Gets a sample profile
	 *
	 * @param   bool  $complete  Whether or not to return full or basic profile
	 * @return  object
	 **/
	public function profile($complete = true)
	{
		// Mock the Oauth class to return an ORCID iD
		$oauth = $this->getMockBuilder('Oauth')
		              ->setMethods(['getOrcid', 'getProfile'])
		              ->getMock();

		$complete = $complete ? 'complete' : 'basic';
		$contents = json_decode(file_get_contents($this->$complete));

		// Tell the oauth method to return an empty ORCID iD
		$oauth->method('getOrcid')
		      ->willReturn('0000-0000-0000-0000');
		$oauth->method('getProfile')
		      ->willReturn($contents);

		$profile = new Profile($oauth);

		return $profile;
	}

	/**
	 * Test to make sure we can get an orcid id
	 *
	 * @return  void
	 **/
	public function testGetOrcidId()
	{
		$this->assertEquals('0000-0000-0000-0000', $this->profile()->id(), 'Failed to fetch properly formatted ID');
	}

	/**
	 * Test to make sure we can get a raw profile
	 *
	 * @return  void
	 **/
	public function testGetRawProfile()
	{
		$contents = json_decode(file_get_contents($this->complete));

		$this->assertEquals($contents->{'orcid-profile'}, $this->profile()->raw(), 'Failed to fetch raw profile data');
	}

	/**
	 * Test to make sure we can get a user bio
	 *
	 * @return  void
	 **/
	public function testGetBio()
	{
		$contents = json_decode(file_get_contents($this->complete));

		$this->assertEquals($contents->{'orcid-profile'}->{'orcid-bio'}, $this->profile()->bio(), 'Failed to fetch bio from profile data');
	}

	/**
	 * Test to make sure we can get a user email
	 *
	 * @return  void
	 **/
	public function testGetEmail()
	{
		$this->assertEquals('testuser@gmail.com', $this->profile()->email(), 'Failed to fetch email from profile data');
	}

	/**
	 * Test to make sure we can get a user name
	 *
	 * @return  void
	 **/
	public function testGetName()
	{
		$this->assertEquals('Test User', $this->profile()->fullName(), 'Failed to fetch full name from profile data');
	}
}