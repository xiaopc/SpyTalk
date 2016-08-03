var routes = {
      "/welcome": {
            on: function () {
                $(".content").hide();
                $("#main-welcome .content").show();
            }
      },
      "/encrypt": {
            on: function () {
                $(".content").hide();
                $("#main-send-content .content").show();
            }
      },
      "/go": {
            on: function () {
                $(document).ready(function() {
                    $(".content").hide();
                    $("#main-recv-req .content").show();
                });
                $(".content").hide();
                $("#main-recv-req .content").show();
            }
      },
      "/go/:file": {
            on: function (file) {
                window.onload = function() {
                    $(".content").hide();
                    $("#main-recv-req .content").show();
                };
                $("#recvName").val(file);
            }
      }
};
var router = new Router(routes);
router.init();

$("#main-start").click(function(){
    $("#main-welcome .content").hide();
    $("#main-send-content .content").show();
});
$("#send-content-next").click(function(){
    if ($("#sendText").val().length === 0) {sweetAlert("请输入内容!", "", "error");} else {
        $("#main-send-content .content").hide();
        $("#main-send-setting .content").show();
    }
});
$("#send-setting-next").click(function(){
    if ($("#sendFilename").val().length === 0) {sweetAlert("您还有未填的项!", "", "error");} else {
        var key = CryptoJS.enc.Utf8.parse($("#sendPassword").val());
        var iv = "initialvector123";
        var encrypted = CryptoJS.AES.encrypt($("#sendText").val(), key.toString(), { iv:iv.toString(),mode:CryptoJS.mode.CFB,padding:CryptoJS.pad.Pkcs7});
        encryptedContent = encrypted.toString();
        
        $.post("/api/file/up",{  
          bucket: $("#sendFilename").val(),  
          method: "aes",
          isDelOnce: $("#isDelOnce").is(':checked'),
          isIPcheck: $('#isIPcheck').val(),
          encrypted: encryptedContent,
          gRecaptchaResponse: grecaptcha.getResponse()
        },function(data,state){  
            switch(data.status){
                case "200":
                    $("#main-send-setting .content").hide();
                    $("#main-send-finish .content").show();
                    var URL = window.location.origin+"/#/go/"+data.data.bucket;
                    $("#sendURL").val(URL);
                    $('#qrcode').empty();
                    $('#qrcode').qrcode(URL);
                    grecaptcha.reset();
                    break;
                case "400":
                    sweetAlert("您还有未填的项!", "", "error");
                    break;
                case "412":
                    sweetAlert("您未通过人机验证!", "", "error");
                    grecaptcha.reset();
                    break;
                case "500":
                    sweetAlert("上传信息失败!", "", "error");
                    grecaptcha.reset();
                    break;
                default:
                    sweetAlert("未知错误!", "", "error");
                    grecaptcha.reset();
            }
        },"json");
    }
});

function getFile(recvname){
    $.post("/api/file/dl",{  
      bucket: recvname
    },function(data,state){
        switch (data.status){
            case "200":
                var recvreq = new Vue({
                    el: '#main-recv-dec',
                    data: { recvMethod: data.data.method, recvEncrypt: data.data.encrypted }
                });
                $("#main-recv-req .content").hide();
                $("#main-recv-dec .content").show();
                break;
            case "400":
                sweetAlert("您还有未填的项!", "", "error");
                break;
            case "404":
                sweetAlert("文件不存在!", "", "error");
                break;
            case "403":
                sweetAlert("您的 IP 不在允许查看的范围内!", "", "error");
                break;
            default:
                sweetAlert("未知错误!", "", "error");
                break;
        }
    },"json");
}

$("#recv-req-next").click(function(){
	recv = $("#recvName").val();
    if (recv.length === 0) {sweetAlert("请输入内容!", "", "error");return 0;}
    $.post("/api/file/check",{  
      bucket: recv
    },function(data,state){
        if (data.status != 200){sweetAlert("获取文件失败!", "", "error");return 0;}
        if (data.data.DelOnce == "true"){
            swal({
               title: "注意!",
               text: "文件设置了阅后即焚,您确定要打开吗?",
               type: "warning",
               showCancelButton: true,
               cancelButtonText: "取消",
               confirmButtonColor: "#DD6B55",
               confirmButtonText: "继续",
               closeOnConfirm: true
            }, function(isConfirm){
               if (isConfirm) {
               	  getFile(recv);
               } else {
               	  return 0;
               }
            });
        } else {getFile(recv);}

    },"json");
});

$("#recv-dec-next").click(function(){
    if ($("#recvPsw").val().length === 0) {return 0;} else {
        var key = CryptoJS.enc.Utf8.parse($("#recvPsw").val());
        var iv = "initialvector123";
        var decrypted = CryptoJS.AES.decrypt($("#recvEncrypt").val(), key.toString(), { iv:iv.toString(),mode:CryptoJS.mode.CFB,padding:CryptoJS.pad.Pkcs7});
        decryptedContent = CryptoJS.enc.Utf8.stringify(decrypted);
        var recvres = new Vue({
            el: '#main-recv-res',
            data: { recvDecrypt: decryptedContent }
        });
        $("#main-recv-dec .content").hide();
        $("#main-recv-res .content").show();

    }
});