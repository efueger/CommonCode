<?php

require('libs/browscap-php/src/phpbrowscap/Browscap.php');
use phpbrowscap\Browscap;

class WebPage
{
    public $title;

    public $head_tags;
    public $body;
    private $bc;
    public $browser;
    public $body_tags;
    public $import_support;

    function __construct($title)
    {
        $this->title = $title;
        $this->head_tags = array();
        $this->body = '';
        $this->bc = new Browscap('/var/php_cache/browser');
        $this->bc->doAutoUpdate = false;
        $this->bc->lowercase = true;
        $this->browser = $this->getBrowser();
        $this->import_support = false;
        
        $browser_name = $this->get_browser_name();
        if($browser_name === 'IE' && $this->get_browser_major_ver() <= 6)
        {
            header( 'Location: /badbrowser.php' ) ;
        }
        else if($browser_name === 'Chrome' && $this->get_browser_major_ver() >= 36)
        {
            $this->import_support = true;
        }
    }

    private function getBrowser()
    {
        static $browser;//No accident can arise from depending on an unset variable.
        if(!isset($browser))
        {
            $browser = $this->bc->getBrowser();
        }
        return $browser;
    }

    private function get_browser_name()
    {
        if(isset($this->browser->Browser))
        {
            return $this->browser->Browser;
        }
        else
        {
            return $this->browser->browser;
        }
    }

    private function get_browser_major_ver()
    {
        if(isset($this->browser->MajorVer))
        {
            return $this->browser->MajorVer;
        }
        else
        {
            return $this->browser->majorver;
        }
    }

    function print_doctype()
    {
        echo "<!DOCTYPE html>\n";
    }

    function print_open_html()
    {
        echo "<HTML>\n";
    }

    function print_close_html()
    {
        echo "</HTML>\n";
    }

    function print_page()
    {
        $this->print_doctype();
        $this->print_open_html();
        $this->print_head('    ');
        $this->print_body('    ');
        $this->print_close_html();
    }

    function add_head_tag($tag)
    {
        array_push($this->head_tags, $tag);
    }

    function create_open_tag($tag_name, $attribs=array(), $self_close=false)
    {
        $tag = '<'.$tag_name;
        $attrib_names = array_keys($attribs);
        foreach($attrib_names as $attrib_name)
        {
            $tag.=' '.$attrib_name;
            if($attribs[$attrib_name])
            {
                $tag.='="'.$attribs[$attrib_name].'"';
            }
        }
        if($self_close)
        {
            return $tag.'/>';
        }
        else
        {
            return $tag.'>';
        }
    }
    
    function create_close_tag($tag_name)
    {
        return '</'.$tag_name.'>';
    }

    function create_link($link_name, $link_target='#')
    {
        $start_tag = $this->create_open_tag('a', array('href'=>$link_target));
        $end_tag = $this->create_close_tag('a');
        return $start_tag.$link_name.$end_tag;
    }

    function print_ie_compatability($prefix='')
    {
       //IE 8 doesn't support HTML 5. Install the shim...
       if($this->get_browser_major_ver() < 9)
       {
           echo $prefix.'<script src="js/html5.js"></script>';
           echo "\n";
       }
       //Tell the browser not to use compatability mode...
       echo $prefix.'<meta http-equiv="X-UA-Compatible" content="IE=edge"/>';
       echo "\n";
    }

    function print_head($prefix='')
    {
        echo $prefix."<HEAD>\n";
        if($this->get_browser_name() === 'IE')
        {
            $this->print_ie_compatability($prefix.$prefix);
        }
        echo $prefix.$prefix."<TITLE>".$this->title."</TITLE>\n";
        foreach($this->head_tags as $tag)
        {
            echo $prefix.$prefix.$tag."\n";
        }
        echo $prefix."</HEAD>\n";
    }

    function print_body($prefix='')
    {
        echo $prefix.'<BODY '.$this->body_tags.'>';
        echo $prefix.$prefix.$this->body."\n";
        echo $prefix."</BODY>\n";
    }

    function current_url()
    {
        if($_SERVER['REQUEST_URI'][0] === '/')
        {
            return 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'."{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        return 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'."{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
