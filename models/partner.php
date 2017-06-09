<?php 
namespace Components\Partners\Models;
use Date;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Session;


/**
* Our partner class, for the partner component
*/
class Partner extends Relational{
		/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'partner';

		/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'name';


		/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'notempty'
	);

		/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 */
	public function link($type='')
	{
		static $base;

		if (!isset($base))
		{
			//change from com_drwho&controller=characters
			$base = 'index.php?option=com_partners&controller=partners';
		}

		$link = $base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'edit':
				$link .= '&task=edit&id=' . $this->get('id');
			break;

			case 'delete':
				$link .= '&task=delete&id=' . $this->get('id') . '&' . Session::getFormToken() . '=1';
			break;

			case 'view':
			case 'permalink':
			default:
				$link .= '&task=view&id=' . $this->get('id');
			break;
		}

		return $link;
	}

	//may need to import a bio function, we will see!

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