<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Models;

use Hubzero\Database\Nested;
use Hubzero\Database\Value\Raw;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Lang;
use User;
use Date;

/**
 * Menu item model
 */
class Item extends Nested
{
	/**
	 * Database state constants
	 **/
	const STATE_TRASHED = -2;

	/**
	 * The help screen key for the menu item.
	 *
	 * @var  string
	 */
	protected $helpKey = 'JHELP_MENUS_MENU_ITEM_MANAGER_EDIT';

	/**
	 * The help screen base URL for the menu item.
	 *
	 * @var  string
	 */
	protected $helpURL;

	/**
	 * True to use local lookup for the help screen.
	 *
	 * @var  boolean
	 */
	protected $helpLocal = false;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__menu';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

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
		'title'    => 'notempty',
		'menutype' => 'notempty',
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias',
		'img'
	);

	/**
	 * Asset rules
	 *
	 * @var  object
	 */
	public $paramsRegistry;

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('link', function($data)
		{
			if ($data['type'] == 'url')
			{
				$data['link'] = str_replace(array('"', '>', '<'), '', $data['link']);

				if (strstr($data['link'], ':') && substr($data['link'], 0, 1) != '/')
				{
					$segments = explode(':', $data['link']);
					$protocol = strtolower($segments[0]);
					$scheme = array(
						'http', 'https', 'ftp', 'ftps', 'gopher', 'mailto', 'news',
						'prospero', 'telnet', 'rlogin', 'tn3270', 'wais', 'url',
						'mid', 'cid', 'nntp', 'tel', 'urn', 'ldap', 'file', 'fax',
						'modem', 'git'
					);

					if (!in_array($protocol, $scheme))
					{
						return Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED');
					}
				}
			}
			return false;
		});

		$this->addRule('alias', function($data)
		{
			$alias = $this->automaticAlias($data);

			// Verify that a first level menu item alias is not 'component'.
			if ($this->get('parent_id') == 1 && $alias == 'component')
			{
				return Lang::txt('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_COMPONENT');
			}

			// Make sure menu alias doesn't interfere with known client routes and system directories
			// Maybe use `Filesystem::directories(PATH_ROOT)` instead?
			if ($this->get('parent_id') == 1 && in_array($alias, ['app', 'core', 'api', 'administrator', 'files']))
			{
				return Lang::txt('JLIB_DATABASE_ERROR_MENU_ROOT_ALIAS_FOLDER', $alias, $alias);
			}

			return false;
		});

		$this->addRule('home', function($data)
		{
			// Verify that the default home menu is not unpublished
			if ($this->get('home') == '1' && $this->get('language') == '*' && $this->get('published') != '1')
			{
				return Lang::txt('JLIB_DATABASE_ERROR_MENU_UNPUBLISH_DEFAULT_HOME');
			}

			// Verify that the home item a component.
			if ($this->get('home') && $this->get('type') != 'component')
			{
				return Lang::txt('JLIB_DATABASE_ERROR_MENU_HOME_NOT_COMPONENT');
			}

			return false;
		});
	}

	/**
	 * Generates automatic alias field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : '');

		if (empty($alias)
		 && $this->get('type') != 'alias'
		 && $this->get('type') != 'url')
		{
			$alias = $this->get('title');
		}

		$alias = trim($alias);

		// Remove any '-' from the string since they will be used as concatenaters
		$alias = str_replace('-', ' ', $alias);

		$alias = Lang::transliterate($alias);

		$alias = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', strtolower($alias));
		$alias = trim($alias, '-');

		if (trim(str_replace('-', '', $alias)) == '')
		{
			$alias = Date::of('now')->format('Y-m-d-H-i-s');
		}

		return $alias;
	}

	/**
	 * Generates automatic alias field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticImg($data)
	{
		if (!isset($data['img']))
		{
			$data['img'] = '';
		}

		return (string)$data['img'];
	}

	/**
	 * Get the necessary data to load an item help screen.
	 *
	 * @return  object  An object with key, url, and local properties for loading the item help screen.
	 */
	public function transformHelp()
	{
		return (object) array(
			'key'   => $this->helpKey,
			'url'   => $this->helpURL,
			'local' => $this->helpLocal
		);
	}

	/**
	 * Get params as Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Make sure params are a string
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParams($data)
	{
		if (!empty($data['params']) && !is_string($data['params']))
		{
			if (!($data['params'] instanceof Registry))
			{
				$data['params'] = new Registry($data['params']);
			}
			$data['params'] = $data['params']->toString();
		}
		return $data['params'];
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticLft($data)
	{
		if (!$data['parent_id'])
		{
			$data['lft'] = 0;
		}
		return $data['lft'];
	}

	/**
	 * Generates automatic lft value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRgt($data)
	{
		if (!isset($data['rgt']))
		{
			if (!isset($data['lft']))
			{
				$data['lft'] = $this->automaticLft($data);
			}
			$data['rgt'] = $data['lft'] + 1;
		}
		return $data['rgt'];
	}

	/**
	 * Get parent
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Item', 'parent_id');
	}

	/**
	 * Get child entries
	 *
	 * @return  object
	 */
	public function children()
	{
		return self::all()
			->whereEquals('parent_id', (int) $this->get('id'));
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove children
		foreach ($this->children()->rows() as $child)
		{
			if (!$child->destroy())
			{
				$this->addError($child->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Save the record
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		if (!$this->get('access'))
		{
			$this->set('access', (int) \Config::get('access'));
		}

		$isNew = $this->isNew();

		if ($isNew)
		{
			if (!$this->get('parent_id'))
			{
				$root = self::rootNode();

				$this->set('lft', $root->get('lft') + 1);
				$this->set('rgt', $root->get('lft') + 2);
				$this->set('parent_id', $root->get('id'));
			}

			$parent = $this->parent;

			if (!$parent->get('id'))
			{
				$this->addError(Lang::txt('Parent node does not exist.'));
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!($reposition = $this->getTreeRepositionData($parent, 2, 'last-child')))
			{
				// Error message set in getNode method.
				return false;
			}

			// Shift left values.
			$query = $this->getQuery()
				->update($this->getTableName())
				->set(['lft' => new Raw('lft + 2')])
				->where($reposition->left_where['col'], $reposition->left_where['op'], $reposition->left_where['val']);

			if (!$query->execute())
			{
				$this->addError($query->getError());
				return false;
			}

			// Shift right values.
			$query = $this->getQuery()
				->update($this->getTableName())
				->set(['rgt' => new Raw('rgt + 2')])
				->where($reposition->right_where['col'], $reposition->right_where['op'], $reposition->right_where['val']);

			if (!$query->execute())
			{
				$this->addError($query->getError());
				return false;
			}

			// Set all the nested data
			$this->set('path', ($parent->get('path') ? $parent->get('path') . '/' : '') . $this->get('alias'));
			$this->set('lft', $reposition->new_lft);
			$this->set('rgt', $reposition->new_rgt);
			$this->set('level', $parent->get('level', 0) + 1);
		}
		/*else
		{
			if (!$this->moveByReference($this->get('parent_id'), 'last-child', $this->get('id')))
			{
				// Error message set in move method.
				return false;
			}
		}*/

		$this->set('params', $this->params->toString());

		$result = parent::save();

		if ($result)
		{
			$this->rebuildPath();

			foreach ($this->children()->rows() as $child)
			{
				// Rebuild the tree path.
				if (!$child->rebuildPath())
				{
					$this->addError($child->getError());
					return false;
				}
			}
		}

		return $result;
	}

	/**
	 * Method to recursively rebuild the whole nested set tree.
	 *
	 * @param   integer  $parentId  The root of the tree to rebuild.
	 * @param   integer  $leftId    The left id to start with in building the tree.
	 * @param   integer  $level     The level to assign to the current nodes.
	 * @param   string   $path      The path to the current nodes.
	 * @return  integer  1 + value of root rgt on success, false on failure
	 */
	public function rebuild($parentId, $leftId = 0, $level = 0, $path = '')
	{
		$query = $this->getQuery()
			->select('id')
			->select('alias')
			->from($this->getTableName())
			->whereEquals('parent_id', (int) $parentId)
			->order('parent_id', 'asc')
			->order('lft', 'asc');

		// Assemble the query to find all children of this node.
		$db = \App::get('db');
		$db->setQuery($query->toString());
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuild($node->id, $rightId, $level + 1, $path . (empty($path) ? '' : '/') . $node->alias);

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false)
			{
				return false;
			}
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft'   => (int) $leftId,
				'rgt'   => (int) $rightId,
				'level' => (int) $level,
				'path'  => $path
			))
			->whereEquals('id', (int) $parentId);

		// If there is an update failure, return false to break out of the recursion.
		if (!$query->execute())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	/**
	 * Method to rebuild the node's path field from the alias values of the
	 * nodes from the current node to the root node of the tree.
	 *
	 * @return  boolean  True on success.
	 */
	public function rebuildPath()
	{
		// Get the aliases for the path from the node to the root node.
		$db = App::get('db');

		$path = $this->parent->get('path');
		$segments = explode('/', $path);

		// Make sure to remove the root path if it exists in the list.
		if ($segments[0] == 'root')
		{
			array_shift($segments);
		}
		$segments[] = $this->get('alias');

		// Build the path.
		$path = trim(implode('/', $segments), ' /\\');

		// Update the path field for the node.
		$query = $db->getQuery()
			->update($this->getTableName())
			->set(array(
				'path' => $path
			))
			->whereEquals('id', (int) $this->get('id'));
		$db->setQuery($query->toString());

		// Check for a database error.
		if (!$db->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_REBUILDPATH_FAILED', get_class($this), $db->getErrorMsg()));
			return false;
		}

		// Update the current record's path to the new one:
		$this->set('path', $path);

		return true;
	}

	/**
	 * Method to get various data necessary to make room in the tree at a location
	 * for a node and its children.  The returned data object includes conditions
	 * for SQL WHERE clauses for updating left and right id values to make room for
	 * the node as well as the new left and right ids for the node.
	 *
	 * @param   object   $referenceNode  A node object with at least a 'lft' and 'rgt' with
	 *                                   which to make room in the tree around for a new node.
	 * @param   integer  $nodeWidth      The width of the node for which to make room in the tree.
	 * @param   string   $position       The position relative to the reference node where the room
	 *                                   should be made.
	 * @return  mixed    Boolean false on failure or data object on success.
	 */
	protected function getTreeRepositionData($referenceNode, $nodeWidth, $position = 'before')
	{
		// Make sure the reference an object with a left and right id.
		if (!is_object($referenceNode) && isset($referenceNode->lft) && isset($referenceNode->rgt))
		{
			return false;
		}

		// A valid node cannot have a width less than 2.
		if ($nodeWidth < 2)
		{
			return false;
		}

		// Initialise variables.
		$k = $this->pk;

		$data = new \stdClass;

		// Run the calculations and build the data object by reference position.
		switch ($position)
		{
			case 'first-child':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->lft);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->lft);

				$data->new_lft = $referenceNode->lft + 1;
				$data->new_rgt = $referenceNode->lft + $nodeWidth;
				$data->new_parent_id = $referenceNode->$k;
				$data->new_level = $referenceNode->level + 1;
			break;

			case 'last-child':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->rgt);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->rgt);

				$data->new_lft = $referenceNode->rgt;
				$data->new_rgt = $referenceNode->rgt + $nodeWidth - 1;
				$data->new_parent_id = $referenceNode->$k;
				$data->new_level = $referenceNode->level + 1;
			break;

			case 'before':
				$data->left_where  = array('col' => 'lft', 'op' => '>=', 'val' => $referenceNode->lft);
				$data->right_where = array('col' => 'rgt', 'op' => '>=', 'val' => $referenceNode->lft);

				$data->new_lft = $referenceNode->lft;
				$data->new_rgt = $referenceNode->lft + $nodeWidth - 1;
				$data->new_parent_id = $referenceNode->parent_id;
				$data->new_level = $referenceNode->level;
			break;

			default:
			case 'after':
				$data->left_where  = array('col' => 'lft', 'op' => '>', 'val' => $referenceNode->rgt);
				$data->right_where = array('col' => 'rgt', 'op' => '>', 'val' => $referenceNode->rgt);

				$data->new_lft = $referenceNode->rgt + 1;
				$data->new_rgt = $referenceNode->rgt + $nodeWidth;
				$data->new_parent_id = $referenceNode->parent_id;
				$data->new_level = $referenceNode->level;
			break;
		}

		return $data;
	}

	/**
	 * Generate a Form object and bind data to it
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/item.xml';
		$file = Filesystem::cleanPath($file);

		Form::addFormPath(__DIR__ . '/forms');
		Form::addFieldPath(__DIR__ . '/fields');

		$form = new Form('item', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->getAttributes();
		$data['params'] = $this->params->toArray();

		$form = $this->preprocessForm($form, $data);

		$form->bind($data);

		// Modify the form based on access controls.
		if (!(($this->get('id') && User::authorise('core.edit.state', 'com_menus.item.' . (int) $this->get('id')))
		 || User::authorise('core.edit.state', 'com_menus')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('menuordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('menuordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * @param   object  $form  A form object.
	 * @param   mixed   $data  The data expected for the form.
	 * @return  void
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(Form $form, $data, $group = 'content')
	{
		// Initialise variables.
		$link = $this->get('link');
		$type = $this->get('type');

		$formFile = false;

		// Initialise form with component view params if available.
		if ($type == 'component')
		{
			$link = htmlspecialchars_decode($link);

			// Parse the link arguments.
			$args = array();
			parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

			// Confirm that the option is defined.
			$option = '';
			$base = '';
			if (isset($args['option']))
			{
				// The option determines the base path to work with.
				$option = $args['option'];
				$base   = \Component::path($option) . '/site';
			}

			// Confirm a view is defined.
			$formFile = false;
			if (isset($args['view']))
			{
				$view = $args['view'];

				// Determine the layout to search for.
				if (isset($args['layout']))
				{
					$layout = $args['layout'];
				}
				else
				{
					$layout = 'default';
				}

				$formFile = false;

				// Check for the layout XML file. Use standard xml file if it exists.
				$path = \Hubzero\Filesystem\Util::normalizePath($base . '/views/' . $view . '/tmpl/' . $layout . '.xml');

				if (Filesystem::exists($path))
				{
					$formFile = $path;
				}

				// if custom layout, get the xml file from the template folder
				// template folder is first part of file name -- template:folder
				if (!$formFile && (strpos($layout, ':') > 0 ))
				{
					$temp = explode(':', $layout);
					$templatePath = \Hubzero\Filesystem\Util::normalizePath(PATH_APP . '/templates/' . $temp[0] . '/html/' . $option . '/' . $view . '/' . $temp[1] . '.xml');

					if (Filesystem::exists($templatePath))
					{
						$formFile = $templatePath;
					}
				}
			}

			// Now check for a view manifest file
			if (!$formFile)
			{
				if (isset($view) && Filesystem::exists($path = \Hubzero\Filesystem\Util::normalizePath($base . '/views/' . $view . '/metadata.xml')))
				{
					$formFile = $path;
				}
				else
				{
					//Now check for a component manifest file
					$path = \Hubzero\Filesystem\Util::normalizePath($base . '/metadata.xml');

					if (Filesystem::exists($path))
					{
						$formFile = $path;
					}
				}
			}
		}

		if ($formFile)
		{
			// If an XML file was found in the component, load it first.
			// We need to qualify the full path to avoid collisions with component file names.
			if ($form->loadFile($formFile, true, '/metadata') == false)
			{
				throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
			}

			// Attempt to load the xml file.
			if (!$xml = simplexml_load_file($formFile))
			{
				throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
			}

			// Get the help data from the XML file if present.
			$help = $xml->xpath('/metadata/layout/help');
		}
		else
		{
			// We don't have a component. Load the form XML to get the help path
			$xmlFile = Filesystem::find(__DIR__ . '/forms', 'item_' . $type . '.xml');

			// Attempt to load the xml file.
			if (!$xmlFile || ($xmlFile && !$xml = simplexml_load_file($xmlFile)))
			{
				throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
			}

			// Get the help data from the XML file if present.
			$help = $xml->xpath('/form/help');
		}

		if (!empty($help))
		{
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);
			$helpLoc = trim((string) $help[0]['local']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
			$this->helpLocal = (($helpLoc == 'true') || ($helpLoc == '1') || ($helpLoc == 'local')) ? true : false;
		}

		// Now load the component params.
		// TODO: Work out why 'fixing' this breaks Form
		if ($isNew = false)
		{
			$path = \Hubzero\Filesystem\Util::normalizePath(\Component::path($option) . '/config/config.xml');
		}
		else
		{
			$path = 'null';
		}

		if (Filesystem::exists($path))
		{
			// Add the component params last of all to the existing form.
			if (!$form->load($path, true, '/config'))
			{
				throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
			}
		}

		// Load the specific type file
		if (!$form->loadFile('item_' . $type, false, false))
		{
			throw new Exception(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		// Association menu items
		if (App::has('menu_associations') && App::get('menu_associations', 0) != 0)
		{
			$languages = Lang::getLanguages('lang_code');

			$addform = new \SimpleXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'associations');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'item_associations');
			$fieldset->addAttribute('description', 'COM_MENUS_ITEM_ASSOCIATIONS_FIELDSET_DESC');

			$add = false;
			foreach ($languages as $tag => $language)
			{
				if ($tag != $data['language'])
				{
					$add = true;

					$field = $fieldset->addChild('field');
					$field->addAttribute('name', $tag);
					$field->addAttribute('type', 'menuitem');
					$field->addAttribute('language', $tag);
					$field->addAttribute('label', $language->title);
					$field->addAttribute('translate_label', 'false');

					$option = $field->addChild('option', 'COM_MENUS_ITEM_FIELD_ASSOCIATION_NO_VALUE');
					$option->addAttribute('value', '');
				}
			}

			if ($add)
			{
				$form->load($addform, false);
			}
		}

		// Trigger the form preparation event.
		$results = Event::trigger($group . '.onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		/*if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}*/

		return $form;
	}

	/**
	 * Get the root node
	 *
	 * @return  object
	 */
	public static function rootNode()
	{
		return self::blank()
			->whereEquals('level', 0)
			->order('lft', 'asc')
			->row();
	}

	/**
	 * Method to change the home state of one or more items.
	 *
	 * @param   array    $pks    A list of the primary keys to change.
	 * @param   int      $value  The value of the home state.
	 * @return  boolean  True on success.
	 */
	public static function setHome(&$pks, $value = 1)
	{
		// Initialise variables.
		$pks = (array) $pks;

		$languages = array();
		$onehome   = false;

		// Remember that we can set a home page for different languages,
		// so we need to loop through the primary key array.
		foreach ($pks as $i => $pk)
		{
			$model = self::one($pk);

			if ($model)
			{
				if (!array_key_exists($model->get('language'), $languages))
				{
					$languages[$model->get('language')] = true;

					if ($model->get('home') == $value)
					{
						unset($pks[$i]);
						Notify::error(Lang::txt('COM_MENUS_ERROR_ALREADY_HOME'));
					}
					else
					{
						$model->set('home', $value);

						if ($model->get('language') == '*')
						{
							$model->set('published', 1);
						}

						if (!$model->save())
						{
							// Prune the items that could not be stored.
							unset($pks[$i]);

							Notify::error($model->getError());
							return false;
						}
					}
				}
				else
				{
					unset($pks[$i]);

					if (!$onehome)
					{
						$onehome = true;
						Notify::error(Lang::txt('COM_MENUS_ERROR_ONE_HOME'));
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 * @return  mixed    Boolean true on success.
	 */
	public function move($delta, $where = '')
	{
		$query = $this->getQuery()
			->select('id')
			->from($this->getTableName())
			->whereEquals('parent_id', $this->get('parent_id'));
		if ($where)
		{
			$query->whereRaw($where);
		}

		$position = 'after';

		if ($delta > 0)
		{
			$query->where('rgt', '>', $this->get('rgt'));
			$query->order('rgt', 'ASC');
			$position = 'after';
		}
		else
		{
			$query->where('lft', '<', $this->get('lft'));
			$query->order('lft', 'DESC');
			$position = 'before';
		}
		$db = \App::get('db');
		$db->setQuery($query->toString());

		$referenceId = $db->loadResult();

		if ($referenceId)
		{
			return $this->moveByReference($referenceId, $position, $this->get('id'));
		}

		$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED') . ': Reference not found for delta ' . $delta);

		return false;
	}

	/**
	 * Method to move a node and its children to a new location in the tree.
	 *
	 * @param   integer  $referenceId  The primary key of the node to reference new location by.
	 * @param   string   $position     Location type string. ['before', 'after', 'first-child', 'last-child']
	 * @param   integer  $pk           The primary key of the node to move.
	 * @return  boolean  True on success.
	 */
	public function moveByReference($referenceId, $position = 'after', $pk = 0)
	{
		// Initialise variables.
		$pk = (is_null($pk)) ? $this->get('id') : $pk;

		// Get the node by id.
		$node = self::oneOrNew($pk);

		if (!$node->get('id'))
		{
			// Error message set in getNode method.
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED') . ': Node not found #' . $pk);
			return false;
		}

		// Get the ids of child nodes.
		$query = $this->getQuery()
			->select('id')
			->from($this->getTableName())
			->whereRaw('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);

		$db = \App::get('db');
		$db->setQuery($query->toString());
		$children = $db->loadColumn();

		// Cannot move the node to be a child of itself.
		if (in_array($referenceId, $children))
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_INVALID_NODE_RECURSION', get_class($this)));
			return false;
		}

		// Move the sub-tree out of the nested sets by negating its left and right values.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft' => new Raw('lft * (-1)'),
				'rgt' => new Raw('rgt * (-1)')
			))
			->whereRaw('lft BETWEEN ' . (int) $node->lft . ' AND ' . (int) $node->rgt);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// Close the hole in the tree that was opened by removing the sub-tree from the nested sets.

		// Compress the left values.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft' => new Raw('lft - ' . (int) ($node->rgt - $node->lft + 1))
			))
			->where('lft', '>', (int) $node->rgt);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// Compress the right values.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'rgt' => new Raw('rgt - ' . (int) ($node->rgt - $node->lft + 1))
			))
			->where('rgt', '>', (int) $node->rgt);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// We are moving the tree relative to a reference node.
		if ($referenceId)
		{
			// Get the reference node by primary key.
			$reference = self::oneOrNew($referenceId);
			if (!$reference->get('id'))
			{
				$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED') . ': Reference not found #' . $referenceId);
				return false;
			}

			// Get the reposition data for shifting the tree and re-inserting the node.
			if (!$repositionData = $this->getTreeRepositionData($reference, ($node->rgt - $node->lft + 1), $position))
			{
				$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED') . ': Reposition data');
				return false;
			}
		}
		// We are moving the tree to be the last child of the root node
		else
		{
			// Get the last root node as the reference node.
			$query = $this->getQuery()
				->select('id')
				->select('parent_id')
				->select('level')
				->select('lft')
				->select('rgt')
				->from($this->getTableName())
				->whereEquals('parent_id', 0)
				->order('lft', 'DESC')
				->limit(1)
				->start(0);

			$db->setQuery($query->toString());
			$reference = $db->loadObject();

			// Get the reposition data for re-inserting the node after the found root.
			if (!$repositionData = $this->getTreeRepositionData($reference, ($node->rgt - $node->lft + 1), 'last-child'))
			{
				$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED') . ': Reposition data');
				return false;
			}
		}

		// Create space in the nested sets at the new location for the moved sub-tree.

		// Shift left values.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'lft' => new Raw('lft + ' . (int) ($node->rgt - $node->lft + 1))
			))
			->where($repositionData->left_where['col'], $repositionData->left_where['op'], $repositionData->left_where['val']);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// Shift right values.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'rgt' => new Raw('rgt + ' . (int) ($node->rgt - $node->lft + 1))
			))
			->where($repositionData->right_where['col'], $repositionData->right_where['op'], $repositionData->right_where['val']);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// Calculate the offset between where the node used to be in the tree and
		// where it needs to be in the tree for left ids (also works for right ids).
		$offset = $repositionData->new_lft - $node->lft;
		$levelOffset = $repositionData->new_level - $node->level;

		// Move the nodes back into position in the tree using the calculated offsets.
		$query = $this->getQuery()
			->update($this->getTableName())
			->set(array(
				'rgt'   => new Raw((int) $offset . ' - rgt'),
				'lft'   => new Raw((int) $offset . ' - lft'),
				'level' => new Raw('level + ' . (int) $levelOffset)
			))
			->where('lft', '<', 0);

		if (!$query->execute())
		{
			$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
			return false;
		}

		// Set the correct parent id for the moved node if required.
		if ($node->parent_id != $repositionData->new_parent_id)
		{
			$query = $this->getQuery()
				->update($this->getTableName());

			$query->set(array(
					'parent_id' => (int) $repositionData->new_parent_id
				))
				->whereEquals('id', (int) $node->id);

			if (!$query->execute())
			{
				$this->addError(Lang::txt('JLIB_DATABASE_ERROR_MOVE_FAILED'));
				return false;
			}
		}

		// Set the object values.
		$this->set('parent_id', $repositionData->new_parent_id);
		$this->set('level', $repositionData->new_level);
		$this->set('lft', $repositionData->new_lft);
		$this->set('rgt', $repositionData->new_rgt);

		return true;
	}

	/**
	 * Saves the manually set order of records.
	 *
	 * @param   array  $pks    An array of primary key ids.
	 * @param   array  $order  An array of order values.
	 * @return  bool
	 */
	public static function saveorder($pks = null, $order = null)
	{
		if (empty($pks))
		{
			return false;
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$model = self::oneOrFail((int) $pk);

			if ($model->get('ordering') != $order[$i])
			{
				$model->set('ordering', $order[$i]);

				if (!$model->save())
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array    $commands  An array of commands to perform.
	 * @param   array    $pks       An array of item ids.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  boolean  Returns true on success, false on failure.
	 */
	public function batchTask($commands, $pks, $contexts)
	{
		if (empty($pks))
		{
			$this->addError(Lang::txt('COM_MENUS_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['menu_id']))
		{
			$cmd = \Hubzero\Utility\Arr::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands['menu_id'], $pks, $contexts);

				if (is_array($result))
				{
					$pks = $result;
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands['menu_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['assetgroup_id']))
		{
			if (!$this->batchAccess($commands['assetgroup_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!empty($commands['language_id']))
		{
			if (!$this->batchLanguage($commands['language_id'], $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->addError(Lang::txt('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
		}

		return $done;
	}

	/**
	 * Batch copy menu items to a new menu or parent.
	 *
	 * @param   integer  $value     The new menu or sub-item.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  mixed    An array of new IDs on success, boolean false on failure.
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		// $value comes as {menutype}.{parent_id}
		$parts = explode('.', $value);
		$menuType = $parts[0];
		$parentId = (int) \Hubzero\Utility\Arr::getValue($parts, 1, 0);

		$table = $this->getTable();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$i = 0;

		// Check that the parent exists
		if ($parentId)
		{
			$model = self::oneOrNew($parentId);

			if (!$model->get('id'))
			{
				// Non-fatal error
				$this->addError(Lang::txt('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
				$parentId = 0;
			}
		}

		// If the parent is 0, set it to the ID of the root item in the tree
		if (empty($parentId))
		{
			if (!$parentId = self::getRoot()->get('id'))
			{
				$this->addError(Lang::txt('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
				return false;
			}
		}

		// Check that user has create permission for menus
		if (!User::authorise('core.create', 'com_menus'))
		{
			$this->addError(Lang::txt('COM_MENUS_BATCH_MENU_ITEM_CANNOT_CREATE'));
			return false;
		}

		// We need to log the parent ID
		$parents = array();

		// Calculate the emergency stop count as a precaution against a runaway loop bug
		$count = self::all()->total();

		// Parent exists so we let's proceed
		while (!empty($pks) && $count > 0)
		{
			// Pop the first id off the stack
			$pk = array_shift($pks);

			$model = self::oneOrNew($pk);

			// Check that the row actually exists
			if (!$model->get('id'))
			{
				$this->addError(Lang::txt('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
				continue;
			}

			// Copy is a bit tricky, because we also need to copy the children
			$childIds = self::all()
				->where('lft', '>', (int) $model->get('lft'))
				->where('rgt', '<', (int) $model->get('rgt'))
				->rows()
				->fieldsByKey('id');

			// Add child ID's to the array only if they aren't already there.
			foreach ($childIds as $childId)
			{
				if (!in_array($childId, $pks))
				{
					array_push($pks, $childId);
				}
			}

			// Make a copy of the old ID and Parent ID
			$oldId = $model->get('id');
			$oldParentId = $model->get('parent_id');

			// Reset the id because we are making a copy.
			$model->set('id', 0);

			// If we a copying children, the Old ID will turn up in the parents list
			// otherwise it's a new top level item
			$model->set('parent_id', isset($parents[$oldParentId]) ? $parents[$oldParentId] : $parentId);
			$model->set('menutype', $menuType);

			// Set the new location in the tree for the node.
			//$table->setLocation($table->parent_id, 'last-child');

			// TODO: Deal with ordering?
			//$model->set('ordering', 1);
			$model->set('level', null);
			$model->set('lft', null);
			$model->set('rgt', null);
			$model->set('home', 0);

			// Alter the title & alias
			list($title, $alias) = $this->generateNewTitle($model->get('parent_id'), $model->get('alias'), $model->get('title'));
			$model->set('title', $title);
			$model->set('alias', $alias);

			// Store the row.
			if (!$model->save())
			{
				$this->setError($model->getError());
				return false;
			}

			// Get the new item ID
			$newId = $model->get('id');

			// Add the new ID to the array
			$newIds[$i] = $newId;
			$i++;

			// Now we log the old 'parent' to the new 'parent'
			$parents[$oldId] = $model->get('id');
			$count--;
		}

		// Rebuild the hierarchy.
		if (!$model->rebuild(1))
		{
			$this->addError($model->getError());
			return false;
		}

		// Rebuild the tree path.
		if (!$model->rebuildPath())
		{
			$this->addError($model->getError());
			return false;
		}

		return $newIds;
	}

	/**
	 * Batch move menu items to a new menu or parent.
	 *
	 * @param   integer  $value     The new menu or sub-item.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  boolean  True on success.
	 */
	protected function batchMove($value, $pks, $contexts)
	{
		// $value comes as {menutype}.{parent_id}
		$parts = explode('.', $value);
		$menuType = $parts[0];
		$parentId = (int) \Hubzero\Utility\Arr::getValue($parts, 1, 0);

		$table = $this->getTable();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Check that the parent exists.
		if ($parentId)
		{
			$model = self::oneOrNew($parentId);

			if (!$model->get('id'))
			{
				// Non-fatal error
				$this->addError(Lang::txt('JGLOBAL_BATCH_MOVE_PARENT_NOT_FOUND'));
				$parentId = 0;
			}
		}

		// Check that user has create and edit permission for menus
		if (!User::authorise('core.create', 'com_menus'))
		{
			$this->addError(Lang::txt('COM_MENUS_BATCH_MENU_ITEM_CANNOT_CREATE'));
			return false;
		}

		if (!User::authorise('core.edit', 'com_menus'))
		{
			$this->addError(Lang::txt('COM_MENUS_BATCH_MENU_ITEM_CANNOT_EDIT'));
			return false;
		}

		// We are going to store all the children and just moved the menutype
		$children = array();

		// Parent exists so we let's proceed
		foreach ($pks as $pk)
		{
			// Check that the row actually exists
			$model = self::oneOrNew($pk);

			// Check that the row actually exists
			if (!$model->get('id'))
			{
				// Not fatal error
				$this->addError(Lang::txt('JGLOBAL_BATCH_MOVE_ROW_NOT_FOUND', $pk));
				continue;
			}

			// Set the new location in the tree for the node.
			//$model->setLocation($parentId, 'last-child');

			// Set the new Parent Id
			$model->set('parent_id', $parentId);

			// Check if we are moving to a different menu
			if ($menuType != $model->get('menutype'))
			{
				// Add the child node ids to the children array.
				$childIds = self::all()
					->where('lft', '>', (int) $model->get('lft'))
					->where('rgt', '<', (int) $model->get('rgt'))
					->rows()
					->fieldsByKey('id');
				$children = array_merge($children, (array) $childIds);
			}

			// Store the row.
			if (!$model->save())
			{
				$this->addError($model->getError());
				return false;
			}

			// Rebuild the tree path.
			if (!$model->rebuildPath())
			{
				$this->addError($model->getError());
				return false;
			}
		}

		// Process the child rows
		if (!empty($children))
		{
			// Remove any duplicates and sanitize ids.
			$children = array_unique($children);
			\Hubzero\Utility\Arr::toInteger($children);

			// Update the menutype field in all nodes where necessary.
			$db = App::get('db');
			$query = $db->getQuery();
			$query->update($this->getTableName());
			$query->set(array('menutype' => $menuType));
			$query->whereIn('id', $children);

			$db->setQuery($query->toString());
			$db->query();
		}

		return true;
	}

	/**
	 * Batch access level changes for a group of rows.
	 *
	 * @param   integer  $value     The new value matching an Asset Group ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 */
	protected function batchAccess($value, $pks, $contexts)
	{
		// Set the variables
		foreach ($pks as $pk)
		{
			if (User::authorise('core.edit', $contexts[$pk]))
			{
				$model = self::oneOrFail($pk);
				$model->set('access', (int) $value);

				if (!$model->save())
				{
					$this->addError($model->getError());
					return false;
				}
			}
			else
			{
				$this->addError(Lang::txt('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Batch language changes for a group of rows.
	 *
	 * @param   string   $value     The new value matching a language.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 */
	protected function batchLanguage($value, $pks, $contexts)
	{
		// Set the variables
		foreach ($pks as $pk)
		{
			if (User::authorise('core.edit', $contexts[$pk]))
			{
				$model = self::oneOrFail($pk);
				$model->set('language', $value);

				if (!$model->save())
				{
					$this->addError($model->getError());
					return false;
				}
			}
			else
			{
				$this->addError(Lang::txt('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}
		}

		return true;
	}

	/**
	 * Increment styles.
	 *
	 * @var  array
	 */
	protected static $incrementStyles = array(
		'dash' => array(
			'#-(\d+)$#',
			'-%d'
		),
		'default' => array(
			array('#\((\d+)\)$#', '#\(\d+\)$#'),
			array(' (%d)', '(%d)'),
		),
	);

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $parent_id  The id of the category.
	 * @param   string   $alias      The alias.
	 * @param   string   $title      The title.
	 * @return  array    Contains the modified title and alias.
	 */
	protected function generateNewTitle($parent_id, $alias, $title)
	{
		// Alter the title & alias
		while (self::all()->whereEquals('alias', $alias)->whereEquals('parent_id', $parent_id)->row()->get('id'))
		{
			$title = self::increment($title);
			$alias = self::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	/**
	 * Increments a trailing number in a string.
	 *
	 * Used to easily create distinct labels when copying objects. The method has the following styles:
	 *
	 * default: "Label" becomes "Label (2)"
	 * dash:    "Label" becomes "Label-2"
	 *
	 * @param   string   $string  The source string.
	 * @param   string   $style   The the style (default|dash).
	 * @param   integer  $n       If supplied, this number is used for the copy, otherwise it is the 'next' number.
	 * @return  string   The incremented string.
	 */
	protected static function increment($string, $style = 'default', $n = 0)
	{
		$styleSpec = isset(self::$incrementStyles[$style]) ? self::$incrementStyles[$style] : self::$incrementStyles['default'];

		// Regular expression search and replace patterns.
		if (is_array($styleSpec[0]))
		{
			$rxSearch = $styleSpec[0][0];
			$rxReplace = $styleSpec[0][1];
		}
		else
		{
			$rxSearch = $rxReplace = $styleSpec[0];
		}

		// New and old (existing) sprintf formats.
		if (is_array($styleSpec[1]))
		{
			$newFormat = $styleSpec[1][0];
			$oldFormat = $styleSpec[1][1];
		}
		else
		{
			$newFormat = $oldFormat = $styleSpec[1];
		}

		// Check if we are incrementing an existing pattern, or appending a new one.
		if (preg_match($rxSearch, $string, $matches))
		{
			$n = empty($n) ? ($matches[1] + 1) : $n;
			$string = preg_replace($rxReplace, sprintf($oldFormat, $n), $string);
		}
		else
		{
			$n = empty($n) ? 2 : $n;
			$string .= sprintf($newFormat, $n);
		}

		return $string;
	}
}
