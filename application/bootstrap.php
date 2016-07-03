<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
class Bootstrap
{
    public function __construct($configSection = 'live')
    {
        $rootDir = dirname(dirname(__FILE__));
        define('ROOT_DIR', $rootDir);

        set_include_path(get_include_path()
            . PATH_SEPARATOR . ROOT_DIR . '/library/'
            . PATH_SEPARATOR . ROOT_DIR . '/application/models/'
            . PATH_SEPARATOR . ROOT_DIR . '/application/forms/'
	    );
        
        include 'Zend/Loader.php';
        Zend_Loader::registerAutoload();

        // Load configuration
        Zend_Registry::set('configSection', $configSection);
        $config = new Zend_Config_Ini(ROOT_DIR.'/application/configuration/config.ini', $configSection);
        Zend_Registry::set('config', $config);
        
        date_default_timezone_set($config->date_default_timezone);
        
        // configure database and store to the registery
        $db = Zend_Db::factory($config->db);
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }

    public function runApp()
    {
        // setup search indexer observer on the database rows
        SearchIndexer::setIndexDirectory(ROOT_DIR . '/var/search_index');
        Places_Db_Table_Row_Observable::attachObserver('SearchIndexer');
        
        // set up action helpers
        Zend_Controller_Action_HelperBroker::addPrefix('Places_Controller_Action_Helper_');

        // setup the layout
        Zend_Layout::startMvc(array(
            'layoutPath' => ROOT_DIR . '/application/views/layouts',
        ));    

        // acl action helper
        $acl = new Places_Acl();
        $aclHelper = new Places_Controller_Action_Helper_Acl(null, array('acl'=>$acl));
        Zend_Controller_Action_HelperBroker::addHelper($aclHelper);
        
        $ajaxContext = new Zend_Controller_Action_Helper_AjaxContext();
        Zend_Controller_Action_HelperBroker::addHelper($ajaxContext);
        
        // setup front controller
        $frontController = Zend_Controller_Front::getInstance();
        
        $frontController->throwExceptions(false);
        $frontController->setControllerDirectory(ROOT_DIR . '/application/controllers');
        
        $frontController->registerPlugin(new Places_Controller_Plugin_ActionSetup());
        $frontController->registerPlugin(new Places_Controller_Plugin_ViewSetup(), 98);
        
        // add a static route for /about
        $router = $frontController->getRouter();
        $route = new Zend_Controller_Router_Route_Static(
            'about',
            array('controller' => 'article', 'action' => 'about')
        );
        $router->addRoute('about', $route);        
        
        
        // setup the layout
        Zend_Layout::startMvc(array(
            'layoutPath' => ROOT_DIR . '/application/views/layouts',
        ));

        // run!
        try {
            $frontController->dispatch();
        } catch (Exception $exception) {
            // an exception has occurred after the ErrorController's postdispatch() has run
            if(Zend_Registry::get('config')->debug == 1) {
                $msg = $exception->getMessage(); 
                $trace = $exception->getTraceAsString();
                echo "<div>Error: $msg<p><pre>$trace</pre></p></div>"; 
            } else {
                try {
                    $logFile = Zend_Registry::get('config')->logFiles->error;
                    $log = new Zend_Log(new Zend_Log_Writer_Stream($logFile));
                    $log->debug($exception->getMessage() . "\n" .  $exception->getTraceAsString() . "\n-----------------------------");
                    die('<p>An error occurred. Please check the log file.</p>');
                } catch (Exception $e) {
                    // can't log it - display error message
                    die("<p>An error occurred with logging an error!");
                }
            }
        }
    }

    public function runXmlRpc()
    {
        //error_reporting(0);
        $writer = new Zend_Log_Writer_Stream(ROOT_DIR . '/logs/xmlrpc.log');
        $logger = new Zend_Log($writer);

        //Zend_XmlRpc_Server_Fault::attachFaultException('Exception');
        $server = new Zend_XmlRpc_Server();

        require_once 'Blogger.php';
        require_once 'Metaweblog.php';
        require_once 'MovableType.php';

        $server->setClass('Blogger', 'blogger');
        $server->setClass('Metaweblog', 'metaWeblog');
        $server->setClass('MovableType', 'mt');

        $response = $server->handle();
        $logger->info($server->getRequest()->getFullRequest());
        $response->setEncoding('UTF-8');
        header('Content-Type: text/xml; charset=UTF-8');
        echo $response;
    }
}