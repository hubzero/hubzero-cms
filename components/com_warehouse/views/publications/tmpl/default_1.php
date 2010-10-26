<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<p>Publication List</p>

<p><a href="/warehouse/experiment/<?php echo $this->experimentId; ?>/project/<?php echo $this->projectId; ?>">Return</a></p>

<table cellpadding="1" cellspacing="1">
  <thead>
    <th>Publication</th>
  </thead>
  <?php

  $oPublicationArray = $this->pubArray;
  foreach($oPublicationArray as $iPubIndex=>$oPublication){
     $strAuthorArray = $oPublication['authors'];

     $strAuthors = "";
     foreach($strAuthorArray as $iAuthorIndex=>$strAuthor){
       $strAuthors .= "<a href='/members/".$strAuthor['authorid']."'>".$strAuthor['name']."</a>";
       if($iAuthorIndex < (sizeof($strAuthorArray)-1)){
         $strAuthors .= "; ";
       }
     }

     $strBgColor = "odd";
     if($iPubIndex%2 === 0){
       $strBgColor = "even";
     }

  ?>

  <tr class="<?php echo $strBgColor; ?>">
    <td id="publication<?php echo $iPubIndex; ?>>">
      <?php echo $strAuthors .", \"". $oPublication['title'] ."\""; ?>
      (<a href="/resources/<?php echo $oPublication['id']; ?>">view</a>)
    </td>
  </tr>

  <?php
  }
  ?>
</table>