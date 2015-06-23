<?php
    /**
    *  预置包后台管理系统
    * @author huangtao
    * @copyright(c) 2013-8-22
    * @version 1.1
    */
    class IndexController extends Yaf_Controller_Abstract{
        const PAGE_ROW = 5;
        const MSG_USERNAME_IS_EMPTY = '用户名不能为空';
        const MSG_USERNAME_EXISTS = '用户名已存在';
        const MSG_PASSWORD_IS_EMPTY = '密码不能为空';
        const MSG_PASSWORD_LENGTH = '密码长度不能小于6位';

        public  function init(){
            $this->need_login_actions = array('index', 'Default', 'add', 'SubmitData', 'AdminManage', 'AdminManageSubmit', 'AdminList', 'AdminUpdate');
            if( in_array( $this->getRequest()->getActionName(), $this->need_login_actions ) ){
                Admin::checkLogin();
            }
            $this->getRequest()->page           =   $this->getRequest()->getParam('page')?$this->getRequest()->getParam('page'):$this->getRequest()->getPost('page');  //分页页码
            $this->getRequest()->page           =   $this->getRequest()->page?(int)$this->getRequest()->page:1;
            $this->getView()->assign("title", "首页");
            $this->getView()->assign("userinfo", Yaf_Registry::get('userinfo'));
            $header             =   $this->getView()->render(PublicFun::fetchCol('header'));
            $nav                =   $this->getView()->render(PublicFun::fetchCol('nav'));
            $foot               =   $this->getView()->render(PublicFun::fetchCol('foot'));
            $menu               =   $this->getView()->render(PublicFun::fetchCol('menu'));
            $this->getView()->assign("menu", $menu);
            $this->getView()->assign("header", $header);
            $this->getView()->assign("nav", $nav);
            $this->getView()->assign("foot", $foot);
        }
        public function indexAction() {
            //echo "index";  
            //print_r($this);
            //var_dump($this->getRequest()->getQuery());
            //var_dump($this->getRequest()->getPost());
            //print_r($this->getRequest()->getParam('de',0)) ;
            //$data =  Bar::defaut();
            //$this->initView();
            //http://localhost:8080/application/public/index/index/index/index/
            //     $this->getView()->assign("data", $data);
        }
        public function DefaultAction() {//默认Action
            //Bar::defaut();
            $this->getView()->assign("content", "Hello W11fsdfsdorld");
        }
        public function addAction(){
            if ($this->getRequest()->isXmlHttpRequest()) {
                Yaf_Dispatcher::getInstance()->disableView();    //ajax请求是 不分配模板
            }
        }
        public function SubmitDataAction(){
            $page                           =   $this->getRequest()->page;
            $where                          =   array();
            $filed                          =   array();
            $limit                          =   ($page-1)*self::PAGE_ROW.','.self::PAGE_ROW;
            $data                           =   MobileInfo::getMobileData($where,$filed,array('z_id'=>-1),$limit);
            #分页开始
            $this->getRequest()->total      =   MobileInfo::getMobileInfoCount($where);
            $this->getRequest()->pageRow    =   self::PAGE_ROW;
            $this->getRequest()->url        =   URL."index/index/index/SubmitData/page/";
            $pageArr                        =   PublicFun::getPage1();
            $this->getView()->assign("title", "查看最新数据");
            $header                         =   $this->getView()->render(PublicFun::fetchCol('header'));   //取得头部公共代码
            $this->getView()->assign("header", $header);
            $this->getView()->assign("pageArr", $pageArr);
            $this->getView()->assign("data", $data);
        }
        public function AdminManageAction(){}

        public function AdminManageSubmitAction(){
            Yaf_Dispatcher::getInstance()->disableView();
            $username       =   $this->getRequest()->getPost('username');
            $password       =   $this->getRequest()->getPost('password');
            $re_password    =   $this->getRequest()->getPost('re_password');
            $validate = new Validate;
            $validate->checkEmpty($username, self::MSG_USERNAME_IS_EMPTY )
                        ->valueExists($username, self::MSG_USERNAME_EXISTS)
                        ->checkEmpty($password, self::MSG_PASSWORD_IS_EMPTY )
                        ->checkLength($password, 6, 0, self::MSG_PASSWORD_LENGTH )
                        ->bothPasswordDifference( $password, $re_password );
            Admin::adminAdd($username, $password);
        }
        public function AdminListAction(){
            $this->getView()->assign("data", Admin::adminList());
        }
        public function AdminUpdateAction(){
            Yaf_Dispatcher::getInstance()->disableView();
            $username   =   $this->getRequest()->getPost('username');
            $password   =   $this->getRequest()->getPost('password');
            $seq        =   $this->getRequest()->getPost('seq');
            $active     =   $this->getRequest()->getPost('active' . $seq);
            $validate = new Validate;
            $validate->checkEmpty($username, self::MSG_USERNAME_IS_EMPTY);
            Admin::adminUpdate( $username, $password, $active );
        }

        public function LoginAction(){}

        public function LoginExecAction(){
            Yaf_Dispatcher::getInstance()->disableView();
            $username   =   $this->getRequest()->getPost('username');
            $password   =   $this->getRequest()->getPost('password');
            Admin::adminLogin( $username, $password );
        }
        public function LogoutAction(){
            Admin::adminLogout();
        }
    }
?>