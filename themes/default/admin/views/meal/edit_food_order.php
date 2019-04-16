<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Food_order'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("meal/edit/" . $order->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("User_ID", "User_ID"); ?>
                        <?php echo form_input('usernames', (isset($_POST['usernames']) ? $_POST['usernames'] : $user->username), 'class="form-control date" readonly id="usernames"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("User_Name", "User_Name"); ?>
                        <?php echo form_input('userfullname', (isset($_POST['userfullname']) ? $_POST['userfullname'] : ($user->first_name.' '.$user->last_name)), 'class="form-control date" readonly id="userfullname"'); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("Order_Date", "sldate"); ?>
                        <?php echo form_input('order_date', (isset($_POST['order_date']) ? $_POST['order_date'] : $order->order_date), 'class="form-control input-tip" readonly id="order_date" required="required"'); ?>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("Order_For", "Order_For"); ?>
                        <?php echo form_input('order_for', (isset($_POST['order_for']) ? $_POST['order_for'] : $order->description), 'class="form-control input-tip" readonly id="order_for"'); ?>

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label"
                               for="customer_group"><?php echo $this->lang->line("Menu"); ?></label>
                        <?php
                        foreach ($menus as $menu) {
                            $cgs[$menu->id] = $menu->product_name;
                        }
                        echo form_dropdown('menu_name', $cgs, $order->product_id, 'class="form-control select" id="menu_name" style="width:100%;" required="required"');
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group company">
                        <?= lang("Order_Quantity", "Order_Quantity");
                        $attribs = array(
                            'class' => 'form-control tip',
                            'id' => 'order_quantity',
                            'name' => 'order_quantity',
                            'required' => 'required',
                            'type' => 'number'
                        );
                        ?>
                        <?php echo form_input($attribs, (isset($_POST['order_quantity']) ? $_POST['order_quantity'] : $order->qty)); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <?= lang("note", "note"); ?>
                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $this->sma->decode_html($order->note)), 'class="form-control" id="note"'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <?php echo form_submit('Edit_Order', lang('Edit_Order'), 'class="btn btn-primary"'); ?>
        </div>

    </div>
</div>
<?php echo form_close(); ?>
<?= $modal_js ?>
