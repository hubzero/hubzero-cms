<?php
namespace Components\Partners\Models;
use Hubzero\Database\Relational;
use Session;
use Date;

// Include the models we'll be using
/**
 * Drwho model class for a season
 */
class Partner_type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'partner';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'alias'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = str_replace(' ', '-', $data['title']);
		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Defines a belongs to one relationship between task and assignee
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a one to many through relationship with records by way of tasks
	 *
	 * @return  $this
	 */
	
	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params String or array of extra params to append
	 * @return     string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			$base = 'index.php?option=com_partners';
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&controller=partner_types&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&controller=partner_types&task=delete&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'view':
			case 'permalink':
			default:
				$link .= '&controller=partners&partner_types=' . $this->get('id');
			break;
		}

		return $link;
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		return parent::destroy();
	}
}

