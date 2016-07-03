<?php
require_once dirname(__FILE__) . '/../TestConfiguration.php';

require_once '../application/models/Places.php';

class Models_PlacesTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // reset database to known state
        TestConfiguration::setupDatabase();       
    }

    public function testFetchAll()
    {
        $placesFinder = new Places();
        $places = $placesFinder->fetchAll(); 
        
        $this->assertSame(3, $places->count());
    }
    
    public function testFetchLatest()
    {
        $placesFinder = new Places();
        $places = $placesFinder->fetchLatest(1);
        
        $this->assertSame(1, $places->count());
        
        $thisPlace = $places->current();
        $this->assertSame(2, (int)$thisPlace->id);
    }
}
