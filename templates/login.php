<p>You may log in to access your account and enable certain Scripto features. 
Login may not be required by the administrator.</p>
<?php if ( $error ): ?>
<p><?php echo $error; ?></p>
<?php endif; ?>
<form action="" method="post">
	<p>Username <input type="input" name="scripto_username" /></p>
	<p>Password <input type="password" name="scripto_password" /></p>
	<p><input type="submit" name="scripto_submit_login" value="Login" /></p>
</form>