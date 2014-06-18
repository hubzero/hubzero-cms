<?php

namespace Plugins\Content\Spamassassin\Service\Client\Remote;

/**
 * Represents the result from a local call on the SpamAssassin server
 */
class Result
{
	/**
	 * Response message.
	 *
	 * @var string
	 */
	public $report;

	/**
	 * Content being changed.
	 *
	 * @var string
	 */
	public $message;

	/**
	 * SpamAssassin score
	 *
	 * @var float
	 */
	public $score;

	/**
	 * How many points the message must score to be considered spam
	 *
	 * @var float
	 */
	public $thresold;

	/**
	 * Is it spam or not?
	 *
	 * @var boolean
	 */
	public $isSpam;

	/**
	 * Raw output from SpamAssassin
	 *
	 * @var string
	 */
	public $output;
}
