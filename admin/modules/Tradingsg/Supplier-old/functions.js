Util.createCPObject('cpm.tradingsg.supplier');

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
    }
}