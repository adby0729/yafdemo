<?php
/**
* 试用商品信息表
* @author huangtao<592805251@qq.com>
* @copyright (c) 2014-1-16
* @version 1.1
*/
class MysqlActiveRecord extends  MysqlAbstract{
    static $table = "weimeng_active_record";
    /**
    * 插入一条试用商品信息
    * @author huangtao
    * @copyright (c) 2013-2-20
    * @version v1.0
    * @param array $data
    * @return boole
    */
    public static function add($data){

        return self::Insert(self::$table,$data);
    }
    /**
    * 查询试用商品信息列表
    * @author huangtao
    * @copyright (c) 2013-2-20
    * @version v1.0
    * @param array $arr
    * @return array
    */
    public static function getData($query=array(),$field="",$limit="",$order=array(),$type="",$like=array()){
        return self::Select(self::$table,$query,$field,$limit,$order,$type,$like);
    }
    /**
    * 查询一条数据详情
    * @author huangtao
    * @copyright (c) 2014-1-16
    * @version v1.0
    * @param mixed $id
    * @return array
    */
    public static function getDataOne($id,$field=""){
        $query = array('id'=>$id);
        return self::Select(self::$table,$query,$field,'',array(),'1');
    }
    /**
    * 修改一条数据
    * @author huangtao
    * @copyright (c) 2014-1-16
    * @version v1.0
    * @param mixed $id
    * @return boole
    */
    public static function edit($id,$param){
        $where=array('id'=>$id);
        return self::Update(self::$table,$param,$where);
    }
    /**
    * 彻底删除试用商品
    * @author huangtao
    * @copyright (c) 2014-1-16
    * @version v1.0
    * @param mixed $id
    * @return boole
    */
    public static function remove($id){
        $query=array('id'=>$id);
        return self::Delete(self::$table,$query);
    }
}