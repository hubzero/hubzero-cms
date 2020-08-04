<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Access;

use Hubzero\Base\Obj;

/**
 * Rules class.
 */
class Rules
{
	/**
	 * A named array.
	 *
	 * @var  array
	 */
	protected $data = array();

	/**
	 * Constructor.
	 *
	 * The input array must be in the form: array('action' => array(-42 => true, 3 => true, 4 => false))
	 * or an equivalent JSON encoded string, or an object where properties are arrays.
	 *
	 * @param   mixed  $input  A JSON format string (probably from the database) or a nested array.
	 * @return  void
	 */
	public function __construct($input = '')
	{
		// Convert in input to an array.
		if (is_string($input))
		{
			$input = json_decode($input, true);
		}
		elseif (is_object($input))
		{
			$input = (array) $input;
		}

		if (is_array($input))
		{
			// Top level keys represent the actions.
			foreach ($input as $action => $identities)
			{
				$this->mergeAction($action, $identities);
			}
		}
	}

	/**
	 * Get the data for the action.
	 *
	 * @return  array  A named array of Rule objects.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Method to merge a collection of Rules.
	 *
	 * @param   mixed  $input  Rule or array of Rules
	 * @return  void
	 */
	public function mergeCollection($input)
	{
		// Check if the input is an array.
		if (is_array($input))
		{
			foreach ($input as $actions)
			{
				$this->merge($actions);
			}
		}
	}

	/**
	 * Method to merge actions with this object.
	 *
	 * @param   mixed  $actions  Rule object, an array of actions or a JSON string array of actions.
	 * @return  void
	 */
	public function merge($actions)
	{
		if (is_string($actions))
		{
			$actions = json_decode($actions, true);
		}

		if (is_array($actions))
		{
			foreach ($actions as $action => $identities)
			{
				$this->mergeAction($action, $identities);
			}
		}
		elseif ($actions instanceof Rules)
		{
			$data = $actions->getData();

			foreach ($data as $name => $identities)
			{
				$this->mergeAction($name, $identities);
			}
		}
	}

	/**
	 * Merges an array of identities for an action.
	 *
	 * @param   string  $action      The name of the action.
	 * @param   array   $identities  An array of identities
	 * @return  void
	 */
	public function mergeAction($action, $identities)
	{
		if (isset($this->data[$action]))
		{
			// If exists, merge the action.
			$this->data[$action]->mergeIdentities($identities);
		}
		else
		{
			// If new, add the action.
			$this->data[$action] = new Rule($identities);
		}
	}

	/**
	 * Checks that an action can be performed by an identity.
	 *
	 * The identity is an integer where +ve represents a user group,
	 * and -ve represents a user.
	 *
	 * @param   string  $action    The name of the action.
	 * @param   mixed   $identity  An integer representing the identity, or an array of identities
	 * @return  mixed   Object or null if there is no information about the action.
	 */
	public function allow($action, $identity)
	{
		// Check we have information about this action.
		if (isset($this->data[$action]))
		{
			return $this->data[$action]->allow($identity);
		}

		return null;
	}

	/**
	 * Get the allowed actions for an identity.
	 *
	 * @param   mixed   $identity  An integer representing the identity or an array of identities
	 * @return  object  Allowed actions for the identity or identities
	 */
	public function getAllowed($identity)
	{
		// Sweep for the allowed actions.
		$allowed = new Obj;

		foreach ($this->data as $name => &$action)
		{
			if ($action->allow($identity))
			{
				$allowed->set($name, true);
			}
		}

		return $allowed;
	}

	/**
	 * Magic method to convert the object to JSON string representation.
	 *
	 * @return  string  JSON representation of the actions array
	 */
	public function __toString()
	{
		$temp = array();

		foreach ($this->data as $name => $rule)
		{
			// Convert the action to JSON, then back into an array otherwise
			// re-encoding will quote the JSON for the identities in the action.
			$temp[$name] = json_decode((string) $rule);
		}

		return json_encode($temp);
	}
}
