<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' ); 

?>


<?php
$iNumberOfFiles = $this->iNumFiles;
$iIndex = 0;
$iIndexDisplay = $iIndex+1;
while($iIndex < $iNumberOfFiles){?>
  <div id="<?php echo "upload-".$iIndex; ?>Input" class="editorInputSize" style="margin-bottom: 5px;">
    <?php echo $iIndexDisplay; ?>. &nbsp;<input type="file" name="upload[]"/>
  </div>
<?php
++$iIndex;
$iIndexDisplay = $iIndex+1;
}
?>