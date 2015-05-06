<?php
require_once("/var/www/secure_settings/class.FlipsideSettings.php");
require_once('class.WebPage.php');

define('JS_JQUERY',       0);
define('JS_JQUERY_UI',    1);
define('JS_BOOTSTRAP',    2);
define('JQUERY_VALIDATE', 3);
define('JQUERY_TOUCH',    4);
define('JS_TINYNAV',      5);
define('JS_BOOTSTRAP_FH', 6);
define('JS_BOOTSTRAP_SW', 7);
define('JS_DATATABLE',    8);
define('JS_CHART',        9);
define('JS_METISMENU',    10);
define('JS_BOOTBOX',         11);
define('JS_DATATABLE_ODATA', 12);
define('JS_FLIPSIDE',     20);
define('JS_LOGIN',        21);

define('CSS_JQUERY_UI',    0);
define('CSS_BOOTSTRAP',    1);
define('CSS_BOOTSTRAP_FH', 2);
define('CSS_BOOTSTRAP_SW', 3);
define('CSS_DATATABLE',    4);

$js_array = array(
     JS_JQUERY => array(
         'no' => array(
             'no'  => '/js/common/jquery.js',
             'min' => '/js/common/jquery.min.js'
         ),
         'cdn' => array(
             'no'  => '//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js',
             'min' => '//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js'
         )
     ),
     JS_JQUERY_UI => array(
         'no' => array(
             'no'  => '/js/common/jquery-ui.js',
             'min' => '/js/common/jquery-ui.min.js'
         ),
         'cdn' => array(
             'no'  => '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.js',
             'min' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js'
         )
     ),
     JS_BOOTSTRAP => array(
         'no' => array(
             'no'  => '/js/common/bootstrap.js',
             'min' => '/js/common/bootstrap.min.js'
         ),
         'cdn' => array(
             'no'  => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.js',
             'min' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
         )
     ),
     JQUERY_VALIDATE => array(
         'no' => array(
             'no'  => '/js/common/jquery.validate.js',
             'min' => '/js/common/jquery.validate.min.js'
         ),
         'cdn' => array(
             'no'  => '//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.js',
             'min' => '//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js'
         )
     ),
     JQUERY_TOUCH => array(
         'no' => array(
             'no'  => '/js/common/jquery.ui.touch-punch.min.js',
             'min' => '/js/common/jquery.ui.touch-punch.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js'
         )
     ),
     JS_TINYNAV => array(
         'no' => array(
             'no'  => '/js/common/tinynav.js',
             'min' => '/js/common/tinynav.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/TinyNav.js/1.2.0/tinynav.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/TinyNav.js/1.2.0/tinynav.min.js'
         )
     ),
     JS_BOOTSTRAP_FH => array(
         'no' => array(
             'no'  => '/js/common/bootstrap-formhelpers.js',
             'min' => '/js/common/bootstrap-formhelpers.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/js/bootstrap-formhelpers.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/js/bootstrap-formhelpers.min.js'
         )
     ),
     JS_BOOTSTRAP_SW => array(
         'no' => array(
             'no'  => '/js/common/bootstrap-switch.js',
             'min' => '/js/common/bootstrap-switch.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/js/bootstrap-switch.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/js/bootstrap-switch.min.js'
         )
     ),
     JS_DATATABLE => array(
         'no' => array(
             'no'  => '/js/common/jquery.dataTables.js',
             'min' => '/js/common/jquery.dataTables.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdn.datatables.net/1.10.5/js/jquery.dataTables.js',
             'min' => '//cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js'
         )
     ),
     JS_CHART => array(
         'no' => array(
             'no'  => '/js/common/Chart.js',
             'min' => '/js/common/Chart.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js'
         )
     ),
     JS_METISMENU => array(
         'no' => array(
             'no'  => '/js/common/metisMenu.js',
             'min' => '/js/common/metisMenu.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/metisMenu/1.1.3/metisMenu.min.js'
         )
     ),
     JS_BOOTBOX => array(
         'no' => array(
             'no'  => '/js/common/bootbox.js',
             'min' => '/js/common/bootbox.min.js'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.3.0/bootbox.js',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.3.0/bootbox.min.js'
         )
     ),
     JS_DATATABLE_ODATA => array(
         'no' => array(
             'no'  => '/js/common/jquery.dataTables.odata.js',
             'min' => '/js/common/jquery.dataTables.odata.js',
         ),
         'cdn' => array(
             'no'  => '/js/common/jquery.dataTables.odata.js',
             'min' => '/js/common/jquery.dataTables.odata.js',
         )
     ),
     JS_FLIPSIDE => array(
         'no' => array(
             'no'  => '/js/common/flipside.js',
             'min' => '/js/common/flipside.min.js'
         ),
         'cdn' => array(
             'no'  => '/js/common/flipside.js',
             'min' => '/js/common/flipside.min.js'
         )
     ),
     JS_LOGIN => array(
         'no' => array(
             'no'  => '/js/common/login.js',
             'min' => '/js/common/login.min.js'
         ),
         'cdn' => array(
             'no'  => '/js/common/login.js',
             'min' => '/js/common/login.min.js'
         )
     )
);

