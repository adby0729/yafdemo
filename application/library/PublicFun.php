<?php
/**
* 公共方法
* @auth huangtao
*/
class PublicFun{
    #激活码缓存时间
    public static $isActiveTime = 86400;
    //取得渲染模版
    public static function fetchCol($file,$dirpath="")
    {
        $extension = Yaf_Application::app()->getConfig()->application->view->ext;
        $dirpath = $dirpath?$dirpath.$file.'.'.$extension:VIEWS_PATH.'plugin/'.$file.'.'.$extension;
        //  echo $dirpath;
        if (empty($file)||!is_file($dirpath))
        {
            trigger_error('$file Is null or does not exist!');
            return false;
        }
        //    print_r($this); die();
        return $dirpath;
    }
    //跳转URL
    public static function toUrl($url,$message=""){
        header("Content-type: text/html; charset=utf-8"); 
        if (empty($url))
        {
            trigger_error('$url dose not empty!');
            return false;
        }
        if($message) {
            echo "<script type='text/javascript'>alert('" , $message , "'); window.location.href='" , $url , "';</script>";die;
        }else{
            echo "<script type='text/javascript'>window.location.href='" , $url , "';</script>";die;
        }
    } 
    //密码加密算法
    public static function getPassword($password,$salt=''){
        if(!$password){return false;}
        $salt = $salt?$salt:substr(uniqid(rand()), -6);
        return array($salt,md5(md5($password).$salt));
    }     

    /**
    * 失败返回
    */
    public static function toErrorBack($message=''){
        header("Content-type: text/html; charset=utf-8"); 
        echo "<script type='text/javascript'>alert('$message');history.back();</script>";die;
    }
    /**
    * 脏数据处理
    *@param $_string  string or array 需过滤数据 
    *@param $strtolower string 是否开启大写转换成小写
    *@param $trim string 是否开启去空格
    */
    public static function Str($_string,$trim='',$strtolower='') { 
        //如果开启状态，那么就不需要转义
        if(!isset($_string)||empty($_string)){return null;}
        if (!get_magic_quotes_gpc()){
            if (is_array($_string)){
                foreach ($_string as $_key => $_value){
                    $_string[$_key] = self::Str($_value); //这里采用了递归
                }
            } else {
                if($strtolower){$_string =  strtolower($_string);}
                if($trim){$_string =  trim($_string);} 
                $_string = htmlspecialchars(strip_tags($_string));
            }
        }else{
            if (is_array($_string)){
                foreach ($_string as $_key => $_value){
                    $_string[$_key] = self::Str($_value); //这里采用了递归
                }
            } else { 
                if($strtolower){$_string =  strtolower($_string);}
                if($trim){$_string =  trim($_string);}
            }

        }
        return $_string;
    }    
    /**
    * 数据转义输出
    */
    public static function OutStr($_string) { 
        if (is_array($_string)) {   
            foreach ($_string as $_key => $_value) {
                $_string[$_key] = self::OutStr($_value); //这里采用了递归
            }
        } else {  
            $_string = htmlspecialchars($_string);
        } 
        return $_string;
    }
    /**
    * 针对ck编辑器过来的字符串处理
    * @param mixed $string
    */
    public static function CkTransFormString($string) {        
        $str = str_replace("\\",'',$string);
        return $str;       
    } 
    function xss_clean($data)
    {
        $data = preg_replace( "@<script(.*?)</script>@is", "", $data );
        // $cp = preg_replace( "@<iframe(.*?)</iframe>@is", "", $cp );
        // $cp = preg_replace( "@<style(.*?)</style>@is", "", $cp );
        return $data;
    }

