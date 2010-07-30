<h4 class="half"><label for="computing-time">Computing time allocation</label></h4>
<h4 class="half">Software used with this allocation</h4>
<fieldset>
	<div class="half">
		<?php $this->errors_on('computing-time'); ?>
		<p>
			<input <?php $this->error_class('computing-time'); ?>type="text" id="computing-time" name="computing-time" value="<?php $this->attr('computing-time', '5000'); ?>" /><span class="unit">CPU hours</span>
		</p>
	</div>
	<div class="half">
		<?php $this->errors_on('software'); ?>
		<p>
			<?php
				$this->checkbox('software-abaqus',   'ABAQUS');
				$this->checkbox('software-ansys',    'Ansys');
				$this->checkbox('software-ls-dyna',  'LS-Dyna');
				$this->checkbox('software-opensees', 'OpenSees');
			?>
		</p>
		<p>Please specify any other software:</p>
		<textarea rows="2" cols="50" name="software-other"><?php $this->attr('software-other'); ?></textarea>
	</div>
	<div class="clear">&nbsp;</div>
</fieldset>
<h4 class="half">Project association</h4>
<h4 class="half">Association details</h4>
<fieldset>
	<div id="project-associations">
		<?php $this->errors_on('association'); ?>
		<p class="association-container<?php $this->add_error_class('association'); ?>">
			<span class="association">
				<input type="radio" name="association" value="pi-neesr" id="association-pi-neesr" <?php $this->selected_if('association', 'pi-neesr'); ?> />
				<label for="association-pi-neesr">PI is associated with a NEESR project</label> 
			</span>
			<span class="other-type">Provide title and institution of NEESR project &raquo;</span>
		</p>
		<p class="association-container<?php $this->add_error_class('association'); ?>">
			<span class="association">
				<input type="radio" name="association" value="pi-shared-use" id="association-pi-shared-use" <?php $this->selected_if('association', 'pi-shared-use'); ?> />
				<label for="association-pi-neesr">PI is associated with a Shared Use project</label>
			</span>
			<span class="other-type">Provide organizations involved and a brief project description &raquo;</span>
		</p>
		<p class="association-container last<?php $this->add_error_class('association'); ?>">
			<span class="association">
				<input type="radio" name="association" value="pi-not-associated" id="association-pi-not-associated" <?php $this->selected_if('association', 'pi-not-associated'); ?> />
				<label for="association-pi-neesr">PI is not associated with a NEESR or Shared Use project</label>
			</span>
			<span class="other-type">Provide a brief project description &raquo;</span>
		</p>
	</div>
	<div id="project-info-container">
		<?php $this->errors_on('project-info'); ?>
		<textarea <?php $this->error_class('project-info'); ?>rows="15" cols="50" id="project-info" name="project-info"><?php $this->attr('project-info'); ?></textarea>
	</div>
	<div class="clear">&nbsp;</div>
</fieldset>
