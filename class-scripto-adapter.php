<?php
require_once 'Scripto/Adapter/Interface.php';

/**
 * Scripto adapter for WordPress.
 * 
 * For these purposes, a Scripto document is a WordPress post that is not an 
 * attachment, and a Scripto document page is a WordPress attachment post 
 * belonging to a parent post.
 */
class Scripto_Adapter implements Scripto_Adapter_Interface {
	
	/**
	 * Check the the post exists.
	 */
	public function documentExists( $post_id ) {
		$post = get_post( $post_id );
		
		// The post must exist.
		if ( ! $post ) {
			return false;
		}
		
		// The post must not be the "attachment" post type.
		if ( 'attachment' == $post->post_type ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check that the attachment post exists.
	 */
	public function documentPageExists( $post_id, $attachment_post_id ) {
		$attachment_post = get_post( $attachment_post_id );
		
		// The post must exist.
		if ( ! $attachment_post ) {
			return false;
		}
		
		// The post must be the "attachment" post type.
		if ( 'attachment' != $attachment_post->post_type ) {
			return false;
		}
		
		// The attachment post must have a parent post.
		if ( ! $attachment_post->post_parent ) {
			return false;
		}
		
		// The parent post must match the passed post ID.
		if ( $post_id != $attachment_post->post_parent ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Get all the attachments belonging to this post
	 */
	public function getDocumentPages( $post_id ) {
		
		// Get attachments posts.
		$args = array(
			'post_type'   => 'attachment', 
			'post_parent' => $post_id, 
			'numberposts' => -1, // get all attachment posts
			'orderby'     => 'menu_order', 
			'order'       => 'ASC',
		);
		$attachment_posts = get_posts( $args );
		
		// Build the attachments array.
		$attachments = array();
		foreach ( $attachment_posts as $attachment_post ) {
			$attachments[$attachment_post->ID] = $attachment_post->post_title;
		}
		
		return $attachments;
	}
	
	/**
	 * Get the attachment post file URL.
	 */
	public function getDocumentPageFileUrl( $post_id, $attachment_post_id ) {
		return wp_get_attachment_url( $attachment_post_id );
	}
	
	/**
	 * Get the first attachment post ID.
	 */
	public function getDocumentFirstPageId( $post_id ) {
		$args = array(
			'post_type'   => 'attachment', 
			'post_parent' => $post_id, 
			'numberposts' => 1, 
			'orderby'     => 'menu_order', 
			'order'       => 'ASC',
		);
		$attachment_post = get_posts( $args );
		return $attachment_post->ID;
	}
	
	/**
	 * Get post title.
	 */
	public function getDocumentTitle( $post_id ) {
		$post = get_post( $post_id );
		return $post->post_title;
	}
	
	/**
	 * Get attachment post title.
	 */
	public function getDocumentPageName( $post_id, $attachment_post_id ) {
		$attachment_post = get_post( $attachment_post_id );
		return $attachment_post->post_title;
	}
	
	public function documentTranscriptionIsImported( $post_id ) {
		
	}
	
	public function documentPageTranscriptionIsImported( $post_id, $attachment_post_id ) {
		
	}
	
	/**
	 * Import the attachment post transcription.
	 */
	public function importDocumentPageTranscription( $post_id, $attachment_post_id, $text ) {
		$text = Scripto::removeNewPPLimitReports( $text );
		update_post_meta( $attachment_post_id, 'scripto_attachment_transcription', $text );
	}
	
	/**
	 * Import the post transcription, i.e. all attachment transcriptions of this 
	 * post.
	 */
	public function importDocumentTranscription( $post_id, $text ) {
		$text = Scripto::removeNewPPLimitReports( $text );
		update_post_meta( $post_id, 'scripto_post_transcription', $text );
	}
}
