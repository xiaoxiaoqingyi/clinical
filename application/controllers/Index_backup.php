<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {
    private $ajax=0;
    private $u='';
    private $uid=0;
	public function __construct() {        
		parent::__construct();
        date_default_timezone_set('Asia/Shanghai');
        session_id()||session_start();

        $this->ajax=$this->input->is_ajax_request();
        $this->u=isset($_SESSION['u'])?$_SESSION['u']:'';
        $this->uid=isset($_SESSION['uid'])?$_SESSION['uid']:0;
	}

	public function index()
	{
		$this->load->view('index.html',array('u'=>$this->u));
	}
	public function q1()
	{
        $a=$this->I('a',1);
        $a==2?$this->response(200,'Correct ')
        :$this->response(-1,'Error. Please try again',$a);
	}
	public function q2()
	{
        $a=$this->I('a',1);
        $a=='1,3'?$this->response(200,'Correct')
        :$this->response(-1,'Error. Please try again',$a);
	}
	public function q3()
	{
        $a=$this->I('a',1);
        $b=$this->I('b',1);
        $c=$this->I('c',1);
        $d=$this->I('d',1);
        $a==2&&$b==2&&$c==1&&$d==2?$this->response(200,'Correct')
        :$this->response(-1,'Error. Please try again',$a);
	}
	public function q4()
	{
        $a=$this->I('a',1);
        $a?$this->response(200,'ok')
        :$this->response(-1,'Error. Please try again',$a);
	}
	public function q5()
	{
        $a0=$this->I('a0',1);
        $a1=$this->I('a1',1);
        $a2=$this->I('a2',1);
        $a3=$this->I('a3',1);
        $a4=$this->I('a4',1);
        $a5=$this->I('a5',1);
        $a0==2&&$a1==1&&$a2==6&&$a3==5&&$a4==3&&$a5==4?$this->response(200,'Correct')
        :$this->response(-1,'Error. Please try again',$a0);
	}
	public function login()
	{
        $account=$this->I('account',1);
        $password=$this->I('password',1);
        $this->load->database();
        $d=$this->db->select('id,password')->from('u')->where('account',$account)->get()->result()
        or $this->response(-1,'account or password error');
        $d[0]->password==md5($password)||$this->response(-1,'password error');
        $_SESSION['u']=$account;
        $_SESSION['uid']=$d[0]->id;
        $this->response(200,'ok',$account);
	}
	public function logout()
	{
        unset($_SESSION['u']);
        unset($_SESSION['uid']);
		$this->response();
	}
    /**
     * 响应移动端，输出 josn 数据 
     * @param int $status //200：成功， 其它数字是错误， 通常 -1:错误
     * @param string $msg //提示信息
     * @param array $result //数据数组
     * @return string // josn 格式的字符串数据
     */
    public function response($status=200, $msg='ok', $result=array(),$is_ajax=null){
        if(empty($result)){
            $res = array();
        } elseif(is_array($result)&&isset($result[0])) {
            $res = $result;
        } else {
            $res = array();
            $res[] = (array)$result;
        }

        $data = array('status'=>$status,'statusMsg'=>$msg,'result'=>$res);
        if($is_ajax===null?$this->ajax:$is_ajax){
            header('Content-type: application/json');
            die(json_encode($data));
        }
        die($msg);
    }
/*
 * 获取GET,POST data
 * @param int $i是否必须
 * @param string $v默认值
 * @param int $max最大值
 * @param int $min最小值
 * return string
 */
    public function I($k='',$i=0,$v='',$min=null,$max=null)
    {
        $dt=array();
        foreach($_GET as $gk=>$gv){
            $dt[$gk]=$gv;
        }
        foreach($_POST as $gk=>$gv){
            $dt[$gk]=$gv;
        }

        $d=$this->akv($dt,$k,$v);
        $d=$this->inject_check($d);
        $d=$k=='page'?$d>0?$d-0:0:$d;
        $d=$k=='pageSize'?$d>0?$d:10:$d;
        $d=$k?($i?$this->akv($dt,$k,$v)!==''
                    ?$d
                    :$this->response(-1,"$k is not null")
                :$d)
            :$dt;
        $d = is_array($d) ? join(',',$d) :$d;
        if($k&& $max!==null) $d=min($max,$d);
        if($k&& $min!==null) $d=max($min,$d);
        return $d;
    }
    public function akv($a, $k, $v = "") {
        return isset($a[$k]) ? $a[$k] : $v;
    }
    public function inject_check($Sql_Str)
    {
        if(gettype($Sql_Str)=='string'){
            $Sql_Str = str_replace("'","‘",$Sql_Str);
            $Sql_Str = addslashes($Sql_Str);//htmlspecialchars($Sql_Str,ENT_QUOTES);
            $check=preg_match('/\'|\\* |\* |\.\.\/|\.\/|load_file|outfile/i',$Sql_Str);
            if ($check) {
                $this->response(-1,"系统警告：参数中包含非法字符！",null,$this->ajax);
            }
        }elseif(gettype($Sql_Str)=='array' || gettype($Sql_Str)=='object'){
            foreach($Sql_Str as $k=>$v){
                $Sql_Str[$k]=$this->inject_check($v);
            }
        }
        return $Sql_Str;
    }
}
