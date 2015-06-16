<?php
$doc = JFactory::getDocument();
//$doc->addScript("https://maps.googleapis.com/maps/api/js?&sensor=false");
$doc->addScript("https://maps.googleapis.com/maps/api/js?v=3.exp");
$doc->addScript("/components/com_geosearch/assets/js/oms.min.js");
$doc->addScript("/components/com_geosearch/assets/js/geosearch.jquery.js");
?>
	<style>
	  #map_canvas {
		width: 95%;
		min-height: 500px;
		margin: 0 0 2em 0;
		padding: 2em 2em 2em 2em;
	  }
	</style>

<div id="content-header" class="full">
	<h2><?php echo JText::_('COM_GEOSEARCH_TITLE'); ?></h2>
</div>
<div class="main section">
<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="get" id="frm_search">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>
	<div class="aside geosearch">
		<div class="container">
			<h3><?php echo JText::_('COM_GEOSEARCH_FILTER'); ?></h3>
			<fieldset>
				<legend><?php echo JText::_('COM_GEOSEARCH_LIM_RES'); ?></legend>
				<div>
					<div class="key">
						<img src="/components/com_geosearch/assets/img/icn_member2.png" />
						<input type="checkbox" name="resource[]" class="resck" value="member "<?php if (in_array("members",$this->resources)) { echo 'checked="checked"'; }?> /> Members
					</div>
				</div>
				<div>
					<div class="key">
						<img src="/components/com_geosearch/assets/img/icn_job2.png" />
					</div>
					<input type="checkbox" name="resource[]" class="resck" value="job" <?php if (in_array("jobs",$this->resources)) { echo 'checked="checked"'; }?> />
					Jobs
			   </div>

				<div>
					<div class="key">
						<img src="/components/com_geosearch/assets/img/icn_event2.png" />
					</div>
					<input type="checkbox" name="resource[]" class="resck" value="event" <?php if (in_array("events",$this->resources)) { echo 'checked="checked"'; }?> /> Events</div>
				<div>
					<div class="key">
						<img src="/components/com_geosearch/assets/img/icn_org2.png" />
					</div>
					<input type="checkbox" name="resource[]" class="resck" value="organization" <?php if (in_array("organizations",$this->resources)) { echo 'checked="checked"'; }?> /> Organizations </div>
				<div class="clear-right"></div>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('COM_GEOSEARCH_LIM_TAGS'); ?></legend>
				<?php
					if (isset($this->stags))
					{
						$stags = implode(",",$this->stags);
					}
					else
					{
						$stags = "";
					}

					// load tags plugin
					JPluginHelper::importPlugin( 'hubzero' );
					$dispatcher = JDispatcher::getInstance();
					$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags','tags','actags','',$stags)) );
					if (count($tf) > 0)
					{
						echo $tf[0];
					}
					else
					{
						echo '<input type="text" name="tags" value="'. $stags .'" />';
					}
				?>
			</fieldset>

			<fieldset>
				<legend><?php echo JText::_('COM_GEOSEARCH_LIM_LOC'); ?></legend>
				<label class="fieldset">Within:</label>
				<input type="text" name="distance" id="idist" value="<?php //echo $this->distance; ?>" />
				<select name="dist_units">
					<option value="mi">Miles</option>
					<option value="km" <?php if ($this->unit == 'km') echo 'selected="selected"'; ?>>Kilometers</option>
				</select>
				<label class="fieldset">of:</label>
				<input type="text" name="location" id="iloc" value="<?php if ($this->location != "") echo $this->location; ?>" <?php if ($this->location == "") echo "placeholder=\"place, address, or zip\""; ?>/>
			</fieldset>

			<input type="submit" value="<?php echo JText::_('COM_GEOSEARCH_FILTER_BUTTON'); ?>" /> <input type="button" value="Clear" id="clears"/>

			<div class="clear"></div>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">

		<div class="container data-entry">
			<input class="entry-search-submit" type="submit" value="Search">
			<fieldset class="entry-search">
				<legend>Search by Keyword</legend>
				<label for="entry-search-field">Enter keyword or phrase</label>
				<input type="text" name="search" id="entry-search-field" value="<?php echo JRequest::getVar('search', ''); ?>" placeholder="Search by keyword or phrase">
			</fieldset>
		</div>

		<!-- <div id="map_results">Displaying: <span>loading results</span></div> -->
		<div id="map_container">
			<div id="map_canvas"></div>
		</div>

		<br />

			<div class="container hide">
				<div class="container-block">
					<h3><?php echo JText::_('COM_GEOSEARCH_LIST'); ?></h3>
							<div class="list">
							<?php
								if (isset($this->members) && count($this->members) > 0)  {
									foreach ($this->members as $row) {
										if ($row->surname != "") {
											$name = $row->surname.", ".$row->givenName;
										} else {
											$name = $row->name;
										}
							?>
								<div class="list-item">
									<div class="list-content">
										<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->uidNumber); ?>" title="<?php echo $name; ?>"><?php echo $name; ?></a><br />
										 <?php
											$job = $this->ROT->loadType($row->orgtype);
											if ($job) { ?>
											<span class="list-detail"><?php echo " ".$this->ROT->title; ?></span><br />
										 <?php } ?>

										   <div class="member-img">
												<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $row->uidNumber); ?>" title="<?php echo $name; ?>">
													<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto($row->uidNumber, 0); ?>" />
												</a>
											</div>
											<?php
												$user = \Hubzero\User\Profile::getInstance($row->uidNumber);
												if ($user->get('bio'))
												{
													$bio = $user->getBio('parsed');
													echo \Hubzero\Utility\String::truncate($bio, 220);
												}
												$tags = $this->MT->get_tag_cloud(0,0,$row->uidNumber);
												echo ($tags) ? "<br /><span class=\"list-detail\">Interests:</span>".$tags : ""; //:  JText::_('COM_HMAP_NO_TAGS');
											?>
									</div>
									<div class="list-label"><?php echo JText::_('COM_GEOSEARCH_LABEL_MEMBER'); ?></div>
									<div class="clear"></div>
								 </div>
							<?php
								}
							} else { ?>
							<?php if (in_array("members",$this->resources)) { echo '<p>'.JText::_('COM_GEOSEARCH_NO_MEMBERS').'</p>'; } ?>

							<?php
							}
							  if (isset($this->jobs) && count($this->jobs) > 0)  {
								  foreach ($this->jobs as $row) {
							?>
								<div class="list-item">
									<div class="list-content">
										<a href="<?php echo JRoute::_('index.php?option=com_jobs&task=job&code=' . $row->code); ?>" title="<?php echo $row->title; ?>"><?php echo $row->title; ?></a><br />
										<span class="list-detail"><?php echo $row->companyName; ?></span>
										<div class="job-desc">
											<?php
												if ($row->description)
												{
													$jobsModelJob = new JobsModelJob($row->id);
													$desc = $jobsModelJob->content('parsed');
													$desc = strip_tags($desc);
													echo \Hubzero\Utility\String::truncate($desc, 290);
												}
											?>
										</div>
									</div>
									<div class="list-label"><?php echo JText::_('COM_GEOSEARCH_LABEL_JOB'); ?></div>
									<div class="clear"></div>
								 </div>
							<?php
									}
								} else { ?>
							<?php if (in_array("jobs",$this->resources)) { echo '<p>'.JText::_('COM_GEOSEARCH_NO_JOBS').'</p>'; } ?>

							 <?php
								}
							  if (isset($this->events) && count($this->events) > 0)
							  {
								  foreach ($this->events as $row)
								  {
									  // object or array?
									  if (is_object($row))
									  {
										  $id = $row->id;
										  $title = $row->title;
										  $publish_up = $row->publish_up;
										  $publish_down = $row->publish_down;
										  $content = $row->content;
									  }
									  else
									  {
										  $id = $row[0];
										  $title = $row[1];
										  $publish_up = $row[2];
										  $publish_down = $row[3];
										  $content = $row[4];
									  }
							?>
								<div class="list-item">
									<div class="list-content">
										<a href="<?php echo JRoute::_('index.php?option=com_events&task=details&id=' . $id); ?>" title="<?php echo stripslashes($title); ?>"><?php echo stripslashes($title); ?></a><br />
										<span class="list-detail">
										<?php
											// date and time
											echo JHTML::_('date', $publish_up, 'l, F j, Y g:i a');
											if ($publish_down != "")
											{
												echo " to ";
												echo JHTML::_('date', $publish_down, 'l, F j, Y g:i a');
											}
										?>
										</span>
										<div class="job-desc">
											<?php if ($content) {
												echo \Hubzero\Utility\String::truncate($content, 290);
											} ?>
										</div>
										<?php
											$tags = $this->ET->get_tag_cloud(0,0,$id);
											echo ($tags) ? $tags :  ""; /*JText::_('COM_HMAP_NO_TAGS');*/
										?>
									</div>
									<div class="list-label"><?php echo JText::_('COM_GEOSEARCH_LABEL_EVENT'); ?></div>
									<div class="clear"></div>
								 </div>
							<?php }
								} else { ?>
							<?php if (in_array("events",$this->resources)) { echo '<p>'.JText::_('COM_GEOSEARCH_NO_EVENTS').'</p>'; } ?>
							<?php }
								if (isset($this->orgs) && count($this->orgs) > 0)  {
									foreach ($this->orgs as $row) {
									  // object or array?
									  if (is_object($row)) {
										  $id = $row->id;
										  $title = $row->title;
										  $text = $row->fulltxt;
									  } else {
										  $id = $row[0];
										  $title = $row[1];
										  $text = $row[2];
									  }
							?>
								<div class="list-item">
									<div class="list-content">
										<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $id); ?>" title="<?php echo stripslashes($title); ?>"><?php echo stripslashes($title); ?></a><br />
										<span class="list-detail">

										</span>
										<?php
											$text = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $text);
											$bio = trim($text);
										?>
										<div class="job-desc">
											<?php if ($text) {
												echo \Hubzero\Utility\String::truncate(stripslashes($bio), 290);
											} ?>
										</div>

										<?php
											$tags = $this->RT->getTags($id);
											$taglist = $this->RT->buildTopCloud($tags);
											echo ($taglist) ? $taglist :  "";
										?>
									</div>
									<div class="list-label"><?php echo JText::_('COM_GEOSEARCH_LABEL_ORG'); ?></div>
									<div class="clear"></div>
								 </div>
							<?php }
								} else { ?>
							<?php if (in_array("orgs",$this->resources)) { echo '<p>' . JText::_('COM_GEOSEARCH_NO_ORGS') . '</p>'; } ?>
							<?php } ?>
							<?php //echo $this->pagenavhtml; ?>
							<br class="clear" />
						</div>
				</div><!-- / .container-block -->
			</div><!-- / .container -->
	</div><!-- / .subject -->
	<div class="clear"></div>
	</form>
</div><!-- / .main section -->

