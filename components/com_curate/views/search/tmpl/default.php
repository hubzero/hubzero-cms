<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>


    <form id="frmProjects" action="/curate?view=default&format=ajax">
      <table summary="A list of public uncurated projects" id="grouplist">
        <thead>
          <tr>
            <th>Name</th>
            <th>Title</th>
            <th>Contact</th>
          </tr>
        </thead>
      
        <?php 
          #display rows from query.
    	  $oProjectArray = $this->projectArray;
    	  foreach ($oProjectArray as $nKey => $oProject) {
    	      $sClass="even";
    	      if($nKey%2===0)$sClass="odd";
    	    ?>
    	      <tr class="<?php echo $sClass ?>">
    	        <td><a href="/curate/project/<?php echo $oProject["PROJID"] ?>"><?php echo $oProject["NAME"] ?></a></td>
    	        <td><?php echo $oProject["TITLE"] ?></td>
    	        <td><a href="mailto:<?php echo $oProject["CONTACT_EMAIL"] ?>"><?php echo $oProject["CONTACT_NAME"] ?></a></td>
    	      </tr>
    	    <?
     	  }
  	    ?>
      </table>
    
      <?php 
        #display pagination
        echo $this->projectsPagination;
      ?>
    </form>  