$(document).ready(function(){
    
});
logInWithFacebook = function() {
    FB.login(function(response) {
      if (response.authResponse) {
        // Now you can redirect the user or do an AJAX request to
        // a PHP script that grabs the signed request from the cookie.
        var url = '/me?fields=name,email,gender,birthday';
        var fbUserInfo = {};   
        FB.api(url, function (response) {
            console.log(JSON.stringify(response));
            if(response["gender"] == "male"){
                response["gender"] = 1;
            }
            else{
                response["gender"] = 0;
            }
            fbUserInfo["username"] = response["email"];
            fbUserInfo["fullname"] = response["name"];
            fbUserInfo["gender"] = response["gender"];
            fbUserInfo["birthday"] = response["birthday"];
            fbUserInfo["fbid"] = response["id"];
            var urlPicture = "/"+response.id+"/picture?type=large";            
            FB.api(
            urlPicture,
            function (response) {
              if (response && !response.error) {
                fbUserInfo["picture"] = response.data.url;
                console.log(JSON.stringify(response));
                $.ajax({
                    url : "fblogin",
                    type : "POST",
                    data : {data: JSON.stringify(fbUserInfo)},
                    dataType: "json",
                    error: function (response) {
                        
                    },
                    success: function (response) {
                        if(response.success){
                            window.location.replace("/tuanp1");
                        }
                        else{
                            alert(response.message);
                        }                        
                    }
                })
              }
            }
            );
        });        
      } else {
        alert('User cancelled login or did not fully authorize.');
      }
    }, {scope: 'user_birthday,email', return_scopes: true});
    return false;
};

window.fbAsyncInit = function() {
    FB.init({
      appId: '1215306148556024',
      cookie: true, // This is important, it's not enabled by default
      version: 'v2.2'
    });
};

(function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function logoutFb(){
    FB.logout(function(response) {
        alert("Person is now logged out");
    });
}

function login(){
    var formData = getFormData($("form"));
    if(validate_form(formData)){
        $.ajax({
            url : "ajaxlogin",
            type : "POST",
            data : {data: JSON.stringify(formData)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                if(response.length != 0){
                    $("form").find("input[type=password]").val("");
                    var errorBox = "";
                    $.each(response, function(index, item){
                        errorBox += "<span>"+item+"</span>";
                    })
                    $("#errorBox").html(errorBox);
                    $("#errorBox").show();
                }
                else{
                    window.location.replace("/tuanp1");
                }
            }
        });   
    }
}

function register(){
    var formData = getFormData($("form"));
    if(validate_form(formData)){
        $.ajax({
            url : "ajaxregister",
            type : "POST",
            data : {data: JSON.stringify(formData)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                $("#errorBox").hide();
                if(response.success){
                    $("form").find("input").val("");
                    $("form").find("input[type=radio]").prop("checked", false);
                    alert(response.message);
                }
                else{
                    $("form").find("input[type=password]").val("");
                    alert(response.message);
                }
            }
        });
    }
}

function AddUser(){
    var formData = getFormData($("form"));
    if(validate_form(formData)){
        $.ajax({
            url : "ajaxadduser",
            type : "POST",
            data : {data: JSON.stringify(formData)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                $("#errorBox").hide();
                if(response.success){
                    $("form").find("input").val("");
                    $("form").find("input[type=radio]").prop("checked", false);
                    alert(response.message);
                }
                else{
                    $("form").find("input[type=password]").val("");
                    alert(response.message);
                }
            }
        });
    }
}

function UpdateUser(){
    var formData = getFormData($("form"));
    var userId = $("#userid").val();
    formData["id"] = userId;
    if(validate_form(formData)){
        $.ajax({
            url : "ajaxupdateuser",
            type : "POST",
            data : {data: JSON.stringify(formData)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                $("#errorBox").hide();
                if(response.success){
                    alert(response.message);
                }
                else{
                    alert(response.message);
                }
            }
        });
    }
}