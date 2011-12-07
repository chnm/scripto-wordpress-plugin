<h2>Discuss <cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<div>
	<?php echo $this->get_media_viewer( $_GET['scripto_doc_page_id'] ); ?>
</div>

<div>
	<a href="<?php echo $url_transcription; ?>">transcription</a> | <a href="<?php echo $url_talk_history; ?>">history</a> 
</div>
<form action="" method="post">
	<textarea name="scripto_talk" cols="45" rows="12"><?php echo $doc->getTalkPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_talk" value="Save Discussion" />
</form>

<h2>Current Discussion</h2>
<div>
	<?php echo $doc->getTalkPageHtml(); ?>
</div>