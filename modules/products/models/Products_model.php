<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Products_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add_product($data)
    {
        $this->db->insert(db_prefix().'product_master', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Product Added [ ID:'.$insert_id.', '.$data['product_name'].', Staff id '.get_staff_user_id().' ]');

            return $insert_id;
        }

        return false;
    }

    public function get_by_id_product($id = false)
    {
        $this->db->join('product_categories', db_prefix().'product_categories.p_category_id='.db_prefix().'product_master.product_category_id', 'LEFT');
        if ($id) {
            $this->db->where_in('id', $id);
            if (is_array($id)) {
                $product = $this->db->get(db_prefix().'product_master')->result();
            } else {
                $product = $this->db->get(db_prefix().'product_master')->row();
            }

            return $product;
        }
        $products = $this->db->get(db_prefix().'product_master')->result_array();

        return $products;
    }

    public function get_category_filter($p_category_id)
    {
        $this->db->where_in('p_category_id', $p_category_id);
        $this->db->order_by('product_master.product_category_id', 'ASC');

        return $this->get_by_id_product();
    }

    public function edit_product($data, $id)
    {
        $product = $this->get_by_id_product($id);
        $this->db->where('id', $id);
        $res = $this->db->update(db_prefix().'product_master', $data);
        if ($this->db->affected_rows() > 0) {
            if (!empty($data['quantity_number']) && $product->quantity_number != $data['quantity_number']) {
                log_activity('Product Quantity updated[ ID: '.$id.', From: '.$product->quantity_number.' To: '.$data['quantity_number'].' Staff id '.get_staff_user_id().']');
            }
            log_activity('Product Details updated[ ID: '.$id.', '.$product->product_name.', Staff id '.get_staff_user_id().' ]');
        }
        if ($res) {
            return true;
        }

        return false;
    }

    public function delete_by_id_product($id)
    {
        $product  = $this->get_by_id_product($id);
        $relPath  = get_upload_path_by_type('products').'/';
        $fullPath = $relPath.$product->product_image;
        unlink($fullPath);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $result = $this->db->delete(db_prefix().'product_master');
        log_activity('Product Deleted[ ID: '.$id.', '.$product->product_name.', Staff id '.get_staff_user_id().' ]');

        return $result;
    }

    public function dublicate_data($id){

        $query = $this->db->get_where(db_prefix().'product_master',array('id'=>$id));
        foreach ($query->result() as $row) {
            unset($row->id);
            $this->db->insert(db_prefix().'product_master',$row);
        }
     
        return true;
    }

    public function add_suppliers($data)
    {
        $this->db->insert(db_prefix().'suppliers', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Supplier Added [ ID:'.$insert_id.', '.$data['name'].']');

            return $insert_id;
        }

        return false;
    }

     public function get_by_id_supplier($id = false)
    {
        // $this->db->join('product_categories', db_prefix().'product_categories.p_category_id='.db_prefix().'product_master.product_category_id', 'LEFT');
        if ($id) {
            $this->db->where_in('id', $id);
            if (is_array($id)) {
                $supplier = $this->db->get(db_prefix().'suppliers')->result();
            } else {
                $supplier = $this->db->get(db_prefix().'suppliers')->row();
            }

            return $supplier;
        }
        $suppliers = $this->db->get(db_prefix().'suppliers')->result_array();

        return $suppliers;
    }

    public function edit_supplier($data, $id)
    {
        $supplier = $this->get_by_id_supplier($id);
        $this->db->where('id', $id);
        $res = $this->db->update(db_prefix().'suppliers', $data);
      

        log_activity('Supplier Details updated[ ID: '.$id.', '.$supplier->name.']');

        if ($res) {
            return true;
        }

        return false;
    }

    public function delete_by_id_supplier($id)
    {
        $supplier  = $this->get_by_id_supplier($id);
        // $relPath  = get_upload_path_by_type('suppliers').'/';
        // $fullPath = $relPath.$supplier->supplier_image;
        // unlink($fullPath);
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $result = $this->db->delete(db_prefix().'suppliers');
        log_activity('Supplier Deleted[ ID: '.$id.', '.$supplier->name.' ]');

        return $result;
    }

}
