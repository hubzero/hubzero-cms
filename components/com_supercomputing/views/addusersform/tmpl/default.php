<div class="person-info<?php if ($this->is_alt()) echo ' alt'; ?>" id="person-info-<?php echo $this->postfix; ?>">
	<p>
		<?php 
			$this->text_input('First name', 1);
			$this->text_input('Telephone', 3);
		?>
	</p>
	<p>
		<?php
			$this->text_input('Last name', 2);
			$this->text_input('Email', 4);
		?>
	</p>
	<p>
		<?php 
			$this->text_input('Organization', 5); 
			$this->textarea('Mailing Address', 6);
		?>
	</p>
	<div class="clear">&nbsp;</div>
</div>

