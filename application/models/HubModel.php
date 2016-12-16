<?php
    class Model_HubModel extends Zend_Db_Table_Abstract{
        protected $_name;
        protected $_primary;
        protected $db;
        
        //function __set($key, $value){
//            if ($key == '_name'){
//                $this->_name = $value;
//            }
//            else if ($key = '_primary'){
//                $this->_primary = $value;
//            }
//        }
//        
//        function __get($key){
//            if($key == "_name" ){
//                return $this->_name;
//            }
//            else if($key == "_primary"){
//                return $this->_primary;
//            }
//        }
        
        function setName($value){
            $this->_name = $value;
        }
        
        function getName(){
            return $this->_name;
        }
        
        function setPrimary($value){
            $this->_primary = $value;
        }
        
        function getPrimary(){
            return $this->_primary;
        }
        
        public function __construct(){
            $this->db=Zend_Registry::get('db');
        }
        
        public function CheckUnique($id, $tableName, $fieldName, $value){
            $unique = false;
            $strQuery = "SELECT id FROM ".$tableName." WHERE ".$fieldName."='".$value."' ";
            if($id != ""){
                $strQuery .= "AND id='".$id."'";
            }
            try{
                $sql = $this->db->query($strQuery);
                if(count($sql->fetchAll()) > 0){
                    return false;
                }
                else{
                    return true;
                }
                return $sql->fetchAll();
            }
            catch(Exception $ex){
                return $ex->getMessage();
            }
        }
        
        public function GetDateTimeNow(){
            $date = new DateTime('now');
            $date->setTimezone(new DateTimeZone("Asia/Bangkok"));
            $str_server_now = $date->format("Y-m-d H:i:s");
            return $str_server_now;
        }
    }
?>