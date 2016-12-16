$(document).ready(function(){
    var thisUrl = $("#thisurl").val();
    var arr = thisUrl.split("/");
    if(arr[2] == ""){
        $("#index").addClass("active");
    }
    $("#"+arr[2]).addClass("active");
    $("#"+arr[2]).find("a[href='"+thisUrl+"']").parent().addClass("active");
})

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function validate_form(listValue) {
    var valid = {};
    for (var item in listValue) {
        valid[item] = {};
    }
    for (var key in valid) {
        valid[key].status = true;
        valid[key].error = [];
    }
    for (var key in valid) {
        valid[key] = validate_input(key);
    }
    var success = true;
    for (var key in valid) {
        if (valid[key].status == false) {
            success = false;
        }
    }
    var errorBox = "";
    for (var key in valid) {
        if (valid[key].status == false) {
            $.each(valid[key].error, function(index,item){
                errorBox += "<span>"+key+" "+item+"</span><br/>";  
            })
        }
    }
    if (!success) {
        $("#errorBox").html(errorBox);
        $("#errorBox").show();
    }
    return success;
}

function validate_input(id_input) {
    var value_input = $("#" + id_input).val();
    var valid = {};
    valid.status = true;
    valid.error = [];
    var regstring = $("#" + id_input).attr('regstring');
    if (typeof regstring !== typeof undefined && regstring !== false) {
        var reg = new RegExp(regstring);
        if (value_input != '' && !reg.test(value_input)) {
            valid.status = false;
            valid.error.push("worng format!");
        }
    }

    if ($("#" + id_input).hasClass("required")) {
        if (value_input == "") {
            valid.status = false;
            valid.error.push("cannot be empty!");
        }
    }

    if ($("#" + id_input).hasClass("unique")) {
        var table = $("#" + id_input).attr('table');
        var id_check = $("#" + id_input).attr('idcheck');
        if (value_input != '' && table != '') {
            if(!CheckUnique(id_check, table, id_input, value_input)){
                valid.status = false;
                valid.error.push("is existed!");
            }
        }
    }
    return valid;
}

function CheckUnique(id, tableName, fieldName, value){
    var success = false;
    $.ajax({
        type: "POST",
        url: "/tuanp1/index/checkunique",
        async: false,
        data: {data: JSON.stringify({id: id, tableName: tableName, fieldName: fieldName, value: value})},
        dataType: "json",
        error: function(data){
            
        },
        success: function(data){
            success = data;
        }
    })
    return success;
}

function CheckFileExist(filePath){
    
}