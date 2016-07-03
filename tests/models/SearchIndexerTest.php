<?php
require_once dirname(__FILE__) . '/../TestConfiguration.php';

class models_SearchIndexerTest extends PHPUnit_Framework_TestCase
{
    protected $_indexDirectory;

    public function __construct()
    {
        parent::__construct();
        
        $this->_indexDirectory = dirname(__FILE__) . '/_files/index';
    }

    public function setUp()
    {
        // reset database to know state
        $db = Zend_Registry::get('db');
        TestConfiguration::setupDatabase($db);
    }

    public function testCreateIndex()
    {
        $index = Places_Search_Lucene::create($this->_indexDirectory);
        
        $reviewsFinder = new Reviews();
        $reviews = $reviewsFinder->fetchAll();
        $numberOfReviews = $reviews->count();
        foreach ($reviews as $review) {
            $class = 'Review';
            $key = $review->id;
            $title = 'Review: '.$review->id;
            $contents = $review->body;
            $summary = substr($review->body, 0, 30);
            $createdBy = $review->created_by;
            $dateCreated = $review->date_created;
            $doc = new Places_Search_Lucene_Document($class, $key, $title, $contents, $summary, $createdBy, $dateCreated);
        
            $index->addDocument($doc);
        }
        
        // after adding all our documents, we need to commit as we
        // have added less than the minimum for auto-commit
        $index->commit();
        
        // check we indexed the right number of docs
        $indexCount = $index->numDocs();
        $this->assertEquals($numberOfReviews, $indexCount);
        
        // get the 2nd review back and check it
        $review2 = $reviewsFinder->fetchRow('id = 2');
        $doc = $index->getDocument(1); // the internal Z_S_L indexing id starts at 0
        $indexClass = $doc->getFieldValue('class');
        $indexKey = $doc->getFieldValue('key');
        $this->assertEquals('Review', $indexClass);
        $this->assertEquals($review2->id, $indexKey);
    }
    
    public function testAddingToIndex()
    {
        $reviewsFinder = new Reviews();
        $reviews = $reviewsFinder->fetchAll();
        $numberOfReviews = $reviews->count();
    	
        try {
            $index = Places_Search_Lucene::open($this->_indexDirectory);
    	} catch(Exception $e) {
    		// if index isn't already created, then we need to add all 
    		// the reviews
    	   $index = Places_Search_Lucene::create($this->_indexDirectory);
    	}
        
        $addCount = 0;
        foreach ($reviews as $review) {
            $class = 'Review';
            $key = $review->id;
        	$title = 'Review: '.$review->id;
            $contents = $review->body;
            $summary = substr($review->body, 0, 30);
            $createdBy = $review->created_by;
            $dateCreated = $review->date_created;
            $doc = new Places_Search_Lucene_Document($class, $key, $title, $contents, $summary, $createdBy, $dateCreated);
        
            $index->addDocument($doc);
        }
        
        // after adding all our documents, we need to commit as we
        // have added less than the minimum for auto-commit
        $index->commit();
        
        $indexCount = $index->numDocs();
        $this->assertEquals($numberOfReviews, $indexCount);
        $this->assertEquals($addCount, 0);
        
        // Get the 2nd review back and check it
        // Note that we test using knowledge of what the 
        // Places_Search_Lucene_Document does to create a unique id
        $term = new Zend_Search_Lucene_Index_Term('Review:2', 'docRef');
        $query = new Zend_Search_Lucene_Search_Query_Term($term);
        $results = $index->find($query);
        $title = '';
        if(count($results) > 0) {
        	$title = $results[0]->title;
        }
        $this->assertEquals('Review: 2', $title);
    }

    public function testFind()
    {
    	$index = Places_Search_Lucene::open($this->_indexDirectory);
        
        // search        $indexCount = $index->numDocs();
        $this->assertEquals(7, $indexCount);
        
        $results = $index->find("All the family enjoyed it");

        $this->assertEquals('Review: 1', $results[0]->title);
        $this->assertContains('facilities', $results[0]->summary);
        
//        echo "\n\n";
//        foreach ($results as $result) {
//            echo $result->score . ', ';
//            echo $result->url . ', ';
//            echo $result->summary . "\n";
//        }
    
    }
}
