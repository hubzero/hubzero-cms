PI: 
<?php 
	echo $this->fields['pi']['last-name'] . ', ' . $this->fields['pi']['first-name'] . "\n"; 
	echo $this->fields['pi']['email'] . "\n"; 
	echo $this->fields['pi']['telephone'] . "\n"; 
	echo $this->fields['pi']['organization'] . "\n"; 
	echo $this->fields['pi']['mailing-address'] . "\n\n"; 
?>
<?php 
if ($this->fields['other-users']): 
	echo "Additional requested users:\n";
	foreach ($this->fields['other-users'] as $user):
		echo $user['last-name'] . ', ' . $user['first-name'] . "\n"; 
		echo $user['email'] . "\n"; 
		echo $user['telephone'] . "\n"; 
		echo $user['organization'] . "\n"; 
		echo $user['mailing-address'] . "\n\n";		
	endforeach;
else: 
	echo "No other users were requested.\n";
endif; 
?>
