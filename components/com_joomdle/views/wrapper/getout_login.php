<?php
// If anyone reads this code and comes with a better way to do it, please tell me ;)

$uri = $_SERVER['REQUEST_URI'];
$host = $_SERVER['HTTP_HOST'];
//print_r ($_SERVER);
//exit ();
$path = strstr ($uri, '/components/com_joomdle/views/wrapper/getout_login.php');
$len = strlen ($uri);
$len2 = strlen ($path);
$root = substr ($uri, 0, $len - $len2);

$root = 'https://'.$host.$root.'/index.php?option=com_user&view=login';
?>
<script type="text/javascript">
top.location.href = "<?php echo $root; ?>";
</script>
