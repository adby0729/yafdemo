/*
    A simple class for displaying file information and progress
    Note: This is a demonstration only and not part of SWFUpload.
    Note: Some have had problems adapting this class in IE7. It may not be suitable for your application.
*/

// Constructor
// file is a SWFUpload file object
// targetID is the HTML element id attribute that the FileProgress HTML structure will be added to.
// Instantiating a new FileProgress object with an existing file will reuse/update the existing DOM elements
function FileProgress(file, targetID) {
    this.fileProgressID = file.id;

    this.opacity = 100;
    this.height = 0;

    this.fileProgressWrapper = document.getElementById(this.fileProgressID);
    
    
    if (!this.fileProgressWrapper) {
        //没有指定的文件对象，建立新的
        this.fileProgressWrapper = document.createElement("div");
        this.fileProgressWrapper.className = "progressWrapper";
        this.fileProgressWrapper.id = this.fileProgressID;

        this.fileProgressElement = document.createElement("div");
        this.fileProgressElement.className = "progressContainer";

        var progressCancel = document.createElement("a");
        progressCancel.className = "progressCancel";
        progressCancel.href = "#";
        progressCancel.style.visibility = "hidden";
        progressCancel.appendChild(document.createTextNode(" "));  

        var progressText = document.createElement("div");
        progressText.className = "progressName";
        progressText.appendChild(document.createTextNode(file.name));

        var progressBar = document.createElement("div");
        progressBar.className = "progressBarInProgress";

        var progressStatus = document.createElement("div");
        progressStatus.className = "progressBarStatus";
        progressStatus.innerHTML = "&nbsp;";
        
        //添加隐藏的表单，保存文件地址
        var progressPath = document.createElement("input");
        progressPath.type = "hidden";
        progressPath.id= "_files[]";
        progressPath.name= "_files[]";

        this.fileProgressElement.appendChild(progressCancel);
        this.fileProgressElement.appendChild(progressText);
        this.fileProgressElement.appendChild(progressStatus);
        this.fileProgressElement.appendChild(progressBar);
        this.fileProgressElement.appendChild(progressPath);

        this.fileProgressWrapper.appendChild(this.fileProgressElement);

        document.getElementById(targetID).appendChild(this.fileProgressWrapper);
    } else {
        this.fileProgressElement = this.fileProgressWrapper.firstChild;
    }

    this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.setProgress = function (percentage) {
    this.fileProgressElement.className = "progressContainer green";
    this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
    this.fileProgressElement.childNodes[3].style.width = percentage + "%";
};
FileProgress.prototype.setComplete = function (filepath) {
    this.fileProgressElement.className = "progressContainer blue";
    this.fileProgressElement.childNodes[3].className = "progressBarComplete";
    this.fileProgressElement.childNodes[3].style.width = "";
    
    //2008-10-24
    //这里加载保存文件路径功能
    if(filepath){
        this.fileProgressElement.childNodes[4].value = filepath;
        
        //2008-11-05
        //文件总数加1
        file_count+=1;
    }
    
    
    
    
    var oSelf = this;
    setTimeout(function () {
        oSelf.disappear();
    }, 5000);
};
FileProgress.prototype.setError = function () {
    this.fileProgressElement.className = "progressContainer red";
    this.fileProgressElement.childNodes[3].className = "progressBarError";
    this.fileProgressElement.childNodes[3].style.width = "";

    var oSelf = this;
    setTimeout(function () {
        oSelf.disappear();
    }, 5000);
};
FileProgress.prototype.setCancelled = function () {
    this.fileProgressElement.className = "progressContainer";
    this.fileProgressElement.childNodes[3].className = "progressBarError";
    this.fileProgressElement.childNodes[3].style.width = "";

    var oSelf = this;
    setTimeout(function () {
        oSelf.disappear();
    }, 2000);
};


FileProgress.prototype.setStatus = function (status) {
    this.fileProgressElement.childNodes[2].innerHTML = status;
};



// Show/Hide the cancel button
FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
    //2008-10-24
    //屏蔽隐藏取消按钮功能
    show = true;
    
    this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
    if (swfUploadInstance) {
        var fileID = this.fileProgressID;
        this.fileProgressElement.childNodes[0].onclick = function () {
            swfUploadInstance.cancelUpload(fileID);
            return false;
        };
    }
    else
    {
        //2008-10-24
        //这里加载删除功能
        var oSelfFile = this.fileProgressWrapper;    
        
        /*this.fileProgressElement.childNodes[0].onclick = function () {
            if(confirm("删除操作确认?")) {
                oSelfFile.parentNode.removeChild(oSelfFile);
                
                //2008-11-05
                //文件总数减1
                file_count-=1;
                document.getElementById("upload_file_name").value='';
                return;
            }
        }; del by zlli 2011-8-30*/
        
    }
};

// Fades out and clips away the FileProgress box.
FileProgress.prototype.disappear = function () {
    
    //2008-10-24
    //屏蔽隐藏功能
    return;

    var reduceOpacityBy = 15;
    var reduceHeightBy = 4;
    var rate = 30;    // 15 fps

    if (this.opacity > 0) {
        this.opacity -= reduceOpacityBy;
        if (this.opacity < 0) {
            this.opacity = 0;
        }

        if (this.fileProgressWrapper.filters) {
            try {
                this.fileProgressWrapper.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.opacity;
            } catch (e) {
                // If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
                this.fileProgressWrapper.style.filter = "progid:DXImageTransform.Microsoft.Alpha(opacity=" + this.opacity + ")";
            }
        } else {
            this.fileProgressWrapper.style.opacity = this.opacity / 100;
        }
    }

    if (this.height > 0) {
        this.height -= reduceHeightBy;
        if (this.height < 0) {
            this.height = 0;
        }

        this.fileProgressWrapper.style.height = this.height + "px";
    }

    if (this.height > 0 || this.opacity > 0) {
        var oSelf = this;
        setTimeout(function () {
            oSelf.disappear();
        }, rate);
    } else {
        this.fileProgressWrapper.style.display = "none";
    }
};