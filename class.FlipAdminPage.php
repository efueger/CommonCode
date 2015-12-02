<?php
require_once('class.FlipPage.php');

class FlipAdminPage extends FlipPage
{
    public $user;
    public $is_admin = false;

    function __construct($title, $admin_group='LDAPAdmins')
    {
        $this->user = FlipSession::getUser();
        if($this->user === false || $this->user === null)
        {
            $this->is_admin = false;
        }
        else
        {
            $this->is_admin = $this->user->isInGroupNamed($admin_group);
        }
        parent::__construct($title);
        if($this->minified === 'min')
        {
            $this->add_css_from_src('/css/common/admin.min.css');
        }
        else
        {
            $this->add_css_from_src('/css/common/admin.css');
        }
        $this->add_js(JS_METISMENU, false);
    }

    function addAllLinks()
    {
        if($this->user === false || $this->user === null)
        {
            $this->add_link('<span class="glyphicon glyphicon-log-in"></span> Login', $this->login_url);
        }
        else
        {
            $this->add_links();
            $this->add_link('<span class="glyphicon glyphicon-log-out"></span> Logout', $this->logout_url);
        }
    }

    function addHeader()
    {
        $sites = '';
        foreach($this->sites as $link => $site_name)
        {
            $sites .= '<li><a href="'.$site_name.'">'.$link.'</a></li>';
        }
        $side_nav = '';
        $link_names = array_keys($this->links);
        foreach($link_names as $link_name)
        {
            if(is_array($this->links[$link_name]))
            {
                $side_nav .= '<li>';
                if(isset($this->links[$link_name]['_']))
                {
                    $side_nav .= $this->create_link($link_name.' <span class="glyphicon arrow"></span>', $this->links[$link_name]['_']);
                }
                else
                {
                    $side_nav .= $this->create_link($link_name.' <span class="glyphicon arrow"></span>');
                }
                $side_nav .= '<ul>';
                $sub_names = array_keys($this->links[$link_name]);
                foreach($sub_names as $sub_name)
                {
                    if(strcmp($sub_name, '_') === 0)
                    {
                        continue;
                    }
                    $side_nav .='<li>'.$this->create_link($sub_name, $this->links[$link_name][$sub_name]).'</li>';
                }
                $side_nav .= '</ul></li>';
            }
            else
            {
                $side_nav .= '<li>'.$this->create_link($link_name, $this->links[$link_name]).'</li>';
            }
        }
        if($this->user === false || $this->user === null)
        {
            $log = '<a href="https://profiles.burningflipside.com/login.php?return='.$this->current_url().'"><span class="glyphicon glyphicon-log-in"></span></a>';
        }
        else
        {
            $log = '<a href="https://profiles.burningflipside.com/logout.php"><span class="glyphicon glyphicon-log-out"></span></a>';
        }
        $this->body = '<div id="wrapper">
                  <nav class="navbar navbar-default navbar-static-top" role=navigation" style="margin-bottom: 0">
                      <div class="navbar-header">
                          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                              <span class="sr-only">Toggle Navigation</span>
                              <span class="icon-bar"></span>
                              <span class="icon-bar"></span>
                              <span class="icon-bar"></span>
                          </button>
                          <a class="navbar-brand" href="index.php">'.$this->title.'</a>
                      </div>
                      <ul class="nav navbar-top-links navbar-right">
                          <a href="..">
                              <span class="glyphicon glyphicon-home"></span>
                          </a>
                          &nbsp;&nbsp;'.$log.'
                          <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                  <span class="glyphicon glyphicon-link"></span>
                                  <b class="caret"></b>
                              </a>
                              <ul class="dropdown-menu dropdown-sites">
                                  '.$sites.'
                              </ul>
                          </li>
                      </ul>
                      <div class="navbar-default sidebar" role="navigation">
                          <div class="sidebar-nav navbar-collapse" style="height: 1px;">
                              <ul class="nav" id="side-menu">
                                  '.$side_nav.'
                              </ul>
                          </div>
                      </div>
                  </nav>
                  <div id="page-wrapper" style="min-height: 538px;">'.$this->body.'</div></div>';
    }

    function print_page($header=true)
    {
        if($this->user === false || $this->user === null)
        {
            $this->body = '
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">You must <a href="'.$this->login_url.'?return='.$this->current_url().'">log in <span class="glyphicon glyphicon-log-in"></span></a> to access the '.$this->title.' Admin system!</h1>
            </div>
        </div>';
        }
        else if($this->is_admin === false)
        {
            $this->body = '
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">The current user does not have access rights to the '.$this->title.' Admin system!</h1>
            </div>
        </div>';
        }
        parent::print_page();
    }
}
/* vim: set tabstop=4 shiftwidth=4 expandtab: */
?>
