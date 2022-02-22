<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

 // No direct access
defined('_HZEXEC_') or die();

/**
 * Content Security Policy Header Plugin
 */
class plgSystemCsp extends \Hubzero\Plugin\Plugin
{
	/**
	 * Modes that can be enabled
	 *
	 * @var  integer
	 */
	const REPORT = 0;
	const ENFORCE = 1;
	const ENFORCE_REPORT = 2;

	/**
	 * List of policies we can enforce
	 *
	 * @var  array
	 */
	protected $policies = array(
		'base-uri',
		'object-src',
		'child-src',
		'connect-src',
		'default-src',
		'font-src',
		'form-action',
		'frame-src',
		'img-src',
		'script-src',
		'style-src'
	);

	/**
	 * Hook for after app initialization
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		if (!App::isSite() && !App::isAdmin())
		{
			return;
		}

		$mode = $this->params->get('mode');

		// Get reporting URI
		$report = '';
		if ($reportUri = $this->params->get('report-uri'))
		{
			$reportUri = str_replace('{host}', Request::host(), $reportUri);
			$reportUri = trim($reportUri);

			$report .= '; report-uri ' . $reportUri;

			/*if ($reportTo = $this->params->get('report-to'))
			{
				$reportTo = str_replace('{host}', Request::host(), $reportTo);
				$reportTo = trim($reportTo);

				$report .= '; report-to ' . $reportTo;
			}*/
		}

		// Enforce Only or Enforce & Report?
		if ($mode == self::ENFORCE
		 || $mode == self::ENFORCE_REPORT)
		{
			$ps = array();

			foreach ($this->policies as $key)
			{
				$val = (string)$this->params->get($key);
				$val = trim($val);

				if ($val)
				{
					$val = str_replace('{host}', Request::host(), $val);
					$ps[] = $key . ' ' . $val;
				}
			}

			$policy = implode('; ', $ps);
			$policy = trim($policy);

			// Add to the headers
			if ($policy)
			{
				App::get('response')->headers->set('Content-Security-Policy', $policy . $report, true);
			}
		}

		// Report Only or Enforce & Report?
		if ($mode == self::REPORT
		 || $mode == self::ENFORCE_REPORT)
		{
			$ps = array();

			foreach ($this->policies as $key)
			{
				$val = '';

				if ($this->params->get('report-' . $key))
				{
					$val = (string)$this->params->get('report-' . $key);
				}

				if (!$val)
				{
					// Deferr to the regular policy
					$val = (string)$this->params->get($key);
				}

				$val = trim($val);

				if ($val)
				{
					$val = str_replace('{host}', Request::host(), $val);
					$ps[] = $key . ' ' . $val;
				}
			}

			$policy = implode('; ', $ps);
			$policy = trim($policy);

			if ($policy)
			{
				App::get('response')->headers->set('Content-Security-Policy-Report-Only', $policy . $report, true);
			}
		}
	}
}
