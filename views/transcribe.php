<div id="scripto">

<p><?php echo $this->get_navigation(); ?></p>

<h2>Transcribe <cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<div><?php echo $this->get_media_viewer( $_GET['scripto_doc_page_id'] ); ?></div>

<?php if ( $doc->canEditTranscriptionPage() ): ?>
<form action="" method="post">
	<textarea name="scripto_transcripton" cols="45" rows="12"><?php echo $doc->getTranscriptionPageWikitext(); ?></textarea>
	<input type="submit" name="scripto_submit_transcription" value="Save transcription" />
</form>
<?php else: ?>
<p>You don't have permission to transcribe this page.</p>
<?php endif; ?>

<p><a href="<?php echo $url_talk; ?>">discuss page</a> | <a href="<?php echo $url_transcription_history; ?>">view history</a> | <a href="<?php echo $url_post; ?>">view post</a></p>

<h2>Current Transcription</h2>

<?php if ( $this->_scripto->canExport() ): ?>
<form action="" method="post">
<input type="submit" name="scripto_submit_import_page" value="Import this page" />
</form>
<?php endif; ?>

<?php if ( $this->_scripto->canProtect() ): ?>
<?php if ( $doc->isProtectedTranscriptionPage() ): ?>
<form action="" method="post">
<input type="submit" name="scripto_submit_unprotect_page" value="Unprotect this page" />
</form>
<?php else: ?>
<form action="" method="post">
<input type="submit" name="scripto_submit_protect_page" value="Protect this page" />
</form>
<?php endif; ?>
<?php endif; ?>

<div><?php echo $doc->getTranscriptionPageHtml(); ?></div>

</div>