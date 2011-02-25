<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/general.js", 'text/javascript');
?>

<?php
  $strUsername = $this->strUsername;
  $oAuthorizer = Authorizer::getInstance();
  //$oAuthorizer->setUser($strUsername);

  $oProject = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);
?>

<?php JHTML::_('behavior.modal'); ?>

  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="quickstart">
    <div id="pdfIcon" class="editorInputFloat">
      <img src="/components/com_projecteditor/images/icons/pdf.jpg"/>&nbsp;&nbsp;
    </div>
    <div id="helpdoc" class="editorInputFloat">
      <a href="<?php echo ProjectEditor::QUICK_START_GUIDE?>" target="peQuickStart">Quick Start Guide</a>
    </div>
    <div class="clear"></div>
  </div>

  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>

    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <div id="title" style="padding-bottom:1em;">
        <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
      </div>

      <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
      <!--
      <div class="aside">
      </div>
      -->
      <div class="subject-full">
        <?php
          if(isset($_SESSION["MEMBER_ERRORS"])){
            if(!empty($_SESSION["MEMBER_ERRORS"])){  ?>
              <div id="memberError">
                <p class="error">
                <?php
                $strErrorArray = $_SESSION["MEMBER_ERRORS"];
                foreach($strErrorArray as $strError){
                  echo $strError."<br>";
                }
                ?>
                </p>
              </div>
        <?php
            }
          }
        ?>

        <form id="frmMemberAdd" method="post">
          <input type="hidden" name="projectId" id="projectId" value="<?php echo $oProject->getId(); ?>"/>
          <input type="hidden" name="personId" id="personId" value="0"/>
          <input type="hidden" name="iNewMemberCount" id="iNewMemberCount" value="0"/>

          <?php if($oAuthorizer->canGrant($oProject)): ?>
            <div id="members-add" class="topSpace10">
              <div class="editorInputFloat">
                Add Team Member:
              </div>
              <div class="editorInputFloat" style="margin-left:10px;">
                <input type="text" id="newMember" name="user" style="color: #999999" onfocus="this.style.color='';this.value='';document.getElementById('memberError').innerHTML='';" onkeyup="suggest('/projecteditor/membersearch?format=ajax', 'userSearch', this.value, this.id)" value="Last, First name" autocomplete="off" />
                <div id="userSearch" class="suggestResults"></div>
              </div>
              <div class="editorInputFloat" style="margin-left:10px;">
                <input type="button" value="Enter" onClick="addTeamMember('/warehouse/projecteditor/editmember', 'setupNewMember')" />
              </div>
              <div class="clear"></div>
            </div>
          <?php endif; ?>

          <div id="memberError"></div>

          <div id="members" class="topSpace20">
            <table id="members-list" cellpadding="1" cellspacing="1">
              <thead>
                <th></th>
                <th>Name</th>
                <th><a style="border-bottom:0px;" href="javascript:void(0);" onclick="return false;" class="Tips3" title="Role :: Click Edit.  Add or remove roles."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a> Role</th>
                <th>Email</th>
                <th><a style="border-bottom:0px;" href="javascript:void(0);" onclick="return false;" class="Tips3" title="Permissions :: Click Edit.  Select the user's privileges."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a> Permissions</th>
                <th><a style="border-bottom:0px;" href="javascript:void(0);" onclick="return false;" class="Tips3" title="Experiments :: Click Edit.  Select experiment user can access. Mouseover link to see title."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a> Experiments</th>
                <th></th>
              </thead>

              <!--
              <tr id="setupNewMember" style="background:#FFFFFF">
                <td colspan="7"></td>
              </tr>
              -->

              <?php
                $oMembersArray = $_REQUEST[PersonPeer::TABLE_NAME];
                foreach($oMembersArray as $iIndex=>$oMember){
                  $strBgColor = "odd";
                  if($iIndex%2===0){
                    $strBgColor = "even";
                  }
              ?>
                <tr class="<?php echo $strBgColor; ?>" id="memberIndex-<?php echo $iIndex; ?>">
                  <td class="photo" width="60"><img width="50" height="50" alt="Photo for <?php echo $oMember['FIRST_NAME'] ." ". $oMember['LAST_NAME']; ?>" src="<?php echo $oMember['PICTURE']; ?>"></td>
                  <?php if($oMember['LINK']){ ?>
                    <td><span class="name"><a href="/members/<?php echo $oMember['HUB_ID']; ?>"><?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?></a></span><br>(<?php echo $oMember['USER_NAME'];?>)</td>
                  <?php }else{ ?>
                    <td><span class="name"><?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?></span><br>(<?php echo $oMember['USER_NAME'];?>)</td>
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
                  <td nowrap><?php echo $oMember['PERMISSIONS']; ?></td>
                  <td>
                    <!--<input type="checkbox" checked="" value="1" name="copyToExp"> Access to all<br>-->
                    <?php echo $oMember['EXPERIMENTS']; ?>
                  </td>
                  <td>
                    <?php if($oAuthorizer->canEdit($oProject)): ?>
                      <div id="editMemberButton<?php echo $iIndex; ?>" style="float:left">
                        <input type="button" onclick="document.getElementById('personId').value=<?php echo $oMember['ID']; ?>;editTeamMember('/warehouse/projecteditor/editmember', <?php echo $iIndex; ?>)" value="Edit"/>
                      </div>
                    <?php endif; ?>
                    <?php if($oAuthorizer->canDelete($oProject)): ?>
                      <div id="removeMemberButton<?php echo $iIndex; ?>" style="float:left; margin-left: 10px;">
                        <a href="/warehouse/projecteditor/deletemember?format=ajax&projectId=<?php echo $oProject->getId(); ?>&personId=<?php echo $oMember['ID']; ?>"
                         title="Remove user <?php echo $oMember['LAST_NAME'] .", ". $oMember['FIRST_NAME']; ?>"
                         style="border-bottom: 0px" class="modal">
                          <img alt="" src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/>
                        </a>
                      </div>
                    <?php endif; ?>
                    <div class="clear"></div>
                  </td>
                </tr>
              <?php
              }
              ?>
            </table>
          </div>

          <div id="membersFooter" class="topSpace20">
            <?php echo $this->pagination; ?>
          </div>
        </form>
      </div>

    <!-- Footer was here... -->
    </div>
    <div class="clear"></div>
  </div>



