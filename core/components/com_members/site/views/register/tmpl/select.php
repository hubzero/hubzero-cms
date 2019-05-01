<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p>Registering at <?php echo $this->sitename; ?> is easy: just sign in using an account you may already have at one of the listed sites/organizations or create a new <?php echo $this->sitename; ?> account.</p>

			<h4>Why is registration required for parts of the <?php echo $this->sitename; ?>?</h4>

			<p>Our sponsors ask us who uses the <?php echo $this->sitename;?> and what they use it for. Registration helps us answer these questions. Usage statistics also focus our attention on improvements, making the <?php echo $this->sitename; ?> experience better for <em>you</em>.</p>
		</div>
		<fieldset>
			<h3>Register with <?php echo $this->sitename; ?></h3>
			<fieldset>
				<legend>Register by signing in with your</legend>

				<?php foreach ($realms as $key => $value) { ?>
					<label>
						<input class="option" type="radio" name="realm" value="<?php echo $key; ?>" />
						<?php echo $value; ?>
					</label>
				<?php } ?>

				<p class="submit">
					<input class="option" type="submit" name="login" value="Log In" />
				</p>
			</fieldset>

			<h3>Or Create a New Account</h3>
			<fieldset>
				<legend>Create a separate account for <?php echo $this->sitename; ?></legend>

				<p class="submit">
					<input class="option" type="submit" name="register" value="Create a New Account" />
				</p>
			</fieldset>
		</fieldset>
		<div class="clear"></div>
		<input type="hidden" name="option" value="com_members" />
		<input type="hidden" name="controller" value="register" />
		<input type="hidden" name="task" value="select" />
		<input type="hidden" name="act" value="submit" />
	</form>
</section><!-- / .main section -->
