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

    public function food_orders($id)
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
        if ($this->Owner || $this->Admin || $this->session->userdata('view_right')) {
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
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-delete_food_order'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $order_details = $this->meal_model->getOrderByID($id);
        $orderTimestamp = strtotime($order_details->order_date);
        $current_Date = date("Y-m-d");
        $current_time = date("H:i:s");
        $fixed_time = strtotime("10:00:00");
        $time = strtotime($current_time);
        $msg = '';
        if ($current_Date == $order_details->order_date ) {
            if ($time > $fixed_time) $msg = 'Order date is small than today.';
        } else
            if ($orderTimestamp < $time) $msg = 'Order date is small than today.';

        if($order_details->user_id != $this->session->userdata('user_id') && !$this->Owner)  $msg = 'You have only permission to delete own data.';

        if ($msg == '') {
            if ($this->meal_model->deleteOrder($id)) $this->sma->send_json(array('error' => 0, 'msg' => lang("Info_Deleted_Successfully")));
            else  $this->sma->send_json(array('error' => 1, 'msg' => lang("Info_Not_Deleted")));
        } else {
            if ($msg) $this->sma->send_json(array('error' => 1, 'msg' => $msg));
        }
    }


    function getMenus($date = NULL)
    {
        // $this->sma->checkPermissions('index');
        $row = true;
        $s_date = $this->sma->fld($this->input->get('date'));
        if (trim($s_date) == date("Y-m-d")) {
            $current_time = date("H:i:s");
            $fixed_time = strtotime("10:00:00");
            $time = strtotime($current_time);
            if ($fixed_time < $time && !$this->Owner) $row = false;
        }
        if ($row) $row = $this->meal_model->getMenusByDate($s_date);
        $this->sma->send_json($row);


//        $s_date = $this->sma->fld($this->input->get('date'));
//        $row = $this->meal_model->getMenusByDate($s_date);
//        $this->sma->send_json($row);
    }


    function checkDuplicateOrder($date = NULL, $id = Null)
    {
        $s_date = $this->sma->fld($this->input->get('date'));
        $row = $this->meal_model->getOrderByDate($s_date, $id);
        $this->sma->send_json($row);
    }


    public function food_order()
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
            $current_user = $this->meal_model->getUserByID($this->session->userdata('user_id'));
            $i = isset($_POST['orderDate']) ? sizeof($_POST['orderDate']) : 0;
            for ($r = 0; $r < $i; $r++) {
                $order_date = isset($_POST['orderDate'][$r]) ? $this->sma->fsd($_POST['orderDate'][$r]) : null;
                $menu_id = $_POST['menu_id'][$r];
                $order_qty = $_POST['menu_quantity'][$r];
                $info = $this->meal_model->getTodayMenuByID($menu_id);
                $discount_amounts = 0;
                $percentage = 100;
                if ($info->product_price > 80) $percentage = 26.67;
                $discount_amounts = 0;
                if ($current_user->allow_discount == 1 && $current_user->discount < 100) $discount_amounts = 40;
                else if ($current_user->allow_discount == 1 && $current_user->discount == 100) $discount_amounts = 80;
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
                    'discount_amount' => $discount_amounts,
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
            $orderTimestamp = strtotime($order_details->order_date);
            $current_Date = date("Y-m-d");
            $current_time = date("H:i:s");
            $fixed_time = strtotime("10:00:00");
            $time = strtotime($current_time);

            if ($current_Date == $order_details->order_date) {
                if ($time > $fixed_time) {
                    $this->session->set_flashdata('warning', lang('Order edit time already passed.'));
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                    redirect('meal');
                }
            } else {
                if ($orderTimestamp < $time) {
                    $this->session->set_flashdata('warning', lang('Order edit time already passed.'));
                    die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                    redirect('meal');
                }
            }


            $menu_id = $this->input->post('menu_name');
            $order_qty = $this->input->post('order_quantity');
            $info = $this->meal_model->getTodayMenuByID($menu_id);
            $current_user = $this->meal_model->getUserByID($order_details->user_id);
            $discount_amounts = 0;
            if ($current_user->allow_discount == 1 && $current_user->discount < 100) $discount_amounts = 40;
            else if ($current_user->allow_discount == 1 && $current_user->discount == 100) $discount_amounts = 80;


            $product = array(
                'title' => $info->title,
                'order_date' => $order_details->order_date,
                'description' => "Own",
                'menu_calendar_id' => $info->id,
                'user_id' => $order_details->user_id,
                'updated_by' => $this->session->userdata('user_id'),
                'updated_date' => date("Y-m-d H:i:s"),
                'product_id' => $info->product_id,
                'product_name' => $info->product_name,
                'product_price' => $info->product_price,
                'discount_amount' => $discount_amounts,
                'status' => 'Order',
                'note' => $this->sma->clear_tags($this->input->post('note')),
                'qty' => 1
            );
            if ($order_details->description == 'Guest') {
                $product['description'] = 'Guest';
                $product['discount_amount'] = 0;
            }
            if ($order_qty > 1 && $order_details->description == 'Own') {
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
            $this->data['user'] = $this->meal_model->getUserByID($order_details->user_id);
            $this->data['order'] = $order_details;
            $this->data['d1'] = $orderTimestamp;
            $this->data['d2'] = $fixed_time;
            $this->data['d3'] = $time;
            $this->load->view($this->theme . 'meal/edit_food_order', $this->data);
        }
    }


    function food_order_group($date = NULL)
    {
        if (!$this->Owner && !$this->Admin) {
            $get_permission = $this->permission_details[0];
            if ((!$get_permission['meal-bulk_food_order'])) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                redirect($_SERVER["HTTP_REFERER"]);
            }
        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['date'] = $date;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('Food_Order_Group'), 'bc' => $bc);
        $this->page_construct('meal/food_order_group', $meta, $this->data);
    }


    function getFoodOrderGroup()
    {
        $order_date = $this->input->get('order_date') ? $this->input->get('order_date') : NULL;
        $menu_id = $this->input->get('menu_id') ? $this->input->get('menu_id') : NULL;
        if (!$order_date) $order_date = date($this->dateFormats['php_sdate']);
        if ($order_date) $order_date = $this->sma->fld($order_date);
        $si="";
        if ($this->Owner || $this->Admin) $si = "( SELECT ut.id as u_id, upper(ut.username) as us_name, concat(ut.first_name,' ',ut.last_name) as u_name FROM " . $this->db->dbprefix('users') . " as ut WHERE  id NOT IN (select ui.user_id from " . $this->db->dbprefix('food_order_details') . " as ui ";
        else $si = "( SELECT ut.id as u_id, upper(ut.username) as us_name, concat(ut.first_name,' ',ut.last_name) as u_name FROM " . $this->db->dbprefix('users') . " as ut WHERE discount_type='Full Free' and id NOT IN (select ui.user_id from " . $this->db->dbprefix('food_order_details') . " as ui ";

        if ($order_date) {
            $si .= " WHERE ";
            $si .= " ui.order_date = '{$order_date}' ";
        }
        $si .= ") group by  id ) POrder";

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('users')}.id as id, {$this->db->dbprefix('users')}.username,concat({$this->db->dbprefix('users')}.first_name,' ',{$this->db->dbprefix('users')}.last_name) as names")
            ->from('users')
            ->join($si, 'POrder.u_id=users.id', 'inner');

        echo $this->datatables->generate();

    }

    public function bulk_food_order()
    {
        if (!$this->Owner && !$this->GP['meal-bulk_food_order']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');
        $this->form_validation->set_rules('orderDate', lang("Order_Date"), 'required');
        $this->form_validation->set_rules('menuId', lang("Menu_Id"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {

                // validate and sync order and menu
                $info = $this->meal_model->getTodayMenuByID($this->input->post('menuId'));
                if ($info->id != $this->input->post('menuId') || $this->sma->fsd($this->input->post('orderDate')) != $info->start) {
                    $this->session->set_flashdata('error', 'Order date not match with menu');
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                foreach ($_POST['val'] as $id) {
                    $current_user = $this->meal_model->getUserByID($id);
                    $orderDate = $this->sma->fsd($this->input->post('orderDate'));
                    $check_duplicate_order = $this->meal_model->getOrderByDate($orderDate, $current_user->id);

                    // if order exit then skip
                    if ($check_duplicate_order) continue;

                    //calculate discount
                    $discount_amounts = 0;
                    if ($current_user->allow_discount == 1 && $current_user->discount < 100) $discount_amounts = 40;
                    else if ($current_user->allow_discount == 1 && $current_user->discount == 100) $discount_amounts = 80;


                    $product = array(
                        'title' => $info->title,
                        'order_date' => $this->sma->fsd($this->input->post('orderDate')),
                        'description' => "Own",
                        'menu_calendar_id' => $info->id,
                        'user_id' => $current_user->id,
                        'created_by' => $this->session->userdata('user_id'),
                        'created_date' => date("Y-m-d H:i:s"),
                        'product_id' => $info->product_id,
                        'product_name' => $info->product_name,
                        'product_price' => $info->product_price,
                        'discount_amount' => $discount_amounts,
                        'status' => 'Order',
                        'note' => $this->sma->clear_tags($this->input->post('note')),
                        'qty' => 1
                    );
                    $products[] = $product;
                }
                if (empty($products)) {
                    $this->form_validation->set_rules('product', lang("order_items"), 'required');
                } else {
                    krsort($products);
                }
                // array build done(For batch insertion)
            } else {
                $this->session->set_flashdata('error', lang("No_User_Selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            // Batch insertion
            if ($this->form_validation->run() == true && $this->meal_model->addGroupOrderBatch($products)) {
                $this->session->set_flashdata('message', lang('Info_Updated_Successfully'));
                admin_redirect('meal');
            } else {
                $this->session->set_flashdata('error', validation_errors());
                redirect($_SERVER["HTTP_REFERER"]);
            }

        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }


    public function meal_actions()
    {
        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    if (!$this->Owner && !$this->Admin) {
                        $get_permission = $this->permission_details[0];
                        if ((!$get_permission['meal-delete_food_order'])) {
                            $this->session->set_flashdata('warning', lang('access_denied'));
                            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . (isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url('welcome')) . "'; }, 10);</script>");
                            redirect($_SERVER["HTTP_REFERER"]);
                        }
                    }
                    foreach ($_POST['val'] as $id) {
                        $order_details = $this->meal_model->getOrderByID($id);
                        $orderTimestamp = strtotime($order_details->order_date);
                        $current_Date = date("Y-m-d");
                        $current_time = date("H:i:s");
                        $fixed_time = strtotime("10:00:00");
                        $time = strtotime($current_time);
                        $msg = '';

//                        time check
                        if ($current_Date == $order_details->order_date) {
                            if ($time > $fixed_time) $msg = 'Order date is small than today.';
                        } else
                            if ($orderTimestamp < $time) $msg = 'Order date is small than today.';

                        if($order_details->user_id != $this->session->userdata('user_id') && !$this->Owner)  $msg = 'You have only permission to delete own data.';

                        //if valid delete then
                        if ($msg == '') {
                            if ($this->meal_model->deleteOrder($id)) {
                                $this->session->set_flashdata('message', lang('Info_Deleted_Successfully'));
                                admin_redirect('meal');
                            } else {
                                $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                                admin_redirect('meal');
                            }
                        } else {
                            $this->session->set_flashdata('error', $msg);
                            admin_redirect('meal');
                        }

                    }
                }
            } else {
                $this->session->set_flashdata('error', lang("No_Order_Selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

}

