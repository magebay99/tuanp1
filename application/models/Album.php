<?php
class Model_Album extends Model_HubModel{
    public function GetAllAlbum(){
        $strQuery = "SELECT al.id AS id, al.title, al.description, img.path ";
        $strQuery .= "FROM album al ";
        $strQuery .= "INNER JOIN image img ON img.album_id = al.id AND img.deleted IS NULL AND img.main_image = 1 ";
        $strQuery .= "WHERE al.deleted IS NULL";
        $listAlbum = array();
        try{
            $sql = $this->db->query($strQuery);
            $listAlbum = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listAlbum;
    }
    
    public function GetListAlbumByUserId($id){
        $strQuery = "SELECT al.id AS id, al.title, al.description, img.path ";
        $strQuery .= "FROM album al ";
        $strQuery .= "INNER JOIN image img ON img.album_id = al.id AND img.deleted IS NULL AND img.main_image = 1 ";
        $strQuery .= "WHERE al.deleted IS NULL AND al.created_user='".$id."'";
        $listAlbum = array();
        try{
            $sql = $this->db->query($strQuery);
            $listAlbum = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listAlbum;
    }
    
    public function CheckUpdateAlbum($albumId,$userId){
        $strQuery = "SELECT * FROM album WHERE id='".$albumId."' AND created_user='".$userId."'";
        $listAlbum = array();
        try{
            $sql = $this->db->query($strQuery);
            $listAlbum = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listAlbum;
    }
    
    public function CheckDeleteAlbum($albumId, $userLoginId){
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
    
    public function GetAlbumWithMainImageById($id){
        $strQuery = "SELECT al.id AS id, al.title, al.description, img.path ";
        $strQuery .= "FROM album al ";
        $strQuery .= "INNER JOIN image img ON img.album_id = al.id AND img.deleted IS NULL AND img.main_image = 1 ";
        $strQuery .= "WHERE al.deleted IS NULL AND al.id ='".$id."'";
        $listAlbum = array();
        try{
            $sql = $this->db->query($strQuery);
            $listAlbum = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listAlbum;
    }
    
    public function GetAlbumById($id){
        $strQuery = "SELECT id, title, description ";
        $strQuery .= "FROM album ";        
        $strQuery .= "WHERE deleted IS NULL AND id ='".$id."'";
        $listAlbum = array();
        try{
            $sql = $this->db->query($strQuery);
            $listAlbum = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listAlbum;
    }
    
    public function AddAlbum($album, $userLogin){
        $datetimeNow = $this->GetDateTimeNow();  
        $album["created"] = $datetimeNow;
        $album["modified"] = $datetimeNow;
        $album["created_user"] = $userLogin["id"];
        $this->db->beginTransaction();
        try{
            $this->db->insert("album",$album);
            $idNewAlbum = $this->db->lastInsertId();
            if(count($this->GetAlbumById($idNewAlbum)) == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Add album is fail",
                );
            }
            $this->db->commit();
            return array(
                    "id" => $idNewAlbum,
                    "success" => true,
                    "message" => "Add album is successful",
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
    
    public function DeleteAlbum($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["deleted"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("album", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Delete album is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Delete album is successful",
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
    
    public function UpdateAlbum($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["modified"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("album", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Update album is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Update album is successful",
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