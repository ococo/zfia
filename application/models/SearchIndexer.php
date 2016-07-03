<?php

class SearchIndexer
{
    /**
     * @var array Array of observers
     */
    protected static $_indexDirectory;

    public static function setIndexDirectory($directory)
    {
    	if(!is_dir($directory)) {
    		throw new Exception('Directory for SearchIndexer is invalid ('. $directory .')');
    	}
    	self::$_indexDirectory = $directory;
    }
    
    public static function getIndexDirectory()
    {
    	return self::$_indexDirectory;
    }

    public static function observeTableRow($event, $row)
    {
        switch ($event) {
            case 'post-insert':
            case 'post-update':
                $doc = self::getDocument($row);
                if ($doc !== false) {
                	self::_addToIndex($doc);
                }
                break;
        }
    }
    
    public static function getDocument(Places_Db_Table_Row_Observable $row)
    {
        if(method_exists($row, 'getSearchIndexFields')) {
            
            $fields = $row->getSearchIndexFields($row);

            $doc = new Places_Search_Lucene_Document($fields['class'], $fields['key'], 
                    $fields['title'], $fields['contents'], $fields['summary'], 
                    $fields['createdBy'], $fields['dateCreated']);
            return $doc;        
        } 
        return false;
    	
    }
    
    protected static function _addToIndex(Places_Search_Lucene_Document $doc)
    {
        try {
            $index = Places_Search_Lucene::open(self::$_indexDirectory);
        } catch (Exception $e) {
            $index = Places_Search_Lucene::create(self::$_indexDirectory);
        }
        $index->addDocument($doc);
        $index->commit();
    }
}
