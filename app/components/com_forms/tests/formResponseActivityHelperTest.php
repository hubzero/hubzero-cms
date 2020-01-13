<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Tests;

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formResponseActivityHelper.php";
require_once "$componentPath/tests/helpers/canMock.php";

use Hubzero\Test\Basic;
use Components\Forms\Helpers\FormResponseActivityHelper as ActivityHelper;
use Components\Forms\Tests\Traits\canMock;

class FormResponseActivityHelperTest extends Basic
{
	use canMock;

	public function testLogStartInvokesSet()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);
		$responseId = 19;

		$expectedArgs = [
			'action' => 'activity',
			'description' => 'started',
			'scope_id' => $responseId
		];

		$activity->expects($this->once())
			->method('set')
			->with($expectedArgs);

		$activityHelper->logStart($responseId);
	}

	public function testLogStartInvokesSave()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);

		$activity->expects($this->once())
			->method('save');

		$activityHelper->logStart(0);
	}

	public function testLogSubmitInvokesSet()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);
		$responseId = 19;

		$expectedArgs = [
			'action' => 'activity',
			'description' => 'submitted',
			'scope_id' => $responseId
		];

		$activity->expects($this->once())
			->method('set')
			->with($expectedArgs);

		$activityHelper->logSubmit($responseId);
	}

	public function testLogSubmitInvokesSave()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);

		$activity->expects($this->once())
			->method('save');

		$activityHelper->logSubmit(0);
	}

	public function testLogReviewInvokesSetWithAcceptedWhenAccepted()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);
		$responseId = 19;

		$expectedArgs = [
			'action' => 'activity',
			'description' => 'accepted',
			'scope_id' => $responseId
		];

		$activity->expects($this->once())
			->method('set')
			->with($expectedArgs);

		$activityHelper->logReview($responseId, true);
	}

	public function testLogReviewInvokesSetWithReversedWhenNotAccepted()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);
		$responseId = 19;

		$expectedArgs = [
			'action' => 'activity',
			'description' => 'reversed',
			'scope_id' => $responseId
		];

		$activity->expects($this->once())
			->method('set')
			->with($expectedArgs);

		$activityHelper->logReview($responseId, false);
	}

	public function testLogReviewInvokesSave()
	{
		$activity = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['set', 'save']
		]);
		$factory = $this->mock([
			'class' => 'ResponseActivity', 'methods' => ['blank' => $activity]
		]);
		$activityHelper = new ActivityHelper(['factory' => $factory]);

		$activity->expects($this->once())
			->method('save');

		$activityHelper->logReview(0, true);
	}

}
