<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$dateFormat = 'M d, Y';

$baseManage = 'publications/submit';
$baseView   = 'publications';

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');

$mconfig = Component::params('com_members');
$pPath   = trim($mconfig->get('webpath'), '/');
$profileThumb = null;

$append = '?from=' . $this->user->get('email');
$lastMonth = date('M Y', strtotime("-1 month"));

$message  = 'Here is the monthly update on your recent publications usage' . "\n";
$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";

for ($a = 0; $a < count($this->pubstats); $a++)
{
	// Check against limit
	if ($a >= $this->limit)
	{
		break;
	}

	$stat = $this->pubstats[$a];

	$sefManage = $baseManage . '/' . $stat->publication_id . $append;
	$sefView   = $baseView . '/' . $stat->publication_id . $append;

	$message .= 'Publication #' . $stat->publication_id . ' "' . stripslashes($stat->title) . '"' . "\n";
	$message .= 'View publication:          ' . $base . '/' . trim($sefView, '/') . "\n";
	$message .= 'Manage publication:        ' . $base . '/' . trim($sefManage, '/') . "\n\n";

	$message .= 'Usage in the past month... ' . "\n";
	$message .= 'Page views:                ' . $stat->monthly_views. "\n";
	$message .= 'Downloads:                 ' . $stat->monthly_primary. "\n";
	$message .= 'Total downloads to date:   ' . $stat->total_primary. "\n";

	$message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'."\n\n";
}

$message = str_replace('<br />', '', $message);
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo $message;