    /**
    * 获得ip
    */
    public static function getClientIp(){
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
    /**
    * 判断图片大小
    * @author caolong
    * @param mixed $img  图片名
    */
    public static function checkImg($img) {
        $imgsize = getimagesize($img);
        $result['width'] = $imgsize[0];
        $result['height'] = $imgsize[1];
        $result['size'] = intval(filesize($img) / 1024);
        return $result;
    }
    /**
    * 去除标签 过滤
    * @param string $string
    * @return string
    */
    public static  function  saddslashes($string) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $newkey = addslashes(strip_tags($key));
                if($newkey != $key) {
                    unset($string[$key]);
                }
                $string[$newkey] = saddslashes($val);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }
    /**
    * 分页信息类
    */
    public static function getPage1( $total, $pagerow, $page, $url ){
        $pageBar = '';
        $pages   =       ceil($total/$pagerow);//总页数进位取整
        $num = 3;
        if($pages<2){
            return false;
        }
        $pageBar .=      '<div class="pagination"><ul>';
        if($page > 1){
            $pageBar .=      '<li><a href="'.$url.'1">«</a></li><li><a target="_self" href="'.$url.($page-1).'"><i class="p-arrow-icon"></i>上一页</a></li>';
        }
        for($i=1;$i<=$pages;$i++){
            if( $page<($num+1)){
                if($i>7){continue; }
            }else{
                if($i< ( $page-$num) or $i>( $page+$num)){continue; }
            }
            if($i== $page){
                $pageBar .= '<li class="active"><a target="_self" >'.$i.'</a></li>';
            }else{
                $pageBar .= '<li><a target="_self" href="'. $url.$i.'">'.$i.'</a></li>';
            }
        }
        if((int) $page < (int)$pages){
            $pageBar .='<li><a href="'. $url.(( $page+1)).'"  target="_self">下一页</a></li><li><a href="'.$url.$pages.'">»</a></li>';
        }
        $pageBar .=      '</div></ul> ';
        return array('pageBar'=>$pageBar,'pages'=>$pages);
    }
    /**
    * 分页信息类
    */
    public  function getPage(){ 
        $pages   =       ceil($this->getRequest()->total/$this->getRequest()->pageRow);//总页数进位取整
        $num = 3;
        if($pages<2){
            return false;
        }
        $pageBar .=      '<div class="pagination"><ul> ';
        if($this->getRequest()->page > 1){
            $pageBar .=      '<li><a target="_self" href="'.$this->getRequest()->url.($this->getRequest()->page-1).'.html"><i class="p-arrow-icon"></i>上一页</a></li>';
        }
        for($i=1;$i<=$pages;$i++){
            if( $this->getRequest()->page<($num+1)){
                if($i>7){continue; }
            }else{
                if($i< ( $this->getRequest()->page-$num) or $i>( $this->getRequest()->page+$num)){continue; }
            }
            if($i== $this->getRequest()->page){
                $pageBar .= '<li><a target="_self" >'.$i.'</a></li>';
            }else{
                $pageBar .= '<li><a target="_self" href="'. $this->getRequest()->url.$i.'.html">'.$i.'</a></li>';
            }
        }
        if((int) $this->getRequest()->page < (int)$ouput->pages){
            $pageBar .='<li><a href="'. $this->getRequest()->url.(( $this->getRequest()->page+1)).'.html"  target="_self">下一页</a></li>';
        }
        $pageBar .=      '</div></ul> ';
        return array('pageBar'=>$pageBar,'pages'=>$pages);
    }
    /**
    * log日志文件
    *
    * @param mixed $file
    * @param mixed $content
    */
    public static function writeLog($file,$content){
        self::getPicPath(UPLOAD_DIR.'/log/');
        @error_log($content."\n",3,UPLOAD_DIR.'/log/'.$file);
        @chmod(UPLOAD_DIR.'log/'.$file, 0777);
    }
    public static function getUserData($userId){
        $where              =   array('z_id'=>(int)$userId);
        return   UserInfo::getUserOne($where);
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
    public static function getCookieValue( $key ){
        if(isset($_COOKIE[$key]) && !empty($_COOKIE[$key])){
            return $_COOKIE[$key];
        }
        return 0;
    }
    /**
    * 图片上传
    *
    * @param mixed $config
    * @param mixed $fileResource
    */
    public static function uploadImges( $config, $fileResource,$file){
        //判断上传文件是否允许
        $filetype   = $fileResource['type'];
        $filesize   = $fileResource['size'];
        $type       = str_ireplace('.','',strrchr($fileResource['name'],'.'));
        //判断文件大小是否符合要求
        if($filesize>$config['size']*1024){
            return 0;die();
        }
        if(!in_array($type,$config['type'])){
            return 0;die();
        }
        $filename = md5(time().rand(0,400)).'.'.$type;
        $file_host=$config['path'].$file;//目录地址
        $file_url =$file.$filename;
        self::getPicPath($file_host);
        $file_host = $file_host.$filename;
        if(move_uploaded_file($fileResource['tmp_name'],$file_host))
        {
            $data = array();
            $data['url'] = $config['host'].$file_url;
            $data['file_url'] = $file_url;
            return $data;
        }
        else
        {
            return 0;
        }
    }
    /**
    * 上传APK等 文件
    *
    * @param mixed $config
    * @param mixed $fileResource
    */
    public static function uploadFile( $config, $fileResource){
        //判断上传文件是否允许
        $filetype   = $fileResource['type'];
        $filesize   = $fileResource['size'];
        $filename   = $fileResource['name'];
        $type       = str_ireplace('.','',strrchr($fileResource['name'],'.'));
        //判断文件大小是否符合要求
        if($filesize>$config['apk_size']*1024){
            return 10;die();
        }
        if(!in_array($type,$config['type'])){
            echo $type;print_r($config['type']);
            return 20;die();
        }
        $file_host=$config['apk_path'].'apk/'.date("Y-m").'/';
        PublicFun::getPicPath($file_host);
        $filename = self::getFileName($filename);
        $file_host = $file_host.$filename.'.'.$type;
        $file_url ='apk/'.date("Y-m").'/'.$filename.'.'.$type;
        //echo $file_host;
        if(move_uploaded_file($fileResource['tmp_name'],$file_host))
        {   
            //$file_host = 'D:\wnmp\www\weimeng/uploadfile/apk/2013-12/4f0787a4df32cfe0472d4cae166ff70e.apk';
            $p = new ApkParser();
            $p->open($file_host);

            $obj = simplexml_load_string($p->getXML());
            $package_name       =   $obj['package'];
            $arr = array();
            $arr['url']         =   $config['host'].$file_url;
            $arr['file_url']    =   $file_url;
            $arr['filesize']    =   $filesize;
            $arr['file_name']   =   $filename;
            $arr['file_type']   =   $type;
            $arr['app_version'] =   $p->getVersionCode();
            $arr['package_name']=   $package_name;
            return $arr;
        }else{
            return 30;
        }
    }
    /**
    * 文件名重定义
    *
    * @param mixed $filename
    * @return string
    */
    public static function  getFileName($filename){
        return md5($filename.time().rand(0,100));
    }

    /**
    * 获取
    * 
    * @param mixed $key 配置地址
    * @param mixed $arrKeyName 配置子键，可多维，用.号分隔
    * @param enum $type PHP|INI
    * @return array|false
    */
    public static function getConfig($key, $arrKeyName = '', $type = 'PHP')
    {
        if (!defined('PRODUCTION_CONFIG_PATH')) {
            define('PRODUCTION_CONFIG_PATH', APP_PATH . '/conf');
        }

        $path = PRODUCTION_CONFIG_PATH . '/' . str_replace('_', '/', $key) . '.' . strtolower($type);                                
        switch ($type) {
            case 'INI':
                $config = parse_ini_file($path);
                break;
            default:
            case 'PHP':
                $config = include($path);
                break;
        }          
        if ($arrKeyName) {
            if (strpos('.', $arrKeyName)) {
                $keyArr = explode('.', $arrKeyName);
                foreach ($keyArr as $key) {
                    if (!$config) {
                        break;
                    }                       
                    $config = empty($config[$key]) ? false : $config[$key];
                }
            } else {
                $config = empty($config[$arrKeyName]) ? false : $config[$arrKeyName];
            }
        }
        return $config;
    }
    /** 发送激活邮件*/
    public static function SendActivateEmail($user_id,$email,$role){ 

        if(!$email||!$user_id||!$role){return false;} 

        $key =  YlmfCookie::encode($user_id);
        $is_active = md5(time().rand(0,600));
        Mem::set($key,$is_active,self::$isActiveTime);
        $url = URL."index/register/activateaccount/k/$key/v/$is_active";
        if($role==1){
            $about_url = URL.'index/webview/developer/';
        }else{
            $about_url = URL.'index/webview/advertiser/';
        }
        $str = "尊敬的用户，恭喜您注册成为微盟聚力广告平台成员！<br>
        通过微盟聚力广告，我们希望帮助手机应用开发者实现持续、公正的收益！<br>
        请点击下方链接激活您的微盟聚力广告账户：<br>
        <a href='".$url."'>$url</a> <br>
        如无法点击激活，请复制链接到浏览器地址栏访问。<br>
        新手上路，您可以通过以下的资料了解微盟聚力<br>
        微盟聚力广告的<a href='".$about_url."'>产品介绍</a><br>
        微盟聚力广告的<a href='".URL.'index/webView/about/'."'>平台介绍</a><br>
        您可以访问帮助中心来了解一些常见问题，或者直接联系我们<br>
        感谢您使用微盟聚力，希望您在微盟聚力的体验有益且愉快，期待合作！<br>
        此致<br>
        微盟聚力广告 敬上";
        $arr['email_to']        = $email;
        $arr['email_subject']   = "微盟聚力--帐号激活";
        $arr['email_message']   = $str;
        $arr['email_from']   = "微盟聚力<noreply@weimeng.net>";
        
        SendEmail::sendXiTie($arr);
    }
}