<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Models;

use Hubzero\Database\Relational;

/**
 * Model for a trusted SAML Service Provider
 */
class ServiceProvider extends Relational
{
	/**
	 * Enabled state
	 */
	const STATE_DISABLED = 0;
	const STATE_ENABLED  = 1;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'saml';

	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 */
	protected $table = '#__saml_service_providers';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'name';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'entity_id' => 'notempty',
		'name'      => 'notempty',
		'acs_url'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Fields to be parsed automatically on each update
	 *
	 * @var  array
	 */
	public $renew = array(
		'modified',
		'modified_by'
	);

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array  $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return \Date::toSql();
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array  $data  the data being saved
	 * @return  integer
	 */
	public function automaticModifiedBy($data)
	{
		return (int) \User::get('id');
	}

	/**
	 * Retrieves one row loaded by the SP entity ID (SAML issuer)
	 *
	 * @param   string  $entityId
	 * @return  mixed
	 */
	public static function oneByEntityId($entityId)
	{
		return self::blank()
			->whereEquals('entity_id', (string) $entityId)
			->row();
	}

	/**
	 * Is this SP enabled for SSO?
	 *
	 * @return  boolean
	 */
	public function isEnabled()
	{
		return (int) $this->get('state') == self::STATE_ENABLED;
	}

	/**
	 * Normalize a certificate into its base64 body (no PEM armor, no whitespace)
	 *
	 * @param   string  $cert
	 * @return  string
	 */
	public static function normalizeCertificate($cert)
	{
		$cert = preg_replace('/-----(BEGIN|END)[^-]+-----/', '', (string) $cert);

		return preg_replace('/\s+/', '', $cert);
	}

	/**
	 * The stored signing certificate as full PEM
	 *
	 * @return  string|null
	 */
	public function certificatePem()
	{
		$data = self::normalizeCertificate($this->get('signing_cert', ''));

		if (!$data)
		{
			return null;
		}

		return "-----BEGIN CERTIFICATE-----\n" . chunk_split($data, 64, "\n") . "-----END CERTIFICATE-----\n";
	}

	/**
	 * The stored signing certificate as a LightSaml credential
	 *
	 * @return  \LightSaml\Credential\X509Certificate|null
	 */
	public function certificate()
	{
		$data = self::normalizeCertificate($this->get('signing_cert', ''));

		if (!$data)
		{
			return null;
		}

		$certificate = new \LightSaml\Credential\X509Certificate();
		$certificate->setData($data);

		return $certificate;
	}

	/**
	 * Can stored certificates be inspected on this install?
	 *
	 * Without ext/openssl they can still be stored and sent — only the
	 * subject/expiry readout and the save-time parse check are unavailable.
	 *
	 * @return  boolean
	 */
	public static function canInspectCertificates()
	{
		return function_exists('openssl_x509_parse');
	}

	/**
	 * Parsed certificate details for display (subject, expiry, fingerprint)
	 *
	 * Null means "no details" — which is not the same as "invalid": callers
	 * that validate must check canInspectCertificates() first.
	 *
	 * @return  array|null
	 */
	public function certificateInfo()
	{
		$pem = $this->certificatePem();

		if (!$pem || !self::canInspectCertificates())
		{
			return null;
		}

		$parsed = @openssl_x509_parse($pem);

		if (!$parsed)
		{
			return null;
		}

		return array(
			'subject'     => isset($parsed['subject']['CN']) ? $parsed['subject']['CN'] : $this->get('entity_id'),
			'issuer'      => isset($parsed['issuer']['CN']) ? $parsed['issuer']['CN'] : '',
			'valid_to'    => isset($parsed['validTo_time_t']) ? (int) $parsed['validTo_time_t'] : 0,
			'fingerprint' => function_exists('openssl_x509_fingerprint') ? @openssl_x509_fingerprint($pem, 'sha256') : ''
		);
	}

	/**
	 * The per-SP attribute release map, or null for the default set
	 *
	 * Entries are {"name": ..., "friendly": ..., "source": ...} objects (§8 of DESIGN.md).
	 *
	 * @return  array|null
	 */
	public function attributeMap()
	{
		$stored = trim((string) $this->get('attribute_map', ''));

		// Nothing configured — the caller releases its default set
		if ($stored === '')
		{
			return null;
		}

		$map = json_decode($stored, true);

		// Configured but unreadable, or deliberately empty: release nothing.
		// Falling back to the default set here would send an SP attributes
		// its registration says it should not get.
		if (!is_array($map))
		{
			return array();
		}

		return $map;
	}

	/**
	 * Group cn's allowed to use this SP, or null for no restriction
	 *
	 * @return  array|null
	 */
	public function allowedGroups()
	{
		$stored = trim((string) $this->get('allowed_groups', ''));

		if ($stored === '')
		{
			return null;
		}

		$groups = json_decode($stored, true);

		// A restriction that cannot be read must not be treated as "no
		// restriction" — return an empty list, which matches nobody
		if (!is_array($groups))
		{
			return array();
		}

		$groups = array_values(array_filter(array_map('trim', $groups)));

		return empty($groups) ? array() : $groups;
	}

	/**
	 * May this user single sign-on to this SP?
	 *
	 * Each allowed group is resolved by cn and asked directly, rather than
	 * enumerating the user's groups: User::groups() only reports hub-type
	 * groups from the members table, which would silently deny everyone when
	 * an SP is restricted to a system or project group, or to managers.
	 *
	 * @param   object  $user  \Hubzero\User\User
	 * @return  boolean
	 */
	public function userCanAccess($user)
	{
		$allowed = $this->allowedGroups();

		if ($allowed === null)
		{
			return true;
		}

		if (!$user || !$user->get('id'))
		{
			return false;
		}

		foreach ($allowed as $cn)
		{
			if ($this->userIsInGroup($cn, $user->get('id')))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Is the user a member or manager of the group with this cn?
	 *
	 * Separated from userCanAccess() so the access policy can be tested
	 * without a group table behind it.
	 *
	 * @param   string   $cn
	 * @param   integer  $userId
	 * @return  boolean
	 */
	protected function userIsInGroup($cn, $userId)
	{
		$group = \Hubzero\User\Group::getInstance($cn);

		if (!$group)
		{
			return false;
		}

		return $group->isMember($userId) || $group->isManager($userId);
	}

	/**
	 * Sessions issued for this SP
	 *
	 * @return  object
	 */
	public function sessions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\SamlSession', 'sp_id');
	}

	/**
	 * Delete the record and all associated sessions
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		foreach ($this->sessions()->rows() as $session)
		{
			if (!$session->destroy())
			{
				$this->addError($session->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
