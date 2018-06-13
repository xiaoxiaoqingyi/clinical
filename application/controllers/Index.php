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
        
        public function welcome(){
            $this->load->view('welcome.html');
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
        
        public function head(){
             $res['name'] = $_SESSION['username'];  
            $this->load->view('html/head.html',$res);
        }
        
        public function stephelp(){
             //获取个人日志
            $notes=$this->db->select('*')->from('notes')->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(!empty($notes)){
                 $res['notes'] = $notes[0];
            }else{
                 $res['notes'] = '';
            }
             
          
            $this->load->view('html/step-help.html',$res);
        }
        
        public function stepfloor($step=0){
            $res['step'] = $step;
            $this->load->view('html/step-floor.html',$res);
        }
        
        public function recommended(){
            $this->load->view('recommended.html');
        }
        
        public function others(){
            $this->load->view('others.html');
        }
        
         public function stepconclusion(){
             $this->load->view('stepconclusion.html');
        }
        
        public function stepsurvey(){
            $data=$this->db->select('*')->from('survey')->where('uid',$_SESSION['uid'])->get()->result_array();
            if(!empty($data)){
                $this->stepconclusion();
                return;
            }
            $this->load->view('stepsurvey.html');
        }
        
        public function submitsurvey(){
            $select = implode(",",$_POST);
            if(!empty($select)){
                 $insert = array(
                       'uid' => $_SESSION['uid'],
                       'answer' => $select,
                       'createtime' => date('Y-m-d H:i:s')
                       );
                $this->db->insert('survey', $insert);
            }
            
            $this->stepconclusion();
        }
        
        public function step(){
            
            $data=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
            if(empty($data)){
                $res['step'] = 1;
            } else {
                $res['step'] = $data[0]['step'];
            }
            
            $this->load->view('case/step.html', $res);
        }
        
        public function case1intro(){
             $this->load->view('case/case1introduce.html');
        }
        
        public function clinical(){
             $this->load->view('clinical.html');
        }
        
        public function fromstep($step){
            
            $data=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
            if(empty($data)){
                 $this->answer(1);
            } else if($step < $data[0]['step']){
                switch ($step){
                    case 1:
                        $this->answer(1);
                        break;
                    case 2:
                        $this->answer(14);
                        break;
                    case 3:
                         $this->answer(31);
                        break;
                    case 4:
                         $this->answer(49);
                        break;
                    case 5:
                         $this->answer(55);
                        break;
                    case 6:
                         $this->answer(60);
                        break;
                    case 7:
                         $this->answer(67);
                        break;
                    case 8:
                         $this->answer(73);
                        break;
                }
            } else if($step == $data[0]['step'] && $data[0]['sid'] >= 78){
                 $this->answer(73);
            }else {
                 $this->answer($data[0]['sid']);
            }
            
        } 
        
         public function saveNotes(){
            $step1 = $this->I('textarea-1');
            $step2 = $this->I('textarea-2');
            $step3 = $this->I('textarea-3');
            $step4 = $this->I('textarea-4');
            $step5 = $this->I('textarea-5');
            $step6 = $this->I('textarea-6');
            $step7 = $this->I('textarea-7');
            $step8 = $this->I('textarea-8');
            
            $notes=$this->db->select('*')->from('notes')->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(empty($notes)){
                $insert = array(
                    'user_id' => $_SESSION['uid'],
                    'step1' => $step1,
                    'step2' => $step2,
                    'step3' => $step3,
                    'step4' => $step4,
                    'step5' => $step5,
                    'step6' => $step6,
                    'step7' => $step7,
                    'step8' => $step8,
                    'update_time' => date("Y-m-d H:i:s")
                    );
               $this->db->insert('notes', $insert);
            }else{
               $update = array(
                    'step1' => $step1,
                    'step2' => $step2,
                    'step3' => $step3,
                    'step4' => $step4,
                    'step5' => $step5,
                    'step6' => $step6,
                    'step7' => $step7,
                    'step8' => $step8,
                    'update_time' => date("Y-m-d H:i:s")
                    );
                $where = "user_id=".$_SESSION['uid'];
                $this->db->update('notes', $update, $where);
            }
            
           $this->response(200,'ok');
            
         }


        public function answer($tid=0){
            if($tid == 0){
                //8个步骤完成
                $this->stepsurvey();             
                return;;
            }
            
            $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
            $options = explode(";",$data[0]['option']);
            $data[0]['option'] = $options;
            
            $lastdata=$this->db->select('*')->from('subject')->where('next_topic_id',$tid)->get()->result_array();
            if(!empty($lastdata)){
                 $data[0]['last_topic_id'] = $lastdata[0]['id'];
            }
            
           
            //判断该题是否已经答过
            $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
           if(!empty($state)){
               if($data[0]['id'] >=  $state[0]['sid'] && $state[0]['sid'] != 0){
                   unset($data[0]['answer']);
               }else{
                   $data[0]['done'] = 1;
               }
                $data[0]['leftstep'] = $state[0]['step'];
           } else {
               unset($data[0]['answer']);
           }

            if($data[0]['type'] == -1){
                //步骤第一个介绍页面
                $this->load->view('topic/stepinfo.html', $data[0]); 
            } else if($data[0]['type'] == 0){
                //介绍页面
               if(empty($state)){
                  $insert = array(
                       'uid' => $_SESSION['uid'],
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid'] && $state[0]['sid'] != 0){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                   $where = "uid=".$_SESSION['uid'];
                   $this->db->update('answer_state', $update, $where);
               }
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
            }else if($data[0]['type'] == 5 || $data[0]['type'] == 9){
                //简答题
                if(isset($data[0]['done'])){
                    $shortAnswer=$this->db->select('*')->from('shortquestion')->where('tid',$data[0]['id'])
                            ->where('uid',$_SESSION['uid'])->order_by('createtime', 'DESC')->get()->result_array();
           
                    if(!empty($shortAnswer)){
                        $data[0]['answer'] = $shortAnswer;
                    }
                }
                if($data[0]['type'] == 5){
                     $this->load->view('topic/short.html', $data[0]); 
                } else {
                     $this->load->view('topic/short_3answer.html', $data[0]); 
                }
               
            }else if($data[0]['type'] == 6){
                //多项判断题
                  $this->load->view('topic/muljudge.html', $data[0]); 
            }else if($data[0]['type'] == 7){
                //步骤选择页面
               if(empty($state)){
                  $insert = array(
                       'uid' => $_SESSION['uid'],
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid'] && $state[0]['sid'] != 0){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                   $where = "uid=".$_SESSION['uid'];
                   $this->db->update('answer_state', $update, $where);
               }
               if(!empty($state)){
                    $data[0]['step'] = $state[0]['step'];
               }

                $this->load->view('case/step.html', $data[0]);
            }else if($data[0]['type'] == 8){
                if(empty($state)){
                  $insert = array(
                       'uid' => $_SESSION['uid'],
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid'] && $state[0]['sid'] != 0){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                   $where = "uid=".$_SESSION['uid'];
                   $this->db->update('answer_state', $update, $where);
               }
                 $this->load->view('topic/stepnext.html', $data[0]);
            }
                 
           
            
        }
        
        /**
         * 单选题提交答案
         * @param type $tid
         */
        public function single($tid){
           
            $select = $this->I('single');
            if($select !== ''){
                
               
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                    
                    $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step']
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
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
                
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     
                      $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    
                    $res['answer'] = $data[0]['answer'];
                    $this->response(-1,'try again!',$res);
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
                
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                     $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                   
                    
                   $res['answer'] = $data[0]['answer'];
                    $this->response(-1,'try again!',$res);
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
                
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                    
                      $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
                     $this->response(200,'ok',$res);
                } else {
                    
                  
                    $res['answer'] = $data[0]['answer'];
                    $this->response(-1,'try again!',$res);
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
           
            $answer = $this->I('answer');
            $answer2 = $this->I('answer2');
            $answer3 = $this->I('answer3');
            if($answer !== ''){
                
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if(!empty($data[0])){
                    $insert= array("tid"=>$data[0]['id'], "uid"=>$_SESSION['uid']
                        ,"title"=>$data[0]['title'], "createtime"=>date("Y-m-d H:i:s")
                        ,"answer"=>$answer
                        ,"answer2"=>$answer2
                        ,"answer3"=>$answer3
                            );

                     $this->db->insert("shortquestion", $insert);
                     
                     
                       $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     
                   
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
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
                
                $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
                if($data[0]['answer'] === $select){
                    
                     $state=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else{
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = "uid=".$_SESSION['uid'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['next_topic_id']));
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