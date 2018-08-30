<?php 
defined('BASEPATH') OR exit('No direct script access allowed');


class Admin extends CI_Controller {
    
    
	public function __construct(){
            
            parent::__construct();
             $this->load->database();
	}

	public function index($case=1, $page=1, $key=''){
            
            if($key != ''){
               $search = $key; 
            } else {
               $search = $this->input->post('key');
            }
            
            $limit_start = ($page -1)*10;
            
            if(!empty($search)){
                 $data=$this->db->select('u.*, answer_state.*')->from('answer_state')->join('user as u', 'u.id = answer_state.uid', 'left')
                    ->where('case',$case)->like('u.account', $search)->order_by('uid', 'ASC')->limit(10, 0)->get()->result_array();
                 $page = 1;
                 $count=$this->db->select('u.*, answer_state.*')->from('answer_state')->join('user as u', 'u.id = answer_state.uid', 'left')
                         ->where('case',$case)->like('u.account', $search)->count_all_results();
            } else{
                 $data=$this->db->select('user.*, answer_state.*')->from('answer_state')->join('user', 'user.id = answer_state.uid', 'left')
                    ->where('case',$case)->order_by('uid', 'ASC')->limit(10, $limit_start)->get()->result_array();
                   $count=$this->db->select('*')->from('answer_state')->where('case',$case)->count_all_results();
            }
           
            
            if(empty($data)){
                $res = array();
                $res['case'] = $case;
                $res['page'] = $page;
                $res['search'] = empty($search)?'':$search;
                $res['data'] = array();
                $res['totalPage'] = 0;
       
                $this->load->view('admin/home.html', $res);
               
            } else{
                 $newArr = array();
            foreach ($data as $key => $value){
                unset($value['password']);
                
                $acRate = false;
                switch ($case){
                    case 1:
                        if($value['sid'] == 78){
                             $value['com_rate'] = '100%';
                             $acRate = true;
                        }else{
                             $value['com_rate'] = ceil(($value['sid']%100)/78*100).'%';
                        }
                    
                        break;
                     case 2:
                        if($value['sid'] == 173){
                            $value['com_rate'] = '100%';
                            $acRate = true;
                        }else{
                             $value['com_rate'] = ceil(($value['sid']%100)/73*100).'%';
                        }
                      
                        break;
                    case 3:
                        
                        if($value['sid'] == 285){
                            $value['com_rate'] = '100%';
                            $acRate = true;
                        }else{
                             $value['com_rate'] = ceil(($value['sid']%100)/85*100).'%';
                        }
                       
                        break;
                }
                
                $sflag = false;
                $survey=$this->db->select('*')->from('survey')->where('case',$case)
                    ->where('uid',$value['uid'])->get()->result_array();
                if(!empty($survey)){
                   $value['survey'] = 'COMPLETED';
                     $sflag = true;
                } else{
                    $value['survey'] = 'NOT COMPLETED';
                }
                
                if($acRate && $sflag){
                    $value['finsih'] = 1;
                } else {
                    $value['finsih'] = 0;
                }
                
                $newArr[$key] = $value;
                
                }
          
                if($count%10 > 0){
                    $totalPage = $count/10 + 1;
                } else{
                    $totalPage = $count/10;
                }

                $res = array();
                $res['case'] = $case;
                $res['page'] = $page;
                $res['search'] = empty($search)?'':$search;
                $res['data'] = $newArr;
                $res['totalPage'] = $totalPage;

                $this->load->view('admin/home.html', $res);
            }
            
            
           
	}
        
        //delete log
        public function deleteOne($case, $page, $uid, $search=''){
            $this->db->delete('answer_state', array('uid' => $uid, 'case' => $case));
            $this->db->delete('answer_log', array('user_id' => $uid, 'case' => $case));
            $this->db->delete('survey', array('uid' => $uid, 'case' => $case));
            
            $this->index($case, $page, $search);
        }
        
