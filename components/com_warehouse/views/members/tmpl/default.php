<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
?>

<?php
  $strUsername = $this->strUsername;
  $oAuthorizer = Authorizer::getInstance();
  $oAuthorizer->setUser($strUsername);
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <form id="frmResults" style="margin:0px;padding:0px;">

      <?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>
        <div id="title" style="padding-bottom:1em;">
          <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
        </div>
  
        <?php echo TabHtml::getSearchFormWithAction( "frmResults", "/warehouse/find" ); ?>
        <?php echo $this->strTabs; ?>
        <div class="aside">
        </div>

        <div class="subject">
          <?php if($oAuthorizer->canView($oProject)){ ?>
          <div id="members" style="padding-top:1em;">
            <table id="members-list" cellpadding="1" cellspacing="1">
            <thead>
              <th></th>
              <th>Name</th>
              <th>Role</th>
              <th>Email</th>
              <th></th>
            </thead>

            <?php
              $oMembersArray = $_REQUEST[PersonPeer::TABLE_NAME];
              foreach($oMembersArray as $iIndex=>$oMember){
                $strBgColor = "odd";
                if($iIndex%2===0){
                  $strBgColor = "even";
                }
              ?>
                <tr class="<?php echo $strBgColor; ?>">
                  <td class="photo" width="60"><img width="50" height="50" alt="Photo for <?php echo $oMember['FIRST_NAME'] ." ". $oMember['LAST_NAME']; ?>" src="<?php echo $oMember['PICTURE']; ?>"></td>
                  <?php if($oMember['LINK']){ ?>
                    <td><span class="name"><a href="/members/<?php echo $oMember['HUB_ID']; ?>"><?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?></a></span></td>
                  <?php }else{ ?>
                    <td><span class="name"><?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?></span></td>
                  <?php } ?>
                  <td>
                    <?php
                      $oMemberRoleArray = unserialize($oMember['ROLE']);
                      foreach($oMemberRoleArray as $iRoleIndex=>$oRole){
                        echo $oRole->getDisplayName();
                        if($iRoleIndex < sizeof($oMemberRoleArray)-1){
                          echo ", ";
                        }
                      }
                    ?>
                  </td>
                  <td><?php echo $oMember['EMAIL']; ?></td>
                  <td></td>
                </tr>
              <?php
              }//end foreach
            ?>
          </table>
          </div>

          <div id="membersFooter" class="topSpace20">
            <?php echo $this->pagination; ?>
          </div>

          <?php
            }else{?>
            <p class="error">You don't have permission to view this project.</p>
          <?php
          }//end canView
          ?>
        </div>
      </form>
    </div>

  </div>
  <div class="clear"></div>
</div>  




