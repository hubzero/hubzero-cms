<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
	//$jdocument =& JFactory::getDocument();
	//$jdocument->addScript('/templates/fresh/html/mod_reportproblems/closetab.js');
?>
	<div id="help-pane">
		<div id="help-container">
			<h1><?php echo JText::_('MOD_REPORTPROBLEMS_SUPPORT'); ?></h1>

			<div class="threecolumn farleft">
				<h2><?php echo JText::_('Support Options'); ?></h2>
				<ul>				
					<li class="kb"><h3><a href="/kb" ><?php echo JText::_('Knowledge Base'); ?></a></h3>
						<p><?php echo JText::_('Find information on common issues.'); ?></p>
					</li>
					<li class="qa"><h3><a href="/answers" ><?php echo JText::_('Ask the Community'); ?></a></h3>
						<p><?php echo JText::_('Ask questions and find answers from other users.'); ?></p>
					</li>
					<li class="wish"><h3><a href="/wishlist" ><?php echo JText::_('Wish List'); ?></a></h3>
						<p><?php echo JText::_('Suggest a new site feature or improvement.'); ?></p>
					</li>
					<li class="tickets"><h3><a href="/support/tickets" ><?php echo JText::_('Support Tickets'); ?></a></h3>
						<p><?php echo JText::_('Check on status of your tickets.'); ?></p>
					</li>
				</ul>	
			</div><!-- / .threecolumn farleft -->
			<div class="threecolumn middleright">
				<h2><?php echo JText::_('Submit a Support Ticket'); ?><span id="closethis"><?php echo JText::_('Close this'); ?></span></h2>
				<form method="post" action="index.php" id="troublereport">
					<fieldset class="reporter">
						<label for="trLogin">
							NEEShub <?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_LOGIN'); ?>: <span class="optional"><?php echo JText::_('MOD_REPORTPROBLEMS_OPTIONAL'); ?></span>
							<input type="text" name="reporter[login]" id="trLogin" value="<?php echo (!$modreportproblems->juser->get('guest')) ? $modreportproblems->juser->get('username') : ''; ?>" />
						</label>
				
						<label for="trName">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_NAME'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
							<input type="text" name="reporter[name]" id="trName" value="<?php echo (!$modreportproblems->juser->get('guest')) ? $modreportproblems->juser->get('name') : ''; ?>" />
						</label>
				
						<label for="trEmail">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_EMAIL'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
							<input type="text" name="reporter[email]" id="trEmail" value="<?php echo (!$modreportproblems->juser->get('guest')) ? $modreportproblems->juser->get('email') : ''; ?>" />
						</label>
<?php if (!$modreportproblems->verified) { ?>
						<label for="trAnswer">
								<a href="/kb/misc/why_the_math_question"><?php echo JText::_('Anti-spam question'); ?></a>: <br /><?php echo JText::sprintf('MOD_REPORTPROBLEMS_MATH_CAPTCHA', $modreportproblems->problem['operand1'], $modreportproblems->problem['operand2']); ?>
							<span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
							<input type="text" name="answer" id="trAnswer" value="" size="3" /></label>
<?php } ?>
					</fieldset>
					<fieldset>
						<label for="trProblem">
							<?php echo JText::_('MOD_REPORTPROBLEMS_LABEL_PROBLEM'); ?>: <span class="required"><?php echo JText::_('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
							<textarea name="problem[long]" id="trProblem" <?php if (!$modreportproblems->verified) { echo 'class="long" '; } ?>rows="10" cols="40"></textarea>
						</label>

						<input type="hidden" name="problem[topic]" value="???" />
						<input type="hidden" name="problem[short]" value="" />
						<input type="hidden" name="problem[referer]" value="<?php echo $modreportproblems->referrer; ?>" />
						<input type="hidden" name="problem[tool]" value="" />
						<input type="hidden" name="problem[os]" value="<?php echo $modreportproblems->os; ?>" />
						<input type="hidden" name="problem[osver]" value="<?php echo $modreportproblems->os_version; ?>" />
						<input type="hidden" name="problem[browser]" value="<?php echo $modreportproblems->browser; ?>" />
						<input type="hidden" name="problem[browserver]" value="<?php echo $modreportproblems->browser_ver; ?>" />
						<input type="hidden" name="krhash" value="<?php echo $modreportproblems->krhash; ?>" />
						<input type="hidden" name="verified" value="<?php echo $modreportproblems->verified; ?>" />
						<input type="hidden" name="reporter[org]" value="<?php echo (!$modreportproblems->juser->get('guest')) ? $modreportproblems->juser->get('org') : ''; ?>" />
						<input type="hidden" name="option" value="com_feedback" />
						<input type="hidden" name="task" value="sendreport" />
						<input type="hidden" name="no_html" value="1" />
<?php if ($modreportproblems->verified) { ?>
						<input type="hidden" name="answer" id="trAnswer" value="<?php echo $modreportproblems->sum; ?>" />
<?php } ?>
				 	</fieldset>
					<div class="submit"><input type="submit" id="send-form" value="<?php echo JText::_('MOD_REPORTPROBLEMS_SUBMIT'); ?>" /></div>
				</form>
				<div id="trSending">
				</div>
				<div id="trSuccess">
				</div>
			</div><!-- / .threecolumn middleright -->
			<div class="clear"></div>
		</div><!-- / #help-container -->
	</div><!-- / #help-pane -->
