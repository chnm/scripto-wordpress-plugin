<?php echo $this->get_navigation(); ?>

<h2>Discuss <cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<div>
	<?php echo $this->get_media_viewer( $_GET['scripto_doc_page_id'] ); ?>
</div>

<div>
	<a href="<?php echo $url_transcription; ?>">transcribe this page</a> | <a href="<?php echo $url_talk_history; ?>">view discussion history</a> 
</div>

<?php if ( $doc->canEditTalkPage() ): ?>
<form action="" method="post">
	<textarea name="scripto_talk" cols="45" rows="12"><?php echo $doc->getTalkPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_talk" value="Save Discussion" />
</form>
<?php else: ?>
<p>You don't have permission to discuss this page.</p>
<?php endif; ?>

<h2>Current Discussion</h2>
<div>
	<?php echo $doc->getTalkPageHtml(); ?>
</div>