<?php

class SearchController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->acl->allow(null);
        $this->_helper->_acl->deny('member', array('reindex'));
        $this->_helper->_acl->allow('admin', array('reindex'));        
    }

    public function indexAction()
    {
        $this->view->title = 'Search Results';
        
        $filters = array('q' => array('StringTrim' , 'StripTags'));
        $validators = array('q' => array('presence' => 'required'));
        $input = new Zend_Filter_Input($filters, $validators, $_GET);
        if ($input->isValid()) {
            $this->view->messages = '';
            $q = $input->getEscaped('q');
            $this->view->q = $q;
            
            // do search
            try {
                $index = Places_Search_Lucene::open(
                    SearchIndexer::getIndexDirectory());
            
                $results = $index->find($q);
            } catch (Exception $e) {
                $results = array();
            }
            
            $this->view->results = $results;

        } else {
            $this->view->messages = $input->getMessages();
        }
    }
    
    public function reindexAction()
    {
    	$this->view->title = 'Search Reindex';
    	$messages = array();
    	
    	$index = Places_Search_Lucene::create(SearchIndexer::getIndexDirectory());
    	
    	$places = new Places();
    	$allPlaces = $places->fetchAll();
    	foreach($allPlaces as $place) {
    		$doc = SearchIndexer::getDocument($place);
    		$index->addDocument($doc);
    		$messages[] = 'Added Place: ' . $doc->title . ' - docRef: ' . $doc->docRef;
    	}
    	$messages[] = '';

    	$index->commit();
    	$messages[] = 'Total documents in index: ' . $index->numDocs();
    	
    	$this->view->messages = $messages;
    }
}
		
