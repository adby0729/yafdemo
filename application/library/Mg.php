<?php
    /**
    * @describe  mongodb 数据库操作类
    * @author    huangtao
    * @version   1.0
    * @copyright 2013-08-20
    */
    class Mg{
        //protected static $db_config       = array();
        protected static $timeout         = 3000;//30s
        protected static $persist_db      = ''; //长连接
        protected static $i      = 0; //长连接
        protected static $mongo_cols      = array();
        private static $db         =       '';   //数据库操作集句柄
        private static $con        =       '';   //连接句柄
        private  static $result     =       '';   //返回数据
        private  static $is_close   =       false;   //是否连接
        //远程连接
        function __construct($persist_db=""){
            //self::$persist_db= $persist_db?$persist_db:'t';   //长连接
            self::$persist_db= $persist_db?$persist_db:DB_PERSIST;   //长连接
        }
        /**
        * 打开连接
        */
        public static function connect () {
            #配置文件的读取
            #配置文件的读取
            try {
                $db_config =  Yaf_Application::app()->getConfig()->application->mongodb;
                // echo self::$i++ . '< &nbsp;>'; //统计mongodb连接数
                $strconnect='mongodb://';
                $strconnect.=$db_config->DB_USER;   //  用户名
                $strconnect.=':';
                $strconnect.=$db_config->DB_PWD;     //密码
                $strconnect.='@';
                $strconnect.=$db_config->DB_HOST;   //地址
                $strconnect.=':';
                $strconnect.=$db_config->DB_PORT;   //端口
                //$strconnect.='/';
                //$strconnect.=self::$db_config['database'];
               // echo  $strconnect;die;
                self::$con= new Mongo($strconnect,array('connectTimeoutMS' => self::$timeout ) );
                self::$db = self::$con->selectDB($db_config->DB_NAME);   //database数据聚合
                self::$is_close  = true;
                return    self::$db;
            }catch (Exception $e){
                die('Mongodb Connection Fail');
            }
        }
        //select collection
        private static function _select_collection($collection_name){
            if (!isset(self::$mongo_cols[$collection_name])) {
                self::$mongo_cols[$collection_name] =  self::$db->selectCollection($collection_name);
            }
            return self::$mongo_cols[$collection_name];
        }
        /**
        * 自增id
        */
        public static function getAutoId($collnections_name){
            // self::connect();
            $update  = array('$inc'=>array("z_id"=>1));
            $query   = array('table_name' => $collnections_name);
            $command = array(
            'findandmodify'=>'autoincre_system', 'update'=>$update,
            'query'=>$query, 'new'=>true, 'upsert'=>true
            );
            $id = self::$db->command($command);
            //self::$_close();
            return $id['value']['z_id'];
        }
        /**
        * 插入数据  程序获取id
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param array  $data_array
        */
        public static function insert($collnections_name,$data_array){
            self::connect();
            $collnection = self::_select_collection($collnections_name);
            #get auto new id
            $data_array['z_id'] = self::getAutoId($collnections_name);
            if($collnections_name=="ad_info"){
                #广告三个排序字段值初始化
                $data_array['sort_hot']  =  $data_array['z_id'];     
                $data_array['sort_soft'] =  $data_array['z_id'];     
                $data_array['sort_game'] =  $data_array['z_id'];     
            }
            $collnection->insert($data_array);
            //self::$_close();
            return $data_array['z_id'];
        }
        /**
        * 插入数据  返回数组
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param array  $data_array
        */
        public static function insert_return($collnections_name,$data_array){
            self::connect();
            $collnection = self::_select_collection($collnections_name);
            #get auto new id
            $data_array['z_id'] = self::getAutoId($collnections_name);
            $collnection->insert($data_array);
            //self::$_close();
            return $data_array;
        }
        /**
        * 插入数据 自增id
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param array  $data_array
        */
        public static function insertTemp($collnections_name,$data_array){
            self::connect();
            $collnection = self::_select_collection($collnections_name);
            $collnection->insert($data_array);
            return $data_array['z_id'];
        }
        /**
        * 查询一条记录
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param array  $query                查询的条件array(key=>value) 相当于key=value
        * @param array  $filed                需要列表的字段信息array(filed1,filed2)
        */
        public static function find_one($collnections_name,$query,$filed=array()){
            self::connect();
            $connnection = self::$db->selectCollection($collnections_name);
            $result      = $connnection->findOne($query,$filed);
            //self::$_close();
            return $result;
        }
        /**
        * 查询多条记录
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param array  $sort                  排序 name desc array("name" => -1),name asc array("name" => 1)
        * @param string $limit                  "10,20" or 10
        * @param array  $query                查询的条件array(key=>value) 相当于key=value
        * @param array  $filed                需要列表的字段信息array(filed1,filed2)
        */
        public static function find_all($collection_name,$sort=array(),$limit=10,$query=array(),$field=array()){  //var_dump($collection_name, $query);
            //var_dump(self);
            self::connect();
            $result     = array();
            #SelectDB
            $collection = self::_select_collection($collection_name);
            #Query
            $cursor     = $collection->find($query,$field);
            //print_r($cursor->explain()); #查看索引情况
            #防止插入影响结果集
            //$cursor->snapshot();
            #Order
            if(!empty($sort)){
                $cursor=$cursor->sort($sort);
            }
            #Limit
            $limit=explode(',',$limit);
            if(!empty($limit[1])){
                $cursor=$cursor->limit($limit[1])->skip($limit[0]);
            }elseif(!empty($limit[0])){
                $cursor=$cursor->limit($limit[0]);
            }
            #Fetch
            while ($cursor->hasNext()){
                $tmp=$cursor->getNext();
                //unset($tmp['_id']);
                $result[] = $tmp;
            }
            //self::$_close();
            return $result;
        }
        /**
        * 查询记录集的条数
        * @param string $collection_name    集合名称(相当于关系数据库中的表)
        * @param array  $query
        * @return int
        */
        public static function find_count($collection_name,$query=array()){
            self::connect();
            $collection = self::_select_collection($collection_name);
            $rs = $collection->count($query);
            //self::$_close();
            return $rs;
        }
        /**
        * 更新或者插入数据(注一次只能更新一条记录)
        * @param string    $collection_name    集合名称|表名
        * @param array     $query              查询条件array(key=>value)
        * @param array     $update_data        要更新的数据
        * @return bool
        */
        public static function update_one($collection_name,$query,$update_data,$options=array("upsert" => true)){
            //$options =  $options?$options:array("upsert" => true);//更新或者插入
            self::connect();
            $collection = self::_select_collection($collection_name);
            //var_dump($update_data);  die;
            $update_data['z_id'] = self::getAutoId($collnection_name);
            $result     = $collection->update($query,array('$set'=>$update_data),$options);
            //self::$_close();
            return $result;
        }
        /**
        * 更新数据   修改更新
        * @param string    $collection_name    集合名称|表名
        * @param array     $query              查询条件array(key=>value)
        * @param array     $update_data        要更新的数据
        * @return bool
        */
        public static function update($collection_name,$query,$update_data,$options=array()){
            self::connect();
            $collection = self::_select_collection($collection_name);
            $result     = $collection->update($query,array('$set'=>$update_data),$options);
            return $result;
        }
        /**
        * 更新所以满足条件的记录
        * @param string $collection_name
        * @param array  $query
        * @param array  $update_data
        * @return boolea
        */
        public static function update_all($collection_name,$query,$update_data){
            self::connect();
            $result     = false;
            $collection = self::_select_collection($collection_name);
            /* $count      = $collection->count($query);
            for ($i = 1;$i<=$count;$i++){
            $result = $collection->update($query,$update_data);
            }*/
            $result = $collection->update($query,array('$set'=>$update_data),array('safe'=>true,'multiple'=>true));
            //self::$_close();
            return $result;
        }
        /**
        * 获取不重复记录
        * @param string $collnections_name    集合名称(相当于关系数据库中的表)
        * @param string  $query                查询的条件array(key=>value) 相当于key=value
        * @param array  $filed                需要列表的字段信息array(filed1,filed2)
        */
        public static function distinct($collnections_name,$filed) {
            self::connect();
            $connnection = self::$db->selectCollection($collnections_name);
            $result      = $connnection->distinct($filed);
            //self::$_close();
            return $result;
        }
        /**
        * 删除记录
        * @param string $collection_name    集合名称(相当于关系数据库中的表)
        * @param array  $query              删除条件
        * @param array  $option             删除的选项详见mongodb开发手册
        * @return unknown
        */
        public static function delete($collection_name,$query,$option=array("justOne"=>false)){
            self::connect();
            $collection = self::_select_collection($collection_name);
            $result     = $collection->remove($query,$option);
            //self::$_close();
            return $result;
        }
        public static function collectionDrop($collection_name) {
            self::connect();
            self::$db->drop($collection_name);
        }
        //__destruct
        public  function __destruct(){
            //null
            self::_close();
        }
        /**
        * 关闭数据库连接
        *
        */
        public  function _close() {
            if(self::$is_close) {
                self::$con->close();
                self::$is_close = true;
            }
        }
        /**
        * 汇总记录
        * @param string $collection_name    集合名称(相当于关系数据库中的表)
        * @param array  $key                要汇总的字段
        * @param array  $initial            初始化汇总值例：{count:0,sum:0}
        * @param MongoCode $reduce          汇总值计算函数，javascript规范的方法，两个参数，一个是当前行，一个包含汇总值的对象。
        * @param array  $query              筛选条件
        * @param array  $option             汇总的选项详见mongodb开发手册
        * @return unknown
        */
        public static function group($collection_name,$key, $initial, $reduce, $option=array()){
            self::connect();
            $collection = self::_select_collection($collection_name);
            $result     = $collection->group($key, $initial, $reduce, $option);
            //self::$_close();
            return $result;
        }
    }
?>