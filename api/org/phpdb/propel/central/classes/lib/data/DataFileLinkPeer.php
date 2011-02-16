<?php

  // include base peer class
  require_once 'lib/data/om/BaseDataFileLinkPeer.php';

  // include object class
  include_once 'lib/data/DataFileLink.php';


/**
 * Skeleton subclass for performing query and update operations on the 'DATA_FILE_LINK' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class DataFileLinkPeer extends BaseDataFileLinkPeer {

	public static function findRepetitionDataFileLinksByExperiment($p_iExperimentId){
	  $oDataFileLinkArray = array();

	  $oCriteria = new Criteria(); 
	  $oCriteria->add(self::REP_ID, 0, Criteria::NOT_EQUAL);
	  $oCriteria->add(self::EXP_ID, $p_iExperimentId);

	  return parent::doSelect($oCriteria);

	  return $oDataFileLinkArray;
	}

	public static function findProjectAndExperimentByRepetition($p_iRepetitionId){
	  $oReturnArray = array();

	  $strQuery = "select distinct proj_id, exp_id
	  			   from data_file_link
	  			   where rep_id=?";

	  $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iRepetitionId);
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
  	    $oReturnArray['PROJ_ID'] = $oResultSet->getInt("PROJ_ID");
  	    $oReturnArray['EXP_ID'] = $oResultSet->getInt("EXP_ID");
      }

      return $oReturnArray;
	}

  public static function findDistinctExperimentsByDirectory($p_strDirectory, $p_iProjectId, $p_iHideExperimentIdArray){
    $strReturnArray = array();

    $strQuery = "select distinct dfl.exp_id, e.name,e.title
                from data_file_link dfl
                inner join experiment e on dfl.exp_id = e.expid
                inner join data_file df on dfl.id = df.id
                left join document_format f on f.document_format_id = df.document_format_id
                where dfl.proj_id = ?
                  and df.path like ?
                  and df.deleted=?
                  and df.directory=?
                  and f.mime_type not like 'image/%'
                  and dfl.deleted=? ";

    if(!empty ($p_iHideExperimentIdArray)){
      $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
      $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
    }
    $strQuery .= "order by dfl.exp_id";

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    $oStatement->setString(2, "%/".$p_strDirectory."%");
    $oStatement->setInt(3, 0);
    $oStatement->setInt(4, 0);
    $oStatement->setInt(5, 0);

    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultSet->next()){
      $strExperimentArray = array();
      $strExperimentArray['EXP_ID'] = $oResultSet->getInt("EXP_ID");
      $strExperimentArray['NAME'] = $oResultSet->getString("NAME");
      $strExperimentArray['TITLE'] = $oResultSet->getString("TITLE");
      array_push($strReturnArray, $strExperimentArray);
    }

    return $strReturnArray;
  }

  public static function findDistinctTrialsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iHideExperimentIdArray){
    $strReturnArray = array();

    $strQuery = "select distinct dfl.trial_id, t.name
                from data_file_link dfl
                inner join trial t on dfl.trial_id = t.trialid
                inner join data_file df on dfl.id = df.id
                left join document_format f on f.document_format_id = df.document_format_id
                where dfl.proj_id = ?
                  and dfl.exp_id = ?
                  and df.path like ?
                  and df.deleted=?
                  and df.directory=?
                  and f.mime_type not like 'image/%'
                  and dfl.deleted=? ";

    if(!empty ($p_iHideExperimentIdArray)){
      $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
      $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
    }
    $strQuery .= "order by dfl.trial_id";

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    $oStatement->setInt(2, $p_iExperimentId);
    $oStatement->setString(3, "%/".$p_strDirectory."%");
    $oStatement->setInt(4, 0);
    $oStatement->setInt(5, 0);
    $oStatement->setInt(6, 0);

    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultSet->next()){
      $strTrialArray = array();
      $strTrialArray['TRIAL_ID'] = $oResultSet->getInt("TRIAL_ID");
      $strTrialArray['NAME'] = $oResultSet->getString("NAME");
      array_push($strReturnArray, $strTrialArray);
    }

    return $strReturnArray;
  }

  public static function findDistinctRepetitionsByDirectory($p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iHideExperimentIdArray){
    $strReturnArray = array();

    $strQuery = "select distinct dfl.rep_id, r.name
                from data_file_link dfl
                inner join repetition r on dfl.rep_id = r.repid
                inner join data_file df on dfl.id = df.id
                left join document_format f on f.document_format_id = df.document_format_id
                where dfl.proj_id = ?
                  and dfl.exp_id = ?
                  and dfl.trial_id = ?
                  and df.path like ?
                  and df.deleted=?
                  and df.directory=?
                  and f.mime_type not like 'image/%'
                  and dfl.deleted=? ";

    if(!empty ($p_iHideExperimentIdArray)){
      $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
      $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
    }
    $strQuery .= "order by dfl.rep_id";

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    $oStatement->setInt(2, $p_iExperimentId);
    $oStatement->setInt(3, $p_iTrialId);
    $oStatement->setString(4, "%/".$p_strDirectory."%");
    $oStatement->setInt(5, 0);
    $oStatement->setInt(6, 0);
    $oStatement->setInt(7, 0);

    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultSet->next()){
      $strRepetitionArray = array();
      $strRepetitionArray['REP_ID'] = $oResultSet->getInt("REP_ID");
      $strRepetitionArray['NAME'] = $oResultSet->getString("NAME");
      array_push($strReturnArray, $strRepetitionArray);
    }

    return $strReturnArray;
  }

  public static function findDistinctExperiments($p_iProjectId, $p_iHideExperimentIdArray, $p_strOpeningTool="", $p_strUsageType=""){
    $strReturnArray = array();

    $strQuery = "select distinct dfl.exp_id, e.name, e.title
                from data_file_link dfl, experiment e, data_file df
                where dfl.proj_id = ?
                  and dfl.exp_id = e.expid
                  and dfl.id = df.id ";

    if(!empty ($p_iHideExperimentIdArray)){
      $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
      $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
    }

    if(strlen($p_strOpeningTool)>0){
      $strQuery .= "and df.opening_tool= ? ";
    }

    if(strlen($p_strUsageType)>0){
      $strQuery .= "and df.usage_type_id in (select id from entity_type where n_table_name like ?) ";
    }
    $strQuery .= "order by dfl.exp_id";

    //echo $strQuery."<br>";

    $iIndex = 2;

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    if(strlen($p_strOpeningTool)>0){
      $oStatement->setString($iIndex, $p_strOpeningTool);
      ++$iIndex;
    }
    if(strlen($p_strUsageType)>0){
      $oStatement->setString($iIndex, $p_strUsageType."%");
    }
    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while($oResultSet->next()){
      $strExperimentArray = array();
      $strExperimentArray['EXP_ID'] = $oResultSet->getInt("EXP_ID");
      $strExperimentArray['NAME'] = $oResultSet->getString("NAME");
      $strExperimentArray['TITLE'] = $oResultSet->getString("TITLE");
      array_push($strReturnArray, $strExperimentArray);
    }

    return $strReturnArray;
  }

	public static function findDistinctTrials($p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId, $p_strOpeningTool="", $p_strUsageType=""){
	  $strReturnArray = array();

	  $strQuery = "select distinct dfl.trial_id, t.name
				from data_file_link dfl, trial t, data_file df
				where dfl.proj_id = ?
				  and dfl.exp_id = ?
				  and dfl.trial_id = t.trialid
				  and dfl.id = df.id ";

          if(!empty ($p_iHideExperimentIdArray)){
            $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
            $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
          }

	  if(strlen($p_strOpeningTool)>0){
		$strQuery .= "and df.opening_tool= ? ";
  	  }

  	  if(strlen($p_strUsageType)>0){
		$strQuery .= "and df.usage_type_id=(select id from entity_type where n_table_name=?) ";
  	  }
	  $strQuery .= "order by dfl.trial_id";

	  //echo $strQuery."<br>";

	  $iIndex = 3;

	  $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iProjectId);
      $oStatement->setInt(2, $p_iExperimentId);
      if(strlen($p_strOpeningTool)>0){
      	$oStatement->setString($iIndex, $p_strOpeningTool);
      	++$iIndex;
      }
      if(strlen($p_strUsageType)>0){
      	$oStatement->setString($iIndex, $p_strUsageType);
      }
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$strExperimentArray = array();
  	    $strExperimentArray['TRIAL_ID'] = $oResultSet->getInt("TRIAL_ID");
  	    $strExperimentArray['NAME'] = $oResultSet->getString("NAME");
  	    array_push($strReturnArray, $strExperimentArray);
      }

      return $strReturnArray;
	}

    public static function findDistinctRepetitions($p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_strOpeningTool="", $p_strUsageType=""){
      $strReturnArray = array();

      $strQuery = "select distinct dfl.rep_id, r.name
				from data_file_link dfl, repetition r, data_file df
				where dfl.proj_id = ?
				  and dfl.exp_id = ?
				  and dfl.trial_id = ?
				  and dfl.rep_id = r.repid
				  and dfl.id = df.id ";

      if(!empty ($p_iHideExperimentIdArray)){
        $strHideExperimentIds = implode(",", $p_iHideExperimentIdArray);
        $strQuery .= " and dfl.exp_id not in ($strHideExperimentIds) ";
      }

      if(strlen($p_strOpeningTool)>0){
            $strQuery .= "and df.opening_tool= ? ";
      }

      if(strlen($p_strUsageType)>0){
            $strQuery .= "and df.usage_type_id=(select id from entity_type where n_table_name=?) ";
      }
      $strQuery .= "order by dfl.rep_id";

      //echo $strQuery."<br>";

      $iIndex = 4;

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iProjectId);
      $oStatement->setInt(2, $p_iExperimentId);
      $oStatement->setInt(3, $p_iTrialId);
      if(strlen($p_strOpeningTool)>0){
      	$oStatement->setString($iIndex, $p_strOpeningTool);
      	++$iIndex;
      }
      if(strlen($p_strUsageType)>0){
      	$oStatement->setString($iIndex, $p_strUsageType);
      }
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$strExperimentArray = array();
  	    $strExperimentArray['REP_ID'] = $oResultSet->getInt("REP_ID");
  	    $strExperimentArray['NAME'] = $oResultSet->getString("NAME");
  	    array_push($strReturnArray, $strExperimentArray);
      }

      return $strReturnArray;
	}


  public static function findDataFileByUsage($p_strUsage, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0) {
        $oReturnArray = array();

        $strQuery = "SELECT *
                     FROM (
                       select df.id, df.name, df.description, df.path, df.title,
                       dfl.proj_id, dfl.exp_id, dfl.trial_id, dfl.rep_id,
                       e.name as e_name, t.name as t_name, r.name as r_name,
                       e.title as e_title, f.default_extension, row_number()
                       OVER (ORDER BY df.path, df.name) as rn
                       from data_file df
                         inner join data_file_link dfl on df.id = dfl.id
                             left join experiment e on dfl.exp_id = e.expid
                             left join trial t on dfl.trial_id = t.trialid
                             left join repetition r on dfl.rep_id = r.repid
                             left join document_format f on f.document_format_id = df.document_format_id
                       where df.usage_type_id in (select id from entity_type where n_table_name like ?)
                         and df.deleted=?
                         and dfl.deleted=?";

        if ($p_iProjectId > 0

            )$strQuery .= " and dfl.proj_id=?";
        if ($p_iExperimentId > 0

            )$strQuery .= " and dfl.exp_id=?";
        if ($p_iTrialId > 0

            )$strQuery .= " and dfl.trial_id=?";
        if ($p_iRepetitionId > 0

            )$strQuery .= " and dfl.rep_id=?";

        $strQuery .= "
  				)
				WHERE rn BETWEEN ? AND ?";

        $iIndex = 4;

        //echo $strQuery."<br>";

        $oConnection = Propel::getConnection();
        $oStatement = $oConnection->prepareStatement($strQuery);
        $oStatement->setString(1, $p_strUsage."%");
        $oStatement->setInt(2, 0);
        $oStatement->setInt(3, 0);
        if ($p_iProjectId > 0) {
            $oStatement->setInt($iIndex, $p_iProjectId);
            ++$iIndex;
        }

        if ($p_iExperimentId > 0) {
            $oStatement->setInt($iIndex, $p_iExperimentId);
            ++$iIndex;
        }

        if ($p_iTrialId > 0) {
            $oStatement->setInt($iIndex, $p_iTrialId);
            ++$iIndex;
        }

        if ($p_iRepetitionId > 0) {
            $oStatement->setInt($iIndex, $p_iRepetitionId);
            ++$iIndex;
        }

        $oStatement->setInt($iIndex, $p_iLowerLimit);
        ++$iIndex;

        $oStatement->setInt($iIndex, $p_iUpperLimit);
        $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
        while ($oResultSet->next()) {
            $strFileArray['ID'] = $oResultSet->getInt("ID");
            array_push($oReturnArray, $strFileArray['ID']);
        }

        return self::retrieveByPKs($oReturnArray);
    }

    public static function getCountByProjectId($p_iProjectId, $p_bDirectory=1){
      $iCount = 0;
      $strQuery = "select count(dfl.proj_id) as TOTAL
                   from data_file_link dfl, data_file df
		   where dfl.proj_id = ?
		     and df.path not like ?
                     and df.directory = ?
                     and dfl.exp_id = ?
                     and dfl.trial_id = ?
                     and dfl.rep_id = ?
                     and dfl.id = df.id ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iProjectId);
      $oStatement->setString(2, "%.Generated_Pics%");
      $oStatement->setInt(3, $p_bDirectory);
      $oStatement->setInt(4, 0); //exp_id
      $oStatement->setInt(5, 0); //trial_id
      $oStatement->setInt(6, 0); //rep_id
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$iCount = $oResultSet->getInt("TOTAL");
      }

      return $iCount;
    }

    public static function getCountByExperimentId($p_iExperimentId, $p_bDirectory=1){
      $iCount = 0;
      $strQuery = "select count(dfl.exp_id) as TOTAL
                   from data_file_link dfl, data_file df
		   where dfl.exp_id = ?
		     and df.path not like ?
                     and df.directory = ?
                     and dfl.id = df.id ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iExperimentId);
      $oStatement->setString(2, "%.Generated_Pics%");
      $oStatement->setInt(3, $p_bDirectory);
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$iCount = $oResultSet->getInt("TOTAL");
      }

      return $iCount;
    }

    public static function getCountByTrialId($p_iTrialId, $p_bDirectory=1){
      $iCount = 0;
      $strQuery = "select count(dfl.trial_id) as TOTAL
                   from data_file_link dfl, data_file df
		   where dfl.trial_id = ?
		     and df.path not like ?
                     and df.directory = ?
                     and dfl.id = df.id ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iTrialId);
      $oStatement->setString(2, "%.Generated_Pics%");
      $oStatement->setInt(3, $p_bDirectory);
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$iCount = $oResultSet->getInt("TOTAL");
      }

      return $iCount;
    }

    public static function getCountByRepetitionId($p_iRepetitionId, $p_bDirectory=1){
      $iCount = 0;

      $strQuery = "select count(dfl.rep_id) as TOTAL
                   from data_file_link dfl, data_file df
		   where dfl.rep_id = ?
		     and df.path not like ?
                     and df.directory = ?
                     and dfl.id = df.id ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iRepetitionId);
      $oStatement->setString(2, "%.Generated_Pics%");
      $oStatement->setInt(3, $p_bDirectory);
      $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
      while($oResultSet->next()){
      	$iCount = $oResultSet->getInt("TOTAL");
      }

      return $iCount;
    }

} // DataFileLinkPeer
