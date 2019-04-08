<?php
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 03-Mar-19
 * Time: 10:58 AM
 */

class Meal extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('sma', $this->Settings->user_language);
        $this->permission_details = $this->site->checkPermissions();
        $this->load->library('form_validation');
        $this->load->admin_model('meal_model');
    }

    public function food_order($id)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-food_order'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        $this->form_validation->set_rules('status', lang("sale_status"), 'required');

        if ($this->form_validation->run() == true) {
            $info = $this->meal_model->getTodayMenuByID($id);
            $guest_data = array();
            $data = array(
                'title' => $info->title,
                'order_date' => $info->start,
                'description' => "Own",
                'menu_calendar_id' => $info->id,
                'user_id' => $this->session->userdata('user_id'),
                'created_by' => $this->session->userdata('user_id'),
                'created_date' => date("Y-m-d H:i:s"),
                'product_id' => $info->product_id,
                'product_name' => $info->product_name,
                'product_price' => $info->product_price,
                'discount_amount' => $info->discount_amount,
                'status' => $this->input->post('status'),
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'qty' => 1
            );


            if ($this->input->post('award_point')) {
                $new_data = $data;
                $new_data['description'] = 'Guest';
                $new_data['discount_amount'] = 0;
                $no_of_guest = $this->input->post('no_of_guest');
                for ($i = 1; $i <= $no_of_guest; $i++) {
                    $guest_data[] = $new_data;
                }

            }

        } elseif ($this->input->post('update')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->form_validation->run() == true && $this->meal_model->addOrder($data, $guest_data)) {
            $this->session->set_flashdata('message', lang('status_updated'));
            admin_redirect('meal');
        } else {
            $this->data['inv'] = $this->meal_model->getTodayMenuByID($id);
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'meal/food_order', $this->data);

        }
    }

    function index()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-index'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('Food_Order_Details')));
        $meta = array('page_title' => lang('Food_Order'), 'bc' => $bc);
        $this->page_construct('meal/index', $meta, $this->data);
    }

    function getFoodHistory()
    {
        $this->load->library('datatables');

        if ($this->Owner || $this->Admin) {
            $this->datatables
                ->select("food_order_details.id as id, users.username,concat(first_name,' ',last_name) as nam,order_date,title, product_name, product_price, discount_amount,(product_price-discount_amount) as amount, status,description")
                ->from("food_order_details")
                ->join('users', 'users.id=food_order_details.user_id', 'left')
                ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("Edit_Order") . "' href='" . admin_url('meal/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("Delete_Order") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('meal/delete_food_order/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
            echo $this->datatables->generate();
        } else {
            $this->datatables
                ->select("food_order_details.id as id, users.username,concat(first_name,' ',last_name) as nam,order_date,title, product_name, product_price, discount_amount, (product_price-discount_amount) as amount,status,description")
                ->from("food_order_details")
                ->join('users', 'users.id=food_order_details.user_id', 'left')
                ->where('food_order_details.user_id', $this->session->userdata('user_id'))
                ->add_column("Actions", "<div class=\"text-center\"><a class=\"tip\" title='" . $this->lang->line("Edit_Order") . "' href='" . admin_url('meal/edit/$1') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . $this->lang->line("Delete_Order") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('meal/delete_food_order/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");
            echo $this->datatables->generate();
        }
    }

    public function delete_food_order($id)
    {
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->meal_model->deleteOrder($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("Info_Deleted_Successfully")));
        } else {
            $this->sma->send_json(array('error' => 1, 'msg' => lang("Info_Not_Deleted")));
        }
    }

    function getMenus($date = NULL)
    {
        // $this->sma->checkPermissions('index');
        $s_date = $this->sma->fld($this->input->get('date'));
        $row = $this->meal_model->getMenusByDate($s_date);
        $this->sma->send_json($row);
    }


    function checkDuplicateOrder($date = NULL)
    {
        $s_date = $this->sma->fld($this->input->get('date'));
        $row = $this->meal_model->getOrderByDate($s_date);
        $this->sma->send_json($row);
    }


    public function add()
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-food_order'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->form_validation->set_rules('note', $this->lang->line("note"), 'trim');
        if ($this->form_validation->run() == true) {
            $i = isset($_POST['orderDate']) ? sizeof($_POST['orderDate']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $order_date = isset($_POST['orderDate'][$r]) ? $this->sma->fsd($_POST['orderDate'][$r]) : null;
                $menu_id = $_POST['menu_id'][$r];
                $order_qty = $_POST['menu_quantity'][$r];
                $info = $this->meal_model->getTodayMenuByID($menu_id);
                $product = array(
                    'title' => $info->title,
                    'order_date' => $order_date,
                    'description' => "Own",
                    'menu_calendar_id' => $info->id,
                    'user_id' => $this->session->userdata('user_id'),
                    'created_by' => $this->session->userdata('user_id'),
                    'created_date' => date("Y-m-d H:i:s"),
                    'product_id' => $info->product_id,
                    'product_name' => $info->product_name,
                    'product_price' => $info->product_price,
                    'discount_amount' => $info->discount_amount,
                    'status' => 'Order',
                    'note' => $this->sma->clear_tags($this->input->post('note')),
                    'qty' => 1
                );

                if ($order_qty > 1) {
                    $guest_product = $product;
                    $guest_product['description'] = 'Guest';
                    $guest_product['discount_amount'] = 0;
                    for ($j = 1; $j < $order_qty; $j++) $products[] = $guest_product;
                }
                $products[] = $product;
            }
        }
        if (empty($products)) {
            $this->form_validation->set_rules('product', lang("order_items"), 'required');
        } else {
            krsort($products);
        }


        // $this->sma->print_arrays($data, $products);

        if ($this->form_validation->run() == true && $this->meal_model->addOrderBatch($products)) {
            $this->session->set_flashdata('message', lang('Info_Updated_Successfully'));
            admin_redirect('meal');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('quotes'), 'page' => lang('quotes')), array('link' => '#', 'page' => lang('Food_Order')));
            $meta = array('page_title' => lang('Food_Order'), 'bc' => $bc);
            $this->page_construct('meal/add', $meta, $this->data);
        }
    }


    function edit($id = NULL)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-edit_food_order'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $order_details = $this->meal_model->getOrderByID($id);
        $this->form_validation->set_rules('order_quantity', 'order_quantity', 'required');
        if ($this->form_validation->run() == true) {
            $menu_id = $this->input->post('menu_name');
            $order_qty = $this->input->post('order_quantity');
            $info = $this->meal_model->getTodayMenuByID($menu_id);
            $product = array(
                'title' => $info->title,
                'order_date' => $order_details->order_date,
                'description' => "Own",
                'menu_calendar_id' => $info->id,
                'user_id' => $this->session->userdata('user_id'),
                'updated_by' => $this->session->userdata('user_id'),
                'updated_date' => date("Y-m-d H:i:s"),
                'product_id' => $info->product_id,
                'product_name' => $info->product_name,
                'product_price' => $info->product_price,
                'discount_amount' => $info->discount_amount,
                'status' => 'Order',
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'qty' => 1
            );
            if ($order_details->description=='Guest') {
                $product['description'] = 'Guest';
                $product['discount_amount'] = 0;
            }
            if ($order_qty > 1 && $order_details->description=='Own') {
                $guest_product = $product;
                $guest_product['description'] = 'Guest';
                $guest_product['discount_amount'] = 0;
                for ($j = 1; $j < $order_qty; $j++) $products[] = $guest_product;
            }
            $products[] = $product;

            if (empty($products)) {
                $this->form_validation->set_rules('product', lang("order_items"), 'required');
            } else {
                krsort($products);
            }
        }

        if ($this->form_validation->run() == true && $this->meal_model->updateOrder($id, $products)) {
            $this->session->set_flashdata('message', lang('Info_Updated_Successfully'));
            admin_redirect('meal');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['menus'] = $this->meal_model->getMenusByDate($order_details->order_date);
            $this->data['order'] = $order_details;
            $this->load->view($this->theme . 'meal/edit_food_order', $this->data);
        }
    }
}