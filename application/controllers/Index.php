<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class Index extends CI_Controller {
    
    
	public function __construct(){
            
            parent::__construct();
            
            session_start();
            //判断是否登录，否则跳转到登录
            if(!isset($_SESSION['uid'])){
                $this->load->helper("url");
                redirect(base_url().'login');
            }
            
            date_default_timezone_set('Asia/Shanghai');
            $this->load->database();
           
	}

	public function index(){
        
            $this->load->view('learn.html');
	}
        
        public function about(){
            $this->load->view('about.html');
        }
        
        public function about2(){
            $this->load->view('about/about2.html');
        }
        
        public function about3(){
            $this->load->view('about/about3.html');
        }
        
        public function about4(){
            $this->load->view('about/about4.html');
        }
        
        public function head(){
            $this->load->view('html/head.html');
        }
        
        public function stephelp(){
            $this->load->view('html/step-help.html');
        }
        
        public function setpfloor(){
            $this->load->view('html/step-floor.html');
        }
        
        public function recommended(){
            $this->load->view('recommended.html');
        }
        
        public function others(){
            $this->load->view('others.html');
        }
        
        
        public function step(){
            $this->load->view('case/step.html');
        }
        
        public function case1intro(){
             $this->load->view('case/case1introduce.html');
        }
        
        public function clinical(){
             $this->load->view('clinical.html');
        }
        
        public function answer($tid=0){
            if($tid == 0){
                //8个步骤完成
                $this->load->view('recommended.html');             
                return;;
            }
            
            $data=$this->db->select('id, title, option, type, des, next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
            if(isset($data)){
                 $options = explode(";",$data[0]['option']);
                 $data[0]['option'] = $options;

                 if($data[0]['type'] == 0){
                     //介绍页面
                     $this->load->view('topic/introduce.html', $data[0]); 
                 } else if($data[0]['type'] == 1){
                     //单选题
                     $this->load->view('topic/single.html', $data[0]); 
                 }else if($data[0]['type'] == 2){
                     //多选题
                      $this->load->view('topic/multiple.html', $data[0]); 
                 }else if($data[0]['type'] == 3){
                     //判断题
                     $this->load->view('topic/judge.html', $data[0]); 
                 }else if($data[0]['type'] == 4){
                     //连线题
                     $des = explode(";",$data[0]['des']);
                     $data[0]['des'] = $des;
                      $this->load->view('topic/match.html', $data[0]); 
                 }else if($data[0]['type'] == 5){
                     //简答题
                     $this->load->view('topic/short.html', $data[0]); 
                 }else if($data[0]['type'] == 6){
                     //多项判断题
                       $this->load->view('topic/muljudge.html', $data[0]); 
                 }else if($data[0]['type'] == 7){
                     //步骤选择页面
                      $this->load->view('case/step.html');
                 }
                 
                
            } else {
                 $this->load->view('errors/index.html');
            }
            
        }
        
        /**
         * 单选题提交答案
         * @param type $tid
         */
        public function single($tid){
           
            $select = $this->I('single');
            if($select !== ''){
                
                $data=$this->db->select('answer,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
            
        }
        
        /**
         * 多选题
         * @param type $tid
         */
        public function multiple($tid){
           
            $select = implode(",",$_POST);
            
            if($select !== ''){
                
                $data=$this->db->select('answer,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
            
        }
        
         /**
         * 判断题
         * @param type $tid
         */
        public function judge($tid){
            $select = implode(",",$_POST);
            
            if($select !== ''){
                
                $data=$this->db->select('answer,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
        }
        
         /**
         * 多项判断题
         * @param type $tid
         */
        public function muljudge($tid){
            $select = implode(",",$_POST);
            
            if($select !== ''){
                
                $data=$this->db->select('answer,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
        }

          /**
         * 简答题提交答案
         * @param type $tid
         */
        public function short($tid){
           
            $select = $this->I('answer');
            if($select !== ''){
                
                $data=$this->db->select('id,title,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if(!empty($data[0])){
                    $insert= array("tid"=>$data[0]['id'], "uid"=>$this->session->userdata('uid')
                            ,"title"=>$data[0]['title'], "createtime"=>date("Y-m-d H:i:s")
                            ,"answer"=>$select);
                    
                    $this->db->insert("shortquestion", $insert);
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
            
        }
        
          /**
         * 连线题
         * @param type $tid
         */
        public function match($tid){
            $select = implode(",",$_POST);
            
            if($select !== ''){
                
                $data=$this->db->select('answer,next_topic_id')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $res = array("url"=>base_url("/index/clinical/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    $this->response(-1,'try again!');
                }
                
               
            } else {
                $this->response(-1,'please select the answer!');
            }
        }


        /**
         * 获取 POST 指定名称值
         * @param type $k
         * @param type $default
         * @return type
         */
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