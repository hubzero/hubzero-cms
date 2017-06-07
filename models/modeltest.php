<?php
namespace Components\Partners\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Session;
use Date;
use PHPUnit\Framework\TestCase;
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






class Modeltest extends TestCase{
	public function testOne()
	{ $record = Partner::all();
		

	}			
}?>
