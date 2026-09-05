Util.createCPObject('cpt.angle');

cpt.angle = {
	init: function(){

        window.onload = getStartedContent();
        function getStartedContent() {
            var activation_expiry_status = $("input[name='activation_expiry_status']").val();
            
            if(activation_expiry_status == "Expired"){
                //var urlUnPublish = 'index.php?plugin=common_login&_spAction=UnpublishLoginForExpired&showHTML=0';
                //$.get(urlUnPublish, {}, function(json){
                    var urlLogout = 'index.php?plugin=common_login&_spAction=logout';
                    document.location = urlLogout;
                //}); 
            }

            var paymentReminder2 = $("input[name='paymentReminder2']").val();
            if(paymentReminder2 == 1){
                Util.showProgressInd();
                var url = 'index.php?module=tradingsg_pos&_spAction=paymentReminder2&showHTML=0';
                var exp = {
                    url: url
                };

                Util.openDialogForLink('Payment Reminder',  600, 200, 0, exp);
            }
            //var popupSession = $('#getStartedPopupOnloadSession').val();

            /*$('#col1').slideToggle(1000, function() {
                $('.leftnavShowHide').toggleClass('leftnavShowHideicon', $('#col1').is(':hidden'));
            });

            $('#col3').addClass('fullleftlist');*/

            /*if(popupSession == ''){
                setTimeout(function() {
                    $("a.getStartedContentTask").trigger('click');
                },100);
            }*/
        }

        $("a.checkProductPrice").livequery('click', function (e){
            Util.showProgressInd();
            var url = 'index.php?module=tradingsg_pos&_spAction=productPrice&showHTML=0';
            var exp = {
                url: url
            };
            Util.openDialogForLink('Check Product Price',  900, 400, 0, exp);
        });

        $(".checkProductPrice input[name='product_title']")
        .livequery(cpt.angle.checkProductPrice);

        $('.leftNav .hlist ul li.first').livequery('click', function(){
            var parent = $(this).closest('li');
            parent.next('ul.displayNone').slideToggle();
        });

    	//show hide description in Help Content - TRADE SMART (USS Product)
        $('.contentTitle').livequery('click', function(){
            //$('.contentDescription').css('display','none');
            var parent = $(this).closest('.helpContentTask');
            $('.contentDescription', parent).slideToggle();
            var parent = $(this).closest('.startedContentTask');
            $('.contentDescription', parent).slideToggle();
        });

		// Adding help button pop window in the content list  - TRADE SMART (USS Product)
		$("a.helpContentTask").livequery('click', function (e){
		    var module_name = $(this).attr('module_name');
		    var url = 'index.php?module=webBasic_content&_spAction=helpContentTask&module_name=' + module_name + '&showHTML=0';
		    var exp = {
		        url: url
		    };
		    Util.openDialogForLink('Help Content',  1000, 500, 0, exp);
		});

    	//show hide description in GET STARTED Content - TRADE SMART (USS Product)
    	$('.contentTitle').livequery('click', function(){
    		var parent = $(this).closest('.getStartedContentTask');
    	    $('.contentDescription', parent).slideToggle();
    	});

		// Adding GET STARTED button pop window in the content list  - TRADE SMART (USS Product)
		$("a.getStartedContentTask").livequery('click', function (e){
		    var module_name = $(this).attr('module_name');
		    var url = 'index.php?module=webBasic_content&_spAction=startedContentTask&module_name=' + module_name + '&showHTML=0';
		    var exp = {
		        url: url
		    };
		    Util.openDialogForLink('Get Started',  1000, 500, 0, exp);
		});

    	$("#nav .hlist ul li a span").addClass('inner');
    	$("#nav .hlist ul li a").blend();

        $("ul.homeTop li").livequery('click', function(){
            $(this).children("ul.sub").slideToggle();
        });

        $("ul.homeTop font a").livequery('click', function(){
            $(this).children("ul.sub").slideToggle();
        });

        $(".leftnavShowHide").livequery('click', function(){
            $('#col1').slideToggle('fast', function() {
                $('.leftnavShowHide').toggleClass('leftnavShowHideicon', $('#col1').is(':hidden'));
            });

            $('#col3').addClass('fullleftlist');

        });

    	$('.contentScroller, .m-common_dashboard .widget div.tableOuter').addClass('scroll-pane');

    	if ($('.tplLogin').length > 0){
    	    var toSubtract = $('#header').outerHeight(true) + $('#footer').outerHeight(true);
    	    var mainPanelHt = $(window).height() - toSubtract - 20;
    	    $('#col3_content').css({'height' : mainPanelHt + 'px', overflow: 'auto', 'overflow-x': 'hidden'});
    	    $("#col3_content #loginOuter").cp_center();
    	}

    	$("table.search td select").change(function() {
    	    $('#searchTop').submit();
    	});
 	},

    checkProductPrice: function() {
        var titleObj = this;
        $(titleObj).autocomplete({
             source : 'index.php?module=tradingsg_pos&_spAction=searchProductTitle&showHTML=0'
            ,minLength : 2
            ,select: function(event, ui) {
                var selectedObj = ui.item;
                var product_id = selectedObj.id
                //alert (product_id);
                $(this).after("<input type='hidden' name='product_id' value=" + product_id + ">");

                //--------------------------------------------
                Util.showProgressInd();
                var url = 'index.php?module=tradingsg_pos&_spAction=productPriceDisplay&showHTML=0';
                $.get(url, {product_id: product_id}, function(html){
                    //cpm.tradingsg.pos.reloadOrderItems();
                    $(".checkProductPrice input[name='product_title']").val('');
                    $('#productDisplay').html(html);
                    Util.hideProgressInd();
                });
            }
        });
    }

}

function DropDown(el) {
                this.dd = el;
                this.placeholder = this.dd.children('span');
                this.opts = this.dd.find('ul.dropdown > li');
                this.val = '';
                this.index = -1;
                this.initEvents();
            }
            DropDown.prototype = {
                initEvents : function() {
                    var obj = this;

                    obj.dd.livequery('click', function(event){
                        $(this).toggleClass('active');
                        return false;
                    });

                    obj.opts.livequery('click',function(){
                        var opt = $(this);
                        obj.val = opt.text();
                        obj.index = opt.index();
                        obj.placeholder.text(obj.val);
                    });
                },
                getValue : function() {
                    return this.val;
                },
                getIndex : function() {
                    return this.index;
                }
            }

            $(function() {

                var dd = new DropDown( $('#dd') );

                $(document).click(function() {
                    // all dropdowns
                    $('.wrapper-dropdown-3').removeClass('active');
                });

            });