$css_array = array(
    CSS_JQUERY_UI => array(
        'no' => array(
             'no'  => '/css/common/jquery-ui.css',
             'min' => '/css/common/jquery-ui.min.css'
         ),
         'cdn' => array(
             'no'  => '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css',
             'min' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.min.css'
         )
    ),
    CSS_BOOTSTRAP => array(
         'no' => array(
             'no'  => '/css/common/bootstrap.css',
             'min' => '/css/common/bootstrap.min.css'
         ),
         'cdn' => array(
             'no'  => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.css',
             'min' => '//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'
         )
    ),
    CSS_BOOTSTRAP_FH => array(
        'no' => array(
             'no'  => '/css/common/bootstrap-formhelpers.css',
             'min' => '/css/common/bootstrap-formhelpers.min.css'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/css/bootstrap-formhelpers.css',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-formhelpers/2.3.0/css/bootstrap-formhelpers.min.css'
         )
    ),
    CSS_BOOTSTRAP_SW => array(
         'no' => array(
             'no'  => '/css/common/bootstrap-switch.css',
             'min' => '/css/common/bootstrap-switch.min.css'
         ),
         'cdn' => array(
             'no'  => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.css',
             'min' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css'
         )
    ),
    CSS_DATATABLE => array(
        'no' => array(
             'no'  => '/css/common/jquery.dataTables.css',
             'min' => '/css/common/jquery.dataTables.min.css'
         ),
         'cdn' => array(
             'no'  => '//cdn.datatables.net/1.10.5/css/jquery.dataTables.css',
             'min' => '//cdn.datatables.net/1.10.5/css/jquery.dataTables.min.css'
         )
    )
);

class FlipPage extends WebPage
{
    public $sites;
    public $links;
    public $notifications;
    public $header;
    protected $minified = null;
    protected $cdn = null;

    function __construct($title, $header=true)
    {
        parent::__construct($title);
        $this->add_viewport();
        $this->add_jquery_ui();
        $this->add_bootstrap();
        if($header)
        {
            $this->add_header_js_and_style();
        }
        $this->header = $header;
        $this->sites = array();
        $this->links = array();
        $this->notifications = array();
        $this->minified = 'min';
        $this->cdn      = 'cdn';
        if(isset(FlipsideSettings::$global))
        {
            if(isset(FlipsideSettings::$global['use_minified']) && !FlipsideSettings::$global['use_minified'])
            {
                $this->minified = 'no';
            }
            if(isset(FlipsideSettings::$global['use_cdn']) && !FlipsideSettings::$global['use_cdn'])
            {
                $this->cdn = 'no';
            }
        }
    }

    function setup_vars()
    {
        if($this->minified !== null && $this->cdn !== null) return;
        $this->minified = 'min';
        $this->cdn      = 'cdn';
        if(isset(FlipsideSettings::$global))
        {
            if(isset(FlipsideSettings::$global['use_minified']) && !FlipsideSettings::$global['use_minified'])
            {
                $this->minified = 'no';
            }
            if(isset(FlipsideSettings::$global['use_cdn']) && !FlipsideSettings::$global['use_cdn'])
            {
                $this->cdn = 'no';
            }
        }
    }

    function add_js_from_src($src)
    {
        $js_tag = $this->create_open_tag('script', array('src'=>$src, 'type'=>'text/javascript'));
        $close_tag = $this->create_close_tag('script');
        $this->add_head_tag($js_tag);
        $this->add_head_tag($close_tag);
    }

    function add_css_from_src($src)
    {
        $css_tag = $this->create_open_tag('link', array('rel'=>'stylesheet', 'href'=>$src, 'type'=>'text/css'), true);
        $this->add_head_tag($css_tag);
    }

    function add_js($type)
    {
        global $js_array;
        $this->setup_vars();
        $src = $js_array[$type][$this->cdn][$this->minified];
        $this->add_js_from_src($src);
    }

    function add_css($type)
    {
        global $css_array;
        $this->setup_vars();
        $src = $css_array[$type][$this->cdn][$this->minified];
        $this->add_css_from_src($src);
    }

