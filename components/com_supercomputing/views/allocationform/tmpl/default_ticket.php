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
	echo "Other requested users:\n";
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
Computing time: <?php echo $this->fields['computing-time']; ?> CPU hours
Association: <?php 
	$assoc = array(
		'pi-neesr' => 'NEESR',
		'pi-shared-use' => 'Shared Use',
		'pi-not-associated' => 'Unassociated'
	);
	echo $assoc[$this->fields['association']]; 
?>

<?php 
	$detail_type = array(
		'pi-neesr' => 'NEESR project title and institution',
		'pi-shared-use' => 'Involved organizations and project description',
		'pi-not-associated' => 'Project description'
	);
	echo $detail_type[$this->fields['association']]  . ": \n" . $this->fields['project-info'] . "\n";
?>

Software: <?php echo implode(', ', $this->fields['software']); ?> 	
