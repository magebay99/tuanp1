<?php
class Model_Image extends Model_HubModel{
    public function GetImageById($id){
        $strQuery = "SELECT id, name, description, path, album_id FROM image WHERE deleted IS NULL AND id='".$id."'";
        $listImg = array();
        try{
            $sql = $this->db->query($strQuery);
            $listImg = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listImg;
    }
    
    public function GetImageByAlbumId($albumId, $userLoginId){
        $strQuery = "SELECT img.id, img.name, img.description, img.path, img.main_image, img.album_id FROM image img";
        $strQuery .= " INNER JOIN album al ON al.id = img.album_id AND al.deleted IS NULL AND al.created_user='".$userLoginId."'";
        $strQuery .= " WHERE img.deleted IS NULL AND img.album_id='".$albumId."'";
        $listImg = array();
        try{
            $sql = $this->db->query($strQuery);
            $listImg = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listImg;
    }
    
    public function CheckDeleteImage($id, $albumId, $userLoginId){
        $strQuery = "SELECT img.id, img.name, img.description, img.path, img.main_image, img.album_id FROM image img";
        $strQuery .= " INNER JOIN album al ON al.id = img.album_id AND al.deleted IS NULL AND al.created_user='".$userLoginId."'";
        $strQuery .= " WHERE img.deleted IS NULL AND img.main_image <> 1 AND img.id ='".$id."' AND img.album_id='".$albumId."'";
        $listImg = array();
        try{
            $sql = $this->db->query($strQuery);
            $listImg = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listImg;
    }
    
    public function GetImageByName($name, $albumId, $userLoginId){
        $strQuery = "SELECT img.id, img.name, img.description, img.path, img.main_image, img.album_id FROM image img";
        $strQuery .= " INNER JOIN album al ON al.id = img.album_id AND al.deleted IS NULL AND al.created_user='".$userLoginId."'";
        $strQuery .= " WHERE img.deleted IS NULL AND img.name LIKE'%".$name."%' AND img.album_id='".$albumId."'";
        $listImg = array();
        try{
            $sql = $this->db->query($strQuery);
            $listImg = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listImg;
    }
    
    public function GetMainImage($albumId){
        $strQuery = "SELECT id, name, description, path FROM image WHERE deleted IS NULL AND main_image = 1 AND album_id='".$albumId."'";
        $listImg = array();
        try{
            $sql = $this->db->query($strQuery);
            $listImg = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listImg[0];
    }
    
    public function AddImage($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["created"] = $datetimeNow;
        $data["modified"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $this->db->insert("image",$data);
            $idNewImg = $this->db->lastInsertId();
            $newImage = $this->GetImageById($idNewImg);
            if(count($newImage) == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Add imgage is fail",
                );
            }
            $this->db->commit();
            return array(
                    "newImage" => $newImage,
                    "success" => true,
                    "message" => "Add imgage is successful",
            );
        }
        catch(Exception $ex){
            $this->db->rollBack();
            return array(
                "success" => false,
                "message" => $ex->getMessage(),
            );
        }
    }
    
    public function UpdateImage($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["modified"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("image", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Update image is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Update image is successful",
                );
            }
        }
        catch(Exception $ex){
            $this->db->rollBack();
            return array(
                "success" => false,
                "message" => $ex->getMessage(),
            );
        }
    }
    
    public function DeleteImage($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["deleted"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("image", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Delete image is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Delete image is successful",
                );
            }
        }
        catch(Exception $ex){
            $this->db->rollBack();
            return array(
                "success" => false,
                "message" => $ex->getMessage(),
            );
        }
    }
}
?>