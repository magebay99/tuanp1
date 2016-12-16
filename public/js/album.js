$(document).ready(function(){
    SetModalAddAlbum();
    GetListAlbum();
    $('#myModal').on('hidden.bs.modal', function(){
        SetDefaultModal()
    })
})

function SetDefaultModal(){
    $("#lblTitle").text("No Title");
    $("#lblDescription").text("No Description");
    $("#title").val("");
    $("#description").val("");
    $("#avatar").val("public/image/default-image.png");
    $("#upload-image").attr("src","/tuanp1/public/image/default-image.png");
    $("#my-file").val("");
}

function SetModalAddAlbum(){
    $("#add-icon").on("click", function(){
        $("#btnShowModal").click();
    })
    $("#title").on("input", function(){
        $("#lblTitle").text($("#title").val());
    })
    $("#description").on("input", function(){
        $("#lblDescription").text($("#description").val());
    })
    $(".modal-header").on("click", function(){
        $("#lblTitle").css("display","none");
        $("#title").css("display","block");
        $("#title").focus();
    })
    $(".modal-footer").on("click", function(){
        $("#lblDescription").css("display","none");
        $("#description").css("display","block");
        $("#description").focus();
    })
    $("#title").on("focusout", function(){
        if($("#title").val() == ""){
            $("#lblTitle").text("No Title");
        }
        $("#lblTitle").css("display","block");
        $("#title").css("display","none");
        
    })
    $("#description").on("focusout", function(){
        if($("#description").val() == ""){
            $("#lblDescription").text("No Description");
        }
        $("#lblDescription").css("display","block");
        $("#description").css("display","none");
        
    })
}

function AddAlbum(){
     var validTitle = validate_input("title"); 
     if(validTitle.status){
        var formData = getFormData($("form"));
        $.ajax({
            url : "../album/addalbum",
            type : "POST",
            data : {data: JSON.stringify(formData)},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                if(response.success){
                    var newAlbum = DrawAlbum(response.album[0]);
                    $(".col-sm-4:last-child").before(newAlbum);
                    $("#myModal").find(".close").click();
                }
                else{
                    alert(response.message);
                }
            }
        });
     }
     else{
        alert("Title "+validTitle.error);
     }
}

function GetListAlbum(){
    $.ajax({
        url : "../album/listalbum",
        type : "POST",
        data : "",
        dataType: "json",
        error: function (response) {
            
        },
        success: function (response) {
            if(response.length > 0){
                var listAlbumHtml = DrawListAlbum(response);
                $(".col-sm-4:last-child").before(listAlbumHtml);
            }
        }
    })
}

function ChangeTitle(input){
    input.prev().val(input.text());
    input.css("display","none");
    input.prev().css("display","inline-block");
    input.prev().focus();
}

function SaveTile(input, albumId){
    var newTitle = input.val();
    if(newTitle != ""){
        var table = input.attr("table");
        var id_check = input.attr("idcheck");
        if(CheckUnique(id_check, table, "title", newTitle)){
            var updatedAlbum = {};
            updatedAlbum["title"] = newTitle;
            updatedAlbum["id"] = albumId;
            $.ajax({
                url : "../album/updatetitle",
                type : "POST",
                data : {updatedAlbum: updatedAlbum},
                dataType: "json",
                error: function (response) {
                    
                },
                success: function (response) {
                    if(response.success){
                        input.next().text(newTitle);                        
                    }
                    else{
                        alert(response.message);
                    }
                }
            })
        }
        else{
            alert("The title is existed!");   
        }        
    }
    input.css("display","none");
    input.next().css("display","");    
}

function DrawAlbum(album){
    var albumHtml = "";
    albumHtml += "<div class='col-sm-4'>";
    albumHtml += "<div class='panel panel-success text-center'>";
    albumHtml += "<div class='panel-heading'>";
    albumHtml += "<input type='text' id='new-tile' class='form-control required unique' table='album' idcheck='"+album["id"]+"' onfocusout='SaveTile($(this),"+album["id"]+");' placeholder='title' />";
    albumHtml += "<label onclick='ChangeTitle($(this));'>"+album["title"]+"</label>";
    albumHtml += "<a onclick='CheckDeleteAlbum($(this),"+album["id"]+")' class='close' >&times;</a>"+"</div>";
    albumHtml += "<a href='/tuanp1/image/listimage?albumId="+album["id"]+"'>"
    albumHtml += "<div class='panel-body'>";
    albumHtml += "<img src='/tuanp1/"+album["path"]+"' class='img-responsive' >";
    albumHtml += "</div></a></div></div>";
    return albumHtml
}

function DrawListAlbum(listAlbum){
    var listAlbumHtml = "";
    $.each(listAlbum, function(index,item){
        listAlbumHtml += DrawAlbum(item);
    })
    return listAlbumHtml;
}

function CheckDeleteAlbum(input,albumId){
    $.ajax({
        url : "../album/checkdeletealbum",
        type : "POST",
        data : {albumId: albumId},
        dataType: "json",
        error: function (response) {
            
        },
        success: function (response) {
            if(response.length > 0){
                DeleteAlbum(input, albumId);
            }
            else{
                alert("You can't delete this album!")
            }
        }
    })
}

function DeleteAlbum(input, albumId){
    var r = confirm("This album still store image.\nYou sure you want to delete the album?");
    if (r == true) {
        $.ajax({
            url : "../album/deletealbum",
            type : "POST",
            data : {albumId: albumId},
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