<?php
/**
* 
* @author fenghaikuan
* @copyright (c) 2012-2-19
* @version v1.0
*/

class MysqlAbstract extends DbMysql{ 
    public static function init(){ 
        self::connect(); 
    } 
    /*public function __call(){
        self::connect(); 
    } */ 
    /**
    * 添加
    * @param   string $table
    * @param   array  $param
    * @return  array $return 
    */
    public static function Insert($table,$param){
        self::init();
        if(NULL==$table || empty($param) || !is_array($param)){return false;}
        $i = '';
        foreach($param as $key=>$value){
            if(!$i){
                $str = $key."='".$value."'";
            }else{
                $str .= ",".$key."='".$value."'";  
            }
            $i++;
        }
        $sql = "INSERT INTO ".$table."
        SET $str"; 
        //echo $sql;die;
        return self::query($sql);
    }
    /**
    * 修改
    * @param  string $table
    * @param  array  $param
    * @param  array  $where
    * @return boolean
    */
    public static function Update($table,$param,$where){
        self::init();
        if(NULL==$table || empty($param)|| empty($where) || !is_array($param) || !is_array($where)){return false;}
        $i = 0;
        $m = 0;
        foreach($param as $key=>$value){
            if(!$i){
                $str = $key."='".$value."'";
            }else{
                $str .= ",".$key."='".$value."'";  
            }
            $i++;
        }
        foreach($where as $k=>$v){
            if(!$m){
                $Where = $k."='".$v."'";
            }else{
                $Where .= " AND ".$k."='".$v."'";  
            }
            $m++;
        }
        $sql = "UPDATE ".$table." 
        SET  
        $str 
        WHERE $Where";
        //  echo $sql;die; 
        return  self::query($sql);
    }
    /**
    * 查询
    * @param string $table = "table_name"
    * @param array $query  = array('id'=>1)
    * @param string $field = " * " or " id , name"
    * @param array $order = array('id'=>'desc')
    * @param string $limit = '1,10';
    * @return
    */
    public static function Select($table,$query=array(),$field="",$limit='',$order=array(),$type="",$like=array()){
        self::init();
        if(NULL==$table  || !is_array($query)){return false;}
        $Q = '';
        $orderBy = '';
        $i = 0; 
        if(!empty($query)){
            foreach($query as $key=>$value){
                if(!$i){
                    $Q = " ".$key."='".$value."'";
                }else{
                    $Q.= " AND ".$key."='".$value."'";
                }
                $i++;
            }
            $Q = " WHERE ".$Q;   
        }
        $i = 0;
        if(!empty($order)){
            foreach($order as $key=>$value){
                if(!$i){
                    $orderBy = " ORDER BY ".$key." ".$value;
                }else{
                    $orderBy .= " , ".$key." ".$value;
                }
                $i++;
            } 
        }
        $i = 0;
        if(!empty($like)){ //模糊查找  
            foreach($like as $key=>$value){
                if(!$i){
                    $Q = " ".$key." like '%".$value."%'"; //覆盖前面赋值 
                }else{
                    $Q.= " AND ".$key." like '%".$value."%'";
                }
                $i++;
            }
            $Q = " WHERE ".$Q;  
            unset($i);
        }
        $field = $field?$field:" * ";
        $limit = $limit?' limit '.$limit:'';
        $sql = "SELECT $field FROM ".$table.$Q.$orderBy."  ".$limit;
        $method = $type?"getRow":"getAll";
        //echo $sql;die;
        return self::$method($sql);
    }
    /**
    * 删除
    * @param string $table
    * @param array  $query 
    * @return boolean
    */
    public static function Delete($table,$query,$limit=0){
        self::init();
        $i = 0; 
        if($table==NULL ||!is_array($query) || empty($query)){return false;}
        $limit = $limit?"LIMIT 0,{$limit}":"LIMIT 1";
        foreach($query as $key=>$value){
            if(!$i){
                $str = $key."='".$value."'";
            }else{
                $str .= " AND ".$key."='".$value."'";
            }
            $i++; 
        }

        $sql = "DELETE FROM ".$table."
        WHERE  
        {$str}  {$limit}";
        //echo $sql;
        return  self::query($sql);
    }
    public static function Exe($sql,$type=0){
        self::init();
        $method = $type ? "getRow":"getAll";
        $data = self::$method($sql); 
        return $data;
    }
    public static function Exe1($sql){
        self::init();
        self::query($sql);
    }
}
