<?php
$titles = array(
	'new' => 'New Request',
	'renew' => 'Renew Request',
	'add_users' => 'Add Users'
);
$this->set_title('Supercomputing Allocation — '.$titles[$this->request_type]);
$this->push_breadcrumb('Supercomputing Allocation', '/supercomputing');
?>
<h2><?php echo $this->get_page_heading(); ?></h2>
<?php $this->errors_on('form'); ?>
<form action="/supercomputing" method="post">
	<h4>Principal investigator information</h4>
	<fieldset>
		<?php 
			$user_form = $this->get_partial('addusercontrol')->inherit_properties($this);
			$user_form->set_postfix('pi')->display(); 
		?>
	</fieldset>
	<h4>Additional users</h4>
	<fieldset>
		<p>Please list the information of additional users who may access your Supercomputing Allocation. You must enter all fields for each user. Any user with incomplete fields will not be submitted with your request.</p>
		<?php for ($idx = 0; $idx < 16; ++$idx): ?>
			<?php $user_form->set_postfix($idx)->display(); ?>
		<?php endfor; ?>
		<p id="add-user"><input id="add-user-button" type="button" value="Add another user" /></p>
	</fieldset>
