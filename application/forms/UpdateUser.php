<?php
class Form_UpdateUser extends Zend_Form{
    public function init(){
        $this->setAction('')->setMethod('post');
        
        $username= new Zend_Form_Element_Text("username");
        $level = new Zend_Form_Element_Select("level");
        $name = new Zend_Form_Element_Text("fullname");
        $gender = new Zend_Form_Element_Radio("gender");
        
        $level->setLabel("Authority")
                ->addMultiOptions(array(
                    "1" => "Normal user",
                    "2" => "Admin",
                ))
                ->setAttribs(array(
                        "class" => "form-control required",
                    ))
                
                ->removeDecorator('Errors')
                ->removeDecorator('HtmlTag')
                ->removeDecorator("Label");
             
        $name->setLabel("Fullname")
             ->setAttribs(array(
                    "class" => "form-control",
                    "placeholder" => "fullname",
                    "regstring" => "^[a-zA-Z ]{8,50}$",
                ))
             ->removeDecorator('Errors')
             ->removeDecorator('HtmlTag')
             ->removeDecorator("Label");
        
        $gender->setLabel("Gender")
                ->addMultiOptions(array(
                    "0" => "Female",
                    "1" => "Male",
                ))
                ->setRequired(true)
                ->setSeparator('')
                ->removeDecorator('Errors')
                ->removeDecorator('HtmlTag')
                ->removeDecorator("Label");
                  
        $submit = new Zend_Form_Element_Button("submit");
        $submit->setLabel("Submit")
                ->setAttrib("class","btn btn-success")
                ->setAttrib("onclick","UpdateUser();")
                ->removeDecorator('DtDdWrapper');
                
        $this->setDecorators(
            array(
                array('viewScript',
                    array('viewScript'=>'Form_UpdateUser.phtml')
                ),
            )
        );
        
        $this->addElements(
            array($username,$password,$level,$name,$gender,$submit)
        );
    }
}