        //delete log
        public function deleteMul($case, $page, $uids){
            
            $uids = urldecode($uids);
            
            $sql = "delete from answer_log where answer_log.case=".$case." and user_id in(".$uids.")";
            $this->db->query($sql);
            
            $sql2 = "delete from answer_state where answer_state.case=".$case." and uid in(".$uids.")";
            $this->db->query($sql2);
            
            $sql3 = "delete from survey where survey.case=".$case." and uid in(".$uids.")";
            $this->db->query($sql3);
            
            
            $this->index($case, $page, '');
        }
         //delete log
        public function deleteAll($case){
            $this->db->delete('answer_state');
            $this->db->delete('answer_log');
            $this->db->delete('survey');
            
            $this->index($case, 1, '');
        }
        
        
        public function downloadOne($case, $uid){
            header('Content-Type: application/vnd.ms-excel;');          
            header('Content-Disposition: attachment;filename="case'.$case.'_log.xls"');
            header('Cache-Control: max-age=0'); 
            
            echo "<table width='500' border='1' cellspacing='2' cellpadding='2'>
                <tr align='center'  style='background-color:#E5E8ED'>
                    <td >netID</td>
                    <td >case</td>
                    <td >题目ID</td>
                    <td >题目类型</td>
                    <td >题目</td>
                    <td >题目答案</td>
                    <td >用户提交答案</td>
                    <td> 简答题答案2</td>
                    <td>简答题答案3 </td>
                    <td>提交时间 </td>
                </tr> ";
             
             
            $data=$this->db->select('*')->from('answer_log')->where('case',$case)->where('user_id',$uid)
                ->order_by('user_id', 'DESC')->order_by('topic_id', 'ASC')->get()->result_array();

            //填充表格信息
           foreach($data as $k=>$v){ 

               switch ($v['topic_type']){
                   case 1:
                       $v['topic_type'] = 'single choice';
                       
                        $v['topic_answer'] = $this->convert( $v['topic_answer']);
                        $v['user_answer'] = $this->convert( $v['user_answer']);
                       
                       break;
                   case 2:
                       $v['topic_type'] = 'multiple choice';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       
                       break;
                   case 3:
                       $v['topic_type'] = 'True or false';
                       break;
                   case 4:
                       $v['topic_type'] = 'Match';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       break;
                   case 5:
                       $v['topic_type'] = 'short answer';
                       break;
                   case 6:
                        $v['topic_type'] = 'multiple True or false';
                       break;
                    case 9:
                        $v['topic_type'] = 'three short answer';
                       break;
                   case 10:
                       $v['topic_type'] = 'survey';
                       break;
               } 
               
             echo  "<tr align='center'  style='background-color:#E5E8ED'>
                   <td>".$v['username']."</td>
                   <td>".$v['case']."</td>
                   <td>".$v['topic_id']."</td>
                   <td>".$v['topic_type']."</td>
                   <td>".$v['topic_title']."</td>
                   <td>".$v['topic_answer']."</td>
                   <td>".$v['user_answer']."</td>
                   <td> ".$v['short_answer2']."</td>
                   <td>".$v['short_answer3']." </td>
                   <td>".$v['createtime']." </td>
                </tr>";

           }

            echo "</table>";
           exit; 

                    
        }
        
