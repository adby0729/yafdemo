<?PHP
    /*
    MySQL 数据库访问封装类
    1,连接数据库 mysql_connect or mysql_pconnect
    2,选择数据库 mysql_select_db
    3,执行SQL查询 mysql_query
    4,处理返回的数据 mysql_fetch_array mysql_num_rows mysql_fetch_assoc mysql_fetch_row etc
    */
    class DbMysql{
        private static $querynum = 0 ; //当前页面进程查询数据库的次数
        private static $dblink ;      //数据库连接资源
        /**
        * 链接数据库
        *
        * @param mixed $dbcharset   编码
        * @param mixed $pconnect    是否长连接
        * @param mixed $halt        是否开启错误提醒
        */
        public static function connect($dbcharset='utf-8',$pconnect=0 , $halt=true){
            $db_config = Yaf_Application::app()->getConfig()->application->mysql;
            $dbhost = $db_config->DB_HOST;
            $dbuser = $db_config->DB_USER;
            $dbpw = $db_config->DB_PWD;
            $dbname = $db_config->DB_NAME;
            $func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect' ;
            self::$dblink = @$func($dbhost,$dbuser,$dbpw);
            if ($halt && !self::$dblink)
            {
                self::$halt("无法链接mysql数据库！");
            }
            //设置查询字符集
            mysql_query("SET character_set_connection={$dbcharset},character_set_results={$dbcharset},character_set_client=binary",self::$dblink) ;
            //选择数据库
            $dbname && @mysql_select_db($dbname,self::$dblink) ; 
        }
        //选择数据库
        public static function select_db($dbname){
            return mysql_select_db($dbname,self::$dblink);
        }
        //执行SQL查询
        public static  function query($sql){
            self::$querynum++ ;
            return mysql_query($sql,self::$dblink);
        }
        #得到单条数据
        public static function getRow($sql){ 
            $result = mysql_query($sql,self::$dblink);
            return  self::fetch_assoc($result);
        }
        #得到全部数据
        public static function getAll($sql){

            $result = mysql_query($sql,self::$dblink); 
            return  self::mysql_fetch_full_result_array($result);
        }
        public static function mysql_fetch_full_result_array($result){
            $table_result=array();
            $r=0;
            while($row = mysql_fetch_assoc($result)){
                $arr_row=array();
                $c=0;
                while ($c < mysql_num_fields($result)) {
                    $col = mysql_fetch_field($result, $c);
                    $arr_row[$col -> name] = $row[$col -> name];
                    $c++;
                }
                $table_result[$r] = $arr_row;
                $r++;
            }
            return $table_result;
        }
        //返回最近一次与连接句柄关联的INSERT，UPDATE 或DELETE 查询所影响的记录行数
        public static function affected_rows(){
            return mysql_affected_rows(self::$dblink) ;
        }
        //取得结果集中行的数目,只对select查询的结果集有效
        public static function num_rows($result){
            return mysql_num_rows($result) ;
        }
        //获得单格的查询结果
        public static function result($result,$row=0){
            return mysql_result($result,$row) ;
        }
        //取得上一步 INSERT 操作产生的 ID,只对表有AUTO_INCREMENT ID的操作有效
        public static function insert_id(){
            return ($id = mysql_insert_id(self::$dblink)) >= 0 ? $id : self::$result(self::$query("SELECT last_insert_id()"), 0);
        }
        //从结果集提取当前行，以数字为key表示的关联数组形式返回
        public static function fetch_row($result){
            return mysql_fetch_row($result) ;
        }
        //从结果集提取当前行，以字段名为key表示的关联数组形式返回
        public static function fetch_assoc($result){
            return mysql_fetch_assoc($result);
        }
        //从结果集提取当前行，以字段名和数字为key表示的关联数组形式返回
        public static function fetch_array($result){
            return mysql_fetch_array($result);
        }
        //关闭链接
        public static function close(){
            return mysql_close(self::$dblink) ;
        }
        //输出简单的错误HTML提示信息并终止程序
        public static function halt($msg){
            $message = "<html>\n<head>\n" ;
            $message .= "<meta content='text/html;charset=gb2312'>\n" ;
            $message .= "</head>\n" ;
            $message .= "<body>\n" ;
            $message .= "数据库出错：".htmlspecialchars($msg)."\n" ;
            $message .= "</body>\n" ;
            $message .= "</html>" ;
            echo $message ;
            exit ;
        }
    }
?>