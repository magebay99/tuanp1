<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload(){
    	$autoloader = new Zend_Application_Module_Autoloader(array(
            	'namespace' => '',
            	'basePath' => dirname(__FILE__),
    	   ));
    	return $autoloader;
	}
    
    protected function _initDatabase(){
        $db = $this->getPluginResource('db')->getDbAdapter();
        Zend_Registry::set('db', $db);
    }
}

