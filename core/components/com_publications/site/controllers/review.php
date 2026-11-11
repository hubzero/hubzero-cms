<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Publications\Models;
use Request;
use Route;
use User;
use Date;
use Lang;
use App;
use Session;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'publication.php';
include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'review.php';

class Review extends SiteController
{
    public function execute()
    {		
		$this->registerTask('__default', 'validate');
		$this->registerTask('generate', 'create');
		$this->registerTask('validate', 'authenticate');
		$this->registerTask('download', 'distribute');
		
		parent::execute();
    }
	
	/**
	 * Generate the review URL and save the review record.
	 *
	 * @return  void
	 */
    public function createTask()
	{
		try
		{
			if (User::isGuest())
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_LOGIN'), array(), 403);
				return;
			}

			if (Request::getMethod() !== 'POST')
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_POST_REQUIRED'), array(), 405);
				return;
			}

			if (Session::isNew() || !\Hubzero\Session\Manager::checkToken('post', true))
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_FORM_TOKEN'), array(), 403);
				return;
			}

			$publicationId = Request::getInt('publication_id', 0);
			$versionNumber = Request::getInt('version_number', 0);
			$publicationVersionId = Request::getInt('publication_version_id', 0);
			$selectedDate = trim(Request::getString('expired', ''));
			$expectedTokenHash = Request::getString('expected_token_hash', '');

			if ($publicationId <= 0 || $versionNumber <= 0 || $publicationVersionId <= 0)
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_PUBLICATION'), array(), 400);
				return;
			}

			$expiryDays = Component::params('com_publications')->get('review_url_expiry_days', 60);
			$expiryDays = filter_var($expiryDays, FILTER_VALIDATE_INT);

			if ($expiryDays === false || $expiryDays < 1)
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_DEFAULT_REVIEW_DAYS'), array(), 500);
				return;
			}

			$createdAt = new \DateTime('now', new \DateTimeZone('America/New_York'));
			$defaultExpired = clone $createdAt;
			$defaultExpired->modify('+' . $expiryDays . ' days');
			$selectedDateTime = null;

			if ($selectedDate !== '')
			{
				$selectedDateTime = \DateTime::createFromFormat('!Y-m-d\TH:i', $selectedDate, $createdAt->getTimezone());
				$parseErrors = \DateTime::getLastErrors();

				if ($selectedDateTime === false || ($parseErrors !== false && ($parseErrors['warning_count'] || $parseErrors['error_count'])) || $selectedDateTime->format('Y-m-d\TH:i') !== $selectedDate)
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_MALFORMED_SELECTED_DATE'), array(), 400 );
					return;
				}

				if ($selectedDateTime <= $createdAt)
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_SELECTED_DATE'), array(), 400);
					return;
				}
			}

			$expiredAt = ($selectedDateTime !== null && $selectedDateTime < $defaultExpired) ? $selectedDateTime : $defaultExpired;

			$pub = new Models\Publication($publicationId, $versionNumber, $publicationVersionId);

			// The constructor loads by version id when one is supplied, so verify
			// all three submitted identifiers against the loaded version.
			if (!$pub->exists()
				|| $pub->isDeleted()
				|| (int) $pub->version->publication_id !== $publicationId
				|| (int) $pub->version->version_number !== $versionNumber
				|| (int) $pub->version->id !== $publicationVersionId)
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_PUBLICATION_NOT_FOUND'), array(), 404);
				return;
			}

			if (!$pub->isPending())
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_PUBLICATION_NOT_SUBMITTED'), array(), 403);
				return;
			}

			$submitter = $pub->submitter();

			if (!is_object($submitter)
				|| !isset($submitter->role, $submitter->status, $submitter->user_id)
				|| $submitter->role !== 'submitter'
				|| (int) $submitter->status !== 1
				|| (int) $submitter->user_id !== (int) User::get('id'))
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_URL_GENERATION_NOT_ALLOWED'), array(), 403);
				return;
			}

			$review = new Models\Review();
			$record = $review->getAnonReviewRecordByPubIdVerNum($publicationId, $versionNumber);

			$sessionKey = 'anon_review_url:' . User::get('id') . ':' . $publicationId . ':' . $versionNumber;
			$sessionToken = Session::get($sessionKey, '');

			if ($record)
			{
				if ((int) $record->publication_version_id !== $publicationVersionId
					|| !isset($record->token_hash)
					|| !preg_match('/\A[0-9a-f]{64}\z/', (string) $record->token_hash)
					|| !hash_equals((string) $record->token_hash, $expectedTokenHash))
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_RECORD_CHANGED'), array(), 409);
					return;
				}

				$hasCurrentToken = is_string($sessionToken)
					&& $sessionToken !== ''
					&& hash_equals(
						(string) $record->token_hash,
						hash('sha256', $sessionToken)
					);

				if ($hasCurrentToken && $review->isAnonReviewRecordUnexpired($record))
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_RECORD_EXIST'), array(), 409);
					return;
				}
			}
			elseif ($expectedTokenHash !== '')
			{
				$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_RECORD_CHANGED'), array(), 409);
				return;
			}

			$rawToken = bin2hex(random_bytes(32));
			$tokenHash = hash('sha256', $rawToken);
			$created = $createdAt->format('Y-m-d H:i:s');
			$expired = $expiredAt->format('Y-m-d H:i:s');

			if ($record)
			{
				$result = $review->regenerateAnonReviewRecord($publicationId, $versionNumber, $publicationVersionId, $expectedTokenHash, $created, $expired, $tokenHash);

				if (!$result)
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_RECORD_CHANGED'), array(), 409);
					return;
				}
			}
			else
			{
				$result = $review->saveAnonReviewRecord($publicationId, $versionNumber, $publicationVersionId, $created, User::get('id'), $expired,
					$tokenHash
				);

				if (!$result)
				{
					$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_SAVE_RECORD_FAILED'), array(), 500);
					return;
				}
			}

			Session::set($sessionKey, $rawToken);

			$route = Route::url(
				'index.php?option=com_publications&controller=review&task=validate'
				. '&token=' . urlencode($rawToken),
				false
			);
			
			if (strpos($route, '/') === 0)
			{
				$origin = substr(Request::root(), 0, -strlen(Request::root(true)));
				$url = $origin . $route;
			}
			elseif (preg_match('#^https?://#i', $route))
			{
				$url = $route;
			}
			else
			{
				$url = rtrim(Request::root(), '/') . '/' . $route;
			}

			$message = $record ? 'COM_PUBLICATIONS_REVIEW_NOTICE_URL_REGENERATED_COPY_NOW' : 'COM_PUBLICATIONS_REVIEW_NOTICE_URL_CREATED_COPY_NOW';

			$this->sendDefaultResponse(true, Lang::txt($message), array('url' => $url, 'expired' => $expired));
		}
		catch (\Throwable $e)
		{
			// Do not log SQL, URLs, or token values.
			error_log('Anonymous review url creation failed: ' . get_class($e) . ' (code ' . $e->getCode() . ')');

			$this->sendDefaultResponse(false, Lang::txt('COM_PUBLICATIONS_REVIEW_CREATE_REVIEW_RECORD_FAILED'), array(), 500);
		}
	}
	
	/**
	 * Validate the review url and download url
	 * For review url, check token, expiry date and publication exist before showing content. 
	 * For download url, check token, publication and token match or not, expiry date, and publication exist
	 *
	 * @return  void
	 */
    public function authenticateTask()
    {
        $rawToken = Request::getString('token', '');
		
		$this->view = new \Hubzero\Component\View(array(
			'name'   => 'review',
			'layout' => 'default'
		));
		
		$this->view->set('rawToken', $rawToken);
		$this->view->set('record', false);
		$this->view->set('expired', false);
		$this->view->set('publication', false);
		$this->view->set('tokenMatch', true);
		
		if (!$rawToken)
		{
			$this->view->set('message', Lang::txt('COM_PUBLICATIONS_REVIEW_MISSING_TOKEN'));
			$this->view->display();
			return;
		}
		
		$tokenHash = hash('sha256', $rawToken);

        $review = new Models\Review();
        $record = $review->getAnonReviewRecordByTokenHash($tokenHash);

        if (!$record)
        {
			$this->view->set('message', Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_TOKEN'));
            $this->view->display();
			return;
        }
		
		$this->view->set('record', $record);

        $publication = new Models\Publication($record->publication_id, $record->version_number, $record->publication_version_id);

        if (!$publication || !$publication->exists() || $publication->isDeleted())
        {
			$this->view->set('message', Lang::txt('COM_PUBLICATIONS_REVIEW_PUBLICATION_NOT_FOUND'));
            $this->view->display();
			return;
        }
		
		$this->view->set('publication', $publication);
		
		if (!$review->isAnonReviewRecordUnexpired($record))
		{
			$this->view->set('expired', true);
			$this->view->set('message', Lang::txt('COM_PUBLICATIONS_REVIEW_URL_EXPIRED'));
			$this->view->display();
			return;
		}

        // Render anonymous review page
		$this->view->display();
    }
	
	/**
	 * Process the download request
	 *
	 * @return  void
	 */
    public function distributeTask()
    {
        $rawToken = Request::getString('token', '');
		$publicationId = Request::getInt('pubid', 0);
		$versionNum = Request::getString('vernum', 0);
		
		if (!$rawToken)
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=validate&token=' . urlencode($rawToken), false));
			return;
		}
		
		if ($publicationId <= 0 || $versionNum <= 0)
		{
			// invalid pub id or version number
			throw new \Exception(Lang::txt('COM_PUBLICATIONS_REVIEW_INVALID_DOWNLOAD_REQUEST'), 400);
			
		}
		
		$tokenHash = hash('sha256', $rawToken);

        $review = new Models\Review();
        $record = $review->getAnonReviewRecordByTokenHash($tokenHash);

        if (!$record)
        {
            // invalid rawToken
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=validate&token=' . urlencode($rawToken), false)
			);
			return;
        }
		
		if (($record->publication_id != $publicationId) || ($record->version_number != $versionNum))
		{
			throw new \Exception(Lang::txt('COM_PUBLICATIONS_REVIEW_TOKEN_PUB_MISMATCH'), 400);
		}
		
		if (!$review->isAnonReviewRecordUnexpired($record))
		{
			App::redirect(Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller	. '&task=validate&token=' . urlencode($rawToken), false));
			return;
		}
		
		// redirect to the actual bundle-serving route
		$url = Route::url(
			'index.php?option=' . $this->_option
			. '&task=serve'
			. '&id=' . $publicationId
			. '&v=' . $versionNum
			. '&render=archive'
			. '&token=' . urlencode($rawToken),
			false
		);

        App::redirect($url);
    }
	
	/**
     * * Process the response
	 *
	 * @param   boolean  $success
	 * @param   string  $message
	 * @param   array  $extra
	 * @param   int  $status
	 *
	 * @return	string or failure
     */
    private function sendDefaultResponse($success, $message, $extra = array(), $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-store');

        $response = array(
            'success' => (bool) $success,
            'message' => $message
        );

        foreach ($extra as $key => $value)
        {
            $response[$key] = $value;
        }

        echo json_encode($response);
		
		// Stop it from rendering the normal page around the JSON.
		App::close();
    }
}