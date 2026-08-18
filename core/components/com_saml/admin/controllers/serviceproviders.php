<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Admin\Controllers;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'Metadata.php';

use Hubzero\Component\AdminController;
use Components\Saml\Models\ServiceProvider;
use Components\Saml\Models\IdP;
use Components\Saml\Helpers\Metadata;
use Request;
use Notify;
use User;
use Lang;
use App;

/**
 * Controller class for trusted Service Providers
 */
class Serviceproviders extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of trusted Service Providers
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$records = ServiceProvider::all();

		if ($filters['search'])
		{
			$records->whereLike('name', strtolower((string) $filters['search']), 1)
				->orWhereLike('entity_id', strtolower((string) $filters['search']), 1)
				->resetDepth();
		}

		if ($filters['state'] >= 0)
		{
			$records->whereEquals('state', (int) $filters['state']);
		}

		$rows = $records
			->ordered('filter_order', 'filter_order_Dir')
			->paginated('limitstart', 'limit')
			->rows();

		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   mixed  $row
	 * @return  void
	 */
	public function editTask($row = null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			$row = ServiceProvider::oneOrNew($id);
		}

		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$fields = Request::getArray('fields', array(), 'post');

		// Editing an existing registration needs core.edit — core.create
		// alone must not be able to repoint an SP's ACS at somewhere else
		$permission = empty($fields['id']) ? 'core.create' : 'core.edit';

		if (!User::authorise($permission, $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Only bind columns the form owns — notably not `state`, which is
		// gated on core.edit.state and applied separately below
		$fields = array_intersect_key($fields, array_flip(array(
			'id',
			'name',
			'entity_id',
			'description',
			'acs_url',
			'slo_url',
			'signing_cert',
			'want_requests_signed',
			'sign_assertion',
			'nameid_format',
			'nameid_source',
			'attribute_map',
			'metadata_url'
		)));

		$row = ServiceProvider::oneOrNew(isset($fields['id']) ? $fields['id'] : 0);

		// A form submitted against a since-deleted row would otherwise take
		// the update branch and report success while writing nothing
		if (!empty($fields['id']) && $row->isNew())
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_GONE'));
			return $this->cancelTask();
		}

		$row->set($fields);

		// Bind every posted value before validating anything, so redisplaying
		// the form on an error shows what the admin actually typed
		$groups = trim((string) Request::getString('allowed_groups', '', 'post'));
		$groups = array_values(array_filter(array_map('trim', explode(',', $groups))));

		$row->set('allowed_groups', $groups ? json_encode($groups) : null);

		$state = Request::getArray('fields', array(), 'post');
		$state = isset($state['state']) ? (int) $state['state'] : null;

		if ($state !== null && $state != (int) $row->get('state', ServiceProvider::STATE_ENABLED))
		{
			if (!User::authorise('core.edit.state', $this->_option))
			{
				App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
			}

			$row->set('state', $state);
		}
		elseif ($row->isNew())
		{
			$row->set('state', ServiceProvider::STATE_ENABLED);
		}

		// All three must be https: assertions and logout messages are bearer
		// credentials, and metadata carries the ACS URL and signing cert we
		// go on to trust — nothing here verifies a signature over it, so the
		// transport is what protects it.
		foreach (array('acs_url', 'slo_url', 'metadata_url') as $field)
		{
			$url = trim((string) $row->get($field, ''));
			$row->set($field, $url);

			if ($url && !preg_match('/^https:\/\//i', $url))
			{
				Notify::error(Lang::txt('COM_SAML_ERROR_INSECURE_URL', $field));
				return $this->editTask($row);
			}
		}

		// Normalize and sanity-check the signing certificate
		$cert = ServiceProvider::normalizeCertificate($row->get('signing_cert', ''));
		$row->set('signing_cert', $cert);

		// Only a build that can parse certificates may judge one invalid
		if ($cert && ServiceProvider::canInspectCertificates() && !$row->certificateInfo())
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_INVALID_CERT'));
			return $this->editTask($row);
		}

		if (!$cert && $row->get('want_requests_signed'))
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_CERT_REQUIRED'));
			return $this->editTask($row);
		}

		// Attribute map must be valid JSON: a list of {name, source} objects
		$map = trim((string) $row->get('attribute_map', ''));
		$row->set('attribute_map', $map);

		if ($map)
		{
			$decoded = json_decode($map, true);

			if (!is_array($decoded))
			{
				Notify::error(Lang::txt('COM_SAML_ERROR_INVALID_ATTRIBUTE_MAP'));
				return $this->editTask($row);
			}

			foreach ($decoded as $entry)
			{
				if (!is_array($entry) || empty($entry['name']) || empty($entry['source']))
				{
					Notify::error(Lang::txt('COM_SAML_ERROR_INVALID_ATTRIBUTE_MAP'));
					return $this->editTask($row);
				}

				// A source the resolver does not know would silently release
				// nothing, so reject it here rather than at SSO time
				if (!IdP::isKnownAttributeSource($entry['source']))
				{
					Notify::error(Lang::txt('COM_SAML_ERROR_UNKNOWN_ATTRIBUTE_SOURCE', $entry['source']));
					return $this->editTask($row);
				}
			}
		}

		foreach ($groups as $cn)
		{
			if (!\Hubzero\User\Group::getInstance($cn))
			{
				Notify::error(Lang::txt('COM_SAML_ERROR_UNKNOWN_GROUP', $cn));
				return $this->editTask($row);
			}
		}

		// The entity ID is uniquely indexed; catch the collision here rather
		// than letting the query layer throw a fatal
		$existing = ServiceProvider::oneByEntityId($row->get('entity_id'));

		if ($existing && $existing->get('id') && $existing->get('id') != $row->get('id'))
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_DUPLICATE_ENTITY_ID'));
			return $this->editTask($row);
		}

		try
		{
			$saved = $row->save();
		}
		catch (\Exception $e)
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_SAVE_FAILED'));
			return $this->editTask($row);
		}

		if (!$saved)
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		Notify::success(Lang::txt('COM_SAML_SP_SAVED'));

		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Remove one or more entries
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$i = 0;

		foreach ($ids as $id)
		{
			// A stale id must not abort the rest of the batch
			$row = ServiceProvider::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				continue;
			}

			if (!$row->destroy())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		if ($i)
		{
			Notify::success(Lang::txt('COM_SAML_ITEMS_REMOVED', $i));
		}

		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries — the list view links this task via GET
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->getTask() == 'publish' ? ServiceProvider::STATE_ENABLED : ServiceProvider::STATE_DISABLED;

		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_SAML_SELECT_ITEM'));
			return $this->cancelTask();
		}

		$i = 0;

		foreach ($ids as $id)
		{
			$row = ServiceProvider::oneOrNew(intval($id));

			if (!$row->get('id'))
			{
				continue;
			}

			$row->set('state', $state);

			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$i++;
		}

		Notify::success(
			$state == ServiceProvider::STATE_ENABLED
				? Lang::txt('COM_SAML_ITEMS_ENABLED', $i)
				: Lang::txt('COM_SAML_ITEMS_DISABLED', $i)
		);

		$this->cancelTask();
	}

	/**
	 * Show the import-from-metadata form
	 *
	 * @return  void
	 */
	public function importTask()
	{
		if (!User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		$this->view
			->setLayout('import')
			->display();
	}

	/**
	 * Parse pasted or fetched SP metadata and prefill the edit form
	 *
	 * @return  void
	 */
	public function processImportTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$xml = trim((string) Request::getVar('metadata_xml', '', 'post', 'none'));
		$url = trim((string) Request::getString('metadata_url', '', 'post'));

		if (!$xml && $url)
		{
			$xml = Metadata::fetch($url);

			if ($xml === false)
			{
				Notify::error(Lang::txt('COM_SAML_ERROR_METADATA_FETCH'));
				return $this->importTask();
			}
		}

		if (!$xml)
		{
			Notify::warning(Lang::txt('COM_SAML_ERROR_METADATA_EMPTY'));
			return $this->importTask();
		}

		$fields = Metadata::parse($xml);

		if ($fields === false)
		{
			Notify::error(Lang::txt('COM_SAML_ERROR_METADATA_PARSE'));
			return $this->importTask();
		}

		// Prefill an unsaved row for review — nothing is stored yet
		$row = ServiceProvider::oneByEntityId($fields['entity_id']);

		if (!$row || !$row->get('id'))
		{
			$row = ServiceProvider::blank();
		}
		else
		{
			Notify::info(Lang::txt('COM_SAML_METADATA_EXISTING_SP'));
		}

		$row->set($fields);

		if ($url)
		{
			$row->set('metadata_url', $url);
		}

		Notify::success(Lang::txt('COM_SAML_METADATA_PARSED'));

		return $this->editTask($row);
	}
}
