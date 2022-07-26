<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course_detail_banners extends CI_Controller {

	public function __construct(){
		parent::__construct();
	    $this->load->model(['common','bannermodel']);
	    $this->data['userInfo'] = $this->common->validateadmin(true);
	    $this->load->library(['form_validation','upload','pagination']);
	    $this->load->helper(array('form', 'url'));
	    $this->data['mpage'] = 'sliders';
	    $this->data['subpage'] = 'course_detail_banners';
	}

	public function index(){
        $this->data['title'] = "List course detail Sliders";
        $table = 'course_detail_banners';
        $this->data['alldata'] = $this->common->getdata('course_detail_banners' , '*' , [] , 'result_array');
	    $this->load->view('admin/course_detail_banners/list',$this->data);
		    
	}
	public function add($id=null){
        $this->data['title'] = "Add course_detail_banners";
        $table = 'course_detail_banners';
	    if(!empty($id)){
	        $this->data['data'] = $this->common->getdata('course_detail_banners','*',['cdb_id'=>$id],'row_array');
	        $this->data['course'] = $this->common->getdata('course','*',['course_id'=>$this->data['data']['course_id']],'row_array');
	    }
	    $this->load->view('admin/course_detail_banners/add',$this->data);
	}
	public function save(){
	    
	    
	    $error_message = [];
	    $widget_array = [];
	    $continew = true;
        $error['status'] = 201;
        $error['message'] = 'Please fill complete form';
        
        $insert_array['title'] = $_POST['title'];
        $insert_array['description'] = $_POST['description'];
        $insert_array['course_id'] = $_POST['course_id'];
        $insert_array['link'] = $_POST['link'];
        $insert_array['link_action'] = $_POST['link_action'];
        $insert_array['button_text'] = $_POST['button_text'];
        
	    if(!empty($_FILES['img']['name'])){
            $config['upload_path'] = 'uploads/slider/';
            $config['allowed_types']='png|jpg|jpeg';
            $config['file_name'] = $_FILES['img']['name'];
            $config['encrypt_name']=true;
            $config['max_size'] = 5120;
    	    $config['overwrite']=true;
            $this->upload->initialize($config);
            if($this->upload->do_upload('img')){
                $uploadData = $this->upload->data();
                $insert_array['img'] = $uploadData['file_name'];
                $error['status'] = 200;
                $error['message'] = 'course_detail_banners Uploaded successfully';
	            $continew = true;
            }else{
	            $continew = false;
                $error_message[] = $this->upload->display_errors();
            }
	    }
	    
	    if($continew){
    	    if(isset($_POST['cdb_id'])){
                $insertdata = $this->common->update('course_detail_banners',$insert_array,['cdb_id'=>$_POST['cdb_id']]);
                $cdb_id = $_POST['cdb_id'];
            }else{
                $cdb_id = $this->common->insert('course_detail_banners',$insert_array);
            }
	    }
	    
	    if(!empty($error_message)){
            $error['status'] = false;
            $error['message'] = implode('<br>',$error_message);
	    }else{
	        
            $error['status'] = true;
            $error['message'] = 'course detail banner successfully saved';
	    }
	    echo json_encode($error);
	}
	
	public function delete($id=null){
        if(!empty($id)){
            $table = 'course_detail_banners';
           $upres= $this->common->delete($table,['cdb_id'=>$id]);
           if($upres){
                $this->common->setError(1,'Deleted');
                redirect("admin/course_detail_banners");
           }
        }
	   
	}
}

