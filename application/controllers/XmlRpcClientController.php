<?php

class XmlRpcClientController extends Zend_Controller_Action 
{
    public function init()
    {
        $this->_helper->acl->allow(null);
    }

    public function indexAction()
    {
        $blogger = new Blogger;
        $metaweblog = new Metaweblog;
        $movableType = new MovableType;

        $config = Zend_Registry::get('config');
        $server = 'http://' . $config->server . '/xmlrpc/';
        $client = new Zend_XmlRpc_Client($server);

        echo '<h1>blogger.getUsersBlogs</h1>';
        try {
            $usersBlogs = $client->call('blogger.getUsersBlogs', array('12','nick','nick'));
            var_dump($usersBlogs);
            var_dump($client->getLastResponse()->__toString());
        } catch (Zend_XmlRpc_HttpException $e) {
            echo 'HTTP Error: ';
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
        } catch (Zend_XmlRpc_FaultException $e) {
            echo 'XML-RPC Error: ';
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
        }

/*
        echo '<h1>blogger.getUserInfo</h1>';
        try {
            $userInfo = $client->call('blogger.getUserInfo', array('12','nick','nick'));
            var_dump($userInfo);
            var_dump($client->getLastResponse()->__toString());
        } catch (Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
            var_dump($blogger->getUserInfo('12','nick','nick'));
        }

        echo '<h1>metaWeblog.getRecentPosts</h1>';
        try {
            $recentPosts = $client->call('metaWeblog.getRecentPosts', array('12','nick','nick', 5));
            var_dump($recentPosts);
            var_dump($client->getLastResponse()->__toString());
        } catch (Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
            var_dump($metaweblog->getRecentPosts('12','nick','nick', 5));
        }

        echo '<h1>metaWeblog.getCategories</h1>';
        $categories = $client->call('metaWeblog.getCategories', array('12','nick','nick'));
        var_dump($categories);
        var_dump($client->getLastResponse()->__toString());

        echo '<h1>metaWeblog.getPost</h1>';
        $getPost = $client->call('metaWeblog.getPost', array('9','nick','nick'));
        var_dump($getPost);
        var_dump($client->getLastResponse()->__toString());
        var_dump($metaweblog->getPost('1','nick','nick'));

        echo '<h1>mt.getCategoryList</h1>';
        $categories = $client->call('mt.getCategoryList', array('12','nick','nick'));
        var_dump($categories);
        var_dump($client->getLastResponse()->__toString());

        echo '<h1>mt.getPostCategories</h1>';
        try {
            $categories = $client->call('mt.getPostCategories', array('1','nick','nick'));
            var_dump($categories);
            var_dump($client->getLastResponse()->__toString());
        } catch (Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
            var_dump($movableType->getPostCategories('1','nick','nick'));
        }

        echo '<h1>mt.setPostCategories</h1>';
        try {
            $categories = $client->call('mt.setPostCategories', array('1','nick','nick', array(array('categoryId' => 2, 'isPrimary' => false))));
            var_dump($categories);
            var_dump($client->getLastResponse()->__toString());
        } catch (Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
            var_dump($movableType->setPostCategories('1','nick','nick', array(array('categoryId' => 2), array('isPrimary' => false))));
        }

        echo '<h1>metaWeblog.editPost</h1>';
        try {
            $editPost = $client->call('metaWeblog.editPost', array('1','nick','nick', array('title' => 'A New Title'), true));
            var_dump($editPost);
            var_dump($client->getLastResponse()->__toString());
        } catch (Exception $e) {
            echo $e->getCode() . ': ' . $e->getMessage() . "\n";
            var_dump($metaweblog->editPost('1','nick','nick', array('title' => 'A New Title'), true));
        }
 */
        $this->_helper->viewRenderer->setNoRender();
    }
}