    function add_viewport()
    {
        $view_tag = $this->create_open_tag('meta', array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1.0'), true);
        $this->add_head_tag($view_tag);
    }

    function add_jquery_ui()
    {
        $this->add_js(JS_JQUERY);
        $this->add_js(JS_JQUERY_UI);
        $this->add_js(JQUERY_TOUCH);
        $this->add_js(JS_FLIPSIDE);
        $this->add_css(CSS_JQUERY_UI);
    }

    function add_bootstrap()
    {
        $this->add_js(JS_BOOTSTRAP);
        $this->add_css(CSS_BOOTSTRAP);
    }

    function add_header_js_and_style()
    {
        $this->add_js(JS_TINYNAV);
        $script_js_tags = file_get_contents(dirname(__FILE__).'/include.HeaderStyleScript.min.php');
        $this->add_head_tag($script_js_tags);
    }

    function add_header()
    {
        $header ="<header>\n";
        $header.="    <section id=\"flipside_nav\">\n";
        $header.="        <nav><div class=\"constrain\">\n";
        $header.="            <ul class=\"sites\">\n";
        $site_names = array_keys($this->sites);
        foreach($site_names as $site_name)
        {
            $header.="                <li>".$this->create_link($site_name, $this->sites[$site_name])."</li>\n";
        }
        $header.="            </ul>\n";
        $header.="            <ul class=\"links\">\n";
        $link_names = array_keys($this->links);
        foreach($link_names as $link_name)
        {
            if(is_array($this->links[$link_name]))
            {
                if(isset($this->links[$link_name]['_']))
                {
                    $header.="                <li class=\"dropdown\">".$this->create_link($link_name, $this->links[$link_name]['_'])."\n";
                }
                else
                {
                    $header.="                <li class=\"dropdown\">".$this->create_link($link_name)."\n";
                }
                $header.="                    <ul>\n";
                $sub_names = array_keys($this->links[$link_name]);
                foreach($sub_names as $sub_name)
                {
                    if(strcmp($sub_name, '_') == 0)
                    {
                        continue;
                    }
                    $header.="                    <li>".$this->create_link($sub_name, $this->links[$link_name][$sub_name])."</li>\n";
                }
                $header.="                    </ul>\n";
                $header.="                </li>\n";
            }
            else
            {
                $header.="                <li>".$this->create_link($link_name, $this->links[$link_name])."</li>\n";
            }
        }
        $header.="            </ul>\n";
        $header.="        </div></nav>\n";
        $header.="    </section>\n";
        $header.="</header>\n";
        $this->body = $header.$this->body;
    }

    const NOTIFICATION_SUCCESS = "alert-success";
    const NOTIFICATION_INFO    = "alert-info";
    const NOTIFICATION_WARNING = "alert-warning";
    const NOTIFICATION_FAILED  = "alert-danger";

    function add_notification($msg, $sev=self::NOTIFICATION_INFO, $dismissible=1)
    {
        $notice = array('msg'=>$msg, 'sev'=>$sev, 'dismissible'=>$dismissible);
        array_push($this->notifications, $notice);
    }

    private function render_notifications()
    {
        for($i = 0; $i < count($this->notifications); $i++)
        {
            $class = 'alert '.$this->notifications[$i]['sev'];
            $button = '';
            if($this->notifications[$i]['dismissible'])
            {
                $class .= ' alert-dismissible';
                $button = '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
            }
            $prefix = '';
            switch($this->notifications[$i]['sev'])
            {
                case self::NOTIFICATION_INFO:
                    $prefix = '<strong>Notice:</strong> '; 
                    break;
                case self::NOTIFICATION_WARNING:
                    $prefix = '<strong>Warning!</strong> ';
                    break;
                case self::NOTIFICATION_FAILED:
                    $prefix = '<strong>Warning!</strong> ';
                    break;
            }
            $style = '';
            if($i+1 < count($this->notifications))
            {
                //Not the last notification, remove the end margin
                $style='style="margin: 0px;"';
            }
            $this->body = '
                <div class="'.$class.'" role="alert" '.$style.'>
                    '.$button.$prefix.$this->notifications[$i]['msg'].'
                </div>
            '.$this->body;
        }
    }

    function print_page($header=true)
    {
        if(count($this->notifications) > 0)
        {
            $this->render_notifications();
        }
        $this->body = '
            <noscript>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>Error!</strong> This site makes extensive use of JavaScript. Please enable JavaScript or this site will not function.
                </div>
            </noscript>
        '.$this->body;
        if($this->header)
        {
            $this->add_header();
        }
        parent::print_page();
    }

    function add_site($name, $url)
    {
        $this->sites[$name] = $url;
    }

    function add_link($name, $url, $submenu=false)
    {
        if(is_array($submenu))
        {
            $submenu['_'] = $url;
            $this->links[$name] = $submenu;
        }
        else
        {
            $this->links[$name] = $url;
        }
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
