<?php
/**
 * Created by PhpStorm.
 * User: a.kader
 * Date: 03-Mar-19
 * Time: 10:59 AM
 */

class Meal_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getTodayMenuByID($id)
    {
        $q = $this->db->get_where('calendar', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addOrder($data = array(),$guest_data=array())
    {

        $this->db->trans_strict(TRUE);
        $this->db->trans_start();
        $this->db->insert('food_order_details', $data);
        if(!empty($guest_data)) $this->db->insert_batch('food_order_details',$guest_data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) return false;
        return true;
    }

    public function getMenusByDate($date)
    {
        $this->db->select("id, title, product_name");
        $q = $this->db->get_where('calendar', array('start' => $date));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getOrderByDate($date)
    {
        $this->db->select("id, title, user_id");
        $q = $this->db->get_where('food_order_details', array('order_date' => trim($date)), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

}