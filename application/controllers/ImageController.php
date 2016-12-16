<?php

class ImageController extends Zend_Controller_Action
{

    public function init()
    {        
        /* Initialize action controller here */
        $session = new Zend_Session_Namespace('Auth');
        $userLogin = $session->data;
        if(empty($userLogin)){
            return $this->redirect("index/index");
        }
    }

    public function indexAction()
    {
        // action body
    }

    public function listimageAction(){
        $listImage = array();
        if(!empty($_GET["albumId"])){
            $session = new Zend_Session_Namespace('Auth');
            $albumId = $_GET["albumId"];
            $modelImage = new Model_Image;
            $userLoginId = $session->data["id"];
            $listImage = $modelImage->GetImageByAlbumId($albumId, $userLoginId);
            if(count($listImage) == 0){
                return $this->redirect("user/index");
            }      
            $this->view->albumId = $_GET["albumId"];
            $this->view->listImage = $listImage;
        }
        else{
            return $this->redirect("user/index");
        }
    }
    
    public function addimageAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $newImage = Zend_Json::decode($this->_request->getPost("data"));
            $filePath = "public/album/".$session->data["id"]."/".$newImage["album_id"]."/".$newImage["name"].".png";
            copy($newImage["path"],$filePath);
            $newImage["path"] = $filePath;
            $modelImage = new Model_Image;
            $result = $modelImage->AddImage($newImage);
            echo Zend_Json::encode($result);
        }        
    }
    
    public function updateimagenameAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $updatedImage = Zend_Json::decode($this->_request->getPost("data"));
            $modelImage = new Model_Image;
            $result = $modelImage->UpdateImage($updatedImage);
            echo Zend_Json::encode($result);
        }        
    }
    
    public function updatemainimageAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $updatedImage = Zend_Json::decode($this->_request->getPost("data"));            
            $modelImage = new Model_Image;
            $mainImage = $modelImage->GetMainImage($updatedImage["album_id"]);
            $mainImage["main_image"] = false;
            $result = $modelImage->UpdateImage($mainImage);
            if($result["success"]){
                $result = $modelImage->UpdateImage($updatedImage);   
            }            
            echo Zend_Json::encode($result);
        }        
    }
    
    public function searchimageAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $name = $this->_request->getPost("name");            
            $albumId = $this->_request->getPost("albumId");    
            $userLoginId = $session->data["id"];    
            $modelImage = new Model_Image;
            $listImage = $modelImage->GetImageByName($name, $albumId, $userLoginId);    
            echo Zend_Json::encode($listImage);
        }
    }
    
    public function deleteimageAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $imageId = $this->_request->getPost("imageId");            
            $albumId = $this->_request->getPost("albumId");    
            $userLoginId = $session->data["id"];    
            $modelImage = new Model_Image;
            $listImage = $modelImage->CheckDeleteImage($imageId, $albumId, $userLoginId);    
            if(count($listImage) == 1){
                $result = $modelImage->DeleteImage($listImage[0]);
            }
            else{
                $result = array("success" => false, "message" => "You can't delete this image");
            }
            echo Zend_Json::encode($result);
        }
    }
}

