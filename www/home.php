
<!-- home section Starts here -->
<section id="home" class="content">
    <div class="fullwidthbanner-container banner" style="background:#555;">
        <div class="fullwidthbanner">
            <ul>
                <li  data-masterspeed="300" data-delay="10000" >
                    <div class="margin20"></div>
                    <h1 class="aligncenter forexautocopying" style="color:#fff;">Forex Auto Copying</h1>
                    <h2 style="color:#fff;" class="aligncenter">The Internet's Most Reliable Automated Forex Signal Copier</h2>
                    <iframe class="aligncenter"  style="background-color:#e8e8e8;" width="640" height="360" src="//www.youtube.com/embed/EsO4aFYse_0?autoplay=0&rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                    <div class="margin20"></div>
                    <a  href="/signup/#1" class=" aligncenter"><img src="/images/homeCTA-green.png"/></a>
                </li>
            </ul>

        </div>

    </div>
    <div class="shadow"></div>
        <div class="welcome">
            <div class="margin30"></div>
            <h1 class="aligncenter">Why Top Forex Signal?</h1>
            <h2  class="aligncenter" style="font-size:40px;color:#555;font-weight:normal;">

                We have
                 <span id="fb-likes-count" style="color:#6970af;font-weight:bold;"></span><img id="fb-likes-count-img" src="/images/like.png"> real fans!

            </h2>
            <div class="container aligncenter">
                <div class="fb-like-box"  data-href="http://www.facebook.com/freeforexsignal" data-width="605"  data-height="300" data-show-faces="true" data-border-color="ffffff" data-stream="false" data-header="false"></div>
            </div>
            <div class="margin50"></div>
        </div>
</section>

<!-- pricing section Starts here -->
<section id="pricing" class="content"  style="background-color:#8aba23;">

    <div class="container" >
        <div class="margin50"></div>

        <h1 style="color:#fff;" class="aligncenter">With Top Performing Forex Signal Systems</h1>

            <?php
            $result = json_decode(file_get_contents('http://' . API_HOST . '/trader/'),true);
            $traders = $result['data']['traders'];
            for($i=0;$i<3;$i++)
            {
                ?>
                <div class="one-third column chart-summary" style="margin-left:15px;">
                    <div class="pr-tb-col" >
                        <div class="tb-header">
                            <div class="tb-title">
                                <h5><? print $traders[$i]['full_name']; ?></h5>
                            </div>
                            <div class="margin10"></div>
                            <!--<img src="/images/chart.png" width="600" height="349"/>-->
                            <canvas id="trader_chart_large-<? print $traders[$i]['id']?>"  class="alignright home_chart" width="260" height="200" style="background-color:#fff;"></canvas>
                            <script>
                                growth_small(<? print $traders[$i]['id']; ?>,'trader_chart_large-<? print $traders[$i]['id']; ?>','<? print API_HOST; ?>','small');
                            </script>
                        </div>
                        <ul class="tb-content">
                            <li><strong><span class="fa fa-users"></span> Followers: </strong><? print ($traders[$i]['followers']>0)?$traders[$i]['followers']:'NEW'; ?></li>

                            <li><strong>30 Day Growth: </strong> <? print $traders[$i]['30day_growth']; ?>%</li>
                            <li><strong>Total Growth: </strong> <? print $traders[$i]['total_growth']; ?>%</li>
                            <li><strong>Max Drawdown: -</strong><? print $traders[$i]['max_drawdown']; ?>%</li>
                        </ul>
                        <div class="buy-now">
                            <a class="button small copysignal" href="http://topforexsignal.com/signup/#1">Copy Signal<span class="fa fa-check"></span></a>

                            <a class="button small" href="/traders#<? print $traders[$i]['user_name']; ?>">More Info<span class="fa fa-caret-right"></span></a>

                        </div>
                    </div>
                </div>
                <?
            }?>



        <div class="margin50"></div>
        <img src="/images/traders03.png" id="img04" class="alignleft" width="234" height="160"/>
        <p style="color:#fff;">
            With our Forex Auto Copier service, you can automagically leverage the wisdom of all of our
            expert traders. You simply earn money away from your computer, while you're at work,
            even when you're asleep.
            <br/><br/>
            Our expert traders leverage the top winning strategies
            to put money into your account whenever they profit! <strong>IT'S THAT SIMPLE.</strong>
        </p>

    </div>
