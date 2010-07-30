<?php
$this->set_title('Supercomputing Allocation — User Addition Request Successful');
$this->push_breadcrumb('Supercomputing Allocation', '/supercomputing');
?>
<h2>User Addition Request Successful</h2>
<p>Thank you. Your request has been submitted successfully.</p>
<p>A summary of the data submitted for your request follows:</p>
<h3><?php echo $this->ticket_title; ?></h3>
<pre><?php echo $this->ticket_text; ?></pre>
<p><a href="/supercomputing">Return to supercomputing allocation</a></p>
<p><a href="/">Home</a></p>
