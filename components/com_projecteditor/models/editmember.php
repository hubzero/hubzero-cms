<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('base.php');
require_once 'lib/data/AuthorizationPeer.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/Project.php';

class ProjectEditorModelEditMember extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }
  
  /**
   * 
   *
   */
  public function getMembersByProjectId($p_iProjectId){
    return PersonPeer::findMembersForEntity($p_iProjectId, 1);
  }
  
  public function findMembersForEntityWithPagination($p_iProjectId, $p_iEntityId=1, $p_iLowerLimit=0, $p_iUpperLimit=25){
    return PersonPeer::findMembersForEntityWithPagination($p_iProjectId, $p_iEntityId, $p_iLowerLimit, $p_iUpperLimit);
  }
  
  public function findMembersForEntityCount($p_iProjectId){
    return PersonPeer::findMembersForEntityCount($p_iProjectId, 1);
  }

  /**
   *
   * @param Person $p_oPerson
   * @param Project $p_oProject
   * @return bool
   */
  public function shouldDisableRevocation($p_oPerson, $p_oProject) {
    if(!$p_oPerson) return true;

    $lastPersonWithFullPremissions = AuthorizationPeer::findLastPersonWithFullPermissions($p_oProject);

    if (is_null($lastPersonWithFullPremissions)) return false;
    return ($lastPersonWithFullPremissions == $p_oPerson->getId());
  }

  /**
   *
   * @param Project $p_oProject
   * @param Person $p_oInvitedPerson
   * @return <type>
   */
  public function inviteWarehouseMemberToHubGroup($p_oProject, $p_oInvitedPerson) {
    ximport('Hubzero_Group');
    ximport('xgroup');

    $strErrorArray = array();

    // Set the page title
    $title  = $p_oProject->getNickname() +": INVITE";

    $document =& JFactory::getDocument();
    $document->setTitle( $title );

    // Check if they're logged in
    $juser =& JFactory::getUser();
    if ($juser->get('guest')) {
      $this->login( $title );
      return;
    }

    $strGroupCn = str_replace("-",  "_",  $p_oProject->getName());
    $strGroupCn = strtolower(trim($strGroupCn));

    // Load the group page
    $group = new XGroup();
    $group->select( $strGroupCn );

    // Ensure we found the group info
    if (!$group || !$group->get('gidNumber')) {
      array_push($strErrorArray, JText::_('GROUPS_NO_GROUP_FOUND'));
    }

    if(empty($strErrorArray)){
      // Incoming
      $process = 1;
      $logins = $p_oInvitedPerson->getUserName();
      $msg = "Please join the NEEShub group ".$p_oProject->getNickname() ." (".$p_oProject->getName().").  You can collaborate with other members of the project.";
      $return = "";

      $invitees = array();
      $inviteemails = array();
      $apps = array();
      $mems = array();
      $registeredemails = array();

      $database =& JFactory::getDBO();

      // Get all the group's managers
      $members = $group->get('members');
      $applicants = $group->get('applicants');

      // Explod the string of logins/e-mails into an array
      $la = explode(',',$logins);
      foreach ($la as $l) {
        // Trim up the content
        $l = trim($l);

        // Check if it's an e-mail address
        if (eregi("^[_\.\%0-9a-zA-Z-]+@([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$", $l)) {
          // Try to find an account that might match this e-mail
          $database->setQuery("SELECT u.id FROM #__users AS u WHERE u.email='". $l ."' OR u.email LIKE '".$l."\'%' LIMIT 1;");
          $uid = $database->loadResult();
          if (!$database->query()) {
            $this->setError( $database->getErrorMsg() );
          }

          // If we found an ID, add it to the invitees list
          if ($uid) {
            $invitees[] = $uid;
          }

          $inviteemails[] = $l;
          $registeredemails[] = $l;
        } else {
          // Retrieve user's account info
          $user = JUser::getInstance($l);

          // Ensure we found an account
          if (is_object($user)) {
            $uid = $user->get('id');
            if (!in_array($uid,$members)) {
              if (in_array($uid,$applicants)) {
                $apps[] = $uid;
                $mems[] = $uid;
              } else {
                $invitees[] = $uid;
                $inviteemails[] = $user->get('email');
                $registeredemails[] = $user->get('email');
              }
            }
          }
        }
      }//end for

      // Add the users to the invitee list and save
      /*
      $group->remove('applicants', $apps );
      $group->add('members', $mems );
      $group->add('invitees', $invitees );
      $group->update();
      */

      // Log the sending of invites
      foreach ($invitees as $invite) {
        $log = new XGroupLog( $database );
        $log->gid = $group->get('gidNumber');
        $log->uid = $invite;
        $log->timestamp = date( 'Y-m-d H:i:s', time() );
        $log->action = 'membership_invites_sent';
        $log->actorid = $juser->get('id');
//        if (!$log->store()) {
//          $this->setError( $log->getError() );
//        }
      }//end foreach

      // Get and set some vars
      $xhub =& XFactory::getHub();
      $jconfig =& JFactory::getConfig();

      // Build the "from" info for e-mails
      $from = array();
      $from['name']  = $jconfig->getValue('config.sitename').' '.JText::_(strtoupper($this->_name));
      $from['email'] = $jconfig->getValue('config.mailfrom');

      // Message subject
      $subject = JText::sprintf('GROUPS_SUBJECT_INVITE', $group->get('cn'));

      // Message body
      $eview = new JView( array('name'=>'emails','layout'=>'invite') );
      $eview->option = "groups";
      $eview->hubShortName = $jconfig->getValue('config.sitename');
      $eview->juser = $juser;
      $eview->group = $group;
      $eview->msg = $msg;
      $message = $eview->loadTemplate();
      $message = str_replace("\n", "\r\n", $message);

      foreach ($inviteemails as $mbr) {
        if (!in_array($mbr, $registeredemails)) {
          $email .= JText::sprintf('GROUPS_PLEASE_REGISTER', $jconfig->getValue('config.sitename'), $juri->base() . 'register')."\r\n\r\n";
        }

        // Send the e-mail
        /*
        if (!$this->email($mbr, $jconfig->getValue('config.sitename').' '.$subject, $message, $from)) {
          $this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED').' '.$mbr );
        }
        */
      }

      // Send the message
      JPluginHelper::importPlugin( 'xmessage' );
      $dispatcher =& JDispatcher::getInstance();
      /*
      if (!$dispatcher->trigger( 'onSendMessage', array( 'groups_invite', $subject, $message, $from, $invitees, "groups" ))) {
        $this->setError( JText::_('GROUPS_ERROR_EMAIL_INVITEE_FAILED') );
      }
      */
    }
  }

  public function email($email, $subject, $message, $from) {
     if ($from) {
        $args = "-f '" . $from['email'] . "'";
        $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/plain; charset=utf-8\n";
        $headers .= 'From: ' . $from['name'] .' <'. $from['email'] . ">\n";
        $headers .= 'Reply-To: ' . $from['name'] .' <'. $from['email'] . ">\n";
        $headers .= "X-Priority: 3\n";
        $headers .= "X-MSMail-Priority: High\n";
        $headers .= 'X-Mailer: '. $from['name'] ."\n";
        if (mail($email, $subject, $message, $headers, $args)) {
           return true;
        }
    }
    return false;
  }

  /**
   *
   * @param Project $p_oProject
   * @param Person $p_oInvitedPerson
   */
  public function addWarehouseMemberToHubGroup($p_oProject, $p_oInvitedPerson){
    ximport('Hubzero_Group');
    ximport('xgroup');

    $strErrorArray = array();

    //get invitee hub id
    $oInvitedJuser =& JFactory::getUser($p_oInvitedPerson->getUserName());
    $iGroupMembershipIdArray = array($oInvitedJuser->get('id'));

    //get group cn
    $strGroupCn = str_replace("-",  "_",  $p_oProject->getName());
    $strGroupCn = strtolower(trim($strGroupCn));

    // Load the group
    $group = new XGroup();
    $group->select( $strGroupCn );

    //ensure we have a group
    if (!$group || !$group->get('gidNumber')) {
      array_push($strErrorArray, JText::_('GROUPS_NO_GROUP_FOUND'));
    }

    if(empty($strErrorArray)){
      $group->add('members', $iGroupMembershipIdArray);
      $group->save();
    }
  }//end addWarehouseMemberToHubGroup
}

?>