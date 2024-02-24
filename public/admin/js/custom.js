$(document).ready(function(){
    // check admin password is correct or not

    $("#current_password").keyup(function(){
        var current_password = $("#current_password").val();
        alert(current_password);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: '/admin/check-current-password',
            data: {current_password:current_password},
            success:function(resp){
                if(resp=="false"){
                    $("#verifyCurrentPwd").html("Current Password is Incorrect");
                }else if(resp=="true"){
                    $("#verifyCurrentPwd").html("Current Password is Correct");
                }
            },error:function(){
                alert("error");
            }
        })
    });
});