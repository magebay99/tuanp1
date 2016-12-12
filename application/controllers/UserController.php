<?php

class UserController extends Zend_Controller_Action
{   
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction(){
        $session = new Zend_Session_Namespace('Auth');
        if(isset($session->data)){
            return $this->redirect("index/index");
        }
        $form = new Form_Login();        
        $this->view->form = $form;
    }
    
    public function logoutAction(){
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        $session->unsetAll();
        return $this->redirect("index/index");
    }
    
    public function registerAction(){
        $form = new Form_Register();        
        $this->view->form = $form;
    }
    
    public function DownloadImage($imgUrl, $imgName){
        $ch = curl_init($imgUrl);
        $filepath = "public/image/".$imgName.".png";
        $fp = fopen($filepath, "wb");
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        return $filepath;
    }
    
    public function fbloginAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $data = Zend_Json::decode($this->_request->getPost()['data']);        
        $imgUrl = $data["picture"];        
        $imgUrl = str_replace("https://","http://",$imgUrl);
        $fbUserId = $data["fbid"];
        $fbUsername = $data["username"];
        unset($data["picture"]);
        unset($data["fbid"]);
        $result = array();
        try{
            $data["avatar"] = $this->DownloadImage($imgUrl,$fbUserId);
            $modelUser = new Model_User;
            $modelUser->setName("users");
            $modelUser->setPrimary("id");
            if(($modelUser->CheckUnique("","users","username",$fbUsername))){
                $result = $modelUser->Register($data);
                echo Zend_Json::encode($result); 
            }
            $fbUser = $modelUser->GetUserByUsername($fbUsername);
            if(count($fbUser) == 1){
                $session = new Zend_Session_Namespace('Auth');
                $session->data = $fbUser[0];
                $result["success"] = true;
                $result["message"] = "Login with FaceBook successful!";
                echo Zend_Json::encode($result); 
            }
            else{
                $result["success"] = false;
                $result["message"] = "Login with FaceBook fail!";
                echo Zend_Json::encode($result); 
            }
            
        }
        catch(exception $ex){
            $result["success"] = false;
            $result["message"] = $ex->getMessage();
            echo Zend_Json::encode($result);
        }        
    }
    
    public function ajaxloginAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $formData = Zend_Json::decode($this->_request->getPost()['data']);
        $errorMessages = array();
        $modelUser = new Model_User;
        $modelUser->setName("users");
        $modelUser->setPrimary("id");
        $username = $formData['username'];
        $password = $formData['password'];
        $data = $modelUser->GetUserByUsername($username);
        if(count($data) != 0){
            if($data[0]["password"] == $password){
                $session = new Zend_Session_Namespace('Auth');
                $session->data = $data[0];
            }
            else{
                array_push($errorMessages,"password is wrong");
            }
        }
        else {
            array_push($errorMessages,"username is wrong");
        }
        
        echo Zend_Json::encode($errorMessages);
    }
    
    public function ajaxregisterAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $formData = Zend_Json::decode($this->_request->getPost()['data']);
        $modelUser = new Model_User;
        $modelUser->setName("users");
        $modelUser->setPrimary("id");
        $id = $modelUser->Register($formData);
        echo Zend_Json::encode($id);
    }
    
    public function adduserAction(){
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            return $this->redirect("index/index");
        }
        if($session->data["level"] != 2){
            return $this->redirect("index/index");
        }
        $form = new Form_AddUser();
        $this->view->form = $form;
    }
    
    public function ajaxadduserAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{
            if($session->data["level"] != 2){
                echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
            }
            else{
                $formData = Zend_Json::decode($this->_request->getPost()['data']);
                $modelUser = new Model_User;
                $modelUser->setName("users");
                $modelUser->setPrimary("id");            
                $result = $modelUser->AddUser($formData, $session->data);
                echo Zend_Json::encode($result);   
            }            
        }
    }
    
    public function listuserAction(){
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            return $this->redirect("index/index");
        }
        if($session->data["level"] != 2){
            return $this->redirect("index/index");
        }
    }
    
    public function getlistuserAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{
            if($session->data["level"] != 2){
                echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
            }
            else{
                $formData = Zend_Json::decode($this->_request->getPost()['data']);
                $modelUser = new Model_User;
                $modelUser->setName("users");
                $modelUser->setPrimary("id");
                $data = $modelUser->GetUserByCondition($formData["condition"], $formData["orderby"], $formData["limit"], $formData["offset"]);
                echo Zend_Json::encode($data);      
            }            
        }        
    }
    
    public function updateuserAction(){
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            return $this->redirect("index/index");
        }
        if($session->data["level"] != 2){
            return $this->redirect("index/index");
        }
        if(empty($_GET["userId"])){
            return $this->redirect("index/index");
        }
        $userId = $_GET["userId"];
        $form = new Form_UpdateUser();
        $modelUser = new Model_User;
        $modelUser->setName("users");
        $modelUser->setPrimary("id");
        $user = $modelUser->GetUserById($userId);
        if(count($user) == 0){
            return $this->redirect("index/index");
        }
        $user = $user[0];
        $form->populate($user);
        $this->view->form = $form;
        $this->view->userId = $userId;
    }
    
    public function ajaxupdateuserAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{
            if($session->data["level"] != 2){
                echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
            }
            else{
                $formData = Zend_Json::decode($this->_request->getPost()['data']);
                $modelUser = new Model_User;
                $modelUser->setName("users");
                $modelUser->setPrimary("id");        
                $result = $modelUser->UpdateUser($formData, $session->data);
                echo Zend_Json::encode($result);   
            }            
        }
    }
    
    public function deleteuserAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{
            if($session->data["level"] != 2){
                echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
            }
            else{
                $userId = $this->_request->getPost()['userId'];
                $modelUser = new Model_User;
                $modelUser->setName("users");
                $modelUser->setPrimary("id");
                $user = $modelUser->GetUserById($userId);
                if(count($user) == 0){
                    return $this->redirect("index/index");
                }
                $user = $user[0];
                $result = $modelUser->DeleteUser($user, $session->data);
                echo Zend_Json::encode($result);   
            }            
        }
    }
}

