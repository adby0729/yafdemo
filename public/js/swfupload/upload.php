<?php
    /**
    * apk�ļ��ϴ�
    */  
    $config=array();
    $config['type']=array("apk","ipa","xap");      
    $config['apk_size']=204800;    
    $config['name']=mktime();   
    $arr = include('../../../conf/ConfigFileDir.php');
    $config['apk_path']=$arr['upload_dir']; 
    $config['host']=$arr['upload_url']; 
    //�ļ��ϴ�
    uploadfile(); 
    function uploadfile()
    {
        global $config; 
        //�ж��ϴ��ļ��Ƿ�����
        $filetype   = $_FILES['Filedata']['type'];
        $filesize   = $_FILES['Filedata']['size'];
        $filename   = $_FILES['Filedata']['name']; 
        $type       = str_ireplace('.','',strrchr($_FILES['Filedata']['name'],'.'));
        //�ж��ļ���С�Ƿ����Ҫ��
        if($filesize>$config['apk_size']*1024){
            echo 0;die();
        }
        if(!in_array($type,$config['type'])){
           echo 0;die();
        }  
        
        
        
        
        
        $file_host=$config['apk_path'].'apk/'.date("Y-m").'/';
        $file_url ='apk/'.date("Y-m").'/'.$filename;
        getPicPath($file_host);
        $file_host = $file_host.$filename;   
        if(move_uploaded_file($_FILES['Filedata']['tmp_name'],$file_host))
        {
            echo  $file_url; 
        }else{
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
