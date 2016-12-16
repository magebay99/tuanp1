var totalPage = 1;

$(document).ready(function(){    
    GetData(1);
    $("#btnSearch").on("click", function(){
        GetData(1);
    })
    $("#filter").on("click", function(){
        if(!$(".panel-body").is(':animated')){
            //$(".panel-body").stop().slideToggle(300);
            $(".panel-body").slideToggle(300);
        }
    })
    
})

function GetData(pageIndex){
    var totalPage = 0;
    var condition = getFormData($("#condition"));
    var limit = $("#record").val();
    var orderby = $("#orderby").val();
    var offset = limit*(pageIndex-1);
    $.ajax({
        url : "getlistuser",
        type : "POST",
        data : {data: JSON.stringify({condition: condition, orderby: orderby, limit: limit, offset: offset})},
        dataType: "json",
        error: function (response) {
            
        },
        success: function (response) {            
            //totalPage = response["totalPage"];
            var totalRecord = response["totalRecord"];
            var listUser = response["listUser"];
            SetPageInfo(totalRecord,pageIndex,limit);
            if(totalRecord > 0){
                var listUserHtml = DrawListUser(listUser);                
                if(totalRecord%limit>0){
                    totalPage = Math.floor(totalRecord/limit) + 1;
                }
                else{
                    totalPage = totalRecord/limit;
                }                
                $("#tbody").html(listUserHtml);
                $(".pagination").html(DrawPaging(totalPage, pageIndex));
                window.scrollTo(0, 160);
            }
            else{
                $("#tbody").html("");
                alert("No record found!");
                SetPageInfo(0,0,0);
            }
           // $(DrawPaging(response["totalPage"], pageIndex)).insertAfter( $(".pagination").find("li:first"));
            //$(".pagination").find("li:first").insertAfter(DrawPaging(response["totalPage"]));                        
        }
    });   
}

function DrawListUser(listUser){
    var listUserHtml = "";
    $.each(listUser, function(index, item){
        if(index%2==0){
            listUserHtml += "<tr class='info'>";
        }
        else{
            listUserHtml += "<tr class=''>";
        }
        listUserHtml += "<td>"+(index+1)+"</td>";
        listUserHtml += "<td>"+item["fullname"]+"</td>";
        listUserHtml += "<td>"+item["username"]+"</td>";
        if(item["gender"] == 1){
            listUserHtml += "<td>Male</td>";
        }
        else{
            listUserHtml += "<td>Female</td>";   
        }
        listUserHtml += "<td>";
        listUserHtml += "<a href='/tuanp1/user/detailuser' class='btn btn-info btn-sm'><span class='glyphicon glyphicon-info-sign'></span> Detail</a>&nbsp;";
        listUserHtml += "<a href='/tuanp1/user/updateuser?userId="+item["id"]+"' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span> Edit</a>&nbsp;";
        listUserHtml += "<button onclick='DeleteUser("+item["id"]+");' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Delete</button>&nbsp;";
        listUserHtml += "</td>";  
        listUserHtml += "</tr>";
    });
    return listUserHtml;
}

function DrawPaging(totalPage, pageIndex){
    var pagingHtml = "";
    var firstPageBtn = "<li class='' onclick='GetData(1);'><a href='javascript:void(0)'>&laquo;</a></li>";
    var lastPageBtn = "<li class='' onclick='GetData("+totalPage+");'><a href='javascript:void(0)'>&raquo;</a></li>";
    var disableBtn = "<li class='disable'><a href='javascript:void(0)'>...</a></li>";
    pagingHtml += firstPageBtn;
    if(totalPage > 10 ){
        if(pageIndex >= 5){
            if(pageIndex >= totalPage-4){
                for(var i = totalPage-8; i <= totalPage-5; i++){
                    pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
                }
                pagingHtml += disableBtn;
                for(var i = totalPage-4; i <= totalPage; i++){
                    if(pageIndex == i){
                        pagingHtml += "<li class='active'><a href='javascript:void(0)'>"+i+"</a></li>";
                    }
                    else{
                        pagingHtml += "<li class=''onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
                    }
                }
            }
            else{
                for(var i = pageIndex-3; i <= pageIndex+1; i++){
                    if(pageIndex == i){
                        pagingHtml += "<li class='active'><a href='javascript:void(0)'>"+i+"</a></li>";
                    }
                    else{
                        pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
                    }
                }
                pagingHtml += disableBtn;
                for(var i = totalPage-3; i <= totalPage; i++){
                    pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
                }
            }
        }
        else{
            for(var i = 1; i <= 5; i++){
                if(pageIndex == i){
                    pagingHtml += "<li class='active'><a href='javascript:void(0)'>"+i+"</a></li>";
                }
                else{
                    pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
                }
            }
            pagingHtml += disableBtn;
            for(var i = totalPage-3; i <= totalPage; i++){
                pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
            }
        }
    }
    else{
        for(var i = 1; i <= totalPage; i++){
            if(pageIndex == i){
                pagingHtml += "<li class='active'><a href='javascript:void(0)'>"+i+"</a></li>";   
            }
            else{
                pagingHtml += "<li class='' onclick='GetData("+i+");'><a href='javascript:void(0)'>"+i+"</a></li>";
            }            
        }
    }
    pagingHtml += lastPageBtn;
    return pagingHtml;
}

function ChangeRecord(){
    GetData(1);
}

function ChangeOrderBy(){
    GetData(1);
}

function SetPageInfo(totalRecord,pageIndex,pageRecord){
    var toRecord = pageIndex*pageRecord;
    var fromRecord = toRecord-pageRecord+1;
    if(totalRecord == 0){
        toRecord = 0;
        fromRecord = 0;
    }
    if(toRecord > totalRecord){
        toRecord = totalRecord;
    }
    $("#total-record").text(totalRecord);
    $("#from-record").text(fromRecord);
    $("#to-record").text(toRecord);
}

function DeleteUser(userId) {
    var r = confirm("Are you sure you want to delete this user?");
    if (r == true) {
        $.ajax({
            url : "deleteuser",
            type : "POST",
            data : {userId: userId},
            dataType: "json",
            error: function (response) {
                
            },
            success: function (response) {
                if(response.success){
                    alert(response.message);
                    window.location.replace("/tuanp1/user/listuser");
                }
                else{
                    alert(response.message);
                }
            }
        });
    }
}