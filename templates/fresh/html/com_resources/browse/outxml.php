<?php 

// Resource Highlight Template
// Jason Lambert NEESHub 2010

// no direct access
defined('_JEXEC') or die('Restricted access');
//$database =& JFactory::getDBO();
?>
<?php $xml = '<?xml version="1.0" encoding="ISO-8859-1"?>'."\n";


$xml .= '<resource>'."\n";

$xml .= '<id>'. $this->resource->id . '</id>'."\n";

if ($this->resource->title)
$xml .= "\t".'<title>' . $this->resource->title . '</title>'."\n";

if ($this->resource->introtext)
$xml .= "\t".'<abstract>' . str_replace('"','\'',$this->resource->introtext) . '</abstract>'."\n";

$xml .= "\t".'<children>' . count($this->children) . '</children>'."\n"; 

foreach ($this->children as $child) 
{
	//build up list of children
	$curl = $child->path;
	$xml .= "\t\t".'<child>'.$curl.'</child>'."\n";
}

// end resource tag
$xml .= '</resource>'."\n";
?>






<?php echo $xml?>