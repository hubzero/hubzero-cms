<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<span class="resultsKeyword">File By Type</span>
<hr/>
<table style="border: 0px;">
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/chart_bar.png" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Chart&path=<?php echo $strCurrentPath; ?>">Charts (<?php echo $iChartCount; ?>)</a></td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-tool.gif" /></td>
    <!--<td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=DataFile&path=<?php echo $strCurrentPath; ?>">Data Files (<?php echo $iDataFileCount; ?>)</a></td>-->
    <td>
      <input type="hidden" id="dataOpen" name="dataOpen" value="0"/>
      <a href="javascript:void(0);" onClick="displayVideoTypes('dataOpen', 'dataOptions');">Data Files (<? echo $iDataFileCount; ?>)</a>
      <div id="dataOptions" style="display: none">
        <span class="warehouseMarginLeft"><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=DataFile&tool=inDEED&path=<?php echo $strCurrentPath; ?>">inDEED (<?php echo $iInDeedDataFileCount; ?>)</a></span><br>
        <!--
        <span class="warehouseMarginLeft"><a href="#">Frame Captures (<?php //echo $iFrameCount; ?>)</a></span>
        -->
      </div>
    </td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-compass.gif" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Drawing&path=<?php echo $strCurrentPath; ?>">Drawings (<?php echo $iDrawingCount; ?>)</a></td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/camera.png" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Photo&path=<?php echo $strCurrentPath; ?>">Photos (<?php echo $iPhotoCount; ?>)</a></td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-presentation.gif" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Presentation&path=<?php echo $strCurrentPath; ?>">Presentations (<?php echo $iPresentationCount; ?>)</a></td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-publication.gif" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Publication&path=<?php echo $strCurrentPath; ?>">Publications (<?php echo $iPublicationCount; ?>)</a></td>
  </tr>
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-series.gif" /></td>
    <td><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Report&path=<?php echo $strCurrentPath; ?>">Reports (<?php echo $iReportCount; ?>)</a></td>
  </tr>
  <!--
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-learningmodule.gif" /></td>
    <td><a href="javascript:void(0);">Thesis (0)</a></td>
  </tr>
  -->
  <tr>
    <td width="18"><img src="/templates/fresh/images/icons/fc-animation.gif" /></td>
    <td>
        <input type="hidden" id="videoOpen" name="videoOpen" value="0"/>
        <a href="javascript:void(0);" onClick="displayVideoTypes('videoOpen', 'videoOptions');">Videos (<? echo $iVideoCount; ?>)</a>
        <div id="videoOptions" style="display: none">
            <span class="warehouseMarginLeft"><a href="/warehouse/filebrowser/<?php echo $iProjectId;?>?type=Movie&path=<?php echo $strCurrentPath; ?>">Movies (<?php echo $iMovieCount; ?>)</a></span><br>
            <!--
            <span class="warehouseMarginLeft"><a href="#">Frame Captures (<?php //echo $iFrameCount; ?>)</a></span>
            -->
        </div>
    </td>
  </tr>
</table>

