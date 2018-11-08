/** =========================
 *  FORM VALIDATION FUNCTIONS
 *  =========================
 */



function validateEmail($email) {

    console.log('validateEmail:' + $email + ' indexOf:' + $email.indexOf('+'));
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
    //var TFS_API = 'api.fxparse';
    //var TFS_WWW = 'fxparse';

    var brokerList = [
        'https://www.hotforex.com/en/account-types/new-live-account.html?refid=131722',
        'http://www.axitrader.com/forex/open-a-live-fx-account/?promocode=AC-435642',
        'http://www.icmarkets.com/forex-trading/open-a-live-account/?camp=2578'
    ];


    var brokerName,brokerIB,brokerID,brokerEmail,brokerPrompt,brokerInstructions,hasAccount=false;

    //$('#countries').bfhcountries({country: 'US'})

    var client = new ZeroClipboard($("#copy-email"));

    client.on( "copy", function (event) {
        var clipboard = event.clipboardData;
        alert('Successfully copied this message to your clipboard!');
        clipboard.setData( "text/plain",$('#broker-copy-paste').text() );
    });

    /** ==============
     *  BROKER TOOLTIP
     *  ==============
     */

    $('html').on('mouseover','div.broker-tooltip',function(){
        $(this).parent().parent().parent().parent().find('div.broker-description').show('fast');
    });

    $('html').on('mouseout','div.broker-description',function(){
        $(this).hide('fast');
    });

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


    /** ====================
     *  SELECT SIGNAL SYSTEM
     *  ====================
     */

    $('#sp-dropwdown').change(function(e){
        var trader = $(this).val();
        console.log('trader selected: ' + trader);
        if(trader.length > 0)
        {
            $('.trader-container-signup').hide();
            var item = $('.trader-container-signup[data-user-name=' + trader + ']');
            $(item).css({display:'inline-block'});
            $(item).show('slow');
        }


    });



    /** =====================
     *  STEP 1: SELECT BROKER
     *  =====================
     */


    //FORM VALIDATION
    $('#register-form .userName').blur(function(e){
        if($(this).val().length > 0) $(this).removeClass('form-error'); else $(this).addClass('form-error');
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

    $('select#verify-select-broker').change(function(e){
        switch($(this).val())
        {
            case 'axi':
                $('input#verify-broker-id').attr('placeholder','MT4 Account #');//'AxiTrader Account Number');
                break;

            case 'hfx':

                $('input#verify-broker-id').attr('placeholder','MT4 Account #');//'myHotForex ID');
                break;

            case 'ic':

                $('input#verify-broker-id').attr('placeholder','MT4 Account #');//'6-digit IC Markets ID');
                break;
        }
    });


    //BROKER SELECTION
    brokerID =  $('#select-broker div.broker-row:first-child').attr('data-broker');
    var brokerSignup = 'https://www.hotforex.com/en/account-types/new-live-account.html?refid=131722';
    brokerIB = '131722';
    brokerName = 'Hotforex';
    $('input.user-broker-id').attr('placeholder','MT4 Account #');


    $('img.broker-selected').attr('src','/images/broker_' + brokerID + '.png');

    $('html').on('click','div.broker-row',function(e){
        e.preventDefault();
        $('div.broker-row').removeClass('selected');
        $(this).addClass('selected');
        var _id = $(this).attr('id');

        $('div.broker-row').each(function(index){
            var _this = this;
            if($(_this).attr('id') == _id)
            {
                $(_this).find('.selected-icon').animate({opacity:1},300);
            }
            else {
                $(_this).find('.selected-icon').animate({opacity:0},300);
                $(_this).find('.rating').show('fast');
            }
        });

        brokerID = $(this).attr('data-broker');
        $('img.broker-selected').attr('src','/images/broker_' + brokerID + '.png');

        switch(brokerID)
        {
            case 'hfx':
                brokerIB = '131722';
                brokerPrompt = 'MT4 Account #';//'myHotForex ID';
                brokerName = 'Hotforex';
                brokerSignup  = brokerList[0];
                brokerInstructions = 'Please copy the content below and email it to <a href="mailto:affiliates@hotforex.com">affiliates@hotforex.com</a>.';
                localStorage.setItem('tfs_brokerID',brokerID);

                break;



            case 'axi':
                brokerIB = 'AC-435642';
                brokerPrompt = 'MT4 Account #';//'AxiTrader Account #';
                brokerName = 'AxiTrader';
                brokerSignup = brokerList[1];
                brokerInstructions = 'Please submit your information via the <a ref="http://www.axitrader.com/clients/client-portal/" style="cursor: pointer;color:#1784ff;" target="_new">AxiTrader Client Portal (click here)</a>, select "Sub-Account Request" and post the following message under "Additional Notes"';
                localStorage.setItem('tfs_brokerID',brokerID);

                break;

            case 'ic':
                brokerIB = '2578';
                brokerPrompt = 'MT4 Account #';//'6-digit IC Markets ID';
                brokerName = 'IC Markets'
                brokerSignup = brokerList[2];
                brokerInstructions = 'Please copy the content below and email it to <a href="partners@icmarkets.com">partners@icmarkets.com</a>.';
                localStorage.setItem('tfs_brokerID',brokerID);

                break;
        }


        $('input.user-broker-id').attr('placeholder',brokerPrompt);
        $('strong.broker-display').text(brokerName);
        $('#form-broker-account .user-broker-id').attr('placeholder',brokerPrompt);
        $('#form-email-notice h2').html(brokerInstructions);

    });



    /** ===============================
     *  STEP 3: CONFIRM (VERIFY BROKER)
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

        if($(this).parent().attr('id') == 'has-account')
        {
            hasAccount = false;
            //$('#redirect').attr('value',brokerSignup); // this was for the old flow
            console.log('redir:' + 'http://' + TFS_WWW + '/signup/' + brokerID + '/#3');
            $('#redirect').attr('value','http://' + TFS_WWW + '/signup/' + brokerID + '/#3');
            currentPage='2b';
            var userName = $('#form-broker-register input.userName');
        } else {


            var userName = $('#form-broker-account input.user-full-name');
            if(!validate('step2a')) return false;
        }

        var userEmail =$('#form-broker-register input.userEmail');
        var userBrokerID = $('#form-broker-account input.user-broker-id');
        var userCountry = $('#countries');


        var o = {
            userBrokerID:userBrokerID.val(),
            userBrokerName:brokerID,
            userFullName:userName.val(),
            userEmail:userEmail.val(),
            userCountry:userCountry.val()
        };
        console.log(o);

        /** ====================
         *  STORE DATA TO SERVER
         *  ====================
         */
          $.post("http://" + TFS_API + "/user/register", o).done(function( data ) {
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
            var formUserName = $('#register-form .userName');
            var formUserEmail  = $('#register-form .userEmail');
            var formUserCountry = $('#register-form .countries');


            //lets retrieve cached data if it exists
            var cache_userName = localStorage.getItem('tfs_userFullName');
            var cache_userEmail = localStorage.getItem('tfs_userEmail');
            var cache_userCountry = localStorage.getItem('tfs_userCountry');
            var cache_brokerID = localStorage.getItem('tfs_brokerID');



            //lets check each field, and if empty, populate it
            if(formUserName.val().length <1 &&  cache_userName !== null)  formUserName.val(cache_userName);
            if(formUserEmail.val().length <1 &&  cache_userEmail !== null )  formUserEmail.val(cache_userEmail);
            if(formUserCountry.val() == 'ZZ' &&   cache_userCountry !== null) {
                $('select option[value="' + cache_userCountry + '"]').attr('selected',true);
            }
            console.log('current broker ID:' + brokerID);
            if(brokerID !== 'undefined' && cache_brokerID !==null)
            {

                brokerID = cache_brokerID;
                console.log('we found a cache_brokerID and its: ' + cache_brokerID);
                //lets isolate the row
                var brokerDiv = $('div.broker-row[data-broker="' + brokerID + '"]');
                $('div.broker-row').removeClass('selected');
                $(this).addClass('selected');
                //lets run the javascript to select it for them
                $(brokerDiv).addClass('selected');

                $('div.broker-row').each(function(index){
                    var _this = this;
                        $(_this).find('.selected-icon').animate({opacity:0},300);
                        $(_this).find('.rating').show('fast');
                });

                $('#' + brokerID).find('.selected-icon').animate({opacity:1},300);


            }

            console.log('current brokerID set to:' + brokerID);


            //lets set or update cache
            if(formUserName.val().length > 0) localStorage.setItem("tfs_userFullName", formUserName.val());
            if(formUserEmail.val().length > 0) localStorage.setItem("tfs_userEmail", formUserEmail.val());
            if(formUserCountry.val().length > 0 && formUserCountry.val() !== 'ZZ' ) localStorage.setItem("tfs_userCountry", formUserCountry.val());
            if(brokerID &&  brokerID.length > 1) localStorage.setItem('tfs_brokerID',brokerID);


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
                console.log('country val: ' + $(formUserCountry).val());
                var isValidUserName,isValidUserEmail,isValidUserCountry;
                hasAccount = false;
                var valid;
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

                if(!isValidUserEmail || !isValidUserEmail || !isValidUserCountry)
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

                $('#userFullName span').text($('#register-form .userName').val());
                $('#userEmail span').text($('#register-form .userEmail').val());

                animating = true;
                //console.log('attempting to switch to page 2 from page: ' + currentPage);
                //$('#form-broker-account').hide('fast');
                current_fs = $('#step1');
                next_fs = $('#step2');

                if(currentPage=='2a')
                {
                    $('#form-broker-account').hide('fast',function(e){
                        $('#has-account').show('fast');
                    });
                }

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

            case '2a':
                hasAccount = true;

                if(validate('step1'))
                {
                    current_fs = $('#step1');
                    next_fs = $('#step2');
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
                }


                $('#redirect').attr('value','http://' + TFS_WWW +'/thankyou/' + brokerID);
                $('#form-broker-account').show();
                $('#has-account').hide();
                currentPage='2a';
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




                switch(bID[2])
                {
                    case 'hfx':
                        bURL = brokerList[0];
                        break;

                    case 'axi':
                        bURL = brokerList[1];
                        break;

                    case 'ic':
                        bURL = brokerList[2];

                        break;
                }

                $('img.broker-img').attr('src','/images/broker_' + bID[2] + '.png');
                $('#broker-apply').attr('href',bURL);
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