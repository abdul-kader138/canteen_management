<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Food_Order'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("sales/food_order/" . $inv->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= lang('Order_Details'); ?>
                </div>
                <div class="panel-body">
                    <table class="table table-condensed table-striped table-borderless" style="margin-bottom:0;">
                        <tbody>
                        <tr>
                            <td><?= lang('date'); ?>:</td>
                            <td><?= $inv->start; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Menu_Name'); ?>:</td>
                            <td><?= $inv->title; ?></td>
                        </tr>

                        <tr>
                            <td><?= lang('Menu_Deatils'); ?>:</td>
                            <td><?= $inv->product_name; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Price'); ?>:</td>
                            <td><?= $inv->product_price; ?></td>
                        </tr>
                        <tr>
                            <td><?= lang('Subsidiary_Amount'); ?>:</td>
                            <td><?= $inv->discount_amount; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if ($returned) { ?>
                <h4><?= lang('sale_x_action'); ?></h4>
            <?php } else { ?>
                <div class="form-group">
                    <?= lang('Action', 'Action'); ?>
                    <?php
                    $opts = array('Order' => lang('Order'), 'Decline' => lang('Decline'));
                    ?>
                    <?= form_dropdown('status', $opts, $inv->sale_status, 'class="form-control" id="status" required="required" style="width:100%;"'); ?>
                </div>

                <div class="form-group">
                    <?= lang("note", "note"); ?>
                    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $this->sma->decode_html($inv->note)), 'class="form-control" id="note"'); ?>
                </div>
            <?php } ?>

        </div>
        <?php if ( ! $returned) { ?>
            <div class="modal-footer">
                <?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
            </div>
        <?php } ?>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
