<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addScript($this->baseurl."/components/com_curate/js/ajax.js", 'text/javascript');
?>

<div class="contentpaneopen heading">
  <div class="content-header">
	<h2 class="contentheading">NEES Data Curation</h2>
  </div>
  <p class="buttonheadings">
  </p>
</div>

<p>
 Projects (Uncurated)
</p>

<div class="main section">
  <div class="aside" style="padding-top: 0em">
    <form id="frmProjectSearch" method="get"
    	  action="/curate?view=search&format=ajax"
    	  style="margin: 0px; padding: 0px;">
     
      <fieldset>
		<label>
			Search by:
			<select name="searchby">
				<option selected="selected" value="name">Name</option>
				<option value="keyword">Keyword</option>
				<option value="title">Title</option>
			</select>
		</label>
		<label>
			Search:
			<input type="text" value="" name="searchTerm">
		</label>
		<input type="button" value="GO" onClick="getMootoolsForm('frmProjectSearch', 'listings', 'click')"/>
	  </fieldset>
	</form>
  </div>
  
  <div id="listings" class="subject">
    <form id="frmProjects" action="/curate?view=default&format=ajax" method="get">
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
    	      <tr class="<?php echo $sClass; ?>">
    	        <td><a href="/curate/project/<?php echo $oProject["PROJID"] ?>"><?php echo $oProject["NAME"] ?></a></td>
    	        <td><?php echo $oProject["TITLE"]; ?></td>
    	        <td><a href="mailto:<?php echo $oProject["CONTACT_EMAIL"]; ?>"><?php echo $oProject["CONTACT_NAME"] ?></a></td>
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
  </div>
</div>
