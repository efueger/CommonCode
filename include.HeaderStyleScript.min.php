<style>
*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}body{margin:0;}#flipside_nav{background-color:#1b1b1b;}#flipside_nav nav{height:34px;}.constrain{max-width:1240px;margin:0 auto;padding:0 20px;}#flipside_nav nav ul{text-align:left;display:inline;float:left;margin:0;list-style:none;border-left:1px solid rgba(0,0,0,0.347656);}#flipside_nav nav ul.links{float:right;}#flipside_nav nav ul li{display:inline-block;position:relative;}#flipside_nav nav ul li a{color:#e6e6e6;font-weight:normal;font-style:normal;text-decoration:none;display:block;padding:8px 12px;}#flipside_nav nav ul.sites li a{font-weight:bold;}#flipside_nav nav ul li.dropdown ul{display:none;position:absolute;top:32px;left:-2px;width:120px;opacity:0;transition:opacity .2s;-webkit-box-shadow:0 43px 5px rgba(0,0,0,0.4);box-shadow:0 4px 5px rgba(0,0,0,0.4);z-index:400;padding:0;}#flipside_nav nav ul li.dropdown:hover ul{display:block;opacity:1;background:#1b1b1b;}#flipside_nav nav ul li.dropdown ul li{display:block;}.tinynav-container{display:none;}.tinynav{display:none;}@media screen and (max-width:40.5em){.tinynav{display:block;}.tinynav1{float:left;}.tinynav2{float:right;}#flipside_nav nav ul{display:none;}}
</style>
<!--[if lt IE 9]>
<style>
    .tinynav { display: block }
    .tinynav1 { float: left }
    .tinynav2 { float: right }
    #flipside_nav nav ul { display: none }
</style>
<![endif]-->
<script>
function do_flip_header_init(){$("#flipside_nav .sites").tinyNav({header:"Sites..."});$("#flipside_nav .links").tinyNav({header:"Options..."});}$(do_flip_header_init);
</script>
