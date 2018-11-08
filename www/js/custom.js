/** =========================
 *  FORM VALIDATION FUNCTIONS
 *  =========================
 */



function validateEmail($email) {

    //console.log('validateEmail:' + $email + ' indexOf:' + $email.indexOf('+'));
    if($email.indexOf('+')> -1) return true;
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if(!emailReg.test($email)) {
        return false;
    } else {
        return true;
    }
}

function validatePhone(value) {
    return (/^\d{7,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
}


function fbTrack() {
    console.log('fb fired');
    (function() {
        var _fbq = window._fbq || (window._fbq = []);
        if (!_fbq.loaded) {
            var fbds = document.createElement('script');
            fbds.async = true;
            fbds.src = '//connect.facebook.net/en_US/fbds.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(fbds, s);
            _fbq.loaded = true;
        }
    })();
    window._fbq = window._fbq || [];
    window._fbq.push(['track', '6024821105435', {'value':'0.00','currency':'USD'}]);
}

jQuery(document).ready(function($){

    var current_fs, next_fs, previous_fs; //fieldsets
    var left, opacity, scale; //fieldset properties which we will animate
    var animating; //flag to prevent quick multi-click glitches
    var currentPage = '1';
    var TFS_API = 'api.topforexsignal.com';
    var TFS_WWW = 'www.topforexsignal.com';
    //var TFS_API = 'api.topforexsignal.com';
    //var TFS_WWW = 'stage.topforexsignal.com';
    //var TFS_API = 'api.fxparse';
    //var TFS_WWW = 'fxparse';

    var traderList = {
        'golddigger':'http://www.4xsolutions.com/subscribe.php?currency=6&pid=&accounts=&sid=182d47b6-781d-46d3-a011-750ef19463f5&support=1',
        'conventional77':'https://www.4xservices.com/subscribe.php?sid=b4ef73c8-8720-49b8-a9e5-3c9f3c5512e8&currency=6&support=1',
        'specialz_trader':'https://www.4xservices.com/subscribe.php?sid=342f10c2-338d-44ed-8183-34854c8e5131&currency=6&support=1',
        'tradersview':'http://www.4xsolutions.com/subscribe.php?currency=6&pid=&accounts=&sid=63d8cc43-765e-4357-a0f3-1c1b86e1f689&support=1',
        'fxpipmakers':'http://www.4xsolutions.com/subscribe.php?currency=6&pid=&accounts=&sid=968e3bcb-6f9c-448a-bc65-4ace5107939a&support=1'
    };


    var traderID,traderName,hasAccount=false;



    /** ========================================
     *  HASH CONTROLLER (SIGNUP OR TRADER LIST)
     *  =======================================
     */


    if(window.location.hash)
    {
        /** ==================
         *  TRADER SIGNAL LIST
         *  ==================
         */
        if(window.location.pathname.indexOf('/traders') > -1) {
            var traderName = window.location.hash.substring(1);
            var el = $('.trader-container[data-user-name=\'' + traderName + '\']');
            $.scrollTo($(el).offset().top - 100, 750);
        } else
        if(window.location.pathname.indexOf('/signup') > -1)
        {
            console.log('hash: ' + window.location.hash + ' length: ' + window.location.hash.length);
            var step = (window.location.hash.length ==2)? window.location.hash.substring(1):window.location.hash.substring(1,3);
            console.log('step:#'+step);
            setUserData();

            if(currentPage !==step) signup(step);
        }
    } else if(window.location.pathname.indexOf('/thankyou')> -1) {
        $('#progressbar li').removeClass('active');
        $("#progressbar li").eq(2).addClass("active");

    }

    $(window).on('hashchange', function() {
        /** ===========
         *  SIGNUP PAGE
         *  ===========
         */
        if(window.location.pathname.indexOf('/signup') > -1)
        {
            console.log('hash: ' + window.location.hash + ' length: ' + window.location.hash.length);

            var step = (window.location.hash.length ==2)? window.location.hash.substring(1):window.location.hash.substring(1,3);
            console.log('step:#'+ step);
            signup(step);
        }
    });




    /** ===========
     *  SETUP GUIDE
     *  ===========
     */

    $('#guide').find('.faq-item img').each(function(){
        var imgClass = (this.width/this.height > 1) ? 'wide' : 'tall';
        $(this).addClass(imgClass);
    });



    /** ===========================
     *  HOME PAGE SUBSCRIPTION FORM
     *  ===========================
     */

    $('form#subscription-form input.userEmail').blur(function(e){


        if(validateEmail($(this).val())) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    $('form#subscription-form input.userName').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });



    /** ===================
     *  SUPPORT POP-UP FORM
     *  ===================
     */

    $('html').on('click','a#support-button',function(e){
        $('#popup-wrapper').fadeIn('fast');
    });

    $('html').on('click','#popup-wrapper div.overlay,#popup-container .close',function(e){
        $('#popup-wrapper').fadeOut('fast');
    });

    $('#popup-container input#userEmail').blur(function(e){
        if(validateEmail($(this).val())) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    $('#popup-container input#userName').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    $('#popup-container textarea#userMessage').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });



    $('html').on('click','a#email-submit',function(){
        var userEmail = $('input#userEmail');
        var userMessage = $('textarea#userMessage');
        var userName = $('input#userName');



        if(validateEmail(userEmail.val()) && (userMessage.val().length > 0) && (userName.val().length >0))
        {
            $('form#email-form').submit();
        } else {
            if(userName.val().length < 1) $(userName).addClass('form-error');
            if(!validateEmail(userEmail.val())) $(userName).addClass('form-error');
            if(userMessage.val().length < 1) $(userMessage).addClass('form-error');
            alert('Please enter valid information for the fields in red');
        }
    });




    //focus the form
    $('#form-broker-register #register-form .userName').focus();


    /** ============================
     *  STEP 1: SELECT SIGNAL SYSTEM
     *  ============================
     */


    //FORM VALIDATION
    $('#register-form .userName').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error');
    });

    $('#sp-dropwdown').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error');
    });

    $('#register-form .userEmail').blur(function(e){


        if(
            (validateEmail($(this).val()) &&
            $(this).val().length > 0)
        )
        {
            $(this).removeClass('form-error');
        } else $(this).addClass('form-error');
    });

    $('#register-form .countries').blur(function(e){
        if($(this).val() !== 'ZZ')
        {
            $(this).removeClass('form-error');
        } else $(this).addClass('form-error');

    });

    //Signal Provider selection
    $('#sp-dropwdown').change(function(e){
        $('.trader-container-signup').hide();

        var trader = $(this).val();
        console.log('trader selected: ' + trader);
        if(trader.length > 0)
        {
            $(this).removeClass('form-error');
            var item = $('.trader-container-signup[data-user-name=' + trader + ']');
            var traderName = $(this).find(':selected').attr('data-user-trader');

            $(item).css({display:'inline-block'});
            $(item).show('slow');
            localStorage.setItem('tfs_userTrader',trader);
            $('h3#userTrader strong').text(traderName);

        }

    });






    /** ===============================
     *  STEP 3: CONFIRM APPLY TO 4X
     *  ===============================
     */




    $('html').on('click','#verify-submit',function(e){
        e.preventDefault();
        var isValidEmail = (validateEmail($('input#verify-email-address').val()) && $('input#verify-email-address').val().length >0 );
        var isValidBrokerAccount =  $('input#verify-broker-id').val().length > 0; //&& $('#verify-select-broker').val().length > 0
        var isValidBroker = $('select#verify-select-broker').val().length > 0
        var isValidTrader = $('select#verify-select-trader').val().length > 0;
        var isValidUserName = $('input#verify-full-name').val().length > 0;

        if(isValidEmail && isValidBrokerAccount && isValidBroker && isValidTrader && isValidUserName)
        {
            $('form#verify-form').submit();
            $('#form-broker-account').html('<h2 style="width:100%;display:block;" class="aligncenter">Verifying your account...</h2><img src="../images/loading.gif" class="aligncenter"/>');
        } else {
            alert('You must select a broker and provide a valid email address and ID for your Broker. Please fix the information in red.');
            if(!isValidEmail) $('#verify-email-address').addClass('form-error');
            if(!isValidBrokerAccount) $('#verify-broker-id').addClass('form-error');
            if(!isValidBroker) $('select#verify-select-broker').addClass('form-error');
            if(!isValidUserName) $('input#verify-full-name').addClass('form-error');
            if(!isValidTrader) $('select#verify-select-trader').addClass('form-error');
            if(!isValidUserName) $('input#verify-full-name').addClass('form-error');
        }
    });



    /** ==========================
     *  STEP 2: HAS BROKER ACCOUNT
     *  ==========================
     */

    $('#form-broker-account .user-full-name').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });


    $('#form-broker-account .user-full-name').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    //incase the user changes their name here, lets update it in the form that actually gets sent
    $('#form-broker-account .user-full-name').change(function(e){
        $('form#register-form input.userName').attr('value',$(this).val())
    });

    $('#form-broker-register input.userName').change(function(e){
        $('#form-broker-account .user-full-name').attr('value',$(this).val());
    });


    $('#form-broker-account .user-broker-id').blur(function(e){
        if($(this).val().length > 0) {
            $(this).removeClass('form-error');
            showEmailCopy();

        } else $(this).addClass('form-error')
    });




    $('input#userEmail').blur(function(e){
        if(validateEmail($(this).val())) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });


    /** =================
     *  VERIFICATION PAGE
     *  =================
     */
    $('input#verify-email-address').blur(function(e){
        if(validateEmail($(this).val())) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    $('input#verify-full-name').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });


    $('input#verify-broker-id').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });



    $('select#verify-select-broker').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });

    $('select#verify-select-trader').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error')
    });



    $('html').on('keyup','input.user-broker-id',function(e){
        showEmailCopy();
    });

    $('html').on('keyup','input.user-full-name',function(e){
        showEmailCopy();
    });

    function showEmailCopy(){
        var userName = $('#form-broker-account .user-full-name');
        var brokerAccountID = $('#form-broker-account .user-broker-id');
        if(userName.val().length > 0  && brokerAccountID.val().length > 0) {
            var message = 'Dear Affiliate Department,\n\nMy name is ' + userName.val() + ' and I would want to put my account - ' + brokerName + ' ID: ' + brokerAccountID.val() +' under the IB of ' + brokerIB + '. The IB email is support@topforexsignal.com \n\nThanks,\n' + userName.val();
            $('textarea#broker-copy-paste').text(message);
            $('#form-email-notice').show('fast');

            //$('#form-broker-account').hide('fast',function(e){$('#form-email-notice').show();});

        } else {
            $('#form-email-notice').hide('fast');
        }
    }


