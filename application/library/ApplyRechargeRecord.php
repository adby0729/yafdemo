<?php
    /**
    * 充值申请记录表 
    * @auth huangtao
    * @copyright 2013-08-28
    */
    class ApplyRechargeRecord extends Mg{
        public  static  $collectionName = 'apply_recharge_record';
        /**
        * 取一条数据
        * @param $where   条件  array()
        * @param $filed   字段  array()
        */
        public static function getOne($where,$filed=array()){
            //print_r($where);die();
            $arr                    =       Mg::find_one(self::$collectionName,$where,$filed);
            return $arr;
        }
        /**
        * 取全部数据
        * @param array  $where                查询的条件array(key=>value) 相当于key=value
        * @param array  $filed                需要列表的字段信息array(filed1,filed2)
        * @param array  $sort                 排序 name desc array("name" => -1),name asc array("name" => 1)
        * @param string $limit                "10,20" or 10
        */
        public static function getData($where,$filed=array(),$sort=array(),$limit=''){
            $arr                    =       Mg::find_all(self::$collectionName,$sort,$limit,$where,$filed);
            return $arr;
        }
        /**
        * 查询总数
        *
        * @param mixed $where
        */
        public static function getDataCount($where){
            return Mg::find_count(self::$collectionName,$where);
        }
        /**
        * 添加一条数据 
        *
        * @param mixed $data 数据
        * @return bool
        */
        public static function add($data){
            if(is_array($data)&&!empty($data)) {
                return Mg::insert(self::$collectionName,$data);
            }else{
                trigger_error('MODEL_ADD_ApplyRemitRecord_ERROR!');
            }
        }
        /**
        * 编辑一条数据
        *
        * @param mixed $data  新数据
        * @param mixed $query 条件
        * @return bool
        */
        public static function edit($data,$query){
            if(is_array($data)&&!empty($data)) {
                return Mg::update(self::$collectionName,$query,$data,$options=array("upsert" => false));
            }else{
                trigger_error('MODEL_EDIT_ApplyRemitRecord_ERROR!');
            }
        }
        /**
        * 删除数据
        *
        * @param mixed $query
        * @return bool
        */
        public static function deleteData($query){
            if(is_array($query)&&!empty($query)) {
                return Mg::delete(self::$collectionName,$query);
            }else{
                trigger_error('MODEL_DELETE_ApplyRemitRecord_ERROR!');
            }
        }
    }
?>