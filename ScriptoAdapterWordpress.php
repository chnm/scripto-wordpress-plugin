<?php
require_once 'Scripto/Adapter/Interface.php';

class ScriptoAdapterWordpress implements Scripto_Adapter_Interface {
	
	public function documentExists( $documentId ) {
		
	}
	
	public function documentPageExists( $documentId, $pageId ) {
		
	}
	
	public function getDocumentPages( $documentId ) {
		
	}
	
	public function getDocumentPageFileUrl( $documentId, $pageId ) {
		
	}
	
	public function getDocumentFirstPageId( $documentId ) {
		
	}
	
	public function getDocumentTitle( $documentId ) {
		
	}
	
	public function getDocumentPageName( $documentId, $pageId ) {
		
	}
	
	public function documentTranscriptionIsImported( $documentId ) {
		
	}
	
	public function documentPageTranscriptionIsImported( $documentId, $pageId ) {
		
	}
	
	public function importDocumentPageTranscription( $documentId, $pageId, $text ) {
		
	}
	
	public function importDocumentTranscription( $documentId, $text ) {
		
	}
}