         public function downloadMul($case, $uids){
             
             $uids = urldecode($uids);
             
            header('Content-Type: application/vnd.ms-excel;');          
            header('Content-Disposition: attachment;filename="case'.$case.'_log.xls"');
            header('Cache-Control: max-age=0'); 
            
            echo "<table width='500' border='1' cellspacing='2' cellpadding='2'>
                <tr align='center'  style='background-color:#E5E8ED'>
                    <td >netID</td>
                    <td >case</td>
                    <td >题目ID</td>
                    <td >题目类型</td>
                    <td >题目</td>
                    <td >题目答案</td>
                    <td >用户提交答案</td>
                    <td> 简答题答案2</td>
                    <td>简答题答案3 </td>
                    <td>提交时间 </td>
                </tr> ";
             
              
//            $this->db->query("select * from answer_log where ");
            $where = "user_id in (".$uids.")";
            $data=$this->db->select('*')->from('answer_log')->where('case',$case)->where($where)
                ->order_by('user_id', 'DESC')->order_by('topic_id', 'ASC')->get()->result_array();

            //填充表格信息
           foreach($data as $k=>$v){ 
              
             switch ($v['topic_type']){
                   case 1:
                       $v['topic_type'] = 'single choice';
                       
                        $v['topic_answer'] = $this->convert( $v['topic_answer']);
                        $v['user_answer'] = $this->convert( $v['user_answer']);
                       
                       break;
                   case 2:
                       $v['topic_type'] = 'multiple choice';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       
                       break;
                   case 3:
                       $v['topic_type'] = 'True or false';
                       break;
                   case 4:
                       $v['topic_type'] = 'Match';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       break;
                   case 5:
                       $v['topic_type'] = 'short answer';
                       break;
                   case 6:
                        $v['topic_type'] = 'multiple True or false';
                       break;
                    case 9:
                        $v['topic_type'] = 'three short answer';
                         break;
                   case 10:
                       $v['topic_type'] = 'survey';
                       break;
               }
               
             echo  "<tr align='center'  style='background-color:#E5E8ED'>
                   <td>".$v['username']."</td>
                   <td>".$v['case']."</td>
                   <td>".$v['topic_id']."</td>
                   <td>".$v['topic_type']."</td>
                   <td>".$v['topic_title']."</td>
                   <td>".$v['topic_answer']."</td>
                   <td>".$v['user_answer']."</td>
                   <td> ".$v['short_answer2']."</td>
                   <td>".$v['short_answer3']." </td>
                   <td>".$v['createtime']." </td>
                </tr>";

           }

            echo "</table>";
           exit; 

                    
        }
        
        public function downloadAll($case){
            header('Content-Type: application/vnd.ms-excel;');          
            header('Content-Disposition: attachment;filename="all_log.xls"');
            header('Cache-Control: max-age=0'); 
            
            echo "<table width='500' border='1' cellspacing='2' cellpadding='2'>
                <tr align='center'  style='background-color:#E5E8ED'>
                    <td >netID</td>
                    <td >case</td>
                    <td >题目ID</td>
                    <td >题目类型</td>
                    <td >题目</td>
                    <td >题目答案</td>
                    <td >用户提交答案</td>
                    <td> 简答题答案2</td>
                    <td>简答题答案3 </td>
                    <td>提交时间 </td>
                </tr> ";
             
             
            $data=$this->db->select('*')->from('answer_log')
                ->order_by('user_id', 'DESC')->order_by('topic_id', 'ASC')->get()->result_array();

            //填充表格信息
           foreach($data as $k=>$v){
               switch ($v['topic_type']){
                   case 1:
                       $v['topic_type'] = 'single choice';
                       
                        $v['topic_answer'] = $this->convert( $v['topic_answer']);
                        $v['user_answer'] = $this->convert( $v['user_answer']);
                       
                       break;
                   case 2:
                       $v['topic_type'] = 'multiple choice';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       
                       break;
                   case 3:
                       $v['topic_type'] = 'True or false';
                       break;
                   case 4:
                       $v['topic_type'] = 'Match';
                       $topicAn = explode(",",  $v['topic_answer']);
                       $newTopic = array();
                       foreach ($topicAn as $key => $value){
                           $newTopic[$key] = $this->convert($value);
                       }
                        $v['topic_answer'] = implode(",",$newTopic);
                        
                       $UserAn = explode(",",  $v['user_answer']);
                       $newUser = array();
                       foreach ($newUser as $key => $value){
                           $newUser[$key] = $this->convert($value);
                       }
                        $v['user_answer'] = implode(",",$newUser);
                       break;
                   case 5:
                       $v['topic_type'] = 'short answer';
                       break;
                   case 6:
                        $v['topic_type'] = 'multiple True or false';
                       break;
                   case 9:
                        $v['topic_type'] = 'three short answer';
                         break;
                   case 10:
                       $v['topic_type'] = 'survey';
                       break;
               }

             echo  "<tr align='center'  style='background-color:#E5E8ED'>
                   <td>".$v['username']."</td>
                   <td>".$v['case']."</td>
                   <td>".$v['topic_id']."</td>
                   <td>".$v['topic_type']."</td>
                   <td>".$v['topic_title']."</td>
                   <td>".$v['topic_answer']."</td>
                   <td>".$v['user_answer']."</td>
                   <td> ".$v['short_answer2']."</td>
                   <td>".$v['short_answer3']." </td>
                   <td>".$v['createtime']." </td>
                </tr>";

           }

            echo "</table>";
           exit; 
                    
        }
        
        
        public function convert($num){
            $res='';
            if( $num == 0){
                $res = 'A';
            }else if( $num == 1){
                  $res = 'B';
            }else if( $num == 2){
                  $res = 'C';
            }else if( $num == 3){
                  $res = 'D';
            }else if( $num == 4){
                  $res = 'E';
            }else if( $num == 5){
                  $res = 'F';
            }else if( $num == 6){
                  $res = 'G';
            }else if( $num == 7){
                  $res = 'H';
            }else if( $num == 8){
                  $res = 'I';
            }else if( $num == 9){
                  $res = 'J';
            }else if( $num == 10){
                  $res = 'K';
            }else if( $num == 11){
                  $res = 'L';
            }else if( $num == 12){
                  $res = 'M';
            }else if( $num == 13){
                  $res = 'N';
            }else if( $num == 14){
                  $res = 'O';
            }else if( $num == 15){
                  $res = 'P';
            }else if( $num == 16){
                  $res = 'Q';
            }else if( $num == 17){
                  $res = 'R';
            }else if( $num == 18){
                  $res = 'S';
            }else if( $num == 19){
                  $res = 'T';
            }
            
            return $res;
        }