</section>


<section class="content" id="upsell">

    <div class="margin80"></div>
    <div class="container">

        <img src="images/guarantee2.png" id="img09" class="alignleft">
        <h1 style="color:#97ac5e;">We Have 100% Profit Guarantee!</h1>

        If your account does not make money in the first month (30 days from starting), we will :

        <ul style="padding-top:10px;">
            <li>Refund 100% of your monthly subscription fee</li>
            <li>Provide you the next month of service for free! </li>
        </ul>
        <a style="color:#8aba23;text-decoration:underline;" href="/guarantee" target="_new">More Details</a>
        <br><br>
                     <span  style="font-size:12px;">
                     Your trading account has to be under our partner broker HotForex for you to qualify for this guarantee.
                     To make this guarantee fair, all the trades have to come from our system’s trades only. Foreign trades besides our trades will forfeit this guarantee.

                    </span>


         <div class="margin70"></div>



        <div id="howitworks">
            <h1 class="aligncenter" >How does Forex Auto Copying work?</h1>
            Forex Auto Copier allows you to copy our forex signal systems on your MT4 platform. Once connected, you do not even need to keep your computer on.

            <div class="margin30"></div>
            <div class="container">
                <h2 style="color:#8aba23;" class="aligncenter">Copy Top Expert Forex Accounts with just 3 simple steps:</h2>
                <div class="margin30"></div>

                <span class="alignleft">Step 1</span>
                <h2 style="color:#555;">
                    Connect your MT4 trading platform to your selected forex signal systems.
                </h2>

                <div class="margin30"></div>
                <span  class="alignleft">Step 2</span>
                <h2 style="color:#555;">
                    You will setup your money management settings.
                </h2>

                <div class="margin30"></div>
                <span class="alignleft">Step 3</span>
                <h2 style="color:#555;">
                    Profit! You’re done, all trades that the signal provider opens, closes or modifies will be copied to your account.
                </h2>
            </div>
        </div>

            <div class="margin30"/div>






            <div class="margin80"></div>

            <h1 class="aligncenter">Why Are We Successful?</h1>
            <div class="margin20"></div>


            <img src="/images/traders06.png" id="img07" class="alignright" width="400" height="209"/>


            <h2 style="color:#97ac5e;">Our System Selection Approach</h2>

            <p>
                We strive for quality and not quantity. Other forex signal sites have many different systems but we like
                to stick with only the top performers. Hence the name Top Forex Signals. We select only a few but top performing systems.
            <br/><br/>
                If you noticed, all of our forex signal systems listed have great results. We use our many years of experience in forex consultation and trading to pick the best signal provider systems. We analyze the combination of signal provider system’s risk, trades, pairs and most importantly successful historical data to determine the best trading system for our users.

            </p>

            <div class="margin50"></div>



            <img src="/images/traders03.png" id="img01" class="alignleft" width="234" height="160"/>

            <h2 style="color:#97ac5e;">Forex Auto Copier</h2>

            <p>
                Our <b>Forex Auto Copier</b> allows you to copy expert forex traders 24 hours a day 5 days a week. Every trade that is executed by your chosen signal system will execute on your account at lightning fast speed. Ensuring your account to mirror your chosen signal system with top precision.

            </p>

            <div class="margin50"></div>


            <img src="/images/traders01.png" id="img02" class="alignright" width="400" height="225"/>




            <h2 style="color:#97ac5e;">Made For Beginners</h2>

            <p>
                If you are just starting out in forex trading and you do not know how to trade. Do not worry!

                <br/><br/>
                All you need to do is connect your account to our top traders and just copy their trades. You don’t need to do anything! Everything is fully automated! We like to keep it easy here.
            </p>


            <div class="margin50"></div>


            <img src="/images/traders02.png" id="img03" class="alignleft" width="335" height="250"/>
            <h2 style="color:#97ac5e;">Fully Supports MT4, Even Offline</h2>

            <p>
                Our system is design to work with all mt4 broker. 95% of the forex brokers out there supports MT4 (metatrader).
                <br/><br/>
                Your MT4 does not need to be online to copy trades. Our technology directly connect to your broker’s server. So even when your MT4 is offline the trades will be copied and executed.             </p>


            <div class="margin50"></div>
            <img src="/images/traders05.png" id="img06" class="alignleft" width="340" height="243" style="display:none;"/>
            <img src="/images/traders07.png" id="img08" class="alignleft" width="150" height="149"/>

            <h2 style="color:#97ac5e;">Cancel Anytime. No Long Term Contract</h2>

            <p>
                This is a month-to-month service and you can cancel anytime if this does not work out for you. We know you will be happy with our service and will continue on for months and months.            </p>


            <a href="/signup/#1" class="aligncenter"><img src="/images/homeCTA.png"/></a>






    </div>
