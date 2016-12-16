$(document).ready(function(){
    $("#upload-image").on("click", function(){
        $("#my-file").click();
    })
});

var imgExtensions = ["image/jpg", "image/jpeg", "image/png"];    
function UpLoadToTemp(oInput) {
    if (!window.FileReader) {  
        alert("Not support file reader");
        return false;
    }
    var img = $("#my-file");
    if (!oInput.files) {
        alert("This browser doesn't seem to support the `files` property of file inputs");
        return false;
    }
    else if (!oInput.files[0]) {
        $("#my-file").val("");
        return false;
    }
    else {
        file = oInput.files[0];
        var form_data = new FormData();
        //$.each(oInput.files, function(index,item){
//            form_data.append('file[]', item);
//        })            
        form_data.append('file', file);
        if($.inArray(file.type, imgExtensions) !== -1){
            $.ajax({
                url : "/tuanp1/user/uploadimage",
                type : "POST",
                cache: false,
                contentType: false,
                processData: false,
                data : form_data,
                dataType: "json",
                error: function (response) {
                    
                },
                success: function (response) {
                    if(response.success){
                        var src = "/tuanp1/"+response.message;
                        $("#upload-image").attr("src",src);
                        $("#avatar").val(response.message);
                    }
                    else{
                        alert(response.message);
                    }
                }
            });
        }
        else{
            $("#my-file").val("");
            document.body.onfocus = null;
            alert("The selected file type must to be "+imgExtensions.join(", "));
            return false;
        }
        
    }
}