<?php 

    $config=array();

    $arr = include('../../../conf/ConfigFileDir.php'); 
    $config=array(); 

    $config['type']=array("jpg","bmp","gif",'png');    //img�����׺
    //$config['flash']=array("flv","swf");    //flash�����׺ 
    //$config['flash_size']=200;    //�ϴ�flash��С���� ��λ��KB
    $config['size']=5000;    //�ϴ�img��С���� ��λ��KB  
    $config['path']=$arr['upload_dir'];
    //$config['flash_dir']=$arr['upload_dir']."flash";    //�ϴ�flash�ļ���ַ ���þ��Ե�ַ ����upload.php�ļ�����վ�ڵ��κ�λ�� ���治��"/" 
    //���ߺ󣬴��޸ġ������ͼƬ��·��
    $config['host']=$arr['upload_url'];   
    //�ļ��ϴ�
    uploadfile(); 
    function uploadfile()
    {
        global $config; 
        //�ж��ϴ��ļ��Ƿ�����
        $filetype   = $_FILES['Filedata']['type'];
        $filesize   = $_FILES['Filedata']['size']; 
        $type       = str_ireplace('.','',strrchr($_FILES['Filedata']['name'],'.'));
        //�ж��ļ���С�Ƿ����Ҫ��
        if($filesize>$config['size']*1024){
            echo 0;die();
        }
        if(!in_array($type,$config['type'])){
            echo 0;die();
        }   
        $file ='img/'.date('Y')."/".date('m').'/';
        $filename = md5(time().rand(0,400)).'.'.$type;
        
        $file_host=$config['path'].$file;//Ŀ¼��ַ
        
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
