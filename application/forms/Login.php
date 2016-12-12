<?php
class Form_Login extends Zend_Form{
    public function init(){
        $this->setAction('')->setMethod('post');
        
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage(" field cannot be empty!");
        //$pwLength = new Zend_Validate_StringLength(array(
//            'min' => 6, 
//            'max' => 20,
//        ));
//        $pwLength->setMessage(" length must between 6 and 20!");
        
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                  ->setRequired(true)
                  ->addValidators(array(
                    array($notEmpty, true),
                  ))
                  ->setAttribs(array(
                    "class" => "form-control required",
                    "placeholder" => "password",
                  ))
                  ->removeDecorator('Errors')
                  ->removeDecorator('HtmlTag')
                  ->removeDecorator("Label");
                 // ->addDecorator(‘Label’, array(‘escape’ => false));
                  //->addDecorator(‘ViewHelper’)
//                  ->addDecorator(‘Errors’)
                  //->addDecorator(
//                    array(‘data’=>’HtmlTag’),
//                    array(‘tag’ => ‘div’, ‘class’ => ‘group’)
//                  );
        
        $username = new Zend_Form_Element_Text("username");
        $username->setLabel('Username')
                  ->setRequired(true)
                  ->addValidators(array(
                    array($notEmpty, true),
                  ))
                  ->setAttribs(array(
                    "class" => "form-control required",
                    "placeholder" => "username",
                  ))
                  ->removeDecorator('Errors')
                  ->removeDecorator('HtmlTag')
                  ->removeDecorator("Label");
                  
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Login")
                ->setAttrib("class","btn btn-success")
                ->removeDecorator('DtDdWrapper');
                
        $this->setDecorators(
            array(
                array('viewScript',
                    array('viewScript'=>'Form_Login.phtml')
                ),
            )
        );
        
        //$this->clearDecorators();
        //$this->setDecorators(array(
//            'FormElements',
//            //array('HtmlTag', array('tag' => 'table')),
//            'Form',
//        ));
        
        $this->addElements(
            array($username,$password,$submit)
        );
    }
}