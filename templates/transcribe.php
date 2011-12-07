<h2>Transcribe <cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<div>
	<?php echo $this->get_media_viewer( $_GET['scripto_doc_page_id'] ); ?>
</div>

<div>
	<a href="<?php echo $url_talk; ?>">discussion</a> | <a href="<?php echo $url_transcription_history; ?>">history</a> 
</div>

<?php if ( $doc->canEditTranscriptionPage() ): ?>
<form action="" method="post">
	<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_transcription" value="Save Transcription" />
</form>
<?php else: ?>
<p>You don't have permission to transcribe this page.</p>
<?php endif; ?>

<h2>Current Transcription</h2>
<div>
	<?php echo $doc->getTranscriptionPageHtml(); ?>
</div>