<?php

TestConfiguration::setup();

class TestConfiguration
{
    static $appRoot;
    
    static function setup()
    {
        // Set your Zend Framework library path(s) here - default is the master lib/ directory
        $zfRoot = realpath(dirname(basename(__FILE__)) . '/../../lib/');
        $appRoot = realpath(dirname(basename(__FILE__)) . '/..');
        
        TestConfiguration::$appRoot = $appRoot;
    
        require_once 'PHPUnit/Framework.php';
        require_once 'PHPUnit/Framework/TestSuite.php';
        require_once 'PHPUnit/TextUI/TestRunner.php';
    
        error_reporting( E_ALL | E_STRICT );
    
        set_include_path($appRoot . '/application/models/'
        . PATH_SEPARATOR . $appRoot . '/library/'
        . PATH_SEPARATOR . $zfRoot
        . PATH_SEPARATOR . $zfRoot . '/incubator'
        . PATH_SEPARATOR . get_include_path());
    
        include 'Zend/Loader.php';
        Zend_Loader::registerAutoload();
    
        // load configuration
        $section = 'test';
        $config = new Zend_Config_Ini($appRoot .'/application/configuration/config.ini', $section);
        Zend_Registry::set('config', $config);
    
        // set up database
        $db = Zend_Db::factory($config->db);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    
    }

    static function setupDatabase()
    {
        $db = Zend_Registry::get('db'); /* @var $db Zend_Db_Adapter_Abstract */

        $db->query(<<<EOT
DROP TABLE IF EXISTS places;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE places (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
date_created DATETIME NOT NULL ,
date_updated DATETIME NOT NULL ,
name VARCHAR( 100 ) NOT NULL ,
address1 VARCHAR( 100 ) NULL ,
address2 VARCHAR( 100 ) NULL ,
address3 VARCHAR( 100 ) NULL ,
town VARCHAR( 75 ) NULL ,
county VARCHAR( 75 ) NULL ,
postcode VARCHAR( 30 ) NULL ,
country VARCHAR( 75 ) NULL
)
EOT
        );

        $db->query(<<<EOT
DROP TABLE IF EXISTS reviews;
EOT
        );

        $db->query(<<<EOT
CREATE TABLE reviews (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
date_created DATETIME NOT NULL ,
date_updated DATETIME NOT NULL ,
place_id INT NOT NULL ,
body MEDIUMTEXT NOT NULL ,
rating INT NULL
);
EOT
        );
        
        $db->query(<<<EOT
INSERT INTO places (name, address1, town, county, postcode, date_created, date_updated)
VALUES 
('London Zoo', 'Regent''s Park', 'London', '', 'NW1 4RY', '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,('Alton Towers', 'Regent''s Park', 'Alton', 'Staffordshire', 'ST10 4DB', '2007-02-20 00:00:00', '2007-02-20 00:00:00')
,('Coughton Court', '', 'Alcester', 'Warwickshire', 'B49 5JA', '2007-02-16 00:00:00', '2007-02-16 00:00:00')
;
EOT
        );
        

        $db->query(<<<EOT
INSERT INTO reviews  (place_id, body, rating, date_created, date_updated)
VALUES
(1, 'The facilities here are really good. All the family enjoyed it', 4, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(1, 'Good day out, but not so many big animals now.', 2, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(1, 'Excellent food in the cafeteria. Even my 2 year old ate her lunch!', 4, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(2, 'Good for teenagers!', 2, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(2, 'A great family day out, but lots of queues!', 2, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(2, 'A fun day was had by our family!', 2, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
,(3, 'Our children enjoyed learning some of the history!', 3, '2007-02-14 00:00:00', '2007-02-14 00:00:00')
;
EOT
        );

    }
}
