<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class Login extends CI_Controller {
    
    
	public function __construct(){
            
            parent::__construct();
//            session_start();
	}

	public function index(){
       
            $this->load->view('login.html');
	}
        
        public function check(){
           
            $account=$this->I('account');
            $password=$this->I('password');
            
            $this->load->database();
            $d=$this->db->select('id,password')->from('u')->where('account',$account)->get()->result()
                or $this->response(-1,'account or password error');
            
            $d[0]->password==md5($password)||$this->response(-1,'account or password error');
            
            
            $newdata = array(
                'username'  => $account,
                'uid'     => $d[0]->id
            );
//            $this->load->library('session');
//            $this->session->set_userdata($newdata);
            $_SESSION['username'] = $account;
            $_SESSION['uid'] = $d[0]->id;

            $this->load->helper("url");
//             header("location:".base_url("/index"));
            $res = array("url"=>base_url("/index/index"));
            
            $this->response(200,'ok',$res);
	}
        
	public function logout(){
//             $this->load->library('session');
//            $this->session->unset_userdata('username');
//            $this->session->unset_userdata('uid');
            unset($_SESSION['username']);
            unset($_SESSION['uid']);

            $this->load->helper("url");
            redirect(base_url().'login');
//            $this->response();
	}
        
          public function I($k='', $default=''){
            $value = $this->input->post($k);
            if(isset($value)){
                $d=$this->inject_check($value);
                return $d;
            }
            return $default;
           
        }
        
         /**
     * 响应移动端，输出 josn 数据 
     * @param int $status //200：成功， 其它数字是错误， 通常 -1:错误
     * @param string $msg //提示信息
     * @param array $result //数据数组
     * @return string // josn 格式的字符串数据
     */
    public function response($status=200, $msg='ok', $result=array()){
        if(empty($result)){
            $res = array();
        } elseif(is_array($result)&&isset($result[0])) {
            $res = $result;
        } else {
            $res = $result;
//             $res[] = (array)$result;
        }

        $data = array('status'=>$status,'statusMsg'=>$msg,'res'=>$res);
        header('Content-type: application/json');
        die(json_encode($data));
      
    }
        
        /**
         * 防SQL注入
         * @param type $Sql_Str
         * @return type
         */
        public function inject_check($Sql_Str){
            if(gettype($Sql_Str)=='string'){
                $Sql_Str = str_replace("'","‘",$Sql_Str);
                $Sql_Str = addslashes($Sql_Str);//htmlspecialchars($Sql_Str,ENT_QUOTES);
                $check=preg_match('/\'|\\* |\* |\.\.\/|\.\/|load_file|outfile/i',$Sql_Str);
                if ($check) {
                    $this->response(-1,"系统警告：参数中包含非法字符！",null);
                }
            }elseif(gettype($Sql_Str)=='array' || gettype($Sql_Str)=='object'){
                foreach($Sql_Str as $k=>$v){
                    $Sql_Str[$k]=$this->inject_check($v);
                }
            }
            return $Sql_Str;
        }
        
}