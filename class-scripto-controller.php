<?php
/**
 * Controls the actions necessary to run the Scripto application.
 */
class Scripto_Controller
{
	/**
	 * @var The instance of this class.
	 */
	protected static $_instance;
	
	/**
	 * @var The Scripto object.
	 */
	protected $_scripto;
	
	/**
	 * @var The post ID of the Scripto application page.
	 */
	protected $_application_page_id;
	
	/**
	 * @var The HTML of the current view.
	 */
	protected $_view = '';
	
	/**
	 * @var The variables to be assigned to the view.
	 */
	protected $_view_vars = array();
	
	/**
	 * @var The message to display on the view.
	 */
	protected $_message = '';
	
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
	 * Construct the Scripto controller.
	 * 
	 * Instantiate using self::get_instance().
	 * 
	 * @param Scripto $scripto
	 * @return string
	 */
	protected function __construct( Scripto $scripto ) {
		$this->_scripto = $scripto;
		$this->_application_page_id = get_option( 'scripto_application_page_id' );
	}
	
	/**
	 * Scripto controller singleton.
	 * 
	 * Must pass the Scripto object on the first instantiation.
	 * 
	 * @param Scripto|null $scripto
	 * @return Scripto
	 */
	public static function get_instance( $scripto = null ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $scripto );
		}
		return self::$_instance;
	}
	
	/**
	 * The index action.
	 */
	public function index_action() {
		if ( $this->_scripto->isLoggedIn() ) {
			$this->redirect( 'user_document_pages' );
		}
	}
	
	/**
	 * The user document pages action.
	 */
	public function user_document_pages_action() {
		
		// Only logged in users have user document pages.
		if ( ! $this->_scripto->isLoggedIn() ) {
			$this->redirect( 'index' );
		}
		
		$_user_document_pages = $this->_scripto->getUserDocumentPages( 100 );
		
		$i = 0;
		$user_document_pages = array();
		foreach ( $_user_document_pages as $user_document_page) {
			
			// "Document Page Name" column.
			if ( 1 == $user_document_page['namespace_index'] ) {
				$scripto_action = 'talk';
				$page_name = 'Talk: ' . $user_document_page['document_page_name'];
			} else {
				$scripto_action = 'transcribe';
				$page_name = $user_document_page['document_page_name'];
			}
			$params = array(
				'scripto_doc_id'      => $user_document_page['document_id'], 
				'scripto_doc_page_id' => $user_document_page['document_page_id']
			);
			$url_transcribe = $this->scripto_url( $scripto_action, $params );
			$document_page_name = '<a href="' . $url_transcribe . '">' . $page_name . '</a>';
			$user_document_pages[$i]['document_page_name'] = $document_page_name;
			
			// "Most Recent Contribution"
			$user_document_pages[$i]['most_recent_contribution'] = gmdate('H:i:s M d, Y', strtotime($user_document_page['timestamp']));
			
			// "Document Title" column.
			$url_post = site_url( '?p=' . $user_document_page['document_id'] );
			$document_title = '<a href="' . $url_post . '">' . $user_document_page['document_title'] . '</a>';
			$user_document_pages[$i]['document_title'] = $document_title;
		}
		
		$this->assign( 'user_document_pages', $user_document_pages );
	}
	
	/**
	 * The recent changes action.
	 */
	public function recent_changes_action() {
		
		$_recent_changes = $this->_scripto->getRecentChanges( 100 );
		
		$i = 0;
		$recent_changes = array();
		foreach ( $_recent_changes as $recent_change ) {
			
			// "Changes" column.
			$changes = ucfirst($recent_change['action']);
			if ( ! in_array( $recent_change['action'], array('Protected', 'Unprotected') ) ) {
				$params = array(
					'scripto_doc_id'      => $recent_change['document_id'], 
					'scripto_doc_page_id' => $recent_change['document_page_id'], 
					'scripto_ns_index'    => $recent_change['namespace_index'], 
					'scripto_old_rev_id'  => $recent_change['old_revision_id'], 
					'scripto_rev_id'      => $recent_change['revision_id'], 
				);
				$url_diff = $this->scripto_url( 'diff', $params );
				$params = array(
					'scripto_doc_id'      => $recent_change['document_id'], 
					'scripto_doc_page_id' => $recent_change['document_page_id'], 
					'scripto_ns_index'    => $recent_change['namespace_index'], 
				);
				$url_history = $this->scripto_url( 'history', $params );
				if ($recent_change['new']) {
					$changes .= ' (diff | <a href="' . $url_history . '">hist</a>)';
				} else {
					$changes .= ' (<a href="' . $url_diff . '">diff</a> | <a href="' . $url_history . '">hist</a>)';
				}
			}
			$recent_changes[$i]['changes'] = $changes;
			
			// "Document Page Name" column.
			if ( 1 == $recent_change['namespace_index'] ) {
				$scripto_action = 'talk';
				$page_name = 'Talk: ' . $recent_change['document_page_name'];
			} else {
				$scripto_action = 'transcribe';
				$page_name = $recent_change['document_page_name'];
			}
			$params = array(
				'scripto_doc_id'      => $recent_change['document_id'], 
				'scripto_doc_page_id' => $recent_change['document_page_id']
			);
			$url_transcribe = $this->scripto_url( $scripto_action, $params );
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
			$url_post = site_url( '?p=' . $recent_change['document_id'] );
			$document_title = '<a href="' . $url_post . '">' . $recent_change['document_title'] . '</a>';
			$recent_changes[$i]['document_title'] = $document_title;
			
			$i++;
		}
		
		$this->assign( 'recent_changes', $recent_changes );
	}
	
	/**
	 * The transcribe action.
	 */
	public function transcribe_action() {
		
		$doc = $this->get_document_page();
		
		// Save the transcription.
		if ( isset( $_POST['scripto_transcripton'] ) ) {
			$doc->editTranscriptionPage( $_POST['scripto_transcripton'] );
		}
		
		// Import the transcription.
		if ( isset( $_POST['scripto_submit_import_page'] ) ) {
			$doc->exportPage( 'html' );
		}
		
		// Set the transcription history URL.
		$params = array(
			'scripto_doc_id'      => $_GET['scripto_doc_id'], 
			'scripto_doc_page_id' => $_GET['scripto_doc_page_id'], 
			'scripto_ns_index'    => '0', 
		);
		$url_transcription_history = $this->scripto_url( 'history', $params );
		
		// Set the talk page URL.
		$params = array(
			'scripto_doc_id'      => $_GET['scripto_doc_id'], 
			'scripto_doc_page_id' => $_GET['scripto_doc_page_id'], 
		);
		$url_talk = $this->scripto_url( 'talk', $params );
		
		$url_document = site_url( '?p=' . $doc->getId() );
		
		$this->assign( 'doc', $doc );
		$this->assign( 'url_transcription_history', $url_transcription_history );
		$this->assign( 'url_talk', $url_talk );
		$this->assign( 'url_document', $url_document );
	}
	
	/**
	 * The talk action.
	 */
	public function talk_action() {
		
		$doc = $this->get_document_page();
		
		// Save the transcription.
		if ( isset( $_POST['scripto_talk'] ) ) {
			$doc->editTalkPage( $_POST['scripto_talk'] );
		}
		
		// Set the talk history URL.
		$params = array(
			'scripto_doc_id'      => $_GET['scripto_doc_id'], 
			'scripto_doc_page_id' => $_GET['scripto_doc_page_id'], 
			'scripto_ns_index'    => '1', 
		);
		$url_talk_history = $this->scripto_url( 'history', $params );
		
		// Set the transcription page URL.
		$params = array(
			'scripto_doc_id'      => $_GET['scripto_doc_id'], 
			'scripto_doc_page_id' => $_GET['scripto_doc_page_id'], 
		);
		$url_transcription = $this->scripto_url( 'transcribe', $params );
		
		$url_document = site_url( '?p=' . $doc->getId() );
		
		$this->assign( 'doc', $doc );
		$this->assign( 'url_talk_history', $url_talk_history );
		$this->assign( 'url_transcription', $url_transcription );
		$this->assign( 'url_document', $url_document );
	}
	
	/**
	 * The history action.
	 */
	public function history_action() {
		
		// Set the default namespace index.
		if ( ! isset( $_GET['scripto_ns_index'] ) ) {
			$_GET['scripto_ns_index'] = '0';
		}
		
		$doc = $this->get_document_page();
		
		if ( '1' == $_GET['scripto_ns_index'] ) {
			$info = $doc->getTalkPageInfo();
			$_history = $doc->getTalkPageHistory( 100 );
		} else {
			$info = $doc->getTranscriptionPageInfo();
			$_history = $doc->getTranscriptionPageHistory( 100 );
		}
		
		$i = 0;
		$history = array();
		foreach ( $_history as $revision ) {
			
			// "Compare Changes" column.
			$params = array(
				'scripto_doc_id'      => $doc->getId(), 
				'scripto_doc_page_id' => $doc->getPageId(), 
				'scripto_ns_index'    => $_GET['scripto_ns_index'], 
				'scripto_old_rev_id'  => $revision['revision_id'], 
				'scripto_rev_id'      => $info['last_revision_id'], 
			);
			$url_current = $this->scripto_url( 'diff', $params );
			$params = array(
				'scripto_doc_id'      => $doc->getId(), 
				'scripto_doc_page_id' => $doc->getPageId(), 
				'scripto_ns_index'    => $_GET['scripto_ns_index'], 
				'scripto_old_rev_id'  => $revision['parent_id'], 
				'scripto_rev_id'      => $revision['revision_id'], 
			);
			$url_previous = $this->scripto_url( 'diff', $params );
			if ( $revision['revision_id'] != $info['last_revision_id'] ) {
				$current = '<a href="' . $url_current . '">current</a>';
			} else {
				$current = 'current';
			}
			if ( '0' != $revision['parent_id'] ) {
				$previous = '<a href="' . $url_previous . '">previous</a>';
			} else {
				$previous = 'previous';
			}
			$history[$i]['compare_changes'] = "($current | $previous)";
			
			// "Changed on" column.
			$params = array(
				'scripto_doc_id'      => $doc->getId(), 
				'scripto_doc_page_id' => $doc->getPageId(), 
				'scripto_ns_index'    => $_GET['scripto_ns_index'], 
				'scripto_rev_id'      => $revision['revision_id'], 
			);
			$url_revision = $this->scripto_url( 'revision', $params );
			$changed_on = '<a href="' . $url_revision . '">' . date( 'H:i:s M d, Y', strtotime( $revision['timestamp'] ) ) . '</a>';
			$history[$i]['changed_on'] = $changed_on;
			
			// "Changed by" column.
			$history[$i]['changed_by'] = $revision['user'];
			
			// "Size (bytes)" column.
			$history[$i]['size'] = $revision['size'];
			
			// "Action" column.
			$history[$i]['action'] = ucfirst($revision['action']);
			
			$i++;
		}
		
		$params = array(
			'scripto_doc_id'      => $doc->getId(), 
			'scripto_doc_page_id' => $doc->getPageId(), 
		);
		$url_transcribe = $this->scripto_url( 'transcribe', $params );
		$url_talk = $this->scripto_url( 'talk', $params );
		
		$url_document = site_url( '?p=' . $doc->getId() );
		
		$this->assign( 'doc', $doc );
		$this->assign( 'history', $history );
		$this->assign( 'url_transcribe', $url_transcribe );
		$this->assign( 'url_talk', $url_talk );
		$this->assign( 'url_document', $url_document );
	}
	
	/**
	 * The diff action.
	 */
	public function diff_action() {
		
		// Set the default namespace index.
		if ( ! isset( $_GET['scripto_ns_index'] ) ) {
			$_GET['scripto_ns_index'] = '0';
		}
		
		$doc = $this->get_document_page();
		
		$old_revision = $this->_scripto->getRevision( $_GET['scripto_old_rev_id'] );
		$revision = $this->_scripto->getRevision( $_GET['scripto_rev_id'] );
		
		$col_1_header = 'Revision as of ' . date('H:i:s, M d, Y', strtotime($old_revision['timestamp'])) . '<br />';
		$col_1_header .= ucfirst( $old_revision['action'] ) . ' by ' . $old_revision['user'];
		
		$col_2_header = 'Revision as of ' . date('H:i:s, M d, Y', strtotime($revision['timestamp'])) . '<br />';
		$col_2_header .= ucfirst( $revision['action'] ) . ' by ' . $revision['user'];
		
		$revision_as_of = date( 'H:i:s, M d, Y', strtotime( $revision['timestamp'] ) );
		
		$revision_html = $revision['html'];
		
		$params = array(
			'scripto_doc_id'      => $doc->getId(), 
			'scripto_doc_page_id' => $doc->getPageId(), 
		);
		$url_transcribe = $this->scripto_url( 'transcribe', $params );
		$url_talk = $this->scripto_url( 'talk', $params );
		
		$url_document = site_url( '?p=' . $doc->getId() );
		
		$this->assign( 'doc', $doc );
		$this->assign( 'col_1_header', $col_1_header );
		$this->assign( 'col_2_header', $col_2_header );
		$this->assign( 'revision_as_of', $revision_as_of );
		$this->assign( 'revision_html', $revision_html );
		$this->assign( 'url_transcribe', $url_transcribe );
		$this->assign( 'url_talk', $url_talk );
		$this->assign( 'url_document', $url_document );
	}
	
	/**
	 * The revision action.
	 */
	public function revision_action() {
		
		// Set the default namespace index.
		if ( ! isset( $_GET['scripto_ns_index'] ) ) {
			$_GET['scripto_ns_index'] = '0';
		}
		
		$doc = $this->get_document_page();
		$revision = $this->_scripto->getRevision( $_GET['scripto_rev_id'] );
		
		if ( isset( $_POST['scripto_submit_revert'] ) ) {
			if ( '1' == $_GET['scripto_ns_index'] ) {
				$doc->editTalkPage($revision['wikitext']);
			} else {
				$doc->editTranscriptionPage($revision['wikitext']);
			}
			$params = array(
				'scripto_doc_id'      => $_GET['scripto_doc_id'], 
				'scripto_doc_page_id' => $_GET['scripto_doc_page_id'], 
				'scripto_ns_index'    => $_GET['scripto_ns_index'], 
			);
			$this->redirect( 'history', $params );
		}
		
		$params = array(
			'scripto_doc_id'      => $doc->getId(), 
			'scripto_doc_page_id' => $doc->getPageId(), 
		);
		$url_transcribe = $this->scripto_url( 'transcribe', $params );
		$url_talk = $this->scripto_url( 'talk', $params );
		
		$url_document = site_url( '?p=' . $doc->getId() );
		
		$this->assign( 'doc', $doc );
		$this->assign( 'revision', $revision );
		$this->assign( 'url_transcribe', $url_transcribe );
		$this->assign( 'url_talk', $url_talk );
		$this->assign( 'url_document', $url_document );
	}
	
	/**
	 * The login action.
	 */
	public function login_action() {
		
		if ( $this->_scripto->isLoggedIn() ) {
			$this->redirect( 'user_document_pages' );
		}
		
		// Handle the login form.
		if ( isset( $_POST['scripto_username'], $_POST['scripto_password'] ) ) {
			try {
				$this->_scripto->login( $_POST['scripto_username'], $_POST['scripto_password'] );
				$this->redirect( 'user_document_pages' );
				exit;
			} catch ( Scripto_Service_Exception $e ) {
				$this->set_message( $e->getMessage() );
			}
		}
	}
	
	/**
	 * The logout action.
	 */
	public function logout_action() {
		$this->_scripto->logout();
		$this->redirect( 'index' );
		exit;
	}
	
	/**
	 * The error page.
	 */
	public function error_action() {
		// Empty the view content that was set before the error.
		$this->reset_view();
	}
	
	/**
	 * Get the main navigation for the view.
	 * 
	 * @return string
	 */
	public function get_navigation() {
		$navigation = '';
		if ( in_array( $_GET['scripto_action'], array('index', 'user_document_pages') ) ) {
			$navigation .= 'home';
		} else {
			$navigation .= '<a href="' . $this->scripto_url( 'index' ) . '">home</a>';
		}
		$navigation .= ' | ';
		if ( 'recent_changes' == $_GET['scripto_action'] ) {
			$navigation .= 'recent changes';
		} else {
			$navigation .= '<a href="' . $this->scripto_url( 'recent_changes' ) . '">recent changes</a>';
		}
		$navigation .= ' | ';
		if ( $this->_scripto->isLoggedIn() ) {
			$navigation .= 'logged in as ' . $this->_scripto->getUserName() . ' (<a href="' . $this->scripto_url( 'logout' ) . '">logout</a>)';
		} else {
			if ( 'login' == $_GET['scripto_action'] ) {
				$navigation .= 'login';
			} else {
				$navigation .= '<a href="' . $this->scripto_url( 'login' ) . '">login</a>';
			}
		}
		return $navigation;
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
	
	/**
	 * Get the document page via URL parameters.
	 * 
	 * @throws Scripto_Exception
	 * @return Scripto_Document
	 */
	public function get_document_page() {
		
		if ( ! isset( $_GET['scripto_doc_id'], $_GET['scripto_doc_page_id'] ) ) {
			throw new Scripto_Exception( 'Missing document ID and/or document page ID.' );
		}
		
		// Load the Scripto document and page.
		$doc = $this->_scripto->getDocument( $_GET['scripto_doc_id'] );
		$doc->setPage( $_GET['scripto_doc_page_id'] );
		
		return $doc;
	}
	
	/**
	 * Build a URL to a Scripto action.
	 * 
	 * @param string $action_name
	 * @param array $params
	 * @return string
	 */
	public function scripto_url( $action_name, $params = array() ) {
		$page_params = array(
			'p' => $this->_application_page_id, 
			'scripto_action' => $action_name, 
		);
		$params = $page_params + $params;
		$url = site_url( '?' . http_build_query( $params ) );
		return $url;
	}
	
	/**
	 * Redirect to another action.
	 * 
	 * @param string $action_name
	 * @param array $params
	 */
	public function redirect( $action_name, $params = array() ) {
		wp_redirect( $this->scripto_url( $action_name, $params ) );
		exit;
	}
	
	/**
	 * Dispatch the specified action.
	 * 
	 * The passed action name must correspond to a method in this class that is 
	 * suffixed with "_action". Must be called before output is sent ot the 
	 * browser. This strategy separates business logic from the view script, 
	 * simulating the MVC pattern. View scripts exist in the views/ directory.
	 * 
	 * @throws Scripto_Exception
	 * @param string $action_name
	 */
	public function dispatch( $action_name ) {
		
		// Dispatch the action.
		$action_method = $action_name . '_action';
		if ( ! method_exists( $this, $action_method ) ) {
			throw new Scripto_Exception( "The Scripto action \"$action_name\" does not exist." );
		}
		$this->$action_method();
		
		// Set the view.
		$view_path = dirname( __FILE__ ) . "/views/$action_name.php";
		if ( ! is_file( $view_path ) ) {
			throw new Scripto_Exception( "The Scripto view script \"$action_name\" does not exist." );
		}
		
		// Import passed variables into the current scope.
		extract( $this->_view_vars );
		
		// Output buffer and include the view, then set the content.
		ob_start();
		include $view_path;
		$this->_view = ob_get_contents();
		ob_end_clean();
	}
	
	/**
	 * Render the view.
	 * 
	 * Assumes self::dispatch() has already been called and that the page 
	 * content is complete. May be called after output is sent to the browser.
	 */
	public function render() {
		echo $this->_view;
	}
	
	/**
	 * Assign a variable to the view.
	 */
	public function assign( $name, $value ) {
		$this->_view_vars[$name] = $value;
	}
	
	/**
	 * Set a message to be shown.
	 * 
	 * @param string $message
	 */
	public function set_message( $message ) {
		$this->_message = $message;
	}
	
	/**
	 * Get the message.
	 */
	public function get_message() {
		return $this->_message;
	}
	
	/**
	 * Reset the view.
	 */
	public function reset_view() {
		$this->_view = '';
	}
}
