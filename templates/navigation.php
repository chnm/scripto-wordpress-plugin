<div id="scripto-navigation">
<?php if ( 'index' == $_GET['scripto_page'] ): ?>
home
<?php else: ?>
<a href="<?php echo $this->scripto_url( 'index' ); ?>">home</a>
<?php endif; ?>
| 

<?php if ( 'recent_changes' == $_GET['scripto_page'] ): ?>
recent changes
<?php else: ?>
<a href="<?php echo $this->scripto_url( 'recent_changes' ); ?>">recent changes</a>
<?php endif; ?>
| 

<?php if ( $this->_scripto->isLoggedIn() ): ?>
<?php $user_name = $this->_scripto->getUserName(); ?>
logged in as <?php echo $user_name; ?> (<a href="<?php echo $this->scripto_url( 'logout' ); ?>">logout</a>)
<?php else: ?>
<?php if ( 'login' == $_GET['scripto_page'] ): ?>
login
<?php else: ?>
<a href="<?php echo $this->scripto_url( 'login' ); ?>">login</a>
<?php endif; ?>
<?php endif; ?>
</div>