<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class Index extends CI_Controller {
    
    
	public function __construct(){
            
            parent::__construct();
            
//            session_start();
            //判断是否登录，否则跳转到登录
            if(!isset($_SESSION['uid'])){
                $this->load->helper("url");
                redirect(base_url().'login');
            }
            
//            if(!isset($_SERVER['cn'])){
//                 redirect(base_url().'error');
//            }
            
            date_default_timezone_set('Asia/Shanghai');
            $this->load->database();
//            $this->updateLoginTime($_SERVER['cn']);
           
	}
        
        
       
       public  function error(){
           $this->load->view('errors/index.html');
       }


       public function updateLoginTime($username){
           
            $data=$this->db->select('*')->from('user')->where('account', $username)->get()->result_array();
            
             
            if(empty($data)){
                 $insert = array(
                       'account' => $username,
                       'last_login_time' => date('Y-m-d H:i:s')
                       );
                $this->db->insert('user', $insert);
                
               $uid = $this->db->insert_id();
            } else {
                $update = array(
                    'last_login_time' => date("Y-m-d H:i:s")
                    );
                $where = array();
                $where['account'] = $username;
                $this->db->update('user', $update, $where);
                $uid = $data[0]['id'];
            }
           
         
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['uid'] = $uid;
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
        
        public function stephelp1($case){
             //获取个人日志
            $notes=$this->db->select('*')->from('notes')->where('case',$case)->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(!empty($notes)){
                 $res['notes'] = $notes[0];
            }else{
                 $res['notes'] = '';
            }
            $this->load->view('html/step-help-case1.html',$res);
        }
        public function stephelp2($case){
             //获取个人日志
            $notes=$this->db->select('*')->from('notes')->where('case',$case)->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(!empty($notes)){
                 $res['notes'] = $notes[0];
            }else{
                 $res['notes'] = '';
            }
          
            $this->load->view('html/step-help-case2.html',$res);
        }
        public function stephelp3($case){
             //获取个人日志
            $notes=$this->db->select('*')->from('notes')->where('case',$case)->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(!empty($notes)){
                 $res['notes'] = $notes[0];
            }else{
                 $res['notes'] = '';
            }
          
            $this->load->view('html/step-help-case3.html',$res);
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
        
         public function stepconclusion($case){
             $res = array();
             $res['case'] = $case;
             if($case == 1){
                $res['last_id'] = 78;
             }else if($case == 2){
                  $res['last_id'] = 173;
             }else if($case == 3){
                  $res['last_id'] = 285;
             }
             $this->load->view('stepconclusion.html',$res);
        }
         
        public function case1intro(){
             $this->load->view('case/case1introduce.html');
        }
        
        public function case2intro1(){
             $this->load->view('case/case2intro1.html');
        }
         public function case2intro2(){
             $this->load->view('case/case2intro2.html');
        }
        
         public function case3intro1(){
             $this->load->view('case/case3intro1.html');
        }
         public function case3intro2(){
             $this->load->view('case/case3intro2.html');
        }
         public function case3intro3(){
             $this->load->view('case/case3intro3.html');
        }
        
        public function clinical(){
             $this->load->view('clinical.html');
        }
        
        public function stepsurvey($case){
            $data=$this->db->select('*')->from('survey')->where('case',$case)
                    ->where('uid',$_SESSION['uid'])->get()->result_array();
            if(!empty($data)){
//                $this->stepconclusion($case);
            $this->clinical();
                return;
            }
            $res = array();
            $res['case'] = $case;
            $this->load->view('stepsurvey.html', $res);
        }
        
        public function getSurveyTopic(){
            $res = array();
            $res[0]= "he interactive platform maintained by interest in learning clinical reasoning skills";
            $res[1]= "The interactive platform helped me to develop plans for nursing therapeutic interventions";
            $res[2]= "The interactive platform helped me to recognize the gaps of my knowledge";
            $res[3]= "The interactive platform helped me to develop ability to solve problems";
            $res[4]= "The interactive platform helped me to develop clinical reasoning ability";
            $res[5]= "The interactive platform helped me to develop decision making ability";
            $res[6]= "I will be able to related the VP scenario to apply to my future practice.";
            $res[7]= "The instructions of the clinical scenarios were clear";
            $res[8]= "My background knowledge was sufficient for understanding the clinical scenarios";
            $res[9]= "The interactive platform is relevant to prepare my clinical practicum";
            $res[10]= "As a result of practicing clinical reasoning skills via the platform, I can see clearly how clinical reasoning relates to becoming a registered nurse";
            $res[11]= "I was able to access technical help when needed";
            $res[12]= "The process of using the platform, has motivated me to learn";
            $res[13]= "This was a valuable learning experience";
            return $res;
        }

        public function submitsurvey($case){
            
            $req = array();
            $req[0] = $_POST['radio-0'];
            $req[1] = $_POST['radio-1'];
            $req[2] = $_POST['radio-2'];
            $req[3] = $_POST['radio-3'];
            $req[4] = $_POST['radio-4'];
            $req[5] = $_POST['radio-5'];
            $req[6] = $_POST['radio-6'];
            $req[7] = $_POST['radio-7'];
            $req[8] = $_POST['radio-8'];
            $req[9] = $_POST['radio-9'];
            $req[10] = $_POST['radio-10'];
            $req[11] = $_POST['radio-11'];
            $req[12] = $_POST['radio-12'];
            $req[13] = $_POST['radio-13'];
            
           $select = implode(",", $req);
            $suggest1 = $_POST['suggest1'];
            $suggest2 = $_POST['suggest2'];

            $insert = array(
                  'uid' => $_SESSION['uid'],
                  'answer' => $select,
                 'short1' => $suggest1,
                 'short2' => $suggest2,
                  'case' => $case,
                  'createtime' => date('Y-m-d H:i:s')
                  );
           $this->db->insert('survey', $insert);
           
           $topicList = $this->getSurveyTopic();
           foreach ($topicList as $key=>$value){
                if($req[$key] == 0){
                    $ans = 'Very agree';
                }else if($req[$key] == 1){
                     $ans = 'Agree';
                }else if($req[$key] == 2){
                      $ans = 'Disagree';
                }else if($req[$key] == 3){
                     $ans = 'Very disagree';
                }
                    
               
                 $log_insert= array(
                    "user_id"=>$_SESSION['uid'],
                    "username"=>$_SESSION['username'],
                    "case"=>$case,
                    "topic_id"=>($key+1),
                    "topic_type"=>10, 
                    "topic_title"=>$value,
                    "topic_answer"=>'',
                    "user_answer"=>$ans,
                    "createtime"=>date("Y-m-d H:i:s")
                        );

                $this->db->insert("answer_log", $log_insert);
           }
           
           $short2="Is there anything about the learning experience regarding using the "
                   . "platform of virtual patient based, interactive computerized platform for"
                   . " students practicing clinical reasoning and decision-making skills that"
                   . " you are particularly satisfied or dissatisfied with but not mentioned above?"
                   . " Please explain if there is anything as such.";
           $short3 = "Any other comments / suggestions:";
           
           $log_insert2= array(
                    "user_id"=>$_SESSION['uid'],
                    "username"=>$_SESSION['username'],
                    "case"=>$case,
                    "topic_id"=>15,
                    "topic_type"=>10, 
                    "topic_title"=>$short2,
                    "topic_answer"=>'',
                    "user_answer"=>$suggest1,
                    "createtime"=>date("Y-m-d H:i:s")
                        );

            $this->db->insert("answer_log", $log_insert2);
            
             $log_insert3= array(
                    "user_id"=>$_SESSION['uid'],
                    "username"=>$_SESSION['username'],
                    "case"=>$case,
                    "topic_id"=>16,
                    "topic_type"=>10, 
                    "topic_title"=>$short3,
                    "topic_answer"=>'',
                    "user_answer"=>$suggest2,
                    "createtime"=>date("Y-m-d H:i:s")
                        );

            $this->db->insert("answer_log", $log_insert3);
           
          
            $res = array("url"=>base_url('/index/clinical'));
            $this->response(200,'ok',$res);
//            $this->clinical();
        }
        
        public function step($case){
            
            $data=$this->db->select('*')->from('answer_state')->where('case',$case)
                    ->where('uid',$_SESSION['uid'])->get()->result_array();
            if(empty($data)){
                $res['step'] = 1;
            } else {
                $res['step'] = $data[0]['step'];
            }
            $res['case'] = $case;
            
            $this->load->view('case/step.html', $res);
        }
       
        
        public function fromstep($case, $step){
            
            $data=$this->db->select('*')->from('answer_state')->where('uid',$_SESSION['uid'])
                    ->where('case',$case)->get()->result_array();
            
            if(empty($data)){
                switch ($case){
                    case 1:
                        $this->answer(1, 1);
                        break;
                     case 2:
                        $this->answer(2, 101);
                        break;
                    case 3:
                        $this->answer(3, 201);
                        break;
                }
                
            } else if($step < $data[0]['step']){
                if($case == 1){
                    switch ($step){
                        case 1:
                            $this->answer($case,1);
                            break;
                        case 2:
                            $this->answer($case,14);
                            break;
                        case 3:
                             $this->answer($case,31);
                            break;
                        case 4:
                             $this->answer($case,49);
                            break;
                        case 5:
                             $this->answer($case,55);
                            break;
                        case 6:
                             $this->answer($case,60);
                            break;
                        case 7:
                             $this->answer($case,67);
                            break;
                        case 8:
                             $this->answer($case,73);
                            break;
                    }
                }else if($case == 2){
                    
                    switch ($step){
                        case 1:
                            $this->answer($case,101);
                            break;
                        case 2:
                            $this->answer($case,114);
                            break;
                        case 3:
                             $this->answer($case,131);
                            break;
                        case 4:
                             $this->answer($case,147);
                            break;
                        case 5:
                             $this->answer($case,152);
                            break;
                        case 6:
                             $this->answer($case,157);
                            break;
                        case 7:
                             $this->answer($case,162);
                            break;
                        case 8:
                             $this->answer($case,168);
                            break;
                    }
                    
                }else if($case == 3){
                    
                    switch ($step){
                        case 1:
                            $this->answer($case,201);
                            break;
                        case 2:
                            $this->answer($case,214);
                            break;
                        case 3:
                             $this->answer($case,242);
                            break;
                        case 4:
                             $this->answer($case,258);
                            break;
                        case 5:
                             $this->answer($case,263);
                            break;
                        case 6:
                             $this->answer($case,368);
                            break;
                        case 7:
                             $this->answer($case,273);
                            break;
                        case 8:
                             $this->answer($case,280);
                            break;
                    }
                    
                }
                
            } else if($step == $data[0]['step']){
                
                if($step == 8){
                    switch ($case){
                        case 1:
                            if($data[0]['sid'] == 78){
                                $this->answer($case, 73);
                                return;
                            }
                            break;
                        case 2:
                            if($data[0]['sid'] == 173){
                                $this->answer($case, 168);
                                return;
                            }
                            break;
                        case 3:
                             if($data[0]['sid'] == 285){
                                $this->answer($case, 280);
                                return;
                            }
                            break;
                    }
                }
                
                 $this->answer($case,$data[0]['sid']);
                
            }
            
        } 
        
         public function saveNotes($case){
            $step1 = $this->I('textarea-1');
            $step2 = $this->I('textarea-2');
            $step3 = $this->I('textarea-3');
            $step4 = $this->I('textarea-4');
            $step5 = $this->I('textarea-5');
            $step6 = $this->I('textarea-6');
            $step7 = $this->I('textarea-7');
            $step8 = $this->I('textarea-8');
            
            $notes=$this->db->select('*')->from('notes')->where('case',$case)->where('user_id',$_SESSION['uid'])->get()->result_array();
            if(empty($notes)){
                $insert = array(
                    'user_id' => $_SESSION['uid'],
                    'case' => $case,
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
                $where = array("user_id"=>$_SESSION['uid'], "case"=>$case);
                $this->db->update('notes', $update, $where);
            }
            
           $this->response(200,'ok');
            
         }


        public function answer($case, $tid=0){
            if($tid == 0){
                //8个步骤完成
//                $this->stepsurvey($case);  
                  $this->stepconclusion($case);
                return;;
            }
            
            $data=$this->db->select('*')->from('subject')->where('id',$tid)->get()->result_array();
            $options = explode(";",$data[0]['option']);
            $data[0]['option'] = $options;
            $data[0]['case'] = $case;
            
            $lastdata=$this->db->select('*')->from('subject')->where('next_topic_id',$tid)->get()->result_array();
            if(!empty($lastdata)){
                 $data[0]['last_topic_id'] = $lastdata[0]['id'];
            }
            
           
            //判断该题是否已经答过
            $state=$this->db->select('*')->from('answer_state') ->where('case', $case)
                    ->where('uid',$_SESSION['uid'])->get()->result_array();
           if(!empty($state)){
               if($data[0]['id'] >=  $state[0]['sid'] && $state[0]['sid'] != 0){
                   unset($data[0]['answer']);
               }else{
                   $data[0]['done'] = 1;
               }
                $data[0]['leftstep'] = $state[0]['step'];
           } else {
               unset($data[0]['answer']);
                $data[0]['leftstep'] = 1;
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
                       'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid'] && $state[0]['sid'] != 0){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                       'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                    $where = array();
                    $where['uid'] = $_SESSION['uid'];
                    $where['case'] = $data[0]['case'];
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
                    $shortAnswer=$this->db->select('*')->from('answer_log')->where('topic_id',$data[0]['id'])
                            ->where('user_id',$_SESSION['uid'])->order_by('createtime', 'DESC')->get()->result_array();
           
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
                       'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid'] && $state[0]['sid'] != 0){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                        'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                    $where = array();
                    $where['uid'] = $_SESSION['uid'];
                    $where['case'] = $data[0]['case'];
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
                      'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                  $this->db->insert('answer_state', $insert);
               } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                    $update = array(
                       'sid' => $data[0]['next_topic_id'],
                       'step' => $data[0]['step'],
                        'case' => $data[0]['case'],
                       'createtime' => date("Y-m-d H:i:s")
                       );
                    
                   $where = array();
                   $where['uid'] = $_SESSION['uid'];
                   $where['case'] = $data[0]['case'];
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
                
                // insert log
                if(!empty($data)){
                      $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$select,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                }
                
                if($data[0]['answer'] === $select){
                    
                    $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                            ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step']
                            );
                        $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                    
                     $res = array("url"=>base_url('/index/answer/'.$data[0]['case'].'/'.$data[0]['next_topic_id']));
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
                
                // insert log
                if(!empty($data)){
                      $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$select,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                }
                
                if($data[0]['answer'] === $select){
                     
                      $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                              ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                        $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['case'].'/'.$data[0]['next_topic_id']));
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
                
                // insert log
                if(!empty($data)){
                      $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$select,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                }
                
                if($data[0]['answer'] === $select){
                     $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                             ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                         $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['case'].'/'.$data[0]['next_topic_id']));
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
                
                // insert log
                if(!empty($data)){
                      $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$select,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                }
                
                if($data[0]['answer'] === $select){
                    
                      $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                              ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    }else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                         $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['case'].'/'.$data[0]['next_topic_id']));
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
                    $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$answer,
                        "short_answer2"=>$answer2,
                        "short_answer3"=>$answer3,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                     
                     
                       $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                               ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                         $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     
                   
                     $res = array("url"=>base_url("/index/answer/".$data[0]['case'].'/'.$data[0]['next_topic_id']));
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
                
                // insert log
                if(!empty($data)){
                      $insert= array(
                        "user_id"=>$_SESSION['uid'],
                        "username"=>$_SESSION['username'],
                        "case"=>$data[0]['case'],
                        "topic_id"=>$data[0]['id'],
                        "topic_type"=>$data[0]['type'], 
                        "topic_title"=>$data[0]['title'].$data[0]['des'].$data[0]['option'],
                        "topic_answer"=>$data[0]['answer'],
                        "user_answer"=>$select,
                        "createtime"=>date("Y-m-d H:i:s")
                            );

                     $this->db->insert("answer_log", $insert);
                }
                
                if($data[0]['answer'] === $select){
                    
                     $state=$this->db->select('*')->from('answer_state')->where('case', $data[0]['case'])
                             ->where('uid',$_SESSION['uid'])->get()->result_array();
                    if(empty($state)){
                       $insert = array(
                            'uid' => $_SESSION['uid'],
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'case' => $data[0]['case'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                       $this->db->insert('answer_state', $insert);
                    } else if($data[0]['next_topic_id'] >  $state[0]['sid']){
                         $update = array(
                            'sid' => $data[0]['next_topic_id'],
                            'step' => $data[0]['step'],
                            'createtime' => date("Y-m-d H:i:s")
                            );
                         $where = array();
                        $where['uid'] = $_SESSION['uid'];
                        $where['case'] = $data[0]['case'];
                        $this->db->update('answer_state', $update, $where);
                    }
                    
                     $res = array("url"=>base_url("/index/answer/".$data[0]['case'].'/'.$data[0]['next_topic_id']));
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