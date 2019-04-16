<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php

$v = "";
if ($this->input->post('order_date')) {
    $v .= "&order_date=" . $this->input->post('order_date');
}
if ($this->input->post('menu_id')) {
    $v .= "&menu_id=" . $this->input->post('menu_id');
}

?>

    <script>
        function setMenu(obj) {
            var date_id = obj.id;

            // all validation

            // user given date
            var from = $('#' + date_id).val();
            var numbers = from.match(/\d+/g);
            var dates = new Date(numbers[2], (numbers[1] - 1), numbers[0]);
            var GivenDate = new Date(dates);

            // current date
            var CurrentDate = new Date();
            var twoDigitMonth = ((CurrentDate.getMonth().length + 1) === 1) ? (CurrentDate.getMonth() + 1) : '0' + (CurrentDate.getMonth() + 1);
            var currentDates = CurrentDate.getDate() + "/" + twoDigitMonth + "/" + CurrentDate.getFullYear();
            var numbers_1 = currentDates.match(/\d+/g);
            var dates_1 = new Date(numbers_1[2], (numbers_1[1] - 1), numbers_1[0]);
            var Current_Date = new Date(dates_1);

            // not less than current date
            $('#orderDate').val('');
            $('#menuId').val('');
            $('#sel_menu_ids').empty();
            if (GivenDate < Current_Date) {
                $('#' + date_id).val('');
                $('#sel_menu_id').empty();
                iziToast.show({
                    title: 'Warning:',
                    color: 'yellow',
                    progressBar: true,
                    message: 'Date cannot be less than Today'
                });
                return true;
            }


            // $.ajax({
            //     type: "get", async: false,
            //     url: site.base_url + "meal/getMenus/",
            //     dataType: "json",
            //     data: {
            //         date: $('#order_date').val()
            //     },
            //     success: function (data) {
            //         $(this).removeClass('ui-autocomplete-loading');
            //         $('#sel_menu_id').remove();
            //         if (data) {
            //             var opt = $("<select id='sel_menu_id' name='sel_menu_id' class='form-control select' />");
            //             $.each(data, function () {
            //                 $("<option />", {value: this.id, text: this.product_name}).appendTo(opt);
            //             });
            //             $('#sel_menu_ids').val('');
            //             $('#sel_menu_ids').empty().append(opt);
            //             $('#orderDate').val($('#order_date').val());
            //             $('#menuId').val($('#sel_menu_ids').val());
            //         } else {
            //             $('#order_date').val('');
            //             $('#sel_menu_id').remove();
            //             $('#orderDate').val('');
            //             $('#menuId').val('');
            //             iziToast.show({
            //                 title: 'Warning:',
            //                 color: 'yellow',
            //                 progressBar: true,
            //                 message: 'Still,Menu not set for this date.'
            //
            //             });
            //         }
            //     }
            // });
        }

        $(document).ready(function () {
            $.ajax({
                type: "get", async: false,
                url: site.base_url + "meal/getMenus/",
                dataType: "json",
                data: {
                    date: $('#order_date').val()
                },
                success: function (data) {
                    $(this).removeClass('ui-autocomplete-loading');
                    $('#sel_menu_id').remove();
                    if (data) {
                        var opt = $("<select id='sel_menu_id' name='sel_menu_id' class='form-control select' />");
                        $.each(data, function () {
                            $("<option />", {value: this.id, text: this.product_name}).appendTo(opt);
                        });
                        $('#sel_menu_ids').empty().append(opt);
                        $('#orderDate').val($('#order_date').val());
                        $('#menuId').val($('#sel_menu_id').val());
                    } else {
                        $('#order_date').val('');
                        $('#sel_menu_ids').empty();
                        $('#menuId').val('');
                        iziToast.show({
                            title: 'Warning:',
                            color: 'yellow',
                            progressBar: true,
                            message: 'Still,Menu not set for this date.'

                        });
                    }
                }
            });
            oTable = $('#SlRData').dataTable({
                "aaSorting": [[0, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= admin_url('meal/getFoodOrderGroup/?v=1' . $v) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                },
                "aoColumns": [{"bSortable": false, "mRender": checkbox}, null, null],
                "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                }
            }).fnSetFilteringDelay().dtFilter([], "footer");
        });
    </script>
    <div class="box">
        <div class="box-content">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <?php echo admin_form_open("meal/food_order_group"); ?>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <?= lang("Order_Date", "Order_Date"); ?>
                                    <?php echo form_input('order_date', (isset($_POST['order_date']) ? $_POST['order_date'] : ""), 'class="form-control date" onchange="setMenu(this)" id="order_date"'); ?>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <?= lang("Menu_Name", "Menu_Name"); ?>
                                    <div id="sel_menu_ids">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                        </div>
                        <?php echo form_close(); ?>

                    </div>
                    <div class="clearfix"></div>
                    <?php if ($Owner || $GP['meal-bulk_food_order']) {
                        echo admin_form_open('meal/bulk_food_order', 'id="action-form"');
                    }
                    ?>
                    <div class="table-responsive">
                        <table id="SlRData"
                               class="table table-bordered table-hover table-striped table-condensed reports-table">
                            <thead>
                            <tr>
                                <th style="min-width:3px; width: 5px; text-align: center;">
                                    <input class="checkbox checkft" type="checkbox" name="check"/>
                                </th>
                                <th style="min-width:20px; width: 100px;"><?= lang("User_Id"); ?></th>
                                <th style="min-width:20px; width: 200px;"><?= lang("Name"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="3" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                            </tr>
                            </tbody>
                            <tfoot class="dtFilter">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if ($Owner || $GP['meal-bulk_food_order']) { ?>
    <div class="row">
    <div class="col-sm-5">
        <div class="form-group">
            <input type="hidden" name="form_action" value="add" id="form_action"/>
            <input type="hidden" name="orderDate" value="0" id="orderDate"/>
            <input type="hidden" name="menuId" value="0" id="menuId"/>
            <?= form_submit('food_orders', 'Food Order', ' class="btn btn-primary" id="action-form-submit"') ?>
        </div>
        </div>
    </div>
    <?= form_close() ?>
<?php }
?>