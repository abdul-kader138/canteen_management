<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href='<?= $assets ?>fullcalendar/css/fullcalendar.min.css' rel='stylesheet' />
<link href='<?= $assets ?>fullcalendar/css/fullcalendar.print.css' rel='stylesheet' media='print' />
<link href="<?= $assets ?>fullcalendar/css/bootstrap-colorpicker.min.css" rel="stylesheet" />

<style>
    .fc th {
        padding: 10px 0px;
        vertical-align: middle;
        background:#F2F2F2;
        width: 14.285%;
    }
    .fc-content {
        cursor: pointer;
    }
    .fc-day-grid-event>.fc-content {
        padding: 4px;
    }

    .fc .fc-center {
        margin-top: 5px;
    }
    .error {
        color: #ac2925;
        margin-bottom: 15px;
    }
    .event-tooltip {
        width:150px;
        background: rgba(0, 0, 0, 0.85);
        color:#FFF;
        padding:10px;
        position:absolute;
        z-index:10001;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-calendar"></i><?= lang('Menu_Calendar'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang("calendar_line") ?></p>

                <div id='calendar'></div>

                <div class="modal fade cal_modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    <i class="fa fa-2x">&times;</i>
                                </button>
                                <h4 class="modal-title">Add Menu</h4>
                            </div>
                            <div class="modal-body">
                                <div class="error"></div>
                                <form>
                                    <input type="hidden" value="" name="eid" id="eid">
                                    <div class="form-group">
                                        <?= lang('title', 'title'); ?>
                                        <?= form_input('title', set_value('title'), 'class="form-control tip" id="title" required="required"'); ?>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?= lang('Date', 'Date'); ?>
                                                <?= form_input('start', set_value('start'), 'class="form-control" id="start" readonly="readonly" required="required"'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <?= lang('event_color', 'color'); ?>
                                            <div class="input-group">
                                                <span class="input-group-addon" id="event-color-addon" style="width:2em;"></span>
                                                <input id="color" name="color" type="text" class="form-control input-md" readonly="readonly" />
                                            </div>
                                        </div>
                                        </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <?= lang('Menu', 'Menu'); ?>
                                                <?php
                                                $cat[''] = "";
                                                foreach ($products as $product) {
                                                    $cat[$product->id] = $product->name.' ('.round($product->price,2).' Tk)';
                                                }
                                                echo form_dropdown('product_id', $cat, (isset($_POST['product_id']) ? $_POST['product_id'] :''), 'class="form-control select product_id" id="product_id" placeholder="' . lang("select") . " " . lang("product") . '" required="required" style="width:100%"')
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <?= lang('description', 'description'); ?>
                                        <textarea class="form-control skip" id="description" name="description"></textarea>
                                    </div>

                                </form>
                            </div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    var currentLangCode = '<?= $cal_lang; ?>', moment_df = '<?= strtoupper($dateFormats['js_sdate']); ?>', cal_lang = {},
    tkname = "<?=$this->security->get_csrf_token_name()?>", tkvalue = "<?=$this->security->get_csrf_hash()?>";
    cal_lang['add_event'] = '<?= lang('Add_Menu'); ?>';
    cal_lang['edit_event'] = '<?= lang('Edit_Menu'); ?>';
    cal_lang['delete'] = '<?= lang('Delete_Menu'); ?>';
    cal_lang['event_error'] = '<?= lang('event_error'); ?>';
</script>
<script src='<?= $assets ?>fullcalendar/js/moment.min.js'></script>
<script src="<?= $assets ?>fullcalendar/js/fullcalendar.js"></script>
<script src="<?= $assets ?>fullcalendar/js/lang-all.js"></script>
<script src='<?= $assets ?>fullcalendar/js/bootstrap-colorpicker.min.js'></script>
<script src='<?= $assets ?>fullcalendar/js/main.js'></script>
