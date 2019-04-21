<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("Food_Order_Details") . " " . $date; ?></title>
    <link href="<?php echo $assets ?>styles/style.css" rel="stylesheet">
    <style type="text/css">
        html, body {
            height: 100%;
            background: #FFF;
        }
        body:before, body:after {
            display: none !important;
        }
        .table th {
            text-align: center;
            padding: 5px;
        }
        .table td {
            padding: 4px;
        }

        table.table-bordered{
            border:1px solid #000000;
            margin-top:20px;
        }
        table.table-bordered > thead > tr > th{
            border:1px solid #000000;
        }
        table.table-bordered > tbody > tr > td{
            border:1px solid #000000;
        }
    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-file"></i><?= lang("Food_Order_Details"); ?></h2>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped print-table order-table">
                                <thead>
                                <tr>
                                    <th><?= lang("no"); ?></th>
                                    <th><?= lang("Date");?></th>
                                    <th><?= lang("ID");?></th>
                                    <th><?= lang("Menu");?></th>
                                    <th><?= lang("Details");?></th>
                                    <th><?= lang("Qty"); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $r = 1;
                                $col = 5;
                                $total = 0;
                                $total_dues = 0;
                                $usage = 0;
                                foreach ($rows as $row):
                                    $dues=0; ?>
                                    <tr>
                                        <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                        <td style="vertical-align:middle;"><?= $row->order_date; ?></td>
                                        <td style="vertical-align:middle;"><?= $row->username; ?></td>
                                        <td style="vertical-align:middle;">  <?= $row->title; ?></td>
                                        <td style="vertical-align:middle;">  <?= $row->product_name; ?></td>
                                        <td style="vertical-align:middle;">   <?= $row->qty; ?></td>
                                    </tr>
                                    <?php
                                    $total = $total + $row->qty;
                                    $r++;
                                endforeach;
                                ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="<?= $col; ?>"
                                        style="text-align:right; font-weight:bold;"><?= lang("Total_Qty"); ?>
                                    </td>
                                    <td style="text-align:right; padding-right:10px; font-weight:bold;">
                                        <?= $this->sma->formatMoney($total); ?>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>