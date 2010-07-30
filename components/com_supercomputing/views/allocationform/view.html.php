<?php

class SuperComputingViewAllocationForm extends SuperComputingView
{
	protected function checkbox($name, $lbl)
	{
?>
		<input type="checkbox" id="<?php echo $name; ?>" name="<?php echo $name; ?>" <?php $this->checked_if($name); ?> />
		<label for="<?php echo $name; ?>"><?php echo $lbl; ?></label>
<?php
	}

	public function display()
	{
		$this->get_partial('peopleform')->inherit_properties($this)->display();
		parent::display();
		$this->get_partial('formtail')->inherit_properties($this)->display();
	}
}
