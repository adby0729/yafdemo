<?PHP
    /*
    MySQL ���ݿ���ʷ�װ��
    1,�������ݿ� mysql_connect or mysql_pconnect
    2,ѡ�����ݿ� mysql_select_db
    3,ִ��SQL��ѯ mysql_query
    4,�����ص����� mysql_fetch_array mysql_num_rows mysql_fetch_assoc mysql_fetch_row etc
    */
    class DbMysql{
        private static $querynum = 0 ; //��ǰҳ����̲�ѯ���ݿ�Ĵ���
        private static $dblink ;      //���ݿ�������Դ
        /**
        * �������ݿ�
        *
        * @param mixed $dbcharset   ����
        * @param mixed $pconnect    �Ƿ�����
        * @param mixed $halt        �Ƿ�����������
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
                self::$halt("�޷�����mysql���ݿ⣡");
            }
            //���ò�ѯ�ַ���
            mysql_query("SET character_set_connection={$dbcharset},character_set_results={$dbcharset},character_set_client=binary",self::$dblink) ;
            //ѡ�����ݿ�
            $dbname && @mysql_select_db($dbname,self::$dblink) ; 
        }
        //ѡ�����ݿ�
        public static function select_db($dbname){
            return mysql_select_db($dbname,self::$dblink);
        }
        //ִ��SQL��ѯ
        public static  function query($sql){
            self::$querynum++ ;
            return mysql_query($sql,self::$dblink);
        }
        #�õ���������
        public static function getRow($sql){ 
            $result = mysql_query($sql,self::$dblink);
            return  self::fetch_assoc($result);
        }
        #�õ�ȫ������
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
        //�������һ�������Ӿ��������INSERT��UPDATE ��DELETE ��ѯ��Ӱ��ļ�¼����
        public static function affected_rows(){
            return mysql_affected_rows(self::$dblink) ;
        }
        //ȡ�ý�������е���Ŀ,ֻ��select��ѯ�Ľ������Ч
        public static function num_rows($result){
            return mysql_num_rows($result) ;
        }
        //��õ���Ĳ�ѯ���
        public static function result($result,$row=0){
            return mysql_result($result,$row) ;
        }
        //ȡ����һ�� INSERT ���������� ID,ֻ�Ա���AUTO_INCREMENT ID�Ĳ�����Ч
        public static function insert_id(){
            return ($id = mysql_insert_id(self::$dblink)) >= 0 ? $id : self::$result(self::$query("SELECT last_insert_id()"), 0);
        }
        //�ӽ������ȡ��ǰ�У�������Ϊkey��ʾ�Ĺ���������ʽ����
        public static function fetch_row($result){
            return mysql_fetch_row($result) ;
        }
        //�ӽ������ȡ��ǰ�У����ֶ���Ϊkey��ʾ�Ĺ���������ʽ����
        public static function fetch_assoc($result){
            return mysql_fetch_assoc($result);
        }
        //�ӽ������ȡ��ǰ�У����ֶ���������Ϊkey��ʾ�Ĺ���������ʽ����
        public static function fetch_array($result){
            return mysql_fetch_array($result);
        }
        //�ر�����
        public static function close(){
            return mysql_close(self::$dblink) ;
        }
        //����򵥵Ĵ���HTML��ʾ��Ϣ����ֹ����
        public static function halt($msg){
            $message = "<html>\n<head>\n" ;
            $message .= "<meta content='text/html;charset=gb2312'>\n" ;
            $message .= "</head>\n" ;
            $message .= "<body>\n" ;
            $message .= "���ݿ����".htmlspecialchars($msg)."\n" ;
            $message .= "</body>\n" ;
            $message .= "</html>" ;
            echo $message ;
            exit ;
        }
    }
?>