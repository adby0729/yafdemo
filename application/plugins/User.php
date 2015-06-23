<?php
    class UserPlugin  extends Yaf_Plugin_Abstract {
        /**
        * 在路由之前触发
        *
        * @param Yaf_Request_Abstract $request
        * @param Yaf_Response_Abstract $response
        */
        public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
            // echo 'routerStartup';
        }
        /**
        * 路由结束之后触发
        * 权限验证
        * @param Yaf_Request_Abstract $request
        * @param Yaf_Response_Abstract $response
        */
        public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
            //$role =  UserInfo::getUserOne(array('z_id'=>(int)$_COOKIE['user_id']),array('role'));
            //$res =  Acl::checkAccess($role,$request->controller,$request->action);
            //var_dump($res);
           /*
            if(!$res){
                PublicFun::toErrorBack('‘权限不够！');
            }                                                      */
        }
        /*
        public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        echo 'dispatchLoopStartup';
        }
        public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        echo 'preDispatch';
        }
        public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        echo 'postDispatch';
        }
        public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        echo 'dispatchLoopShutdown';
        } */
    }
?>