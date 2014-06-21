<?php
class WebPage
{
    public $title;

    public $head_tags;
    public $body;

    function __construct($title)
    {
        $this->title = $title;
        $this->head_tags = array();
        $this->body = '';
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

    function print_head($prefix='')
    {
        echo $prefix."<HEAD>\n";
        echo $prefix.$prefix."<TITLE>".$this->title."</TITLE>\n";
        foreach($this->head_tags as $tag)
        {
            echo $prefix.$prefix.$tag."\n";
        }
        echo $prefix."</HEAD>\n";
    }

    function print_body($prefix='')
    {
        echo $prefix."<BODY>\n";
        echo $prefix.$prefix.$this->body."\n";
        echo $prefix."</BODY>\n";
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
