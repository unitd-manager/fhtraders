Util.createCPObject('cpm.tradingsg.supplier');

var poCodeChecked = [];

cpm.tradingsg.supplier = {
    init: function(){
        $('#createLogin').live('click', function (e){
                var title = "Create Login";
                var supplier_id = $(this).attr('supplier_id');
                var email = $(this).attr('email');
                e.preventDefault();
                var expObj = {
                    validate: true
                   ,callbackOnSuccess: function(){
                        var msg = 'Login Created Successfully';
                        Util.alert(msg, function(){
                            Util.closeAllDialogs();
                            window.location.reload(true);
                        });
                    }
                }
            Util.openFormInDialog.call(this, 'createLoginForm', title, 450, 350, expObj);
        });

        $('#generatePO').live('click', function (e){
                var title = "Make Supplier Payment";
                //var supplier_id = $(this).attr('supplier_id');
                e.preventDefault();
                var supplier_id = $('#record_id').val();
                var expObj = {
                    validate: true
                   ,callbackOnSuccess: function(){
                        var msg = 'Supplier Payment done Successfully';
                        Util.alert(msg, function(){
                            Util.closeAllDialogs();
                            window.location.reload(true);
                        });
                    }
                }
            Util.openFormInDialog.call(this, 'portalForm', title, 450, 500, expObj);
        });

        $('#generateAdvancePayment').live('click', function (e){
                var title = "Advance Payment";
                //var supplier_id = $(this).attr('supplier_id');
                e.preventDefault();
                var supplier_id = $('#record_id').val();
                var expObj = {
                    validate: true
                   ,callbackOnSuccess: function(){
                        var msg = 'Advance Payment done Successfully';
                        Util.alert(msg, function(){
                            Util.closeAllDialogs();
                            window.location.reload(true);
                        });
                    }
                }
            Util.openFormInDialog.call(this, 'portalForm', title, 450, 250, expObj);
        });

        $('.m-tradingsg_supplier input.poCode').livequery('click', function (e){
            Util.showProgressInd();
            po_code = $(this).val();
            var purchase_order_id = $(this).attr('purchase_order_id');
            var checked    = $(this).attr('checked') ? 'checked' : '';
            var checkedVal = checked == 'checked' ? 1 : 0;

            var url = 'index.php?_topRm=inventory&module=tradingsg_supplier&_spAction=populatePOAmount&showHTML=0';
            $.get(url,{po_code: po_code, purchase_order_id: purchase_order_id, checkedVal: checkedVal}, function(html){
                //$('div#fld_amount').html(html);
                $('input[name=amount]').val(html);
                $('input[name=totalAmountPo]').val(html);
                Util.hideProgressInd();
            });
        });

        $('.m-tradingsg_supplier input.advanceAmount').livequery('click', function (e){
            advance_amount = $(this).val();
            var checked    = $(this).attr('checked') ? 'checked' : '';
            var checkedVal = checked == 'checked' ? 1 : 0;
            if(checkedVal == 1){
                $('input[name=advance_amount]').show();
                $('input[name=advanceAmountchk]').val(1);
            } else {
                $('input[name=advance_amount]').hide();                
                $('input[name=advanceAmountchk]').val(0);
            }
        });

        $(".receiptViewHistory").live('click', function (e){
            var purchase_order_id = $(this).attr('purchase_order_id');
            e.preventDefault();

            var expObj = {
                beforeCloseFn: function(){
                    Util.closeAllDialogs();
                }
            }
            Util.openDialogForLink.call(this, 'Supplier Payment History', 700, 500, expObj);
        });

        $('.click-all-top .check-all').livequery('click', function(e){
            e.preventDefault();
            cpm.tradingsg.supplier.checkAllCol.call(this);
        });

        $('.click-all-top .uncheck-all').livequery('click', function(e){
            e.preventDefault();
            cpm.tradingsg.supplier.uncheckAllCol.call(this);
        });

        /* Filtering month with respect to values chosen */
        $('#purchaseordermonthfilter select[name=month]').livequery('change', function(){
            var month = $(this).val();
            var supplier_id = $("input[name='supplier_id']").val();
            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=addPurchaseOrderDetail&showHTML=0';
            Util.showProgressInd();
            $.get(url,{month: month, supplier_id:supplier_id}, function(html){
                $('#purchaseordermonthfilter tbody').html(html);
                Util.hideProgressInd();
            });
        });

        /* Filtering month with respect to values chosen */
        $('#purchaseordermonthfilter select[name=site]').livequery('change', function(){
            var site_id = $(this).val();
            var month   = $('#purchaseordermonthfilter select[name=month]').val();
            var supplier_id = $("input[name='supplier_id']").val();
            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=addPurchaseOrderDetail&showHTML=0';
            Util.showProgressInd();
            $.get(url,{month:month, site_id: site_id, supplier_id:supplier_id}, function(html){
                $('#purchaseordermonthfilter tbody').html(html);
                Util.hideProgressInd();
            });
        });

        /* Filtering month with respect to values chosen */
        $('#supplierpaymentmonthfilter select[name=month]').livequery('change', function(){
            var month = $(this).val();
            var supplier_id = $("input[name='supplier_id']").val();
            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=supplierPaymentDetail&showHTML=0';
            Util.showProgressInd();
            $.get(url,{month: month, supplier_id:supplier_id}, function(html){
                $('#supplierpaymentmonthfilter table.renewallist > tbody').html(html);
                Util.hideProgressInd();
            });
        });

        /* Filtering month with respect to values chosen */
        $('#investigationMonthfilter select[name=month]').livequery('change', function(){
            var month = $(this).val();
            var supplier_id = $("input[name='supplier_id']").val();
            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=medTestVisitPortalDetail&showHTML=0';
            Util.showProgressInd();
            $.get(url,{month: month, supplier_id:supplier_id}, function(html){
                $('#investigationMonthfilter tbody').html(html);
                Util.hideProgressInd();
            });
        });

        /* Filtering month with respect to values chosen */
        $('#investigationMonthfilter select[name=site]').livequery('change', function(){
            var site_id = $(this).val();
            var month   = $('#investigationMonthfilter select[name=month]').val();
            var supplier_id = $("input[name='supplier_id']").val();
            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=medTestVisitPortalDetail&showHTML=0';
            Util.showProgressInd();
            $.get(url,{month:month, site_id: site_id, supplier_id:supplier_id}, function(html){
                $('#investigationMonthfilter tbody').html(html);
                Util.hideProgressInd();
            });
        });

        $('.cancelSupplierReceipt').livequery('click', function (e){
            msg = "Do you like to cancel the Receipt?";
            if (!confirm(msg)){
                return false;
            }
            else {
                var url = 'index.php?module=tradingsg_supplier&_spAction=cancelSupplierReceipt&showHTML=0';
                Util.showProgressInd();
                var supplier_receipt_id = $(this).attr('supplier_receipt_id');
                var purchase_order_id = $(this).attr('purchase_order_id');
                var supplier_id = $(this).attr('supplier_id');
                $.get(url,{supplier_receipt_id: supplier_receipt_id, purchase_order_id:purchase_order_id}, function(html){
                    alert ('Receipt Cancelled Succesfully');
                    Util.hideProgressInd();
                    Util.closeAllDialogs();
                    window.location.reload(true);
                    //cpm.tradingsg.supplier.reloadPurchaseOrderDetail(supplier_id);
                });
            }
        });
    },

    reloadPurchaseOrderDetail: function(supplier_id){
        var url = 'index.php?module=tradingsg_supplier&_spAction=addPurchaseOrder&showHTML=0';

        $.get(url,{supplier_id:supplier_id}, function(html){
            $('#renewalLinkPortal').html(html);
        });
    },

    checkAllCol: function(e){
        var colPo = $(this).parent().index();
        Util.showProgressInd();
        $('.room-poCode-table tbody tr').each(function(rowIndex, trObj) {
            var checkbox          = $(trObj).find('td:eq(' + colPo + ') input');
            var po_code           = checkbox.val();
            checkbox.attr('checked', 'checked');
            var checked           = checkbox.attr('checked') ? 'checked' : '';
            var checkedVal        = checked == 'checked' ? 1 : 0;
            var purchase_order_id = $(checkbox).attr('purchase_order_id');
            var supplier_id       = $("input[name='supplier_id']").val();
            
            /*if(is_checked == 1){
                poCodeChecked.push(checkbox.val());
            }*/

            var url = 'index.php?_topRm=pharmacy&module=tradingsg_supplier&_spAction=populatePOAmount&showHTML=0';
            $.get(url, {purchase_order_id: purchase_order_id, supplier_id: supplier_id, checkedVal: checkedVal}, function(html){
                $('div#fld_amount').html(html);
                $('input[name=totalAmountPo]').val(html);
            });
        });
        
        Util.hideProgressInd();
    },

    uncheckAllCol: function(e){
        var colPo = $(this).parent().index();
        Util.showProgressInd();
        $('.room-poCode-table tbody tr').each(function(rowIndex, trObj) {
            var checkbox          = $(trObj).find('td:eq(' + colPo + ') input');
            checkbox.removeAttr('checked');
            var is_checked        = checkbox.is(':checked');
            var purchase_order_id = $(checkbox).attr('purchase_order_id');
            var supplier_id       = $("input[name='supplier_id']").val();
            
            if(is_checked == 0){
                var index = poCodeChecked.indexOf(checkbox.val());
                poCodeChecked.splice(index, 1);
            }

            $('div#fld_amount').html(0);
            $('input[name=totalAmountPo]').val(0);
        });

        Util.hideProgressInd();
    },

   
}