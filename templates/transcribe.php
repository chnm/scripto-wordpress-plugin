<div>
	<?php echo $media_viewer; ?>
</div>
<div>
	<a href="<?php echo $transcription_page_url; ?>">transcription history</a> | <a href="<?php echo $talk_page_url; ?>">discussion history</a>
</div>
<form action="" method="post">
	<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_transcription" value="Save Transcription" />
</form>