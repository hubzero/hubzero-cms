<div id="overlay"></div>
<div id="questions">
	<h2>Help us keep this website and its services free</h2>
	<p>Please provide a little more information about yourself. <small>(<a href="/legal/privacy">Why do we need this information?</a>)</small></p>
	<p>We'll award you with <strong>15</strong> points for each question you answer. You can use these points towards items in the site <a href="/store">store</a>, or to place bounties on <a href="/answers">questions</a> and <a href="/wishlist">wishes</a>.</p>
	<form action="" method="post">
		<ol>
				<?php if (isset($row['orgtype'])): ?>
				<li>
					<label for="orgtype">Which item best describes your organizational affiliation? </label>
					<div style="margin-left: 4em;">
					<?php if (isset($errors['organization'])): ?>
						<p class="warning">Please select your organizational affiliation</p>
					<?php endif; ?>
					<select id="orgtype" name="orgtype">
						<option selected="selected" value="">(select from list)</option>
						<option value="universityundergraduate">University / College Undergraduate</option>
						<option value="universitygraduate">University / College Graduate Student</option>
						<option value="universityfaculty">University / College Faculty</option>
						<option value="universitystaff">University / College Staff</option>
						<option value="precollegestudent">K-12 (Pre-College) Student</option>
						<option value="precollegefacultystaff">K-12 (Pre-College) Faculty/Staff</option>
						<option value="nationallab">National Laboratory</option>
						<option value="industry">Industry / Private Company</option>
						<option value="government">Government Agency</option>
						<option value="military">Military</option>
						<option value="unemployed">Retired / Unemployed</option>
					</select>
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['organization'])): ?>
				<li>
					<label for="org">Which organization are you affiliated with? </label><br />
					<div style="margin-left: 4em;">
					<?php if (isset($errors['organization'])): ?>
						<p class="warning">Please select an organization or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="org" name="org">
						<option value="">(select from list or enter other)</option>
						<?php 
						$dbh->setQuery('SELECT organization FROM #__xorganizations ORDER BY organization');
						foreach ($dbh->loadAssocList() as $org)
							echo '<option value="'.$org['organization'].'"'.(isset($_POST['org']) && $_POST['org'] === $org['organization'] ? ' selected="selected"' : '').'>'.$org['organization'].'</option>';
						?>
					</select>
					<br />
					<label for="org-other">Not listed? Enter your organization here: </label><br />
					<input id="org-other" type="text" name="org-other" value="<?php echo isset($_POST['org-other']) ? str_replace('"', '&quot;', $_POST['org-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
				<?php if (isset($row['reason'])): ?>
				<li>
					<label for="reason">What is the primary purpose of your account? </label>
					<div style="margin-left: 4em">
					<?php if (isset($errors['reason'])): ?>
						<p class="warning">Please select the reason for your account or enter one in the provided "other" field</p>
					<?php endif; ?>
					<select id="reason" name="reason">
						<?php $val = isset($_POST['reason']) ? $_POST['reason'] : ''; ?>
						<option value="">(select from list or enter other)</option>
						<option <?php if ($val === 'Required for class') echo 'selected="selected" '; ?>value="Required for class">Required for class</option>
						<option <?php if ($val === 'Developing a new course') echo 'selected="selected" '; ?>value="Developing a new course">Developing a new course</option>
						<option <?php if ($val === 'Using in an existing course') echo 'selected="selected" '; ?>value="Using in an existing course">Using in an existing course</option>
						<option <?php if ($val === 'Using simulation tools for research') echo 'selected="selected" '; ?>value="Using simulation tools for research">Using simulation tools for research</option>
						<option <?php if ($val === 'Using as background for my research') echo 'selected="selected" '; ?>value="Using as background for my research">Using as background for my research</option>
						<option <?php if ($val === 'Learning about subject matter') echo 'selected="selected" '; ?>value="Learning about subject matter">Learning about subject matter</option>
						<option <?php if ($val === 'Keeping current in subject matter') echo 'selected="selected" '; ?>value="Keeping current in subject matter">Keeping current in subject matter</option>
					</select>
					<br />
					<label for="reason-other">Have a different reason? </label><br />
					<input id="reason-other" type="text" name="reason-other" value="<?php echo isset($_POST['reason-other']) ? str_replace('"', '&quot;', $_POST['reason-other']) : ''; ?>" />
					</div>
				</li>
				<?php endif; ?>
			</ol>
		<p>
			<input type="hidden" name="incremental-registration" value="update" />
			<button type="submit" name="submit" value="submit" type="submit">Submit</button>
			<button type="submit" name="submit" value="opt-out" type="submit">Ask me later</button>
		</p>
	</form>
</div>
