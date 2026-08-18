<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Saml\Models\IdP;
use Components\Saml\Models\ServiceProvider;
use Components\Saml\Models\SamlSession;

/**
 * Overview dashboard: IdP status, endpoints, certificate health
 */
class Saml extends AdminController
{
	/**
	 * Display the IdP status overview
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$idp = new IdP($this->config);

		$certFile = $idp->getCertificateFile();
		$keyFile  = $idp->getKeyFile();

		// Parse the IdP certificate directly — no LightSAML needed here
		$cert = null;
		if (is_readable($certFile) && function_exists('openssl_x509_parse'))
		{
			$parsed = @openssl_x509_parse((string) file_get_contents($certFile));

			if ($parsed)
			{
				$cert = array(
					'subject'  => isset($parsed['subject']['CN']) ? $parsed['subject']['CN'] : '',
					'valid_to' => isset($parsed['validTo_time_t']) ? (int) $parsed['validTo_time_t'] : 0
				);
			}
		}

		$this->view
			->set('idp', $idp)
			->set('params', $this->config)
			->set('certFile', $certFile)
			->set('certReadable', is_readable($certFile))
			->set('cert', $cert)
			->set('keyFile', $keyFile)
			->set('keyReadable', is_readable($keyFile))
			->set('spTotal', ServiceProvider::all()->total())
			->set('spEnabled', ServiceProvider::all()->whereEquals('state', ServiceProvider::STATE_ENABLED)->total())
			->set('sessionsActive', SamlSession::all()->whereEquals('state', SamlSession::STATE_ACTIVE)->total())
			->display();
	}
}
