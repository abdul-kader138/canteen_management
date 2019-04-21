<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= $assets; ?>js/hc/highcharts.js"></script>
<script type="text/javascript">


    $(function () {
        Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
            return {
                radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
            };
        });
        $('#chart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {text: ''},
            credits: {enabled: false},
            tooltip: {
                formatter: function () {
                    return '<div class="tooltip-inner hc-tip" style="margin-bottom:0;">' + this.key + '<br><strong>' + currencyFormat(this.y) + '</strong> (' + formatNumber(this.percentage) + '%)';
                },
                followPointer: true,
                useHTML: true,
                borderWidth: 0,
                shadow: false,
                valueDecimals: site.settings.decimals,
                style: {fontSize: '14px', padding: '0', color: '#000000'}
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        formatter: function () {
                            return '<h3 style="margin:-15px 0 0 0;"><b>' + this.point.name + '</b>:<br><b> ' + currencyFormat(this.y) + '</b></h3>';
                        },
                        useHTML: true
                    }
                }
            },
            series: [{
                type: 'pie',
                name: '<?php echo $this->lang->line("Order_Details"); ?>',
                data: [
                    ['<?php echo $this->lang->line("Price_Without_Discount"); ?>', <?php echo ($totals->price ? $totals->price : 0); ?>],
                    ['<?php echo $this->lang->line("Total_Price"); ?>', <?php echo ($totals->total_price ? $totals->total_price : 0); ?>],
                    ['<?php echo $this->lang->line("Total_Discount"); ?>', <?php echo ($totals->d_price ? $totals->d_price : 0); ?>],
                ]

            }]
        });

    });
</script>
<?php
function row_status($x)
{
    if ($x == null) {
        return '';
    } elseif ($x == 'pending') {
        return '<div class="text-center"><span class="label label-warning">' . lang($x) . '</span></div>';
    } elseif ($x == 'completed' || $x == 'paid' || $x == 'sent' || $x == 'received') {
        return '<div class="text-center"><span class="label label-success">' . lang($x) . '</span></div>';
    } elseif ($x == 'partial' || $x == 'transferring') {
        return '<div class="text-center"><span class="label label-info">' . lang($x) . '</span></div>';
    } elseif ($x == 'due') {
        return '<div class="text-center"><span class="label label-danger">' . lang($x) . '</span></div>';
    } else {
        return '<div class="text-center"><span class="label label-default">' . lang($x) . '</span></div>';
    }
}

?>
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa fa-th"></i><span class="break"></span><?= lang('quick_links') ?>
                </h2>
            </div>
            <div class="box-content">
                <?php if ($Owner || $Admin || $GP['products-index']) { ?>
                    <div class="col-lg-2 col-md-2 col-xs-12">
                        <a class="bblue white quick-button small" href="<?= admin_url('products') ?>">
                            <i class="fa fa-barcode"></i>
                            <p><?= lang('products') ?></p>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($Owner || $Admin || $GP['meal-index']) { ?>
                    <div class="col-lg-2 col-md-2 col-xs-6">
                        <a class="bpink white quick-button small" href="<?= admin_url('meal') ?>">
                            <i class="fa fa-cutlery"></i>
                            <p><?= lang('Order_Details') ?></p>
                        </a>
                    </div>
                <?php } ?>

                <?php if ($Owner || $Admin || $GP['calendar-index']) { ?>
                    <div class="col-lg-2 col-md-2 col-xs-6">
                        <a class="bgrey white quick-button small" href="<?= admin_url('calendar') ?>">
                            <i class="fa fa-calendar"></i>
                            <p><?= lang('Menu_Details') ?></p>
                        </a>
                    </div>
                <?php } ?>
                <?php if ($Owner || $Admin || $GP['reports-order_summary_report']) { ?>
                    <div class="col-lg-2 col-md-2 col-xs-6">
                        <a class="blightOrange white quick-button small"
                           href="<?= admin_url('reports/order_summary_report') ?>">
                            <i class="fa fa-th"></i>
                            <p><?= lang('Reports') ?></p>
                        </a>
                    </div>
                <?php } ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<div class="box" style="margin-top: 15px;">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('Food_Order_Summary'); ?></p>
                <?php if ($totals) { ?>

                    <div class="small-box padding1010 col-sm-6 bblue">
                        <div class="inner clearfix">
                            <a>
                                <h3><?= $this->sma->formatQuantity($totals->total_quantity) ?></h3>

                                <p><?= lang('Total_Quantity') ?></p>
                            </a>
                        </div>
                    </div>

                    <div class="small-box padding1010 col-sm-6 bdarkGreen">
                        <div class="inner clearfix">
                            <a>
                                <h3><?= $this->sma->formatQuantity($totals->price) ?></h3>

                                <p><?= lang('Total_Price') ?></p>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix" style="margin-top:20px;"></div>
                <?php } ?>
                <div id="chart" style="width:100%; height:450px;"></div>
            </div>
        </div>
    </div>
</div>
