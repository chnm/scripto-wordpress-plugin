<h2>Transcribe <?php echo $doc->getPageName(); ?></h2>
<div>
	<?php echo $media_viewer; ?>
</div>
<div>
	<a href="<?php echo $transcription_history_url; ?>">history</a> | <a href="<?php echo $talk_url; ?>">discuss</a>
</div>
<form action="" method="post">
	<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_transcription" value="Save Transcription" />
</form>