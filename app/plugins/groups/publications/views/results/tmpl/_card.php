<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
$database = App::get('db');
// Instantiate a helper object
//$RE = new \Components\Resources\Helpers\Helper($this->row->id, $database);
//$RE->getContributors();
// Get the component params and merge with resource params
$config = Component::params('com_publications');
$rparams = new \Hubzero\Config\Registry($this->row->params);
$params = $config;
$params->merge($rparams);
if (strstr($this->row->href, 'index.php'))
{
	$this->row->href = Route::url($this->row->href);  //where href is set
	
}
switch ($this->row->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public'; break;
}
?>

<div class="<?php echo $cls; ?> resource simple-card">
	<img src="https://qubeshub.org/groups/partnerprojectsupport/File:/uploads/annie-spratt-600.jpg" alt="image placeholder">
	<h3><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></h3>
	<p><?php echo $this->escape(stripslashes($this->row->abstract)); ?></p>
</div>
