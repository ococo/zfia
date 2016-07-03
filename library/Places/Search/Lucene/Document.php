<?php

require_once ('Zend/Search/Lucene/Document.php');

class Places_Search_Lucene_Document extends Zend_Search_Lucene_Document
{

    public function __construct($class, $key, $title, $contents, $summary, $createdBy, $dateCreated, $keywords = array())
    {
        $this->addField(Zend_Search_Lucene_Field::Keyword('docRef', "$class:$key"));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('class', $class));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('key', $key));
    	$this->addField(Zend_Search_Lucene_Field::Text('title', $title));
        $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $contents));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('summary', $summary));
        $this->addField(Zend_Search_Lucene_Field::Keyword('createdBy', $createdBy));
        $this->addField(Zend_Search_Lucene_Field::Keyword('dateCreated', $dateCreated));
        
        if (! is_array($keywords)) {
            $keywords = explode(' ', $keywords);
        }
        foreach ($keywords as $name => $value) {
            if (! empty($name) && ! empty($value)) {
                $this->addField(Zend_Search_Lucene_Field::Keyword($name, $value));
            }
        }
    }

}
