<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
?>

<div class="innerwrap>
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>
      <div id="title" style="padding-bottom:1em;">
        <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
      </div>
  
  	  <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
      <div class="aside">
        
      </div>
      <div class="subject">
        <div id="members" style="padding-top:1em;">
          <table id="members-list" cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
            <?php 
              $oMembersArray = $_REQUEST[PersonPeer::TABLE_NAME];
              foreach($oMembersArray as $oMember){?>
                <tr>
                  <td class="photo" width="60"><img width="50" height="50" alt="Photo for <?php echo $oMember['FIRST_NAME'] ." ". $oMember['LAST_NAME']; ?>" src="/components/com_members/images/profile_thumb.gif"></td>
				  <td><span class="name"><a href="#/members/<?php echo $oMember['ID']; ?>"><?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?></a></span><BR><span class="status"><?php echo $oMember['ROLE']; ?></span><br></td>
				  <td class="remove-member"><!-- <a href="/collaborate/groups/neesit/members?task=remove&amp;users[0]=1078" class="remove tooltips">Remove</a> --></td>
				  <td class="promote-member"><!-- <a href="/collaborate/groups/neesit/members?task=promote&amp;users[0]=1078" class="promote tooltips">Promote</a>--></div></td>
				  <td class="message-member"> </td>
                </tr>	
              <?php 
              }
            ?>
          </table>
        </div>
      </div>
      
      <div id="membersFooter">
        <form id="frmMembersFooter">
        <?php  
          jimport('joomla.html.pagination');
              
          $lim   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 25, 'int'); //I guess getUserStateFromRequest is for session or different reasons
		  $lim0  = JRequest::getVar('limitstart', 0, '', 'int');
		  $iCount = $this->iMemberCount;
			  
		  $pageNav = new JPagination( $iCount, $lim0, $lim );
		  
		  echo ViewHtml::fixPaginationLinks("warehouse", $_SERVER["REQUEST_URI"], $pageNav->getListFooter());
        ?>
        </form>
      </div>
    </div>
    <div class="clear"></div>
  </div>  
</div>



