<?php
/**
 * Contains the Scripto application.
 */
class Scripto_Application
{
	protected static $_instance;
	
	/**
	 * @var The Scripto object.
	 */
	protected $_scripto;
	
	/**
	 * @var The content of the current page.
	 */
	protected $_content;
	
	/**
	 * @var MIME types compatible with Zoom.it.
	 */
	protected $_mime_types_zoom_it = array(
		// gif
		'image/gif', 'image/x-xbitmap', 'image/gi_', 
		// jpg
		'image/jpeg', 'image/jpg', 'image/jpe_', 'image/pjpeg', 
		'image/vnd.swiftview-jpeg', 
		// png
		'image/png', 'application/png', 'application/x-png', 
		// bmp
		'image/bmp', 'image/x-bmp', 'image/x-bitmap', 
		'image/x-xbitmap', 'image/x-win-bitmap', 
		'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 
		'application/bmp', 'application/x-bmp', 
		'application/x-win-bitmap', 
		// ico
		'image/ico', 'image/x-icon', 'application/ico', 'application/x-ico', 
		'application/x-win-bitmap', 'image/x-win-bitmap', 
		// tiff
		'image/tiff', 
	);
	
	/**
	* @var MIME types compatible with Google Docs viewer.
	*/
	protected $_mime_types_google_docs = array(
		// pdf
		'application/pdf', 'application/x-pdf', 
		'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 
		'text/x-pdf', 
		// docx
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
		// doc
		'application/msword', 'application/doc', 'appl/text', 
		'application/vnd.msword', 'application/vnd.ms-word', 
		'application/winword', 'application/word', 'application/vnd.ms-office', 
		'application/x-msw6', 'application/x-msword', 
		// ppt
		'application/vnd.ms-powerpoint', 'application/mspowerpoint', 
		'application/ms-powerpoint', 'application/mspowerpnt', 
		'application/vnd-mspowerpoint', 'application/powerpoint', 
		'application/x-powerpoint', 'application/x-m', 
		// pptx
		'application/vnd.openxmlformats-officedocument.presentationml.presentation', 
		// xls
		'application/vnd.ms-excel', 'application/msexcel', 
		'application/x-msexcel', 'application/x-ms-excel', 
		'application/vnd.ms-excel', 'application/x-excel', 
		'application/x-dos_ms_excel', 'application/xls', 
		// xlsx
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
		// tiff
		'image/tiff', 
		// ps, ai
		'application/postscript', 'application/ps', 
		'application/x-postscript', 'application/x-ps', 
		'text/postscript', 'application/x-postscript-not-eps', 
		// eps
		'application/eps', 'application/x-eps', 'image/eps', 
		'image/x-eps', 
		// psd
		'image/vnd.adobe.photoshop', 'image/photoshop', 
		'image/x-photoshop', 'image/psd', 'application/photoshop', 
		'application/psd', 'zz-application/zz-winassoc-psd', 
		// dxf
		'application/dxf', 'application/x-autocad', 
		'application/x-dxf', 'drawing/x-dxf', 'image/vnd.dxf', 
		'image/x-autocad', 'image/x-dxf', 
		'zz-application/zz-winassoc-dxf', 
		// xvg
		'image/svg+xml', 
		// xps
		'application/vnd.ms-xpsdocument', 
	);
	
	/**
	 * Construct the Scripto application.
	 * 
	 * Instantiate using self::get_instance().
	 * 
	 * @param Scripto $scripto The Scripto object.
	 * @return string
	 */
	protected function __construct( Scripto $scripto ) {
		$this->_scripto = $scripto;
	}
	
