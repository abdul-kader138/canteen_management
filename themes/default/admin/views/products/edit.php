<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!empty($variants)) {
    foreach ($variants as $variant) {
        $vars[] = addslashes($variant->name);
    }
} else {
    $vars = array();
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'products');
        });
        $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
            placeholder: "<?= lang('select_category_to_load') ?>", data: [
                {id: '', text: '<?= lang('select_category_to_load') ?>'}
            ]
        });
        $('#category').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= admin_url('products/getSubCategories') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            scdata.push({id: '', text: '<?= lang('select_subcategory') ?>'});
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                                placeholder: "<?= lang('select_category_to_load') ?>",
                                minimumResultsForSearch: 7,
                                data: scdata
                            });
                        } else {
                            $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                                placeholder: "<?= lang('no_subcategory') ?>",
                                minimumResultsForSearch: 7,
                                data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                            });
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_category_to_load') ?>").select2({
                    placeholder: "<?= lang('select_category_to_load') ?>",
                    minimumResultsForSearch: 7,
                    data: [{id: '', text: '<?= lang('select_category_to_load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
        $('#code').bind('keypress', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-edit"></i><?= lang('edit_product'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('update_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("products/edit/" . $product->id, $attrib)
                ?>
                <div class="col-md-5">
                    <div class="form-group">
                        <?= lang("product_type", "type") ?>
                        <?php
                        $opts = array('service' => lang('service'));
                        echo form_dropdown('type', $opts, (isset($_POST['type']) ? $_POST['type'] : ($product ? $product->type : '')), 'class="form-control" id="type" required="required"');
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_name", "name") ?>
                        <?= form_input('name', (isset($_POST['name']) ? $_POST['name'] : ($product ? $product->name : '')), 'class="form-control gen_slug" id="name" required="required"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_code", "code") ?>
                        <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($product ? $product->code : '')), 'class="form-control" id="code"  required="required"') ?>
                        <span class="help-block"><?= lang('you_scan_your_barcode_too') ?></span>
                    </div>

                    <div class="form-group all">
                        <?= lang("category", "category") ?>
                        <?php
                        $cat[''] = "";
                        foreach ($categories as $category) {
                            $cat[$category->id] = $category->name;
                        }
                        echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ($product ? $product->category_id : '')), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("subcategory", "subcategory") ?>
                        <div class="controls" id="subcat_data"> <?php
                            echo form_input('subcategory', ($product ? $product->subcategory_id : ''), 'class="form-control" id="subcategory"  placeholder="' . lang("select_category_to_load") . '"');
                            ?>
                        </div>
                    </div>

                    <div class="form-group all">
                        <?= lang("barcode_symbology", "barcode_symbology") ?>
                        <?php
                        $bs = array('code25' => 'Code25', 'code39' => 'Code39', 'code128' => 'Code128', 'ean8' => 'EAN8', 'ean13' => 'EAN13', 'upca' => 'UPC-A', 'upce' => 'UPC-E');
                        echo form_dropdown('barcode_symbology', $bs, (isset($_POST['barcode_symbology']) ? $_POST['barcode_symbology'] : ($product ? $product->barcode_symbology : 'code128')), 'class="form-control select" id="barcode_symbology" required="required" style="width:100%;"');
                        ?>
                    </div>
                    <div class="form-group all">
                        <?= lang('product_unit', 'unit'); ?>
                        <?php
                        $pu[''] = lang('select').' '.lang('unit');
                        foreach ($base_units as $bu) {
                            $pu[$bu->id] = $bu->name .' ('.$bu->code.')';
                        }
                        ?>
                        <?= form_dropdown('unit', $pu, set_value('unit', $product->unit), 'class="form-control tip" required="required" id="unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group standard">
                        <?= lang('default_purchase_unit', 'default_purchase_unit'); ?>
                        <?= form_dropdown('default_purchase_unit', $uopts, $product->purchase_unit, 'class="form-control" id="default_purchase_unit" style="width:100%;"'); ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_cost", "cost") ?>
                        <?= form_input('cost', (isset($_POST['cost']) ? $_POST['cost'] : ($product ? $this->sma->formatDecimal($product->cost) : '')), 'class="form-control tip" id="cost" required="required"') ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("product_price", "price") ?>
                        <?= form_input('price', (isset($_POST['price']) ? $_POST['price'] : ($product ? $this->sma->formatDecimal($product->price) : '')), 'class="form-control tip" id="price" required="required"') ?>
                    </div>
                    <div class="form-group all">
                        <?= lang("Discount_Amount", "Discount_Amount") ?>
                        <?= form_input('discount_amount', (isset($_POST['discount_amount']) ? $_POST['discount_amount'] : $product->discount_amount), 'class="form-control tip" id="discount_amount" ') ?>
                    </div>



                    <div class="form-group all">
                        <?= lang("product_image", "product_image") ?>
                        <input id="product_image" type="file" data-browse-label="<?= lang('browse'); ?>" name="product_image" data-show-upload="false"
                               data-show-preview="false" accept="image/*" class="form-control file">
                    </div>

                    <div class="form-group all">
                        <?= lang("product_gallery_images", "images") ?>
                        <input id="images" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile[]" multiple="true" data-show-upload="false"
                               data-show-preview="false" class="form-control file" accept="image/*">
                    </div>
                    <div id="img-details"></div>
                </div>

                <div class="col-md-12">

                    <div class="form-group all">
                        <?= lang("product_details", "product_details") ?>
                        <?= form_textarea('product_details', (isset($_POST['product_details']) ? $_POST['product_details'] : ($product ? $product->product_details : '')), 'class="form-control" id="details"'); ?>
                    </div>

                    <div class="form-group">
                        <?php echo form_submit('edit_product', $this->lang->line("edit_product"), 'class="btn btn-primary"'); ?>
                    </div>

                </div>
                <?= form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('form[data-toggle="validator"]').bootstrapValidator({ excluded: [':disabled'] });
        var audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3');
        var audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
        var items = {};
        <?php
        if($combo_items) {
            echo '
                var ci = '.json_encode($combo_items).';
                $.each(ci, function() { add_product_item(this); });
                ';
        }
        ?>
        <?=isset($_POST['cf']) ? '$("#extras").iCheck("check");': '' ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });

        <?= isset($_POST['promotion']) || $product->promotion ? '$("#promotion").iCheck("check");': '' ?>
        $('#promotion').on('ifChecked', function (e) {
            $('#promo').slideDown();
        });
        $('#promotion').on('ifUnchecked', function (e) {
            $('#promo').slideUp();
        });

        $('.attributes').on('ifChecked', function (event) {
            $('#options_' + $(this).attr('id')).slideDown();
        });
        $('.attributes').on('ifUnchecked', function (event) {
            $('#options_' + $(this).attr('id')).slideUp();
        });

        //$('#cost').removeAttr('required');
        $('#type').change(function () {
            var t = $(this).val();
            if (t !== 'standard') {
                $('.standard').slideUp();
                // $('#unit').attr('disabled', true);
                // $('#cost').attr('disabled', true);
            } else {
                $('.standard').slideDown();
                $('#unit').attr('disabled', false);
                $('#cost').attr('disabled', false);
            }
            if (t !== 'digital') {
                $('.digital').slideUp();
            } else {
                $('.digital').slideDown();
            }
            if (t !== 'combo') {
                $('.combo').slideUp();
            } else {
                $('.combo').slideDown();
            }
            if (t == 'standard' || t == 'combo') {
                $('.standard_combo').slideDown();
            } else {
                $('.standard_combo').slideUp();
            }
        });

        $("#add_item").autocomplete({
            source: '<?= admin_url('products/suggestions'); ?>',
            minLength: 1,
            autoFocus: false,
            delay: 5,
            response: function (event, ui) {
                if ($(this).val().length >= 16 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');
                }
                else if (ui.content.length == 1 && ui.content[0].id != 0) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                }
                else if (ui.content.length == 1 && ui.content[0].id == 0) {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>', function () {
                        $('#add_item').focus();
                    });
                    $(this).val('');

                }
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item.id !== 0) {
                    var row = add_product_item(ui.item);
                    if (row) {
                        $(this).val('');
                        $('#add_item').removeAttr('required');
                        $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
                    }
                } else {
                    //audio_error.play();
                    bootbox.alert('<?= lang('no_product_found') ?>');
                }
            }
        });
        $('#add_item').removeAttr('required');
        $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');

        function add_product_item(item) {
            if (item == null) {
                return false;
            }
            item_id = item.id;
            if (items[item_id]) {
                items[item_id].qty = (parseFloat(items[item_id].qty) + 1).toFixed(2);
            } else {
                items[item_id] = item;
            }

            $("#prTable tbody").empty();
            $.each(items, function () {
                var row_no = this.id;
                var newTr = $('<tr id="row_' + row_no + '" class="item_' + this.id + '"></tr>');
                tr_html = '<td><input name="combo_item_id[]" type="hidden" value="' + this.id + '"><input name="combo_item_name[]" type="hidden" value="' + this.name + '"><input name="combo_item_code[]" type="hidden" value="' + this.code + '"><span id="name_' + row_no + '">' + this.code + ' - ' + this.name + '</span></td>';
                tr_html += '<td><input class="form-control text-center rquantity" name="combo_item_quantity[]" type="text" value="' + formatQuantity2(this.qty) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="quantity_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td><input class="form-control text-center rprice" name="combo_item_price[]" type="text" value="' + formatDecimal(this.price) + '" data-id="' + row_no + '" data-item="' + this.id + '" id="combo_item_price_' + row_no + '" onClick="this.select();"></td>';
                tr_html += '<td class="text-center"><i class="fa fa-times tip del" id="' + row_no + '" title="Remove" style="cursor:pointer;"></i></td>';
                newTr.html(tr_html);
                newTr.prependTo("#prTable");
            });
            $('.item_' + item_id).addClass('warning');
            //audio_success.play();
            return true;

        }

        function calculate_price() {
            var rows = $('#prTable').children('tbody').children('tr');
            var pp = 0;
            $.each(rows, function () {
                pp += formatDecimal(parseFloat($(this).find('.rprice').val())*parseFloat($(this).find('.rquantity').val()));
            });
            $('#price').val(pp);
            return true;
        }

        $(document).on('change', '.rquantity, .rprice', function () {
            calculate_price();
        });

        $(document).on('click', '.del', function () {
            var id = $(this).attr('id');
            delete items[id];
            $(this).closest('#row_' + id).remove();
            calculate_price();
        });

        var su = 2;
        $('#addSupplier').click(function () {
            if (su <= 5) {
                $('#supplier_1').select2('destroy');
                $('#supplier_1').select2('destroy');
                var html = '<div style="clear:both;height:5px;"></div><div class="row"><div class="col-xs-12"><div class="form-group"><input type="hidden" name="supplier_' + su + '", class="form-control" id="supplier_' + su + '" placeholder="<?= lang("select") . ' ' . lang("supplier") ?>" style="width:100%;display: block !important;" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_part_no" class="form-control tip" id="supplier_' + su + '_part_no" placeholder="<?= lang('supplier_part_no') ?>" /></div></div><div class="col-xs-6"><div class="form-group"><input type="text" name="supplier_' + su + '_price" class="form-control tip" id="supplier_' + su + '_price" placeholder="<?= lang('supplier_price') ?>" /></div></div></div>';
                $('#ex-suppliers').append(html);
                var sup = $('#supplier_' + su);
                suppliers(sup);
                su++;
            } else {
                bootbox.alert('<?= lang('max_reached') ?>');
                return false;
            }
        });

        var _URL = window.URL || window.webkitURL;
        $("input#images").on('change.bs.fileinput', function () {
            var ele = document.getElementById($(this).attr('id'));
            var result = ele.files;
            $('#img-details').empty();
            for (var x = 0; x < result.length; x++) {
                var fle = result[x];
                for (var i = 0; i <= result.length; i++) {
                    var img = new Image();
                    img.onload = (function (value) {
                        return function () {
                            ctx[value].drawImage(result[value], 0, 0);
                        }
                    })(i);

                    img.src = 'images/' + result[i];
                }
            }
        });
        var variants = <?=json_encode($vars);?>;
        $(".select-tags").select2({
            tags: variants,
            tokenSeparators: [","],
            multiple: true
        });
        $(document).on('ifChecked', '#attributes', function (e) {
            $('#attr-con').slideDown();
        });
        $(document).on('ifUnchecked', '#attributes', function (e) {
            $(".select-tags").select2("val", "");
            $('.attr-remove-all').trigger('click');
            $('#attr-con').slideUp();
        });
        $('#addAttributes').click(function (e) {
            e.preventDefault();
            var attrs_val = $('#attributesInput').val(), attrs;
            attrs = attrs_val.split(',');
            for (var i in attrs) {
                if (attrs[i] !== '') {
                    $('#attrTable').show().append('<tr class="attr"><td><input type="hidden" name="attr_name[]" value="' + attrs[i] + '"><span>' + attrs[i] + '</span></td><td class="code text-center"><input type="hidden" name="attr_warehouse[]" value=""><span></span></td><td class="quantity text-center"><input type="hidden" name="attr_quantity[]" value="0"><span></span></td><td class="price text-right"><input type="hidden" name="attr_price[]" value="0"><span>0</span></span></td><td class="text-center"><i class="fa fa-times delAttr"></i></td></tr>');
                }
            }
        });
        $(document).on('click', '.delAttr', function () {
            $(this).closest("tr").remove();
        });
        $(document).on('click', '.attr-remove-all', function () {
            $('#attrTable tbody').empty();
            $('#attrTable').hide();
        });
        var row, warehouses = <?= json_encode($warehouses); ?>;
        $(document).on('click', '.attr td:not(:last-child)', function () {
            row = $(this).closest("tr");
            $('#aModalLabel').text(row.children().eq(0).find('span').text());
            $('#awarehouse').select2("val", (row.children().eq(1).find('input').val()));
            $('#aquantity').val(row.children().eq(2).find('span').text());
            $('#aprice').val(row.children().eq(3).find('span').text());
            $('#aModal').appendTo('body').modal('show');
        });

        $(document).on('click', '#updateAttr', function () {
            var wh = $('#awarehouse').val(), wh_name;
            $.each(warehouses, function () {
                if (this.id == wh) {
                    wh_name = this.name;
                }
            });
            row.children().eq(1).html('<input type="hidden" name="attr_warehouse[]" value="' + wh + '"><input type="hidden" name="attr_wh_name[]" value="' + wh_name + '"><span>' + wh_name + '</span>');
            row.children().eq(2).html('<input type="hidden" name="attr_quantity[]" value="' + ($('#aquantity').val() ? $('#aquantity').val() : 0) + '"><span>' + $('#aquantity').val() + '</span>');
            row.children().eq(3).html('<input type="hidden" name="attr_price[]" value="' + $('#aprice').val() + '"><span>' + currencyFormat($('#aprice').val()) + '</span>');

            $('#aModal').modal('hide');
        });
    });

    <?php if ($product) { ?>
    $(document).ready(function () {
        $('#enable_wh').click(function () {
            var whs = $('.wh');
            $.each(whs, function () {
                $(this).val($('#v' + $(this).attr('id')).val());
            });
            $('#warehouse_quantity').val(1);
            $('.wh').attr('disabled', false);
            $('#show_wh_edit').slideDown();
        });
        $('#disable_wh').click(function () {
            $('#warehouse_quantity').val(0);
            $('#show_wh_edit').slideUp();
        });
        $('#show_wh_edit').hide();
        $('.wh').attr('disabled', true);
        var t = "<?=$product->type?>";
        if (t !== 'standard') {
            $('.standard').slideUp();
            // $('#unit').attr('disabled', true);
            // $('#cost').attr('disabled', true);
        } else {
            $('.standard').slideDown();
            $('#unit').attr('disabled', false);
            $('#cost').attr('disabled', false);
        }
        if (t !== 'digital') {
            $('.digital').slideUp();
        } else {
            $('.digital').slideDown();
        }
        if (t !== 'combo') {
            $('.combo').slideUp();
        } else {
            $('.combo').slideDown();
        }
        if (t == 'standard' || t == 'combo') {
            $('.standard_combo').slideDown();
        } else {
            $('.standard_combo').slideUp();
        }
        $('#add_item').removeAttr('required');
        $('form[data-toggle="validator"]').bootstrapValidator('removeField', 'add_item');
        //$("#code").parent('.form-group').addClass("has-error");
        //$("#code").focus();
        $("#product_image").parent('.form-group').addClass("text-warning");
        $("#images").parent('.form-group').addClass("text-warning");
        $.ajax({
            type: "get", async: false,
            url: "<?= admin_url('products/getSubCategories') ?>/" + <?= $product->category_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('select_subcategory') ?>").select2({
                        placeholder: "<?= lang('select_category_to_load') ?>",
                        minimumResultsForSearch: 7,
                        data: scdata
                    });
                } else {
                    $("#subcategory").select2("destroy").empty().attr("placeholder", "<?= lang('no_subcategory') ?>").select2({
                        placeholder: "<?= lang('no_subcategory') ?>",
                        minimumResultsForSearch: 7,
                        data: [{id: '', text: '<?= lang('no_subcategory') ?>'}]
                    });
                }
            }
        });
    });
    <?php } ?>
</script>

