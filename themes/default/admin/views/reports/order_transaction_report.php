<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
?>
<script>
    $(document).ready(function () {
        function order_status(x) {
            if (x == null) {
                return '';
            } else if (x == 'Own') {
                return '<div class="text-center"><span class="row_status label label-success">' + x + '</span></div>';
            } else if (x == 'Guest' || x == 'paid' || x == 'sent' || x == 'received') {
                return '<div class="text-center"><span class="row_status label label-warning">' + x + '</span></div>';
            } else {
                return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
            }
        }

        function discount_status(x) {
            if (x == null) {
                return '';
            } else {
                return '<div class="text-center"><span class="row_status label label-info">' + x + '</span></div>';
            }
        }

        function price_status(x) {
            if (x == null) {
                return '';
            } else {
                return '<div class="text-center"><span class="row_status label label-default">' + x + '</span></div>';
            }
        }

        function grand_status(x) {
            if (x == null) {
                return '';
            } else {
                return '<div class="text-center"><span class="row_status label label-danger">' + x + '</span></div>';
            }
        }

        var pb = <?= json_encode($pb); ?>;

        function paid_by(x) {
            return (x != null) ? (pb[x] ? pb[x] : x) : x;
        }

        function ref(x) {
            return (x != null) ? x : ' ';
        }

        oTable = $('#PayRData').dataTable({
            "aaSorting": [[0, "asc"], [1, "asc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getOrderTransactionReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{"mRender": fsd}, null, {"mRender": order_status}, null, null, null, {"mRender": price_status}, {"mRender": discount_status}, {"mRender": grand_status}],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total_qty = 0;
                var total_price = 0;
                var total_dis = 0;
                var total_g = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total_qty += parseFloat(aaData[aiDisplay[i]][5]);
                    total_price += parseFloat(aaData[aiDisplay[i]][6]);
                    total_dis += parseFloat(aaData[aiDisplay[i]][7]);
                    total_g += parseFloat(aaData[aiDisplay[i]][8]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[5].innerHTML = '<b>' + parseFloat(total_qty) + '</b>';
                nCells[6].innerHTML = '<b>' + parseFloat(total_price) + '</b>';
                nCells[7].innerHTML = '<b>' + parseFloat(total_dis) + '</b>';
                nCells[8].innerHTML = '<b>' + parseFloat(total_g) + '</b>';
            }
        }).fnSetFilteringDelay().dtFilter([
            {
                column_number: 0,
                filter_default_label: "[<?=lang('date');?> (dd/mm/yyyy)]",
                filter_type: "text",
                data: []
            },
            {column_number: 1, filter_default_label: "[<?=lang('Full_Name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Order_For');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Menu_Type');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Item');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-money"></i><?= lang('Order_Details_Report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            } ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open("reports/order_transaction_report"); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("Customer_Name"); ?></label>
                                <?php
                                $us[""] = lang('select') . ' ' . lang('Customer');
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . " " . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control date" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                                class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>


                <div class="table-responsive">
                    <table id="PayRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">

                        <thead>
                        <tr>
                            <th><?= lang("Order_Date"); ?></th>
                            <th><?= lang("Full_Name"); ?></th>
                            <th><?= lang("Order_For"); ?></th>
                            <th><?= lang("Menu_Type"); ?></th>
                            <th><?= lang("Item"); ?></th>
                            <th><?= lang("Quantity"); ?></th>
                            <th><?= lang("Total"); ?></th>
                            <th><?= lang("Discount"); ?></th>
                            <th><?= lang("Grand_Total"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getOrderTransactionReport/pdf/?v=1' . $v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getOrderTransactionReport/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
