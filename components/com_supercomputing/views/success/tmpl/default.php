<?php
$this->set_title('Allocation request submitted');
$this->push_breadcrumb('Supercomputing allocation', '/supercomputing');
?>
<h2>Allocation request submitted</h2>
<p>Thank you, your request has been submitted successfully.</p>
<p>A summary of the data submitted for your request follows:</p>
<h3><?php echo $this->ticket_title; ?></h3>
<pre><?php echo $this->ticket_text; ?></pre>
<p><a href="/supercomputing">Return to supercomputing allocation</a></p>
<p><a href="/">Home</a></p>
