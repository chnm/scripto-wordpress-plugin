<h2><?php echo $doc->getPageName(); ?></h2>
<h3><?php echo $doc->getTitle(); ?></h3>

<div>
	<?php echo $media_viewer; ?>
</div>

<div>
	<h2>Transcribe</h2>
	<div>
		<a href="<?php echo $url_talk; ?>">discussion</a> 
		| <a href="<?php echo $url_transcription_history; ?>">history</a> 
	</div>
	<form action="" method="post">
		<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
		<input type="submit" name="scripto_submit_transcription" value="Save Transcription" />
	</form>
</div>

<div>
	<h2>Current Transcription</h2>
	<div>
		<?php echo $doc->getTranscriptionPageHtml(); ?>
	</div>
</div>