        public function login(){
       
            $this->load->view('admin/login.html');
	}
        
        public function account($page=1, $key=''){
            
            if($key != ''){
               $search = $key; 
            } else {
               $search = $this->input->post('key');
            }
            
            
            $limit_start = ($page -1)*10;
            if(!empty($search)){
                 $data=$this->db->select('id, username, status, last_time')->from('admin')->like('username', $search)
                         ->order_by('id', 'DESC')->limit(10, 0)->get()->result_array();
                 $page = 1;
                 $count=$this->db->select('id, username, status, last_time')->from('admin')->like('username', $search)->count_all_results();
            } else{
                   $data=$this->db->select('*')->from('admin')->order_by('id', 'DESC')->limit(10, $limit_start)->get()->result_array();
                   $count=$this->db->select('*')->from('admin')->count_all_results();
            }
           
            
            if(empty($data)){
                $res = array();
                $res['page'] = $page;
                $res['search'] = empty($search)?'':$search;
                $res['data'] = array();
                $res['totalPage'] = 0;
                $this->load->view('admin/account.html', $res);
               
            } else{
                
                if($count%10 > 0){
                    $totalPage = $count/10 + 1;
                } else{
                    $totalPage = $count/10;
                }
                $res = array();
                $res['page'] = $page;
                $res['data'] = $data;
                $res['search'] = empty($search)?'':$search;
                $res['totalPage'] = $totalPage;
                $this->load->view('admin/account.html', $res);
            }
            
             
	}
        
        public function addAccount(){
             $username = $this->input->post('username');
             
             if(!empty($username)){
                  $insert = array(
                       'username' => $username,
                       'status' => 1,
                       'last_time' => date('Y-m-d H:i:s')
                       );
                $this->db->insert('admin', $insert);
             }
             
            $res = array();
            $res['status'] = 200;
            $res['url'] = base_url().'admin/account';
            echo json_encode($res);
        }
        
        public function deleteAccount($id){
              $this->db->delete('admin', array('id' => $id));
              
              $this->account();
        }
        public function updateAccount($id, $status){
            
            if($status == 1){
                  $update = array(
                    'status' => 0
                    );
            } else{
                 $update = array(
                    'status' => 1
                    );
            }
            
            $where = array();
            $where['id'] = $id;
            $this->db->update('admin', $update, $where);
            
            $this->account();
        }
        
       
}