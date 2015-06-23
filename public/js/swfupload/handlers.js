function fileQueueError(file, errorCode, message) {
    try {
        var imageName = "error.gif";
        var errorName = "";
        if (errorCode === SWFUpload.errorCode_QUEUE_LIMIT_EXCEEDED) {
            errorName = "You have attempted to queue too many files.";
        }

        if (errorName !== "") {
            alert(errorName);
            return;
        }

        switch (errorCode) {
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                //imageName = "zerobyte.gif";
                alert('文件为空');
                break;
            case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                //imageName = "toobig.gif";
                alert('文件大于 '+this.settings.file_size_limit);
                break;
            case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
            case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
            default:
                alert(message);
                break;
        } 

    } catch (ex) {
        this.debug(ex);
    }

}

//关闭文件对话框是触发的动作，这里是直接开始上传
function fileDialogComplete(numFilesSelected, numFilesQueued) {

    try {
        if (numFilesSelected > 0) {

            this.startUpload();
        }
    } catch (ex) {
        this.debug(ex);
    }
}

//上传处理函数
//file是当前上传的文件信息,bytesLoaded当前传送的字节
function uploadProgress(file, bytesLoaded) {

    try {
        var percent = Math.ceil((bytesLoaded / file.size) * 100);

        var progress = new FileProgress(file,  this.customSettings.progress_target);

        //progress.setProgress(percent);
        if (percent === 100) {
            //progress.setStatus("Creating thumbnail...");
            progress.toggleCancel(false, this);
        } else {
            progress.setStatus("上传中...");
            progress.toggleCancel(true, this);
            $("#jindu").show();
            $("#jindu1").show();
            percent = percent+4;
            $(".bar").css("width",percent+"%"); 
        }
    } catch (ex) {
        this.debug(ex);
    }
}

//上传apk成功处理函数
//file是当前上传的文件信息,serverData是服务器返回的信息
function uploadSuccess(file, serverData) {


    try {
        var progress = new FileProgress(file,this.customSettings.progress_target);

        /*        if (serverData.substring(0, 7)=="FILEID") {*/
        //addImage("thumbnail.php?id=" + serverData.substring(7));
        //显示服务器返回的信息
        //var filepath = serverData.substring(8);
        var filepath =  serverData.split("FILEID:");
        //alert(filepath[1]);     //FILEID:542541
        addFile(serverData.substring(7),this.customSettings.input_target);
        if(serverData == '0'){
            alert('服务器繁忙,请重新上传！');
            return false;
        }else{ 
          
            var obj = eval('('+serverData+')');
            console.log(obj);
            //$("#app_display").attr("href",obj.url);
            $("#app_display").text(obj.file_name);
            $("#app_url").val(obj.file_url); 
            $("#app_type").val(obj.file_type);
            $("#package_name").val(obj.package_name['0']);
        } 

    } catch (ex) {
        this.debug(ex);
    }
}
//上传img成功处理函数
//file是当前上传的文件信息,serverData是服务器返回的信息
function uploadSuccessImg(file, serverData) {


    try {
        var progress = new FileProgress(file,this.customSettings.progress_target);

        /*        if (serverData.substring(0, 7)=="FILEID") {*/
        //addImage("thumbnail.php?id=" + serverData.substring(7));
        //显示服务器返回的信息
        //var filepath = serverData.substring(8);
        var filepath =  serverData.split("FILEID:");
        //alert(filepath[1]);     //FILEID:542541
        addFile(serverData.substring(7),this.customSettings.input_target);
        if(serverData == '0'){
            alert('服务器繁忙,请重新上传！');
            return false;
        }
        progress.setComplete(filepath);
        progress.setStatus("上传成功");
        progress.toggleCancel(false);

        var obj = eval('('+serverData+')');
        $("#img_in").attr("src",obj.url);
        $("#ad_img").attr("value",obj.file_url);
        $("#del").css({"display":''}); 

    } catch (ex) {
        this.debug(ex);
    }
}

//全部上传结束
function uploadComplete(file) {


    try {
        /*  I want the next upload to continue automatically so I'll call startUpload here */
        if (this.getStats().files_queued > 0) {
            this.startUpload();
        } else {
            var progress = new FileProgress(file,  this.customSettings.progress_target);
            //2008-11-05
            //全部上传结束，设置参数为false,表示不加文件数量
            progress.setComplete();
            progress.setStatus("上传结束"); 
            //    location.href="/index.php?c=SalesManagement_Album&a=EditAlbum&albumid="+albumId;
            //progress.toggleCancel(false);
        }
    } catch (ex) {
        this.debug(ex);
    }
}

//上传发生错误
function uploadError(file, errorCode, message) {

    var imageName =  "error.gif";
    var progress;
    try {
        switch (errorCode) {
            case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
            try {
                progress = new FileProgress(file,  this.customSettings.progress_target);
                progress.setCancelled();
                progress.setStatus("取消上传");
                progress.toggleCancel(false);
            }
            catch (ex1) {
                this.debug(ex1);
            }
            break;
            case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
            try {
                progress = new FileProgress(file,  this.customSettings.progress_target);
                progress.setCancelled();
                progress.setStatus("停止上传");
                progress.toggleCancel(true);
            }
            catch (ex2) {
                this.debug(ex2);
            }
            case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                imageName = "uploadlimit.gif";
                break;
            default:
                alert(message);
                break;
        }

        //addImage("images/" + imageName);

    } catch (ex3) {
        this.debug(ex3);
    }

}


//显示服务器返回的信息,放到指定的容器中
function addFile(str,target_id) {


    //屏蔽此函数
    return;

    var input_target =document.getElementById(target_id);    

    input_target.value = str;
    return ;
}

function fadeIn(element, opacity) {
    var reduceOpacityBy = 5;
    var rate = 30;    // 15 fps


    if (opacity < 100) {
        opacity += reduceOpacityBy;
        if (opacity > 100) {
            opacity = 100;
        }

        if (element.filters) {
            try {
                element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
            } catch (e) {
                // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
                element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
            }
        } else {
            element.style.opacity = opacity / 100;
        }
    }

    if (opacity < 100) {
        setTimeout(function () {
            fadeIn(element, opacity);
        }, rate);
    }
}



