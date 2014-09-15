<?php
require_once('class.WebPage.php');
class FlipPage extends WebPage
{
    public $sites;
    public $links;
    public $notifications;
    public $header;

    function __construct($title, $header=true)
    {
        parent::__construct($title);
        $this->add_viewport();
        $this->add_jquery_ui();
        if($header)
        {
            $this->add_header_js_and_style();
        }
        $this->header = $header;
        $this->sites = array();
        $this->links = array();
        $this->notifications = array();
    }

    function add_viewport()
    {
        $view_tag = $this->create_open_tag('meta', array('name'=>'viewport', 'content'=>'width=device-width, initial-scale=1.0'), true);
        $this->add_head_tag($view_tag);
    }

    function add_jquery_ui()
    {
        $js_tag = $this->create_open_tag('script', array('src'=>'/js/jquery.js', 'type'=>'text/javascript'));
        $close_tag = $this->create_close_tag('script');
        $this->add_head_tag($js_tag);
        $this->add_head_tag($close_tag);
        $js_tag = $this->create_open_tag('script', array('src'=>'/js/jquery-ui.js', 'type'=>'text/javascript'));
        $this->add_head_tag($js_tag);
        $this->add_head_tag($close_tag);
        $js_tag = $this->create_open_tag('script', array('src'=>'/js/jquery.ui.touch-punch.min.js', 'type'=>'text/javascript'));
        $this->add_head_tag($js_tag);
        $this->add_head_tag($close_tag);
    }

    function add_header_js_and_style()
    {
        $js_tag = $this->create_open_tag('script', array('src'=>'/js/tinynav.js', 'type'=>'text/javascript'));
        $close_tag = $this->create_close_tag('script');
        $this->add_head_tag($js_tag);
        $this->add_head_tag($close_tag);
        $script_js_tags = file_get_contents(dirname(__FILE__).'/include.HeaderStyleScript.php');
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
