<?php

class Places_Search_Lucene extends Zend_Search_Lucene
{

    /**
     * Create index
     *
     * @param mixed $directory
     * @return Zend_Search_Lucene_Interface
     */
    public static function create($directory)
    {
        return new Zend_Search_Lucene_Proxy(new Places_Search_Lucene($directory, true));
    }

    /**
     * Open index
     *
     * @param mixed $directory
     * @return Zend_Search_Lucene_Interface
     */
    public static function open($directory)
    {
        return new Zend_Search_Lucene_Proxy(new Places_Search_Lucene($directory, false));
    }

    /**
     * Adds a document to this index.
     *
     * @param Zend_Search_Lucene_Document $document
     */
    public function addDocument(Zend_Search_Lucene_Document $document)
    {
        // check document doesn't already exist - docRef should be unique        $docRef = $document->docRef;
        
        $term = new Zend_Search_Lucene_Index_Term($docRef, 'docRef');
        $query = new Zend_Search_Lucene_Search_Query_Term($term);
        $results = $this->find($query);

        if(count($results) > 0) {
	        foreach($results as $result)
	        {
	        	$this->delete($result->id);  
	        }
        }
        
        return parent::addDocument($document);
    }

}