	/**
	 * Scripto application singleton.
	 * 
	 * @param Scripto|null $scripto
	 */
	public static function get_instance( $scripto = null ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $scripto );
		}
		return self::$_instance;
	}
	
	/**
	 * The transcribe page.
	 * 
	 * @return string
	 */
	public function transcribe_action() {
		
		$doc = $this->get_document_page();
		
		// Save the transcription.
		if ( isset( $_POST['scripto_transcripton'] ) ) {
			$doc->editTranscriptionPage( $_POST['scripto_transcripton'] );
		}
		
		$media_viewer = $this->get_media_viewer( $_GET['scripto_doc_page_id'] );
		
		$vars = array(
			'media_viewer' => $media_viewer, 
			'scripto' => $this->_scripto, 
			'doc' => $doc, 
		);
		return $this->_set_content( 'transcribe', $vars);
	}
	
	/**
	 * The transcription page history page.
	 * 
	 * @return string
	 */
	public function transcription_page_history_action() {
		
		$doc = $this->get_document_page();
		$this->_set_content( 'transcription-page-history', compact( 'doc' ) );
	}
	
	/**
	 * The recent changes page.
	 * 
	 * @return string
	 */
	public function recent_changes_action() {
		
		$_recent_changes = $this->_scripto->getRecentChanges( 100 );
		$application_page_id = get_option( 'scripto_application_page_id' );
		
		$i = 0;
		$recent_changes = array();
		foreach ( $_recent_changes as $recent_change ) {
			// "Changes" column.
			$changes = ucfirst($recent_change['action']);
			if ( ! in_array( $recent_change['action'], array('Protected', 'Unprotected') ) ) {
				$url_diff_params = array(
					'p'                   => $application_page_id, 
					'scripto_action'      => 'diff', 
					'scripto_doc_id'      => $recent_change['document_id'], 
					'scripto_doc_page_id' => $recent_change['document_page_id'], 
					'scripto_ns_index'    => $recent_change['namespace_index'], 
					'scripto_old_rev_id'  => $recent_change['old_revision_id'], 
					'scripto_rev_id'      => $recent_change['revision_id'], 
				);
				$url_diff = site_url( '?' . http_build_query( $url_diff_params ) );
				$url_history_params = array(
					'p'                   => $application_page_id, 
					'scripto_action'      => 'history', 
					'scripto_doc_id'      => $recent_change['document_id'], 
					'scripto_doc_page_id' => $recent_change['document_page_id'], 
					'scripto_ns_index'    => $recent_change['namespace_index'], 
				);
				$url_history = site_url( '?' . http_build_query( $url_history_params ) );
				if ($recent_change['new']) {
					$changes .= ' (diff | <a href="' . $url_diff . '">hist</a>)';
				} else {
					$changes .= ' (<a href="' . $url_diff . '">diff</a> | <a href="' . $url_history . '">hist</a>)';
				}
			}
			$recent_changes[$i]['changes'] = $changes;
			
			// "Document Page Name" column.
			if ( 1 == $recent_change['namespace_index'] ) {
				$scripto_action = 'discuss';
				$page_name = 'Talk: ' . $recent_change['document_page_name'];
			} else {
				$scripto_action = 'transcribe';
				$page_name = $recent_change['document_page_name'];
			}
			$url_transcribe_params = array(
				'p'                   => $application_page_id, 
				'scripto_action'      => $scripto_action, 
				'scripto_doc_id'      => $recent_change['document_id'], 
				'scripto_doc_page_id' => $recent_change['document_page_id']
			);
			$url_transcribe = site_url( '?' . http_build_query( $url_transcribe_params ) );
			$document_page_name = '<a href="' . $url_transcribe . '">' . $page_name . '</a>';
			$recent_changes[$i]['document_page_name'] = $document_page_name;
			
			// "Changed on" column.
			$recent_changes[$i]['changed_on'] = date( 'H:i:s M d, Y', strtotime( $recent_change['timestamp'] ) );
			
			// "Length Changed" column.
			$length_changed = $recent_change['new_length'] - $recent_change['old_length'];
			if ( 0 <= $length_changed ) {
				$length_changed = "+$length_changed";
			}
			$recent_changes[$i]['length_changed'] = $length_changed;
			
			// "User" column.
			$recent_changes[$i]['user'] = $recent_change['user'];
			
			// "Document Title" column.
			$url_post = site_url( '?' . array('p' => $recent_change['document_id']) );
			$document_title = '<a href="' . $url_post . '">' . $recent_change['document_title'] . '</a>';
			$recent_changes[$i]['document_title'] = $document_title;
			
			$i++;
		}
		
		$this->_set_content( 'recent-changes', compact( 'recent_changes' ) );
	}
	
	/**
	 * The login action.
	 * 
	 * @return string.
	 */
	public function login_action() {
		
		$url_redirect_params = array(
			'p'              => get_option( 'scripto_application_page_id' ), 
			'scripto_action' => 'recent_changes', 
		);
		if ($this->_scripto->isLoggedIn()) {
			wp_redirect( site_url( '?' . http_build_query( $url_redirect_params ) ) );
		}
		
		$error = false;
		
		// Handle the login form.
		if ( isset($_POST['scripto_username']) && isset($_POST['scripto_password']) ) {
			try {
				$this->_scripto->login( $_POST['scripto_username'], $_POST['scripto_password'] );
				wp_redirect( site_url( '?' . http_build_query( $url_redirect_params ) ) );
			} catch ( Scripto_Service_Exception $e ) {
				$error = $e->getMessage();
			}
		}
		
		$this->_set_content( 'login', compact( 'error' ) );
	}
	
	/**
	 * Get the appropriate media viewer for an attachment.
	 * 
	 * @param int $attachment_post_id 
	 * @return string
	 */
	public function get_media_viewer( $attachment_post_id ) {
		
		$mime_type = get_post_mime_type( $attachment_post_id );
		$attachment_url = wp_get_attachment_url( $attachment_post_id );
		
		// Image attachment.
		if ( in_array( $mime_type, $this->_mime_types_zoom_it ) ) {
			$args = array( 'url' => $attachment_url );
			$url = 'http://api.zoom.it/v1/content?' . http_build_query($args);
			$response = wp_remote_get( $url );
			$response_body = json_decode( $response['body'], true );
			$media_viewer = $response_body['embedHtml'];
			
		// Document attachmnet.
		} else if ( in_array( $mime_type, $this->_mime_types_google_docs ) ) {
			$args = array('url' => $attachment_url, 'embedded' => 'true' );
			$url = 'http://docs.google.com/viewer?' . http_build_query($args);
			$media_viewer = '<iframe src="' . $url . '" width="500" height="600"></iframe>';
		
		// Other attachment.
		} else {
			$media_viewer = '<a href="' . $attachment_url . '">' . get_the_title( $attachment_post_id ) . '</a>';
		}
		
		return $media_viewer;
	}
	
	public function get_document_page() {
		
		// Check for required parameters.
		if ( ! isset( $_GET['scripto_doc_id'] ) || ! isset( $_GET['scripto_doc_page_id'] ) ) {
			throw new Scripto_Exception( 'Missing required parameters.' );
		}
		
		// Load the Scripto document and page.
		$doc = $this->_scripto->getDocument( $_GET['scripto_doc_id'] );
		$doc->setPage( $_GET['scripto_doc_page_id'] );
		
		return $doc;
	}
	
	/**
	 * Run the specified action.
	 * 
	 * The passed action name must correspond to a method in this class, 
	 * suffixed with "_action". Must be called before output is sent ot the 
	 * browser.
	 * 
	 * @param string $action_name
	 * @return string
	 */
	public function run_action( $action_name ) {
		$action_method = $action_name . '_action';
		if ( ! method_exists( $this, $action_method ) ) {
			throw new Scripto_Exception( 'The Scripto application action does not exist.' );
		}
		$this->$action_method();
	}
	
	/**
	 * Get the application page content.
	 * 
	 * Assumes self::set_page_content() has already been run. May be called 
	 * after output is sent to the browser.
	 * 
	 * @return string
	 */
	public function get_content() {
		return $this->_content;
	}
	
	/**
	 * Get the specified template.
	 * 
	 * Used in methods ending with "_action" to get the content of a page. This 
	 * separates business logic from the HTML template, simulating the MVC 
	 * pattern. Template files exist in the templates/ directory.
	 * 
	 * @param string $template_name
	 * @param array $vars
	 * @return string
	 */
	protected function _set_content( $template_name, $vars = array() ) {
		
		// Import passed variables into the current scope.
		extract( $vars );
		
		// Output buffer and include the template, then set the content.
		ob_start();
		include "templates/$template_name.php";
		$this->_content = ob_get_contents();
		ob_end_clean();
	}
}
