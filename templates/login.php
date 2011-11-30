<?php if ( $error ): ?>
<p><?php echo $error; ?></p>
<?php endif; ?>
<form action="" method="post">
	<p>Username <input type="input" name="scripto_username" /></p>
	<p>Password <input type="password" name="scripto_password" /></p>
	<p><input type="submit" name="scripto_submit_login" /></p>
</form>