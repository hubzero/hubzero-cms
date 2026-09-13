<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation\Tests;

use Hubzero\Moderation\Moderatable;

/**
 * A moderatable thing, standing in for whatever a component would register
 *
 * Its existence is the point of the interface: the library moderates this
 * without knowing what it is, and a component that can answer these six
 * questions gets the economy without the library learning about comments.
 */
class TestItem implements Moderatable
{
	/**
	 * The item's id
	 *
	 * @var  integer
	 */
	public $id = 1;

	/**
	 * Its current score
	 *
	 * @var  float
	 */
	public $score = 0.0;

	/**
	 * Who wrote it
	 *
	 * @var  integer
	 */
	public $author = 0;

	/**
	 * What it sits in
	 *
	 * @var  integer
	 */
	public $container = 0;

	/**
	 * The bounds its score is held to
	 *
	 * @var  array
	 */
	public $bounds = array(-1.0, 5.0);

	/**
	 * Whether applyScore() should refuse, to exercise the rollback
	 *
	 * @var  bool
	 */
	public $refuses = false;

	/**
	 * Constructor
	 *
	 * @param   integer  $id
	 * @param   integer  $author
	 * @param   float    $score
	 * @param   integer  $container
	 * @return  void
	 */
	public function __construct($id = 1, $author = 99, $score = 0.0, $container = 500)
	{
		$this->id        = $id;
		$this->author    = $author;
		$this->score     = $score;
		$this->container = $container;
	}

	public function itemType()    { return 'test.item'; }
	public function itemId()      { return $this->id; }
	public function currentScore(){ return $this->score; }
	public function scoreBounds() { return $this->bounds; }
	public function authorId()    { return $this->author; }
	public function containerId() { return $this->container; }

	/**
	 * Move the score
	 *
	 * @param   float    $delta
	 * @param   integer  $reasonId
	 * @return  bool
	 */
	public function applyScore($delta, $reasonId)
	{
		if ($this->refuses)
		{
			return false;
		}

		$this->score += $delta;

		return true;
	}
}
