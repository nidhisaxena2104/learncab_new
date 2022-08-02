<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class slider extends CI_Controller {

	public function __construct(){
		parent::__construct();
	    $this->load->model(['common','bannermodel']);
	    $this->data['userInfo'] = $this->common->validateadmin(true);
	    $this->load->library(['form_validation','upload','pagination']);
	    $this->load->helper(array('form', 'url'));
	    $this->data['mpage'] = 'sliders';
	    $this->data['subpage'] = 'homepage_slider';
	}

	public function list(){
        $this->data['title'] = "List slider";
        $table = 'slider';
        $this->load->library('pagination');
        $config['base_url'] = site_url('slider/list?pagefor=').$_GET['pagefor'];
        $config['per_page'] = "100";
        $config["uri_segment"] = 3;
        $this->data['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $alldata = $this->common->getAllData($table , $config["per_page"], $this->data['page'],['page' => $_GET['pagefor']]);
        $config['total_rows'] = $alldata['totalrows'];
        $choice = $config["total_rows"]/$config["per_page"];
        $config["num_links"] = floor($choice);
        
        // integrate bootstrap pagination
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '«';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '»';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        
        $this->data['alldata'] = $alldata['result'];
        $this->data['pagination'] = $this->pagination->create_links();

        
	    $this->load->view('admin/slider/list',$this->data);
		    
	}
	public function add($id=null){
        $this->data['title'] = "Add slider";
        $table = 'slider';
	    if(!empty($id)){
	        $this->data['data'] = $this->common->getdata('slider','*',['slider_id'=>$id],'row_array');
	    }
	    $this->load->view('admin/slider/add',$this->data);
        
	}
	public function save(){
	    $error_message = [];
	    $widget_array = [];
	    $continew = true;
        $error['status'] = 201;
        $error['message'] = 'Please fill complete form';
        $insert_array['title'] = $_POST['title'];
        $insert_array['description'] = $_POST['description'];
        $insert_array['page'] = $_POST['page'];
        $insert_array['link'] = $_POST['link'];
        $insert_array['link_action'] = $_POST['link_action'];
        $insert_array['button_text'] = $_POST['button_text'];
        
	    if(!empty($_FILES['img']['name'])){
            $config['upload_path'] = 'uploads/slider/';
            $config['allowed_types']='png|jpg|jpeg|webp|avif|gif';
            $config['file_name'] = $_FILES['img']['name'];
            $config['encrypt_name']=true;
            $config['max_size'] = 5120;
    	    $config['overwrite']=true;
            $this->upload->initialize($config);
            if($this->upload->do_upload('img')){
                $uploadData = $this->upload->data();
                $insert_array['img'] = $uploadData['file_name'];
	            $continew = true;
            }else{
	            $continew = false;
                $error_message[] = $this->upload->display_errors();
            }
	    }
	    
	    if($continew)
	    {
	        if(!empty($_FILES['mob_img']['name']))
	        {
                $config['upload_path'] = 'uploads/slider/mobile_image/';
                $config['allowed_types']='png|jpg|jpeg';
                $config['file_name'] = $_FILES['mob_img']['name'];
                $config['encrypt_name']=true;
                $config['max_size'] = 5120;
        	    $config['overwrite']=true;
                $this->upload->initialize($config);
                if($this->upload->do_upload('mob_img')){
                    $uploadData = $this->upload->data();
                    $insert_array['mob_img'] = $uploadData['file_name'];
    	            $continew = true;
                }else{
    	            $continew = false;
                    $error_message[] = $this->upload->display_errors();
                }
	        }
	    
	    }
	    
	    if($continew)
	    {
    	    if(isset($_POST['slider_id'])){
                $insertdata = $this->common->update('slider',$insert_array,['slider_id'=>$_POST['slider_id']]);
                $slider_id = $_POST['slider_id'];
            }else{
                $slider_id = $this->common->insert('slider',$insert_array);
            }
	    }
	    
	    if(!empty($error_message)){
            $error['status'] = false;
            $error['message'] = implode('<br>',$error_message);
	    }else{
	        
            $error['status'] = true;
            $error['message'] = 'slider successfully saved..';
	    }
	    echo json_encode($error);
	}
	
	public function delete($id=null){
        if(!empty($id)){
            $table = 'slider';
           $upres= $this->common->delete($table,['slider_id'=>$id]);
           if($upres){
                $this->common->setError(1,'Deleted');
                redirect("admin/slider/list?pagefor=0");
           }
        }
	   
	}
}

