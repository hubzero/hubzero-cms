<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma\Tests;

use Hubzero\Test\Database;
use Hubzero\Database\Relational;
use Hubzero\Karma\Karma;
use Hubzero\Karma\Scale;
use Hubzero\Karma\Rule;
use Hubzero\Karma\Gate;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Karma visibility tests
 *
 * Who may see a karma value is four questions, not one, and they pull in
 * opposite directions. These cover the whole matrix, because the failure mode
 * is disclosure.
 */
class VisibilityTest extends Database
{
	/**
	 * Whose karma
	 *
	 * @var  integer
	 */
	const SUBJECT = 42;

	/**
	 * Somebody else
	 *
	 * @var  integer
	 */
	const STRANGER = 7;

	/**
	 * Sets up the tests, called prior to each test
	 *
	 * @return  void
	 */
	public function setUp(): void
	{
		parent::setUp();

		$driver = $this->getMockDriver();

		Relational::setDefaultConnection($driver);
		Karma::setConnection($driver);

		Scale::forget();
		Rule::forget();
		Gate::forget();
	}

	/**
	 * Set the visibility of the global scale
	 *
	 * @param   string  $self
	 * @param   string  $public
	 * @return  object
	 */
	protected function visibility($self, $public)
	{
		$scale = Scale::oneByAlias('global');
		$scale->set('visibility_self', $self);
		$scale->set('visibility_public', $public);
		$scale->save();

		Scale::forget();

		return $scale;
	}

	/**
	 * Tests what the subject themselves sees
	 *
	 * @return  void
	 */
	#[DataProvider('selfProvider')]
	public function testSubjectSeesWhatTheScaleAllows($setting, $expected)
	{
		$this->visibility($setting, Scale::PUBLIC_HIDDEN);

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertEquals(
			$expected,
			Karma::describe(self::SUBJECT, 'global', self::SUBJECT)
		);
	}

	/**
	 * Self-visibility settings and what karma of 1 renders as
	 *
	 * @return  array
	 */
	public static function selfProvider()
	{
		return array(
			'the number'   => array(Scale::SELF_EXACT, '1'),
			'an adjective' => array(Scale::SELF_ADJECTIVE, 'Established'),
		);
	}

	/**
	 * Tests what a stranger sees
	 *
	 * @return  void
	 */
	#[DataProvider('publicProvider')]
	public function testStrangerSeesWhatTheScaleAllows($setting, $expected)
	{
		$this->visibility(Scale::SELF_EXACT, $setting);

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertEquals(
			$expected,
			Karma::describe(self::SUBJECT, 'global', self::STRANGER)
		);
	}

	/**
	 * Public visibility settings and what a stranger sees at karma of 1
	 *
	 * Opt-in reads as nothing here because no preference has been set, and
	 * absence is a no.
	 *
	 * @return  array
	 */
	public static function publicProvider()
	{
		return array(
			'hidden'       => array(Scale::PUBLIC_HIDDEN, null),
			'opt in, unset' => array(Scale::PUBLIC_OPT_IN, null),
			'an adjective' => array(Scale::PUBLIC_ADJECTIVE, 'Established'),
			'the number'   => array(Scale::PUBLIC_EXACT, '1'),
		);
	}

	/**
	 * Tests that an administrator always sees the exact number
	 *
	 * This is the assertion to defend hardest. There is no setting that hides
	 * a value from somebody investigating an abuse report.
	 *
	 * @return  void
	 */
	#[DataProvider('everyPublicSettingProvider')]
	public function testAdministratorAlwaysSeesTheNumber($setting)
	{
		$this->visibility(Scale::SELF_ADJECTIVE, $setting);

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertEquals(
			'1',
			Karma::describe(self::SUBJECT, 'global', self::STRANGER, true)
		);
	}

	/**
	 * Every public visibility setting
	 *
	 * @return  array
	 */
	public static function everyPublicSettingProvider()
	{
		return array(
			'hidden'     => array(Scale::PUBLIC_HIDDEN),
			'opt in'     => array(Scale::PUBLIC_OPT_IN),
			'adjective'  => array(Scale::PUBLIC_ADJECTIVE),
			'exact'      => array(Scale::PUBLIC_EXACT),
		);
	}

	/**
	 * Tests that a stranger never sees more than the scale permits
	 *
	 * The mirror of the administrator test: whatever the subject's own
	 * setting says, it must not widen what anybody else sees.
	 *
	 * @return  void
	 */
	public function testSelfSettingDoesNotWidenPublicDisclosure()
	{
		$this->visibility(Scale::SELF_EXACT, Scale::PUBLIC_HIDDEN);

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertNull(Karma::describe(self::SUBJECT, 'global', self::STRANGER));
	}

	/**
	 * Tests that describe defaults to the subject as viewer
	 *
	 * @return  void
	 */
	public function testDescribeDefaultsToTheSubject()
	{
		$this->visibility(Scale::SELF_EXACT, Scale::PUBLIC_HIDDEN);

		Karma::award(self::SUBJECT, 'comment.upmod');

		$this->assertEquals('1', Karma::describe(self::SUBJECT, 'global'));
	}

	/**
	 * Tests that an unknown scale describes as nothing
	 *
	 * @return  void
	 */
	public function testUnknownScaleDescribesAsNothing()
	{
		$this->assertNull(Karma::describe(self::SUBJECT, 'no.such.scale', self::SUBJECT));
	}

	/**
	 * Tests that standing ignores visibility entirely
	 *
	 * What a member may do is always shown to them. This flips every
	 * visibility column and asserts the answer does not move.
	 *
	 * @return  void
	 */
	public function testStandingIsUnaffectedByVisibility()
	{
		Karma::award(self::SUBJECT, 'comment.upmod');

		$expected = Karma::standing(self::SUBJECT);

		$this->assertNotEmpty($expected, 'The fixture should define at least one gate');

		$settings = array(
			Scale::PUBLIC_HIDDEN,
			Scale::PUBLIC_OPT_IN,
			Scale::PUBLIC_ADJECTIVE,
			Scale::PUBLIC_EXACT
		);

		foreach ($settings as $public)
		{
			foreach (array(Scale::SELF_EXACT, Scale::SELF_ADJECTIVE) as $self)
			{
				$this->visibility($self, $public);

				$this->assertEquals(
					$expected,
					Karma::standing(self::SUBJECT),
					'Standing moved when visibility changed to ' . $self . '/' . $public
				);
			}
		}
	}
}