/*
    $('html').on('click','#form-broker-account-next',function(e){
        var userName = $('#form-broker-account .user-full-name');
        var brokerAccountID = $('#form-broker-account .user-broker-id');
        if(userName.val().length > 0  && brokerAccountID.val().length > 0) {

            //$('span.user-full-name').text($('input.user-full-name').val());
            //$('span.user-broker-id').text($('input.user-broker-id').val());
            var message = 'Dear Affiliate Department,\n\nMy name is ' + userName.val() + ' and I would want to put my account - ' + brokerName + ' ID: ' + brokerAccountID.val() +' under the IB of ' + brokerIB +'. The IB email is support@topforexsignal.com \n\nThanks,\n' + userName.val();
            $('textarea#broker-copy-paste').text(message);

            $('#form-broker-account').hide('fast',function(e){$('#form-email-notice').show();});

        }
        else {
            if(brokerAccountID.val().length < 1) $(brokerAccountID).addClass('form-error');
            if(userName.val().length < 1) $(userName).addClass('form-error');
            alert('Please enter valid information for the fields in red');
            return false;
        }
    });*/

    /*
    //back button
    $('html').on('click','#form-broker-account-back',function(e){
        $('#form-broker-account').hide('fast',function(e){
            $('#has-account').show('fast');
        });
    });

    //back button
    $('html').on('click','#broker-register-back',function(e){
        $('#form-email-notice').hide('fast',function(e){
            $('#form-broker-account').show('fast');
        });
    });

    //back button
    $('html').on('click','#form-broker-register-back',function(e){

        if(hasAccount)
        {
            $('#form-broker-register').hide('fast',function(e){
                $('#form-email-notice').show('fast');
            });
        } else {
            $('#form-broker-register').hide('fast',function(e){
                $('#has-account').show('fast');
            });
        }
    });
*/

    $('html').on('click','#form-subscription-button',function(e){
        var _this = this;

        var userEmail =$('#subscription-form input.userEmail');
        var userName = $('#subscription-form input.userName');
        var userCountry = $('#subscription-form input.userCountry');
        if(userName.val().length > 0  && validateEmail(userEmail.val())) {

            $('#subscription-form').submit();

        }
        else {
            if(userEmail.val().length < 1) $(userEmail).addClass('form-error');
            if(userName.val().length < 1) $(userName).addClass('form-error');

            alert('Please enter valid information for the fields in red');
            return false;
        }
    });

    /** ================
     *  SUBMIT USER DATA
     *  ================
     */
    $('html').on('click','a.form-broker-register-next',function(e){


        var userEmail =$('#form-broker-register input.userEmail');
        var userName =$('#form-broker-register input.userName');
        var userCountry = $('#countries');


        var o = {
            userTraderID:traderID,
            userFullName:userName.val(),
            userEmail:userEmail.val(),
            userCountry:userCountry.val()
        };
        console.log(o);

        /** ====================
         *  STORE DATA TO SERVER
         *  ====================
         */
          var url = "http://" + TFS_API + "/user/register2";
        console.log('url:' + url);
          $.post(url, o).done(function( data ) {
                console.log(data);
                 if(data !=='undefined' && data.response==0){
                     alert(data.data.message);
                     return false;

                 } else {
                     //$(this).attr({"action":"http://www.aweber.com/scripts/addlead.pl"});


                     /** ===========
                      *  SUBMIT FORM
                      *  ===========
                      */
                    //$('#register-form').attr("target", "_blank");

                     $('#register-form').submit();


                 }

            });


    });


    /** =====================
     *  Set and get user data
     *  =====================
     */

    function setUserData()
    {
        console.log('setUserData()');

        if(typeof(Storage) !== "undefined") {
            var formUserName = $('#register-form input.userName');
            var formUserEmail  = $('#register-form input.userEmail');
            var formUserCountry = $('#register-form .countries');
            var formUserTrader = $('#select-sp select');



            //lets retrieve cached data if it exists
            var cache_userName = localStorage.getItem('tfs_userFullName');
            var cache_userEmail = localStorage.getItem('tfs_userEmail');
            var cache_userCountry = localStorage.getItem('tfs_userCountry');
            var cache_userTrader = localStorage.getItem('tfs_userTrader');




            //lets check each field, and if empty, populate it
            if(formUserName.val().length <1 &&  cache_userName !== null)  formUserName.val(cache_userName);
            if(formUserEmail.val().length <1 &&  cache_userEmail !== null )  formUserEmail.val(cache_userEmail);
            if(formUserTrader.val().length <1 && cache_userTrader !== null)
            {
                $('select#sp-dropwdown option[value=' + cache_userTrader + ']').attr('selected',true);
            }
            if(formUserCountry.val() == 'ZZ' &&   cache_userCountry !== null) {
                $('select option[value="' + cache_userCountry + '"]').attr('selected',true);
            }
            console.log('current trader ID:' + traderID);
            if(traderID !== 'undefined' && cache_userTrader !==null)
            {

                traderID = cache_userTrader;
                var item = $('.trader-container-signup[data-user-name=' + traderID + ']');
                $(item).css({display:'inline-block'});
                $(item).show('slow');
                traderName = $('#sp-dropwdown').find(':selected').attr('data-user-trader');
                console.log('traderName:' + traderName);

                $('h3#userTrader strong').text(traderName);
                //console.log('we found a cache_userTrader and its: ' + cache_userTrader);
                //lets isolate the row
                //var brokerDiv = $('div.broker-row[data-broker="' + brokerID + '"]');
                //$('div.broker-row').removeClass('selected');
                //$(this).addClass('selected');
                //lets run the javascript to select it for them
               // $(brokerDiv).addClass('selected');

                /*
                $('div.broker-row').each(function(index){
                    var _this = this;
                        $(_this).find('.selected-icon').animate({opacity:0},300);
                        $(_this).find('.rating').show('fast');
                });

                $('#' + brokerID).find('.selected-icon').animate({opacity:1},300);

                */
            }

            console.log('current traderID set to:' + traderID);


            //lets set or update cache
            if(formUserName.val().length > 0) localStorage.setItem("tfs_userFullName", formUserName.val());
            if(formUserEmail.val().length > 0) localStorage.setItem("tfs_userEmail", formUserEmail.val());
            if(formUserCountry.val().length > 0 && formUserCountry.val() !== 'ZZ' ) localStorage.setItem("tfs_userCountry", formUserCountry.val());
            if(traderID &&  traderID.length > 1) localStorage.setItem('tfs_userTrader',traderID);


        } else {
            return;
        }
    }



    /** ===========================
     *  VALIDATES A PARTICULAR FORM
     *  ===========================
     */

    function validate(el)
    {
        console.log('validating: ' + el );
        switch(el)
        {
            case 'step1':
                //FORM VALIDATION
                var formUserName = $('#register-form .userName');
                var formUserEmail  = $('#register-form .userEmail');
                var formUserCountry = $('#register-form .countries');
                var formUserTrader = $('#select-sp select');

                var isValidUserName,isValidUserEmail,isValidUserCountry,isValidUserTrader;

                if($(formUserName).val().length > 0)
                {
                    $(formUserName).removeClass('form-error')
                    isValidUserName = true;
                }
                    else {
                    $(formUserName).addClass('form-error');
                    isValidUserName = false;
                }
                if(validateEmail($(formUserEmail).val()) && $(formUserEmail).val().length > 0)
                {
                    isValidUserEmail = true;
                    $(formUserEmail).removeClass('form-error');
                } else {
                    $(formUserEmail).addClass('form-error');
                    isValidUserEmail = false;
                }

                if($(formUserCountry).val() !== 'ZZ')
                {
                    isValidUserCountry = true;
                    $(formUserCountry).removeClass('form-error');
                } else {
                    $(formUserCountry).addClass('form-error');
                    isValidUserCountry = false;
                }


                if($(formUserTrader).val().length > 0)
                {
                    isValidUserTrader = true;
                    $(formUserTrader).removeClass('form-error');
                } else {
                    $(formUserTrader).addClass('form-error');
                    isValidUserTrader = false;
                }


                if(!isValidUserEmail || !isValidUserEmail || !isValidUserCountry || !isValidUserTrader)
                {
                    alert('Please fix the invalid information highlighted in red before proceeding. Thanks!');
                    window.location.hash = '#1';
                    return false;
                } else {
                    setUserData();

                    return true;
                }
                break;

            case 'step2':
                break;

            case 'step2a':
                var userFullName = $('#form-broker-account input.user-full-name');
                var userBrokerID = $('#form-broker-account input.user-broker-id');
                var isValidFullName,isValidBrokerID;

                if($(userFullName).val().length > 0)
                {
                    $(userFullName).removeClass('form-error')
                    isValidFullName = true;
                } else {
                    $(userFullName).addClass('form-error')
                    isValidFullName = false;
                }

                if($(userBrokerID).val().length > 0)
                {
                    $(userBrokerID).removeClass('form-error')
                    isValidBrokerID = true;
                } else {
                    $(userBrokerID).addClass('form-error')
                    isValidBrokerID = false;
                }

                if(!isValidFullName || !isValidBrokerID)
                {
                    alert('Please fix the invalid information highlighted in red before proceeding. Thanks!');
                    return false;
                } else {
                    setUserData();

                    return true;
                }

                break;
        }
    }






    function signup(page)
    {
        console.log('signup:' + page + ' animating: ' + animating);
        if(animating) return false;
        console.log('#step' + page + ' typeof: ' + typeof page);

        switch(page)
        {
            case '1':
                //if(currentPage=='1') return false;

                animating = true;
                $('fieldset').hide('fast');

                //console.log('attempting to switch to page 1 from page: ' + currentPage);
                current_fs = $('#step2');
                next_fs = $('#step1');
                current_fs.animate({opacity: 0},{duration:300,complete:function(){
                    next_fs.animate({opacity:1});
                    current_fs.hide('fast',function(){next_fs.show();});

                }});
                animating = false;
                $('#progressbar li').removeClass('active');
                $("#progressbar li").eq(0).addClass("active");
                currentPage = '1';

                break;
            case '2':
                if(!validate('step1') ) return false;
                $('fieldset').hide('fast');

                $('#userFullName span').text($('#register-form .userName').val());
                $('#userEmail span').text($('#register-form .userEmail').val());

                animating = true;
                //console.log('attempting to switch to page 2 from page: ' + currentPage);
                //$('#form-broker-account').hide('fast');
                current_fs = $('#step1');
                next_fs = $('#step2');

                $('#redirect').attr('value','http://' + TFS_WWW +'/signup/' + traderID + '/#3');


                current_fs.animate({
                    opacity: 0,
                    float:'left',
                    display:'block'
                },{duration:300,complete:function(){
                    next_fs.animate({opacity:1});
                    current_fs.hide('fast',function(){next_fs.show();});

                }});
                animating = false;
                $('#progressbar li').removeClass('active');
                $("#progressbar li").eq(1).addClass("active");
                currentPage='2';

                break;



            case '3':


                var bID = window.location.pathname.split('/');
                var bURL;

                $('fieldset').hide('fast');
                current_fs = $('#step2');
                next_fs = $('#step3');
                current_fs.animate({
                    opacity: 0,
                    float:'left',
                    display:'block'
                },{duration:300,complete:function(){
                    next_fs.animate({opacity:1});
                    current_fs.hide('fast',function(){next_fs.show();});

                }});
                animating = false;
                $('#progressbar li').removeClass('active');
                $("#progressbar li").eq(2).addClass("active");


                console.log('bID: ' + bID[2]);
                var bURL ='';
                bURL = traderList[bID[2]];
                if(bURL.length < 1) bURL = 'http://' + TFS_WWW + '/error';
                console.log('bURL: ' + bURL);
                $('#fx-signup').attr('href',bURL);
                fbTrack();
                console.log('didfbfire and ' + bID[2]);

                return true;
                break;


            default:
                return false;
                break;
        }

    }




    $(".submit").click(function(){
        return false;
    });


    /** ===================
     *  TRADER PROFILE PAGE
     *  ===================
     */

    $('#open_trades').dataTable({
        stateSave:false,
        "paging":   true,
        "ordering": true,
        "info":     false,
        "filter":   false,
        "order": [[ 0, "desc" ]],
        "aoColumnDefs": [ {
            "aTargets": [6,7],
            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if ( sData < "0" ) {
                    $(nTd).css('color', '#ff0000')
                    $(nTd).css('font-weight', '400')
                } else
                {
                    $(nTd).css('color','#2ca02c')
                }
            }
        } ]
    });


    $('#closed_trades').dataTable({
        stateSave:false,
        "paging":   true,
        "ordering": true,
        "info":     false,
        "filter":   false,
        "order": [[ 1, "desc" ]],
        "aoColumnDefs": [ {
            "aTargets": [7,8],

            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if ( sData < "0" ) {
                    $(nTd).css('color', '#ff0000')
                    $(nTd).css('font-weight', '400')
                } else
                {
                    $(nTd).css('color','#2ca02c')
                }
            }
        } ]

    });



    /** ================
     *  MOBILE VIEW MENU
     *  ================
     */    $('nav#main-menu').meanmenu({
        meanMenuContainer :  $('header #menu-container'),
        meanRevealPosition:  'left',
        meanScreenWidth   :  797,
        meanMenuClose	  :  "<span /><span /><span />"
    });



});