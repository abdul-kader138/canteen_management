<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";
$o = "";
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
    $var = $this->input->post('start_date');
    $date_v = str_replace('/', '-', $var);
    $o_date = date('Y-m-d', strtotime($date_v));
    $o .= strtotime($o_date);
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
?>
<script>
    $(document).ready(function () {

        var pb = <?= json_encode($pb); ?>;

        oTable = $('#PayRData').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getOrderMenuWiseReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, null, null, null],
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var total_qty = 0;
                for (var i = 0; i < aaData.length; i++) {
                    total_qty += parseFloat(aaData[aiDisplay[i]][3]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = '<b>' + parseFloat(total_qty) + '</b>';
            }
        }).fnSetFilteringDelay().dtFilter([
            {
                column_number: 0,
                filter_default_label: "[<?=lang('date');?> (yyyy-mm-dd)]",
                filter_type: "text",
                data: []
            },
            {column_number: 1, filter_default_label: "[<?=lang('Title');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Order_Date');?>]", filter_type: "text", data: []},
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
        <h2 class="blue"><i class="fa-fw fa fa-money"></i><?= lang('Order_Report_(Menu_Wise)'); ?> <?php
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
                    <a href="<?= admin_url('reports/order_pdf/' . $o) ?>" id="xls11" class="tip"
                       title="<?= lang('Download_PDF') ?>">
                        <i class="icon fa fa-download"></i>
                    </a>
                </li>
            </ul>
    </div>
</div>
<div class="box-content">
    <div class="row">
        <div class="col-lg-12">
            <div id="form">
                <?php echo admin_form_open("reports/order_report_menu_wise"); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= lang("start_date", "start_date"); ?>
                            <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control date" id="start_date"'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
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
                        <th><?= lang("Title"); ?></th>
                        <th><?= lang("Details"); ?></th>
                        <th><?= lang("Quantity"); ?></th>
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
            window.location.href = "<?=admin_url('reports/order_pdf/?v=1' . $o)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getOrderMenuWiseReport/0/xls/?v=1' . $v)?>";
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
