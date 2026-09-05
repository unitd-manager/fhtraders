Util.createCPObject('cpm.tradingsg.reports');

cpm.tradingsg.reports = {
    init: function(){
        $('#header, #nav, #footer').slideUp(1000, function(){
            $('#header, #nav, #footer').remove();
            LoadReady.makeScrollableTable();
        });
        cpm.tradingsg.reports.setSearchForm();

        $('.search select[name=report]').change(function(){
            var report = $(this).val();
            $('#reportSearchPanel').slideUp();
            if (report == ''){
                return;
            }
            var url = 'index.php?_topRm=reports&module=tradingsg_reports&_spAction=search&showHTML=0&report=' + report;
            $.get(url, function(html){
                $('#reportSearchPanel').html(html).slideDown();
                $('#reportContainer').html('');
            });
        });

        $('.w-tradingsg-summaryPurchaseSalesReport .purchaseVal').livequery('click', function(){
        	var parent = $(this).closest('.purchaseSalesSummary');
            $('.purchaseDetails', parent).slideToggle();
        }); 

        $('.w-tradingsg-profitByMonth .profitVal').livequery('click', function(){
            var parent = $(this).closest('.profitByMonthSummary');
            $('.profitDetails', parent).slideToggle();
        }); 

        $('.w-tradingsg-profitByMonth .monthValDisable').livequery('click', function(){
            $(this).toggleClass('monthVal');
        }); 

        $('.w-tradingsg-profitLossReport .expenseDetails').livequery('click', function(){
            var parent = $(this).closest('.expenseDetailsHead');
            $('.subTitles', parent).slideToggle();
        }); 

        $('.w-tradingsg-profitLossReport .expenseSubHeadDetails').livequery('click', function(){
            var parent = $(this).closest('.expenseDetailsSubHead');
            $('.subTitlesWithoutGroup', parent).slideToggle();
        });

        $('.w-tradingsg-profitLossReport .incomePaymentCheckBox').livequery('click', function(){
            var paymentReceipt = parseFloat(0);
            var paymentDue     = parseFloat(0);
            var overallTotal   = parseFloat(0);
            var overallBalance = parseFloat(0);

            $(".w-tradingsg-profitLossReport .incomePaymentCheckBox:checked").each(function() {
                var checkedVal = $(this).val();
                if(checkedVal == "Payment Received") {
                    paymentReceipt = $(".w-tradingsg-profitLossReport input[name=total_receipt_amount]").val();
                }

                if(checkedVal == "Payment Due") {
                    paymentDue = $(".w-tradingsg-profitLossReport input[name=total_invoice_amount]").val();
                }
            });

            overallTotal = parseFloat(paymentReceipt) + parseFloat(paymentDue);
            $(".w-tradingsg-profitLossReport .overallIncome input[name=overAllIncome]").val(overallTotal);
            var overallExpense = $(".w-tradingsg-profitLossReport input[name=overAllExpense]").val();
            overallBalance = parseFloat(overallTotal) - parseFloat(overallExpense);
            
            overallTotal = overallTotal.toLocaleString('en-IN', {
                maximumFractionDigits: 2,
                style: 'currency',
                currency: 'INR'
            });

            overallBalance = overallBalance.toLocaleString('en-IN', {
                maximumFractionDigits: 2,
                style: 'currency',
                currency: 'INR'
            });

            $(".w-tradingsg-profitLossReport .totalValue .overallIncome").html(overallTotal);
            $(".w-tradingsg-profitLossReport .totalValue .overAllProfit").html(overallBalance);
        });
    },

    setSearchForm: function(){
        $('table.search input.button').livequery('click', function(e) {
            e.preventDefault();
            Util.showProgressInd();
            $('#reportContainer').addClass('reportLoading');
            var allVars = $('table.search').serializeAnything();
            var url = 'index.php?_topRm=reports&module=tradingsg_reports&_spAction=displayReport&showHTML=0' + allVars;
            $.ajax({
                type: "GET",
                url: url,
                dataType: "html",
                success: function(html) {
                    $('#reportContainer').html(html);
                    $('#reportContainer').removeClass('reportLoading');
                    if($('#reportName').val() == 'liquiditySummary'){
                        drawChart();
                    }
                    Util.hideProgressInd();
                }
            });
        });

        //$('form#searchTop').livequery(function() {
        //    var formName = 'searchTop';
        //    var extraPar = {};
        //    var cpCSRFToken = $('#cpCSRFToken').val();
        //
        //    var additionalData = {
        //        cpCSRFToken: cpCSRFToken
        //    };
        //
        //    var options = {
        //        success: function(html, statusText, xhr, jqFormObj) {
        //            Util.hideProgressInd();
        //            $('#' + formName).unblock();
        //            $('#reportContainer').removeClass('reportLoading');
        //            $('#reportContainer').html(html);
        //        },
        //        beforeSubmit: function(frmData) {
        //            $('#reportContainer').addClass('reportLoading');
        //            Util.clearPrepopulatedTextbox('#' + formName, frmData);
        //            Util.showProgressInd();
        //            $('#' + formName).block({ message: null });
        //        }
        //        ,data: additionalData
        //        ,dataType: 'html'
        //    };
        //
        //    $('#' + formName).ajaxForm(options);
    	//});
    }
}