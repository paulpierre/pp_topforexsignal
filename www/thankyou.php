<?
global $controllerObject,$controllerFunction,$controllerID;



if(strtolower($controllerFunction) == 'subscription')
{

?>
<div class="main-title" style="padding-top:120px;padding-bottom:40px;"><div class="container"><h2 style="color:#fff;">Signal Subscription</h2></div></div><div class="margin30 header-padding"></div>
<div class="container">
    <h2>You're Almost Done - Activate Your Subscription!</h2>
    <p>

        You've just been sent an email that contains a *confirm link*.
        In order to activate your subscription, check your email and click on the link in that email. You will not receive your subscription until you *click that link to activate it*.
        If you don't see that email in your inbox shortly, fill out the form again to have another copy of it sent to you.
    </p>
</div><div class="margin300"></div>
<script>(function() {
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
window._fbq.push(['track', '6024810593435', {'value':'0.01','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6024810593435&amp;cd[value]=0.01&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

<?

} else {
    if(strtolower($controllerFunction) == 'broker')
    {
        $brokerID = strtolower($controllerID);
    } else {
        $brokerID = strtolower($controllerFunction);
    }

    switch($brokerID)
    {
        case 'hfx':
            $startURL = 'https://www.hotforex.com/en/account-types/new-live-account.html?refid=14034';
            break;
        case 'ic':
            $startURL = 'http://www.icmarkets.com/?camp=2578';
            break;
        case 'xm':
            $startURL = 'http://clicks.pipaffiliates.com/afs/come.php?id=2801&cid=23729&atype=2&ctgid=0';
            break;
    }

?>
<!-- home section Starts here -->
<section id="home" class="content signup">

<div class="margin30"></div>

<div class="container">
    <div class="margin10"></div>

    <ul id="progressbar">
        <li class="active">Select Broker</li>
        <li class="active">Setup Broker<span> </span></li>
        <li class="active">Confirm<span> </span></li>
    </ul>
    <div id="thankyou" class="trader-container" style="padding-bottom:30px;">
        <div id="msform">


            <fieldset id="step3">
                <div style="<? print($controllerFunction == 'broker')?'':'display:none;';?>">
                    <div class="margin10"></div>
                    <h2 class="aligncenter">Thanks!</h2>

                    <p class="aligncenter">

                        Your next step is to open a live trading account with your chosen broker
                        <div class="margin10"></div>
                        <a href="<? print $startURL;?>">
                        <img src="/images/broker_<? print $brokerID; ?>.png" class="broker-image aligncenter"/>
                        <div class="aligncenter"><a class="button large btnFinish" href="<? print $startURL;?>">Get Started</a></div>
                        </a>
                        <div class="margin10"></div>
                        We also emailed you the instructions to open your live account with our partner broker.
                        See our  <strong><a href="/guide" target="_new">Setup Guide</a></strong> to access our comprehensive instructions on getting started.
                        To begin trading with Top Forex Signal, click the "Get Started" to connect your account.
                    </p>
                    <div class="margin85"></div>

                </div>


                <div style="<? print($controllerFunction !== 'broker')?'':'display:none;';?>">
                    <div class="margin10"></div>
                    <h2 class="aligncenter">Thanks!</h2>

                    <p class="aligncenter">
                        Your next step is to wait for a confirmation for the changed of IB from your broker
                    </p>
                        <div class="margin10"></div>

                        <img src="/images/broker_<? print $controllerFunction; ?>.png" class="broker-image aligncenter"/>
                        <div class="margin10"></div>
                    <p class="aligncenter">

                    Once you received the confirmation, you can verify your account.
                    </p>
                    <div class="margin20"></div>
                    <a class="button large  alignright btnFinish" href="/verify">Verify Your Account</a>
                </div>

            </fieldset>
        </div>



    </div>

</div>
</section>
<? } ?>