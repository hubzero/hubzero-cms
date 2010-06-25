<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
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
        <div id="upload" style="padding-top:1em;">
          <input type="button" value="Upload Image"/>
        </div>
        
        <div id="images" style="padding-top:1em;">
          <table id="image-list" cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
            <tr>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060615_mast_TWall_NTW1_Test1.3_S3B_img_200344CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060615_mast_TWall_NTW1_Test1.3_S3B_img_200344CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060616_mast_TWall_NTW1_Test1.3_S4A_img_083228CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060616_mast_TWall_NTW1_Test1.3_S4A_img_083228CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060616_mast_TWall_NTW1_Test1.3_S4A_img_095710CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060616_mast_TWall_NTW1_Test1.3_S4A_img_095710CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S1B_img_103555CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S1B_img_103555CDT-tn.jpg">
	            </a>
              </td>
            </tr>
            <tr>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S3A_img_103707CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S3A_img_103707CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S3B_img_092504CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S3B_img_092504CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S4A_img_081627CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S4A_img_081627CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S4B_img_102203CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060618_mast_TWall_NTW1_Test1.7_S4B_img_102203CDT-tn.jpg">
	            </a>
              </td>
            </tr>
            <tr>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S2B_img_152042CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S2B_img_152042CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S2B_img_165714CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S2B_img_165714CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S4A_img_152204CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S4A_img_152204CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S4A_img_162202CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060619_mast_TWall_NTW1_Test1.1_S4A_img_162202CDT-tn.jpg">
	            </a>
              </td>
            </tr>
            
            <tr>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S2B_img_092656CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S2B_img_092656CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S3A_img_093335CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S3A_img_093335CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S4A_img_093147CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060627_mast_TWall_NTW1_Test1.1_S4A_img_093147CDT-tn.jpg">
	            </a>
              </td>
              <td style="width:25%;padding-top:40px;" align="center">
                <a title="Description" href="/components/com_warehouse/images/prototype/pics/20060628_mast_TWall_NTW1_Test1.1_S4B_img_151308CDT.jpg" rel="lightbox">
	              <img class="thumbima" alt="thumbnail" src="/components/com_warehouse/images/prototype/pics/20060628_mast_TWall_NTW1_Test1.1_S4B_img_151308CDT-tn.jpg">
	            </a>
              </td>
            </tr>
          </table>
        </div>
      </div>
      
      <div id="imagesFooter">
        <form id="frmImagesFooter">
        <?php
          /*  
          jimport('joomla.html.pagination');
              
          $lim   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 25, 'int'); //I guess getUserStateFromRequest is for session or different reasons
		  $lim0  = JRequest::getVar('limitstart', 0, '', 'int');
		  $iCount = $this->iMemberCount;
			  
		  $pageNav = new JPagination( $iCount, $lim0, $lim );
		  echo $pageNav->getListFooter();
		  */
        ?>
        </form>
      </div>
    </div>
    <div class="clear"></div>
  </div>  
</div>



