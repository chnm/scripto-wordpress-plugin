<p>
	<!-- scripto navigation: index -->
	<?php if ( 'index' == $_GET['scripto_action'] ): ?>
	home
	<?php else: ?>
	<a href="<?php echo $this->scripto_url( 'index' ); ?>">home</a>
	<?php endif; ?>
	| 
	
	<!-- scripto navigation: login/logout -->
	<?php if ( $this->_scripto->isLoggedIn() ): ?>
	<?php $user_name = $this->_scripto->getUserName(); ?>
	logged in as <?php echo $user_name; ?> (<a href="<?php echo $this->scripto_url( 'logout' ); ?>">logout</a>)
	<?php else: ?>
	<?php if ( 'login' == $_GET['scripto_action'] ): ?>
	login
	<?php else: ?>
	<a href="<?php echo $this->scripto_url( 'login' ); ?>">login</a>
	<?php endif; ?>
	<?php endif; ?>
	| 
	
	<!-- scripto navigation: recent changes -->
	<?php if ( 'recent_changes' == $_GET['scripto_action'] ): ?>
	recent changes
	<?php else: ?>
	<a href="<?php echo $this->scripto_url( 'recent_changes' ); ?>">recent changes</a>
	<?php endif; ?>
</p>