</section>




<!-- home section Ends here -->
<!-- services section Starts here -->
<section id="services" class="content" style="margin:0;padding:0;" >

    <div class="content-main" style="background-color:#8aba23;">

        <div class="container">



            <div class="one-half column">

                    <img src="/images/traders04.png" id="img05" width="342" height="139"/>
                    <br/>


                <div class="margin20"></div>

                <div class="newsletter-container">
                    <h2>Get FREE Signals</h2>
                    <p>Subscribe now and get free signals into your inbox from TopForexSignal.</p>
                        <!--<form name="frmnewsletter" class="newsletter-form" action="http://www.aweber.com/scripts/addlead.pl"  accept-charset="iso-8859-1"  method="post" style="padding:10px;">-->
                            <form id="subscription-form" method="post" accept-charset="iso-8859-1" action="http://www.aweber.com/scripts/addlead.pl">
                                <!-- form data -->
                                <input type="hidden" name="meta_web_form_id" value="780652826" />
                                <input type="hidden" name="meta_split_id" value="" />
                                <input type="hidden" name="listname" value="awlist3708179" />
                                <input type="hidden" name="redirect" value="http://www.topforexsignal.com/thankyou/subscription" id="redirect_48bd540de494da7898cb3f6a2004d112" />

                                <input type="hidden" name="meta_adtracking" value="PreLaunch_Survey" />welcome
                                <input type="hidden" name="meta_message" value="1" />
                                <input type="hidden" name="meta_required" value="name,email,custom Country" />

                                <input type="hidden" name="meta_tooltip" value="" />

                                <input type="text" class="userName" name="name" placeholder="Full name" />
                                <input name="email" type="text"  placeholder="Email address" class="userEmail" />
                                <select  class="form-control bfh-countries userCountry" data-country="ZZ" name="custom Country" ></select>
                                <div class="margin10"></div>
                                <a  id="form-subscription-button" class="button large alignright">Subscribe</span></a>
                            </form>
                </div>
            </div>

            <div class="one-half column last">

            </div>

            <div class="one-half column last facebook_panel">


                <div id="fb-root"></div>
                <script type="text/javascript">
                    (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                </script>
                <div class="fb-like-box alignright" data-href="http://www.facebook.com/freeforexsignal" data-width="400" data-height="500" data-show-faces="true" data-border-color="ffffff" data-bgcolor="ffffff" data-stream="false" data-header="true"></div>

            </div>
            <div class="margin50"></div>
        </div>
        <a href="/signup/#1" class="aligncenter"><img src="/images/homeCTA.png"/></a>
        <div class="margin80"></div>

    </div>

</section>



<!-- contact section Ends here -->
