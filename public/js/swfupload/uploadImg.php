<?php 

    $config=array();

    $arr = include('../../../conf/ConfigFileDir.php'); 
    $config=array(); 

    $config['type']=array("jpg","bmp","gif",'png');    //img允许后缀
    //$config['flash']=array("flv","swf");    //flash允许后缀 
    //$config['flash_size']=200;    //上传flash大小上限 单位：KB
    $config['size']=5000;    //上传img大小上限 单位：KB  
    $config['path']=$arr['upload_dir'];
    //$config['flash_dir']=$arr['upload_dir']."flash";    //上传flash文件地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/" 
    //上线后，待修改。这个是图片的路径
    $config['host']=$arr['upload_url'];   
    //文件上传
    uploadfile(); 
    function uploadfile()
    {
        global $config; 
        //判断上传文件是否允许
        $filetype   = $_FILES['Filedata']['type'];
        $filesize   = $_FILES['Filedata']['size']; 
        $type       = str_ireplace('.','',strrchr($_FILES['Filedata']['name'],'.'));
        //判断文件大小是否符合要求
        if($filesize>$config['size']*1024){
            echo 0;die();
        }
        if(!in_array($type,$config['type'])){
            echo 0;die();
        }   
        $file ='img/'.date('Y')."/".date('m').'/';
        $filename = md5(time().rand(0,400)).'.'.$type;
        
        $file_host=$config['path'].$file;//目录地址
        
        $file_url =$file.$filename;
        getPicPath($file_host);
        $file_host = $file_host.$filename;   

        if(move_uploaded_file($_FILES['Filedata']['tmp_name'],$file_host))
        { 
            $data = array();
            $data['url'] = $config['host'].$file_url;
            $data['file_url'] = $file_url;
            echo json_encode($data);
        }
        else
        {
            echo 0;
        } 
    } 
    function getPicPath($filePath) {
        $file = explode('/', $filePath); 
        foreach ($file as $k => $v) {
            $path .= $k == 0 ? $v : '/'.$v;    
            if (!file_exists($path)) {
                @mkdir($path, 0777);
                @chmod($path, 0777);
            }   
        } 
        return  true;
    } 
?>
