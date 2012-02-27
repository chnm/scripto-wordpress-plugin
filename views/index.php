<div id="scripto">

<p><?php echo $this->get_navigation(); ?></p>

<h2>Welcome to Scripto!</h2>

<?php if ($home_page_text): ?>
<?php echo $home_page_text; ?>
<?php else: ?>
<p>By using this application you are helping to transcribe documents in 
<i><?php echo get_option('blogname'); ?></i>. All posts with attachments can be 
transcribed. For these purposes a post is a <em>document</em>, and an post's 
attachments are its <em>pages</em>. To begin transcribing documents, 
<a href="<?php echo home_url(); ?>">browse posts</a> or 
<a href="<?php echo $this->scripto_url( 'recent_changes' ); ?>">view recent changes</a> to Scripto. 
You may <a href="<?php echo $this->scripto_url( 'login' ); ?>">log in</a> to access your account 
and enable certain Scripto features. Login may not be required by the 
administrator.</p>
<?php endif; ?>

</div>