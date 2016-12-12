<?php
class Form_Register extends Zend_Form{
    public function init(){
        $this->setAction('')->setMethod('post');
        
        $username = new Zend_Form_Element_Text("username");
        $password = new Zend_Form_Element_Password("password");
        $level = new Zend_Form_Element_Select("level");
        $name = new Zend_Form_Element_Text("fullname");
        $gender = new Zend_Form_Element_Radio("gender");
        
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(" field cannot be empty!");
        
        $password->setLabel('Password')
                  ->setRequired(true)
                  ->addValidators(array(
                    array($notEmpty, true),
                  ))
                  ->setAttribs(array(
                    "class" => "form-control required",
                    "placeholder" => "password",
                    "regstring" => "^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,20}$",
                  ))
                  ->removeDecorator('Errors')
                  ->removeDecorator('HtmlTag')
                  ->removeDecorator("Label");
        
        $username->setLabel('Username')
                  ->setRequired(true)
                  ->addValidators(array(
                    array($notEmpty, true),
                  ))
                  ->setAttribs(array(
                    "class" => "form-control required unique",
                    "placeholder" => "username",
                    "regstring" => "^[a-zA-Z0-9]{8,20}$",
                    "table" => "users",
                    "idcheck" => "",
                  ))
                  ->removeDecorator('Errors')
                  ->removeDecorator('HtmlTag')
                  ->removeDecorator("Label");
        
        $level->setLabel("Authority")
                ->setAttribs(array(
                        "class" => "form-control",
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
                ->addValidators(array(
                    array($notEmpty, true),
                  ))
                ->setSeparator('')
                ->removeDecorator('Errors')
                ->removeDecorator('HtmlTag')
                ->removeDecorator("Label");
                  
        $submit = new Zend_Form_Element_Button("submit");
        $submit->setLabel("Register")
                ->setAttrib("class","btn btn-success")
                ->setAttrib("onclick","register();")
                ->removeDecorator('DtDdWrapper');
                
        $this->setDecorators(
            array(
                array('viewScript',
                    array('viewScript'=>'Form_Register.phtml')
                ),
            )
        );
        
        $this->addElements(
            array($username,$password,$level,$name,$gender,$submit)
        );
    }
}