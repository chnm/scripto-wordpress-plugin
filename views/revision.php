<div id="scripto">

<p><?php echo $this->get_navigation(); ?></p>

<h2>Revision of <?php if ( '1' == $_GET['scripto_ns_index'] ): ?>Talk: <?php endif; ?><cite><?php echo $doc->getPageName(); ?></cite><br />
as of <?php echo date( 'H:i:s, M d, Y', strtotime( $revision['timestamp'] ) ) ?>; <?php echo ucfirst( $revision['action'] ); ?> by <?php echo $revision['user']; ?></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<p><a href="<?php echo $this->scripto_url(  ); ?>">transcribe page</a> | <a href="">discuss page</a></p>

<div><?php echo $revision['html']; ?></div>

<?php if ( ( '1' == $_GET['scripto_ns_index'] && $doc->canEditTalkPage() ) || ( '0' == $_GET['scripto_ns_index'] && $doc->canEditTranscriptionPage() ) ): ?>
<form action="" method="post">
	<input type="submit" name="scripto_submit_revert" value="Revert to this revision" /> 
</form>
<?php endif; ?>

</div>