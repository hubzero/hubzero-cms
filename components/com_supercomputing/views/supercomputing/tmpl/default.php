<?php
$this->set_title('Apply for Supercomputing Allocation');
$this->push_breadcrumb('Supercomputing Allocation', '/supercomputing');
?>
<h2>Supercomputing Allocations</h2>
<p>NEESit has received a renewed grant for 212,500 hours of National Science Foundation Cyberinfrastructure Resources computing time. The NEES research community now has access to two of San Diego Supercomputer Center's supercomputers: DataStar and TeraGrid, as well as two other TeraGrid supercomputers: Abe@NCSA and Ranger@TACC, to run high-end computing (HEC) programs such as Abaqus, Ansys and OpenSees.</p>
<h3>Apply for Supercomputing Allocation</h3>
<form action="/supercomputing" method="post">
	<p>All NEES research projects are encouraged to apply for an allocation. Non-NEES research projects may also apply, however, NEES-affiliated requests may be given preference over non-NEES requests due to the limited number of hours available. Please select the type of request you would like to make to get started:</p>
	<fieldset>
		<ul id="request-types">
			<li><input type="submit" name="request-type" value="<?php echo NEW_REQUEST_TEXT; ?>" /></li>
			<li><input type="submit" name="request-type" value="<?php echo RENEW_REQUEST_TEXT; ?>" /></li>
			<li><input type="submit" name="request-type" value="<?php echo ADD_USERS_REQUEST_TEXT; ?>" /></li>
		</ul>
	</fieldset>
	<p>To avoid delays, please reapply early when requesting to extend access to our supercomputing resources.</p>
	<p><input type="hidden" name="task" value="request" /></p>
</form>
