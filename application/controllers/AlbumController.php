<?php

class AlbumController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function listalbumAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{
            $modelAlbum = new Model_Album;
            $modelAlbum->setName("album");
            $modelAlbum->setPrimary("id");
            $result = $modelAlbum->GetListAlbumByUserId($session->data["id"]);
            echo Zend_Json::encode($result);
        }        
    }
    
    public function addalbumAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{      
            $userPath = "public/album/".$session->data["id"];
            if(!is_dir($userPath)){
                mkdir($userPath, 0777, true);
            }
            $formData = Zend_Json::decode($this->_request->getPost()['data']);
            $newAlbum = array();
            $newAlbum["title"] = $formData["title"];
            $newAlbum["description"] = $formData["description"];
            $mainImage = array();
            $mainImage["name"] = "main image";
            $mainImage["description"] = "";
            $mainImage["path"] = $formData["path"];
            $mainImage["main_image"] = 1;
            $modelAlbum = new Model_Album;
            $modelAlbum->setName("album");
            $modelAlbum->setPrimary("id");
            $result = $modelAlbum->AddAlbum($newAlbum, $session->data);            
            if($result["success"]){
                $albumPath = $userPath."/".$result["id"];
                mkdir($albumPath, 0777, true);
                $mainImage["path"] = $albumPath."/main_image.png";                
                $modelImage = new Model_Image;
                $modelImage->setName("image");
                $modelImage->setPrimary("id");      
                $mainImage["album_id"] = $result["id"];
                $result = $modelImage->AddImage($mainImage);
                copy($formData["path"],$mainImage["path"]);
                if($result["success"]){
                    $result["album"] = $modelAlbum->GetAlbumWithMainImageById($mainImage["album_id"]);
                }
            }  
            echo Zend_Json::encode($result);
        }        
    }
    
    public function checkdeletealbumAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array());
        }
        else{        
            $albumId = $this->_request->getPost("albumId");    
            $userLoginId = $session->data["id"];    
            $modelAlbum = new Model_Album;
            $listImage = $modelAlbum->CheckDeleteAlbum($albumId, $userLoginId);
            echo Zend_Json::encode($listImage);
        }
    }
    
    public function deletealbumAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{        
            $albumId = $this->_request->getPost("albumId");
            $userLoginId = $session->data["id"];    
            $modelAlbum = new Model_Album;
            $album = $modelAlbum->GetAlbumById($albumId);
            if(count($album) == 1){
                $listImage = $modelAlbum->CheckDeleteAlbum($albumId, $userLoginId);
                if(count($listImage) > 0){
                    $modelImage = new Model_Image;
                    foreach($listImage as $image){
                        $modelImage->DeleteImage($image);
                    }
                    $result = $modelAlbum->DeleteAlbum($album[0]);
                    
                }                
                else{
                   $result = array("success" => false, "message" => "You can't delete this album!");
                }
            }
            else{
                $result = array("success" => false, "message" => "Album not found!");
            }
            echo Zend_Json::encode($result);
        }
    }
    
    public function updatetitleAction(){
        $this->_helper->layout->disableLayout();        
        $this->_helper->viewRenderer->setNoRender();
        $session = new Zend_Session_Namespace('Auth');
        if(empty($session->data)){
            echo Zend_Json::encode(array("success" => false, "message" => "Error!"));
        }
        else{
            $updatedAlbum = $this->_request->getPost("updatedAlbum");
            $userLoginId = $session->data["id"];
            $modelAlbum = new Model_Album;
            $album = $modelAlbum->CheckUpdateAlbum($updatedAlbum["id"],$userLoginId);
            if(count($album) == 1){
                $result = $modelAlbum->UpdateAlbum($updatedAlbum);
                echo Zend_Json::encode($result);
            }
            else{
                echo Zend_Json::encode(array("success" => false, "message" => "You can't update this album title"));
            }
        }
    }
}

