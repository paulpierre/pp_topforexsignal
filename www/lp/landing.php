<?




    switch($controllerFunction)
    {
        case '1dk':
            $css = 'dark1.css';
            $banner = 'banner1.jpg';
            $landing_copy = 'cp_straight.php';
            break;

        case '1lt':
            $css = 'light1.css';
            $banner = 'banner1.jpg';
            $landing_copy = 'cp_straight.php';
            break;


        case '2dk':
            $css = 'dark1.css';
            $banner = 'banner1.jpg';
            $landing_copy = 'cp_mystery.php';
            break;

        case '2lt':
            $css = 'light1.css';
            $banner = 'banner1.jpg';
            $landing_copy = 'cp_mystery.php';
            break;

        case '2':
            include(WWW_PATH . '/lp/cp_default2.php');
            exit();
            break;

        default:
            include(WWW_PATH . '/lp/cp_default.php');
            exit();
            break;
    }






?>

<!doctype html>
<!--[if IE 7 ]>    <html lang="en-gb" class="isie ie7 oldie no-js"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en-gb" class="isie ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en-gb" class="isie ie9 no-js"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en-gb" class="no-js"> <!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!--[if lt IE 9]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->

    <title> Top Forex Signal - Copy Professional Traders for Free!</title>

    <meta name="description" content="Top Forex Signal is the Forex community's FREE premiere money making Forex signal provider. Get started in less than 5 minutes.">
    <meta name="author" content="Top Forex Signal">
    <meta name="keywords" content="high quality forex signal, good forex signal, profitable forex signal, forex signals, make money with forex signals, forex signals" />
    <!-- **Favicon** -->
    <link href="/lp/favicon.ico" rel="shortcut icon" type="image/x-icon" />

    <!-- **CSS - stylesheets** -->
    <link id="default-css" href="/lp/css/<? echo $css; ?>" rel="stylesheet" media="all" />
    <link href="/lp/css/responsive.css" rel="stylesheet" media="all" />
    <script type="text/javascript" src="/lp/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="/lp/js/jquery.sticky.js"></script>

    <!-- SLIDER STYLES ENDS -->

    <!-- **jQuery** -->
    <link href='/lp/css/fonts.css' rel='stylesheet' type='text/css'/>

    <script>
        $(document).ready(function(){
            $("#signup").sticky({topSpacing:90});
            $("#awf_field-68842501").focus();


            $('body').on('mouseover','#af-submit-image-1125929737',function(e){
                $(this).attr('src','/lp/img/btncta.png');
            });

            $('body').on('mouseout','#af-submit-image-1125929737',function(e){
                $(this).attr('src','/lp/img/btncta1.png');
            });




        });
    </script>
</head>
<body>

<div id="fb-root"></div>
<script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=##########&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!-- Header div Starts here -->
<header id="header">
    <div class="container">
        <div id="logo">
            <img src="/lp/img/logo.png" alt="" title=""> </a
        </div>

    </div>
</header>

<? include($landing_copy); ?>


<footer style="margin:0;padding:0;" class="copyright">
    <div class="container">
        <div class=copyright" >
        <p>&copy; 2014 Top Forex Signal | All Rights Reserved </p>

    </div>
    </div>
</footer>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-##########-1', 'auto');
    ga('send', 'pageview');

</script>


</body>
</html>