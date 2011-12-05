<h2><?php echo $doc->getPageName(); ?></h2>
<h3><?php echo $doc->getTitle(); ?></h3>

<div>
	<?php echo $media_viewer; ?>
</div>

<div>
	<h2>Discuss</h2>
	<div>
		<a href="<?php echo $url_transcription; ?>">transcription</a> 
		| <a href="<?php echo $url_talk_history; ?>">history</a> 
	</div>
	<form action="" method="post">
		<textarea name="scripto_talk" cols="45" rows="12"><?php echo $doc->getTalkPageWikitext(); ?></textarea>
		<input type="submit" name="scripto_submit_talk" value="Save Discussion" />
	</form>
</div>

<div>
	<h2>Current Discussion</h2>
	<div>
		<?php echo $doc->getTalkPageHtml(); ?>
	</div>
</div>