</div>
    <footer>

        <div class="copyright" style="padding-top:0;">
            <div class="container">

                <div class="margin20 aligncenter" style="max-width:800px;padding:30px;font-size:12px; color:#fff;">
                    RISK WARNING: Past performance is not indicative of future results. Trading forex carries a high level of risk, and may not be suitable for all investors. The high degree of leverage can work against you as well as for you. Before deciding to trade any such leveraged products you should carefully consider your investment objectives, level of experience, and risk appetite. The possibility exists that you could sustain a loss of some or all of your initial investment and therefore you should not invest money that you cannot afford to lose. You should be aware of all the risks associated with trading on margin, and seek advice from an independent financial advisor if you have any doubts. ​
                </div>
                <div class="aligncenter" style="width:100%;">
                    <span style="position:relative;top:20px;">&copy; 2015 Top Forex Signal All Rights Reserved | <a href="/provider">Be A Signal Provider</a> | <a href="/tos">Terms of Service</a></span>
                    <ul class="social-media">
                        <li style="padding:10px;text-align:center;"><a href="http://www.facebook.com/freeforexsignal" target="_new" class="fa fa-facebook"></a></li>
                        <li style="padding:10px;text-align:center;"><a href="https://twitter.com/topfxsignal" target="_new"  class="fa fa-twitter"></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </footer>

    <a href="#" id="support-button"></a>
    <div id="popup-wrapper" style="display:none;">

        <div id="popup-container">
            <div class="close fa fa-close alignright"></div>
            <h2>Need Help?</h2>
            <div class="content">
                <form method="post" id="email-form" action="/mail/support" >
                    <input type="text" id="userName" class="userName" name="userName" placeholder="Full name" />
                    <input name="userEmail" type="text"  placeholder="Email address" id="userEmail" />
                    <textarea name="userMessage" type="text"  id="userMessage" placeholder="Question / comment"></textarea>
                    <div class="margin30"></div>
                    <a  id="email-submit" class="button large alignright" >Send</a>
                </form>
            </div>
        </div>
        <div class="overlay"></div>
    </div>


</div>
</div><!-- Wrapper End -->

<!-- Java Scripts -->
<script type="text/javascript" src="/js/jquery.scrollTo.js"></script>
<!--
    <script type="text/javascript" src="js/jquery.inview.js"></script>

    <script type="text/javascript" src="js/jquery.nav.js"></script>
    -->
    <script type="text/javascript" src="/js/jquery-menu.js"></script>

	<script type="text/javascript" src="/js/jquery.meanmenu.min.js"></script>
    <!--
	<script type="text/javascript" src="js/jquery.quovolver.min.js"></script>
    
	<script type="text/javascript" src="js/jquery.donutchart.js"></script>        

	<script type="text/javascript" src="js/jquery.isotope.min.js"></script>
    -->
<script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>

<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/js/jquery.dataTables.js"></script>


<!--<script type="text/javascript" src="js/jquery.tabs.min.js"></script>

<script type="text/javascript" src="js/jquery.nicescroll.min.js"></script>-->

<!-- Layer Slider Starts -->
<script src="/js/layerslider/jquery-easing-1.3.js" type="text/javascript"></script>
<script src="/js/layerslider/jquery-transit-modified.js" type="text/javascript"></script>
<script src="/js/layerslider/layerslider.transitions.js" type="text/javascript"></script>
<script src="/js/layerslider/layerslider.kreaturamedia.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#layerslider').layerSlider({
            skinsPath : '/js/layerslider/skins/',
            skin : 'borderlessdark3d',
            width : '940px',
            height : '500px',
            responsive : true,
            thumbnailNavigation : 'hover',
            showCircleTimer : false,
            navPrevNext	 : true,
            navButtons	 : true,
            hoverPrevNext: true
        });
    });
</script>

<script src="/js/revolution/jquery.themepunch.revolution.min.js" type="text/javascript"></script>
<script type="text/javascript">
    /*
    jQuery(document).ready(function($){
        if($.fn.cssOriginal != undefined)
            $.fn.css = $.fn.cssOriginal;

        var api = $('.fullwidthbanner').revolution(
                {
                    delay:9000,
                    startwidth:940,
                    startheight:570,

                    onHoverStop:"on",						// Stop Banner Timet at Hover on Slide on/off

                    thumbWidth:100,							// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
                    thumbHeight:50,
                    thumbAmount:3,

                    hideThumbs:200,
                    navigationType:"none",				// bullet, thumb, none
                    navigationArrows:"solo",				// nexttobullets, solo (old name verticalcentered), none

                    navigationStyle:"round",				// round,square,navbar,round-old,square-old,navbar-old, or any from the list in the docu (choose between 50+ different item), custom

                    navigationHAlign:"center",				// Vertical Align top,center,bottom
                    navigationVAlign:"bottom",					// Horizontal Align left,center,right
                    navigationHOffset:30,
                    navigationVOffset:-40,

                    soloArrowLeftHalign:"left",
                    soloArrowLeftValign:"center",
                    soloArrowLeftHOffset:20,
                    soloArrowLeftVOffset:0,

                    soloArrowRightHalign:"right",
                    soloArrowRightValign:"center",
                    soloArrowRightHOffset:20,
                    soloArrowRightVOffset:0,

                    touchenabled:"on",						// Enable Swipe Function : on/off

                    stopAtSlide:-1,							// Stop Timer if Slide "x" has been Reached. If stopAfterLoops set to 0, then it stops already in the first Loop at slide X which defined. -1 means do not stop at any slide. stopAfterLoops has no sinn in this case.
                    stopAfterLoops:-1,						// Stop Timer if All slides has been played "x" times. IT will stop at THe slide which is defined via stopAtSlide:x, if set to -1 slide never stop automatic

                    hideCaptionAtLimit:0,					// It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
                    hideAllCaptionAtLilmit:0,				// Hide all The Captions if Width of Browser is less then this value
                    hideSliderAtLimit:0,					// Hide the whole slider, and stop also functions if Width of Browser is less than this value

                    fullWidth:"on",

                    shadow:0								//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows -  (No Shadow in Fullwidth Version !)
                });
    });*/
</script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/bootstrap-formhelpers.js"></script>
<script type="text/javascript" src="/js/jquery.easing.min.js"></script>
<script type="text/javascript" src="/js/ZeroClipboard.min.js"></script>
<script type="text/javascript" src="/js/custom.js"></script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('require', 'displayfeatures');
        ga('create', 'UA-58431912-1', 'auto');
        ga('send', 'pageview');


    </script>
</body>
</html>