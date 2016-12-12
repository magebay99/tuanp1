<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //$session = new Zend_Session_Namespace('Auth');
        
        // action body
    }
    
    public function checkuniqueAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $model = new Model_HubModel;
        $model->setName("users");
        $model->setPrimary("id");
        $data = Zend_Json::decode($this->_request->getPost()['data']);
        $unique = $model->CheckUnique($data["id"], $data["tableName"], $data["fieldName"], $data["value"]);
        echo Zend_Json::encode($unique);
    }

}

