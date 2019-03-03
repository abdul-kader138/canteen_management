<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Food_Order'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("meal/food_order/" . $inv->id, $attrib); ?>
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
                        <tr>

                        </tr>
                        </tbody>
                    </table>


                </div>
            </div>
            <?php if ($returned) { ?>
                <h4><?= lang('sale_x_action'); ?></h4>
            <?php } else { ?>
                <div class="form-group">
                    <p class="bold"><span id="award_points"></span></p>
                    <input type="checkbox" class="checkbox award_point" name="award_point" id="award_point"><label
                            for="award_points" class="padding05"><?= lang('Guest_Include'); ?></label>
                </div>
                <div class="form-group" id="ca-points-con" style="display:none;">
                    <?= lang("No_Of_Guest", "No_Of_Guest"); ?>
                    <?php $attributes = array ('class' => 'form-control input-tip','name' => 'no_of_guest', 'id' => 'no_of_guest','type'=>'number','value'=>1);?>
                    <?php echo form_input($attributes, (isset($_POST['no_of_guest']) ? $_POST['no_of_guest'] : "")); ?>
                </div>
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
        <?php if (!$returned) { ?>
            <div class="modal-footer">
                <?php echo form_submit('update', lang('update'), 'class="btn btn-primary"'); ?>
            </div>
        <?php } ?>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="application/javascript">
        $('#award_point').on('ifChecked', function (event) {
            console.log('ifChecked');
            $('#ca-points-con').show();
        });
        $('#award_point').on('ifUnchecked', function (event) {
            console.log('ifUnchecked');
            $('#ca-points-con').hide();
        });
</script>
