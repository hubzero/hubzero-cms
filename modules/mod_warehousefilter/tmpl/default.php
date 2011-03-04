<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>


<div id="search-filter-lhs">
  <?php
    $oProjectTypeFilterArray = $_SESSION[Search::PROJECT_TYPE_FILTER];
    $iProjectTypeCount = count($oProjectTypeFilterArray);
    if($iProjectTypeCount > 0){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">Project Type</span><br>
      <div id="search-filter-ptypes">
      <?php
        foreach($oProjectTypeFilterArray as $iProjectTypeIndex=>$strProjectTypeArray){
      ?>
          <a href="/warehouse/filter?projectType=<?php echo urlencode($strProjectTypeArray["MONIKER"]); ?>"><?php echo $strProjectTypeArray["MONIKER"] ." (".$strProjectTypeArray["TOTAL"].")"; ?></a>
      <?
          if($iProjectTypeIndex < $iProjectTypeCount){
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
   }
  ?>

  <?php
    $oNeesSiteFilterArray = $_SESSION[Search::NEES_SITE_FILTER];
    $iNeesSiteCount = $_REQUEST[Search::NEES_SITE_COUNT];
    if($iNeesSiteCount > 0){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">NEES Site</span><br>
      <div id="search-filter-sites">
      <?php
        foreach($oNeesSiteFilterArray as $iNeesSiteIndex=>$strNeesSiteArray){
        ?>
          <a href="/warehouse/filter?neesSite=<?php echo urlencode($strNeesSiteArray["ID"]); ?>"><?php echo $strNeesSiteArray["MONIKER"] ." (".$strNeesSiteArray["TOTAL"].")"; ?></a>
        <?
          if($iNeesSiteIndex > 2){
            if($iNeesSiteCount > 4){
            ?>
              <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=site&action=show&format=ajax&target=search-filter-sites&field=neesSite', 'search-filter-sites');">more...</a>
            <?php
            }
            break;
          }else{
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
    }
  ?>

  <?php
    $oSponsorFilterArray = $_SESSION[Search::SPONSORS_FILTER];
    $iSponsorCount = $_REQUEST[Search::SPONSORS_COUNT];
    if($iSponsorCount > 0){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">Sponsor</span><br>
      <div id="search-filter-sponsors">
      <?php
        foreach($oSponsorFilterArray as $iSponsorIndex=>$strSponsorArray){
        ?>
          <a href="/warehouse/filter?funding=<?php echo urlencode($strSponsorArray["MONIKER"]); ?>"><?php echo $strSponsorArray["MONIKER"] ." (".$strSponsorArray["TOTAL"].")"; ?></a>
        <?
          if($iSponsorIndex > 2){
            if($iSponsorCount > 4){
            ?>
              <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=sponsor&action=show&format=ajax&target=search-filter-sponsors&field=funding', 'search-filter-sponsors');">more...</a>
            <?php
            }
            break;
          }else{
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
  }
  ?>

  <?php
    $oResearchTypeFilterArray = $_SESSION[Search::NEES_RESEARCH_TYPES_FILTER];
    $iResearchTypeCount = count($oResearchTypeFilterArray);
    if($iResearchTypeCount > 0){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">NEES Research Type</span><br>
      <div id="search-filter-research">
      <?php
        foreach($oResearchTypeFilterArray as $iResearchTypeIndex=>$strResearchTypeArray){
        ?>
          <a href="/warehouse/filter?researchType=<?php echo urlencode($strResearchTypeArray["ID"]); ?>"><?php echo $strResearchTypeArray["MONIKER"] ." (".$strResearchTypeArray["TOTAL"].")"; ?></a>
        <?
          if($iResearchTypeIndex > 2){
            if($iResearchTypeCount > 4){
            ?>
              <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=researchtype&action=show&format=ajax&target=search-filter-research&field=researchType', 'search-filter-research');">more...</a>
            <?php
            }
            break;
          }else{
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
  }
  ?>

  <?php
        $oMaterialTypeFilterArray = $_SESSION[Search::MATERIAL_TYPES_FILTER];
        $iMaterialTypeCount = $_REQUEST[Search::MATERIAL_TYPES_COUNT];
        if($iMaterialTypeCount){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">Material Type</span><br>
      <div id="search-filter-materials">
      <?php
        foreach($oMaterialTypeFilterArray as $iMaterialTypeIndex=>$strMaterialTypeArray){
        ?>
          <a href="/warehouse/filter?materialType=<?php echo urlencode($strMaterialTypeArray["MONIKER"]); ?>"><?php echo $strMaterialTypeArray["MONIKER"] ." (".$strMaterialTypeArray["TOTAL"].")"; ?></a>
        <?
          if($iMaterialTypeIndex > 2){
            if($iMaterialTypeCount > 4){
            ?>
              <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=material&action=show&format=ajax&target=search-filter-materials&field=materialType', 'search-filter-materials');">more...</a>
            <?php
            }
            break;
          }else{
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
  }
  ?>

  <?php
    $oPrincipleInvestigatorFilterArray = $_SESSION[Search::PRINCIPLE_INVESTIGATORS_FILTER];
    $iPrincipleInvestigatorCount = $_REQUEST[Search::PRINCIPLE_INVESTIGATORS_COUNT];
    if($iPrincipleInvestigatorCount){
  ?>
  <ul>
    <li class="search-filter-icon-header-li">
      <span class="search-filter-header-span">Principle Investigator</span><br>
      <div id="search-filter-pis">
      <?php
        foreach($oPrincipleInvestigatorFilterArray as $iPrincipleInvestigatorIndex=>$strPrincipleInvestigatorArray){
        ?>
          <a href="/warehouse/filter?member=<?php echo $strPrincipleInvestigatorArray["ID"]; ?>"><?php echo $strPrincipleInvestigatorArray["MONIKER"] ." (".$strPrincipleInvestigatorArray["TOTAL"].")"; ?></a>
        <?
          if($iPrincipleInvestigatorIndex > 2){
            if($iPrincipleInvestigatorCount > 4){
            ?>
              <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=pi&action=show&format=ajax&target=search-filter-pis&field=member', 'search-filter-pis');">more...</a>
            <?php
            }
            break;
          }else{
            echo "<br>";
          }
        }
      ?>
      </div>
    </li>
  </ul>
  <?php
  }
  ?>
</div>