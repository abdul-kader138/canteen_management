<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Food_Order'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'id'=>'addMeal', 'name'=>'addMeal', 'role' => 'form');
                echo admin_form_open("meal/food_order", $attrib)
                ?>
                <div class="row">
                    <div class="col-lg-12" id="sticker">
                        <div class="col-md-12">
                            <div class="control-group table-group">
                                <label class="table-label"><?= lang("order_items"); ?> *</label>

                                <div class="controls table-controls">
                                    <table id="quTable"
                                           class="table items table-striped table-bordered table-condensed table-hover sortable_table">
                                        <thead>
                                        <tr>
                                            <th class="col-md-2"><?= lang('Order_Date'); ?></th>
                                            <th class="col-md-6"><?= lang("Menu"); ?></th>
                                            <th class="col-md-3"><?= lang("quantity"); ?></th>
                                            <th class="col-md-1"
                                                style="width: 30px !important; text-align: center; cursor: pointer;"
                                                id="addCF"><i
                                                        class="fa fa-plus-circle"
                                                        style="opacity:0.5; filter:alpha(opacity=50);"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                        <div class="row" id="bt">
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?= lang("note", "qunote"); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="qunote" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-12">
                            <div
                                    class="fprom-group"><?php echo form_button('add_quote', $this->lang->line("submit"), 'id="add_quote" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var isinValidRow = false;
        $("#addCF").click(function () {
            rowCount = $('#quTable tr').length;

            // only can add if provide all info of current data
            if (rowCount != 0) {
                $(".datelist").each(function () {
                    var ids = this.id;
                    var idVal = $('#' + ids).val();
                    var menu_ids = ids.split("_");
                    var menu_id = $('#sel_menu_id_' + menu_ids[2]).val();
                    var qty_id = $('#qty_id_' + menu_ids[2]).val();

                    isinValidRow = false;
                    if (idVal == undefined || idVal == null || idVal == '') {
                        iziToast.show({
                            title: 'Warning:',
                            color: 'yellow',
                            progressBar: true,
                            message: 'Please fill the blank date information first'
                        });
                        isinValidRow = true;
                    }

                    if (menu_id == undefined || menu_id == null || menu_id == '') {
                        iziToast.show({
                            title: 'Warning:',
                            color: 'yellow',
                            progressBar: true,
                            message: 'Please fill the blank menu information first'
                        });
                        isinValidRow = true;
                    }

                    if (qty_id == undefined || qty_id == null || qty_id == '') {
                        iziToast.show({
                            title: 'Warning:',
                            color: 'yellow',
                            progressBar: true,
                            message: 'Please fill the blank qty information first'
                        });
                        isinValidRow = true;
                    }
                });

                if (isinValidRow==false) {
                    if (rowCount <= 7) {
                        var row_id = 0;
                        if (!localStorage.getItem('row_id')) localStorage.setItem('row_id', 1);
                        else {
                            row_id = localStorage.getItem('row_id');
                            row_id = (parseInt(row_id) + 1);
                            localStorage.setItem('row_id', row_id)
                        }
                        $("#quTable").append('<tr><td width="20%"><input class="form-control datelist input-tip date" readonly type="text" name="orderDate[]" value="" placeholder="Select date" onchange="setMenuList(this)" id="row_id_' + row_id + '"/></td><td width="55%" id="menu_id_' + row_id + '"></td><td width="20%"><input class="form-control input-tip" name="menu_quantity[]" value="" placeholder="Quantity" type="number" id="qty_id_' + row_id + '"/></td><td width="5%" style="text-align: center;"><a href="javascript:void(0);" class="remCF"><i class="fa fa-trash-o"></i></a></td></tr>');
                    }
                }
            }
        });

        $("#quTable").on('click', '.remCF', function () {
            $(this).parent().parent().remove();
        });

        $("#add_quote").click(function(){
            var isinValidRows = false;
            $(".datelist").each(function () {
                var ids = this.id;
                var idVal = $('#' + ids).val();
                var menu_ids = ids.split("_");
                var menu_id = $('#sel_menu_id_' + menu_ids[2]).val();
                var qty_id = $('#qty_id_' + menu_ids[2]).val();

                isinValidRows = false;
                if (idVal == undefined || idVal == null || idVal == '') {
                    iziToast.show({
                        title: 'Warning:',
                        color: 'yellow',
                        progressBar: true,
                        message: 'Please fill the blank date information first'
                    });
                    isinValidRows = true;
                }

                if (menu_id == undefined || menu_id == null || menu_id == '') {
                    iziToast.show({
                        title: 'Warning:',
                        color: 'yellow',
                        progressBar: true,
                        message: 'Please fill the blank menu information first'
                    });
                    isinValidRows = true;
                }

                if (qty_id == undefined || qty_id == null || qty_id == '') {
                    iziToast.show({
                        title: 'Warning:',
                        color: 'yellow',
                        progressBar: true,
                        message: 'Please fill the blank qty information first'
                    });
                    isinValidRows = true;
                }
            });

            var rowCounts = $('#quTable tr').length;
            console.log(rowCounts);
            if (rowCounts == 1 || rowCounts ==null || rowCounts ==undefined) {
                iziToast.show({
                    title: 'Warning:',
                    color: 'yellow',
                    progressBar: true,
                    message: 'Please add the meal info'
                });
                isinValidRows = true;
            }
            if(isinValidRows== false)  $('#addMeal')[0].submit();
        });
    });
</script>