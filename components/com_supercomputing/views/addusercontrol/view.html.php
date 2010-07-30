<?php

class SuperComputingViewAddUserControl extends SuperComputingView
{
	protected $postfix, $tabindex;

	public function set_postfix($postfix) 
	{ 
		$this->postfix = $postfix; 
		$this->tabindex = is_int($postfix) ? $postfix + 1 : 0; 
		return $this; 
	}
	protected function is_alt() { return is_int($this->postfix) && $this->postfix&1; }

	protected function text_input($cap, $tabindex)
	{
		$name = strtolower(str_replace(' ', '-', $cap)).'-'.$this->postfix;;
		if (array_key_exists($name, $this->errors))
			echo '<span class="error">'.$this->errors[$name][0].'</span>';
?>
		<label for="<?php echo $name; ?>"><?php echo $cap; ?></label>
		<input <?php $this->error_class($name); ?>type="text" tabindex="<?php echo $this->tabindex * 6 + $tabindex; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php self::attr($name); ?>" />
<?php
	}
	protected function textarea($cap, $tabindex)
	{
		$name = strtolower(str_replace(' ', '-', $cap)).'-'.$this->postfix;;
		if (array_key_exists($name, $this->errors))
			echo '<span class="error">'.$this->errors[$name][0].'</span>';
?>
		<label for="<?php echo $name; ?>"><?php echo $cap; ?></label>
		<textarea <?php $this->error_class($name); ?>rows="4" cols="23" tabindex="<?php echo $this->tabindex * 6 + $tabindex; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>"><?php self::attr($name); ?></textarea>
<?php
	}
}
