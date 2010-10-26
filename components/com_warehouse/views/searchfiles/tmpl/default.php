<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $oFileArray = unserialize($_REQUEST['FOUND_FILES']);
  $iFindBy = $this->iFindBy;
?>

Search Results<br>

<ul>
<?php
  foreach($oFileArray as $iIndex=>$oDataFile){
    /* @var $strLink DataFile */
    $strLink = $oDataFile->getFriendlyPath();
    $strPath = $strLink;
    $strTitle = $oDataFile->getTitle();
    $strName = $oDataFile->getName();
    if(strlen($strName) > 30){
      $strNameLeft = substr($strName, 0, 15);
      $strNameRight = substr($strName, -5);
      $strName = $strNameLeft."...".$strNameRight;
    }
    $strDesc = $oDataFile->getDescription();
    
    $strTooltip = (StringHelper::hasText($strDesc)) ? $strDesc : $strTitle;
    $strTooltip .= (StringHelper::hasText($strTooltip)) ? " ::: $strPath" : $strPath;

    $strDisplay = ($iFindBy==1) ? $strTitle : $strName;
    $strDisplay = (StringHelper::hasText($strDisplay)) ?$strDisplay : $strName;
?>
    <li><a href="/data/get<?php echo $strLink; ?>" title="<?php echo $strTooltip; ?>"><?php echo $strDisplay; ?></a>
<?php
  }
 ?>
</ul>

<table style="border: 0px;">
  <tr>
    <td>Found: <?php echo $this->iFileCount; ?></td>
    <?php if($this->iFileCount > 0): ?>
      <td align="right">
        <?php if($this->iPrev >= 0): ?>
          <a href="javascript:void(0);" onClick="fileSearch('findby', 'term', <?php echo $this->iPrev; ?>, 'fileSearch');">Prev 10</a> |
        <?php endif; ?>
        <a href="javascript:void(0);" onClick="fileSearch('findby', 'term', <?php echo $this->iNext; ?>, 'fileSearch');">Next 10</a>
      </td>
    <?php endif; ?>
  </tr>

</table>