$(document).ready(function() {
    GetListImage();    
     $("#my-file").on("click", function(){
        document.body.onfocus = ClearNewImage;
     })
    $("#btnAddImage").on("click", function(){
        var newImage = $(".new-image");
        if(newImage.length == 0){
            CreateNewImageItem();
            $("#my-file").click();   
        }
        else{
            alert("Please complete the creation of your new image!");
        }
    })
    $("#btnSearch").on("click", function(){
        SearchImage();
    })
});

function ChangeName(lblName, id){
    lblName.prev().val(lblName.text());
    lblName.css("display","none");
    lblName.prev().css("display","inline-block");
    lblName.prev().focus();
}

function SaveName(nameInput, id){
    var name = nameInput.val();
        if(name != ""){
            var id_check = nameInput.attr("idcheck");
            var table = nameInput.attr("table");            
            if(CheckUnique(id_check, table, "name", name)){
                nameInput.next().text(name);
                if(id != 0){
                    var updateImage = {};
                    updateImage["name"] = name;
                    updateImage["id"] = id;
                    $.ajax({
                        url : "../image/updateimagename",
                        type : "POST",
                        data : {data: JSON.stringify(updateImage)},
                        dataType: "json",
                        error: function (response) {
                            
                        },
                        success: function (response) {
                            alert(response.message);
                        }
                    })
                }
            }
            else{
                alert("Image name is existed!");
            }
        }
        nameInput.next().css("display","");
        nameInput.css("display","none");
}

function ClearNewImage(){
    setTimeout(function(){
        if($("#my-file").val().length == 0){
            $(".image:first-child").remove();
        }
        $("#my-file").val("");
        document.body.onfocus = null;
    }, 500);        
}

function FullScreenImage(){
    var $lightbox = $('#lightbox');
    
    $('img').on('click', function(event) {
        var $img = $(this).parent().find('img'), 
            src = $img.attr('src'),
            css = {
                'maxWidth': $(window).width() - 100,
                'maxHeight': $(window).height() - 100
            };
        $lightbox.find('img').attr('src', src);
        $lightbox.find('img').css(css);
        $('[data-target="#lightbox"]').click();
    });
    
    $lightbox.on('shown.bs.modal', function (e) {
        var $img = $lightbox.find('img');            
        $lightbox.find('.modal-dialog').css({'width': $img.width()});
    });
}

function GetListImage(){
    var albumId = $("#album-id").val();
    var listImage = $.parseJSON($("#list-image-data").val());
    var listImageHtml = DrawListImage(listImage);
    $("#list-image").html(listImageHtml);
    FullScreenImage();
}

function DrawImage(image){
    var imageHtml = "";
    imageHtml += "<div class='image'>";
    imageHtml += "<div class='panel panel-default'>";
    imageHtml += "<div class='panel-body'>";
    imageHtml += "<a onclick='DeleteImage($(this),"+image["id"]+");' class='close' >&times;</a>";
    imageHtml += "<img class='img-responsive' src='/tuanp1/"+image["path"]+"' />";    
    imageHtml += "</div>";
    imageHtml += "<div class='panel-footer'>";
    imageHtml += "<input type='text' class='form-control unique required' maxlength='30' onfocusout='SaveName($(this),"+image["id"]+");' table='image' idcheck='"+image["id"]+"' placeholder='name' style='display:none' />";
    imageHtml += "<a onclick='ChangeName($(this),"+image["id"]+");'>"+image["name"]+"</a>";
    if(image["main_image"] == 1){
        imageHtml += "<span class='glyphicon glyphicon-star' onclick='SetMainImage($(this),"+image["album_id"]+","+image["id"]+");'></span>";
    }
    else{
        imageHtml += "<span class='glyphicon glyphicon-star-empty' onclick='SetMainImage($(this),"+image["album_id"]+","+image["id"]+");'></span>";
    }    
    imageHtml += "</div></div></div>";
    return imageHtml;
}

function SetMainImage(main,album_id, id){
    var updatedImage = {};
    updatedImage["id"] = id;
    updatedImage["main_image"] = true;
    updatedImage["album_id"] = album_id;
    $.ajax({
         url : "../image/updatemainimage",
        type : "POST",
        data : {data: JSON.stringify(updatedImage)},
        dataType: "json",
        error: function (response) {
            
        },
        success: function (response) {
            if(response.success){
                $(".glyphicon-star").addClass("glyphicon-star-empty");
                $(".glyphicon-star").removeClass("glyphicon-star");
                main.addClass("glyphicon-star");
                main.removeClass("glyphicon-star-empty");
            }                
        }
    })
}

function DrawListImage(listImage){
    var listImageHtml = "";
    $.each(listImage, function(index,item){
        listImageHtml += DrawImage(item);
    })
    return listImageHtml;
}

function AddImage(imagePath){
    var newImage = {};
    var validName = validate_input("name");
    if(validName.status){
        newImage["path"] = $("#avatar").val();
        newImage["name"] = $("#name").val();
        newImage["album_id"] = $("#album-id").val();
        newImage["main_image"] = false;
        $.ajax({
            url : "../image/addimage",
            type : "POST",
            data : {data: JSON.stringify(newImage)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                if(response.success){
                    var imageHtml = DrawListImage(response.newImage);
                    $(".image:first-child").remove();
                    $(".image:first-child").before(imageHtml);
                    //FullScreenImage();
                }
                else{
                    alert(response.message);
                }                
            }
        })
    }
    else{
        alert("Image name "+validName.error);
    }
}

function CancelAddImage(){
    $(".new-image").remove();
}

function CreateNewImageItem(){
    var imageHtml = "";
    imageHtml += "<div class='image new-image'>";
    imageHtml += "<div class='panel panel-default'>";
    imageHtml += "<div class='panel-body'>";
    imageHtml += "<img id='upload-image' class='img-responsive' src='/tuanp1/public/image/default-image.png' />";
    imageHtml += "</div>";
    imageHtml += "<div class='panel-footer'>";
    imageHtml += "<input type='text' class='form-control unique required' maxlength='30' onfocusout='SaveName($(this),0);' table='image' idcheck='' placeholder='name' id='name' style='display:none' />";
    imageHtml += "<a onclick='ChangeName($(this),0);' >Name</a>";
    imageHtml += "<a class='btn btn-default btn-sm' onclick='AddImage();'>Create</a>";
    imageHtml += "<a class='btn btn-default btn-sm' onclick='CancelAddImage();'>Cancel</a>";
    imageHtml += "</div></div></div>";
    $(".image:first-child").before(imageHtml);
}

function SearchImage(){
    var name = $("#search-name").val();
    var albumId = $("#album-id").val();
    $.ajax({
        url : "../image/searchimage",
        type : "POST",
        data : {name: name, albumId: albumId},
        dataType: "json",
        error: function (response) {
            
        },
        success: function (response) {
            if(response.length > 0){
                var listImageHtml = DrawListImage(response);
                $("#list-image").html(listImageHtml);
                FullScreenImage();
            }
            else{
                alert("Image not found!");
            } 
             $("#search-name").val("");               
        }
    })
}

function DeleteImage(input,imageId){
    var r = confirm("You sure you want to delete this image!");
    if (r == true) {
        var albumId = $("#album-id").val();
        $.ajax({
            url : "../image/deleteimage",
            type : "POST",
            data : {imageId: imageId, albumId: albumId},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                if(response.success){
                    input.parent().parent().parent().remove();
                }
                else{
                    alert(response.message);
                }                
            }
        })
    }
}