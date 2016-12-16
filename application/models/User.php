<?php
class Model_User extends Model_HubModel{
    public function GetUserByUsername($username){
        $strQuery = "SELECT id, username, password, fullname, gender, birthday, avatar, created, modified, deleted, created_user, modified_user, deleted_user, level FROM users ";
        $strQuery .= "WHERE deleted IS NULL AND username='".$username."'";
        try{
            $sql = $this->db->query($strQuery);
            return $sql->fetchAll();
        }
        catch(Exception $ex){
            return $ex->getMessage();
        }
    }
    
    public function GetAllUser(){
        $strQuery = "SELECT id, username, fullname, gender, birthday, created, modified, deleted, created_user, modified_user, deleted_user, level FROM users WHERE deleted is null ";
        $listUser = array();
        try{
            $sql = $this->db->query($strQuery);
            $listUser = $sql->fetchAll();
        }
        catch(Exception $ex){
            // làm log
            //return $ex->getMessage();
        }
        return $listUser;
    }
    
    public function GetUserByCondition($condition, $orderBy, $limit, $offset){
        $listUser = array();
        $totalRecord = 0;
        $strQuery = "SELECT id, username, fullname, gender, birthday, level FROM users ";
        $strQuery .= "WHERE deleted is null ";
        foreach($condition as $key => $value){
            if($value != ""){
                if($key == "gender"){
                    $strQuery .= "AND ".$key."='".$value."' ";   
                }
                else{                                    
                    $strQuery .= "AND ".$key." LIKE'%".$value."%' ";   
                }
            }
        }
        try{
            $sql = $this->db->query($strQuery);
            $totalRecord = count($sql->fetchAll());
        }
        catch(Exception $ex){
            //return $ex->getMessage();
        }
        $strQuery .= "ORDER BY ".$orderBy;
        $strQuery .= " LIMIT ".$limit." OFFSET ".$offset;
        try{
            $sql = $this->db->query($strQuery);
            $listUser = $sql->fetchAll();
        }
        catch(Exception $ex){
            //return $ex->getMessage();
        }
        return array("listUser" => $listUser, "totalRecord" => $totalRecord);
    }
    
    public function GetUserById($id){
        $strQuery = "SELECT id, username, password, fullname, gender, birthday, avatar, level FROM users ";
        $strQuery .= "WHERE deleted IS NULL AND id='".$id."'";
        try{
            $sql = $this->db->query($strQuery);
            return $sql->fetchAll();
        }
        catch(Exception $ex){
            //return $ex->getMessage();
        }
    }
    
    public function Register($data){
        $datetimeNow = $this->GetDateTimeNow();
        $data["created"] = $datetimeNow;
        $data["modified"] = $datetimeNow;
        $data["level"] = 1;
        $this->db->beginTransaction();
        try{
            $this->db->insert("users",$data);
            $id = $this->db->lastInsertId();
            if(count($this->GetUserById($id)) == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Register is fail",
                );
            }
            $data["id"] = $id;
            $data["created_user"] = $id;
            $data["modified_user"] = $id;
            $update = $this->db->update("users", $data, "id='".$data["id"]."'");
            $this->db->commit();
            return array(
                    "success" => true,
                    "message" => "Register is successful",
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
    
    public function UpdateUser($data, $userLogin){
        $datetimeNow = $this->GetDateTimeNow();
        $data["modified_user"] = $userLogin["id"];
        $data["modified"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("users", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Update user is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Update user is successful",
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
    
    public function DeleteUser($data, $userLogin){
        $datetimeNow = $this->GetDateTimeNow();
        $data["modified_user"] = $userLogin["id"];
        $data["deleted_user"] = $userLogin["id"];
        $data["modified"] = $datetimeNow;
        $data["deleted"] = $datetimeNow;
        $this->db->beginTransaction();
        try{
            $update = $this->db->update("users", $data, "id='".$data["id"]."'");
            if($update == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Delete user is fail",
                );
            }
            else{
                $this->db->commit();
                return array(
                    "success" => true,
                    "message" => "Delete user is success full",
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
    
    public function AddUser($data, $userLogin){        
        $datetimeNow = $this->GetDateTimeNow();  
        $data["avatar"] = "public/image/user_default.png";
        $data["created"] = $datetimeNow;
        $data["modified"] = $datetimeNow;
        $id = $userLogin["id"];
        $data["created_user"] = $id;
        $data["modified_user"] = $id;
        $this->db->beginTransaction();
        try{
            $this->db->insert("users",$data);
            $idNewUser = $this->db->lastInsertId();
            if(count($this->GetUserById($idNewUser)) == 0){
                $this->db->rollBack();
                return array(
                    "success" => false,
                    "message" => "Add user is fail",
                );
            }
            $this->db->commit();
            return array(
                    "success" => true,
                    "message" => "Add user is successful",
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
}
?>