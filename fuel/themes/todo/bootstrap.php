<?php

\Theme::instance()->asset->css('bootstrap.css', array(), 'header.css');
\Theme::instance()->asset->css(array('animate.css', 'font-awesome.min.css', 'font.css', 'plugin.css', 'todo.css'), array('dependencies' => 'bootstrap.css'), 'header.css');

\Theme::instance()->asset->js('jquery.min.js', array(), 'footer.js');
\Theme::instance()->asset->js('bootstrap.js', array('dependencies' => 'jquery.min.js'), 'footer.js');
\Theme::instance()->asset->js(array('todo.js', 'todo.plugin.js', 'todo.data.js', 'fuelux/fuelux.js'), array('dependencies' => 'bootstrap.js'), 'footer.js');

\Config::load('pagination', true);

\Config::set('pagination.todo', array(
    'wrapper'                 => "<ul class=\"pagination m-t-none m-b-none\">\n\t{pagination}\n\t</ul>\n",
    
    'first'                   => "\n\t\t<li>{link}</li>",
    'first-marker'            => "<i class=\"fa fa-angle-double-left\"></i>",
    'first-link'              => "<a href=\"{uri}\" rel=\"first\">{page}</a>",
    
    'first-inactive'          => "",
    'first-inactive-link'     => "",
    
    'previous'                => "\n\t\t<li>{link}</li>",
    'previous-marker'         => "<i class=\"fa fa-angle-left\"></i>",
    'previous-link'           => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'previous-inactive'       => "\n\t\t<li class=\"disabled\">{link}</li>",
    'previous-inactive-link'  => "<a href=\"#\" rel=\"prev\">{page}</a>",
    
    'regular'                 => "\n\t\t<li>{link}</li>",
    'regular-link'            => "<a href=\"{uri}\">{page}</a>",
    
    'active'                  => "\n\t\t<li class=\"active\">{link}</li>",
    'active-link'             => "<a href=\"#\">{page} <span class=\"sr-only\"></span></a>",
    
    'next'                    => "\n\t\t<li>{link}</li>",
    'next-marker'             => "<i class=\"fa fa-angle-right\"></i>",
    'next-link'               => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'next-inactive'           => "\n\t\t<li class=\"disabled\">{link}</li>",
    'next-inactive-link'      => "<a href=\"#\" rel=\"next\">{page}</a>",
    
    'last'                    => "\n\t\t<li>{link}</li>",
    'last-marker'             => "<i class=\"fa fa-double-angle-right\"></i>",
    'last-link'               => "<a href=\"{uri}\" rel=\"last\">{page}</a>",
    
    'last-inactive'           => "",
    'last-inactive-link'      => "",
));

\Config::set('pagination.todo-sm', array(
    'wrapper'                 => "<ul class=\"pagination pagination-sm m-t-none m-b-none\">\n\t{pagination}\n\t</ul>\n",
    
    'first'                   => "\n\t\t<li>{link}</li>",
    'first-marker'            => "<i class=\"fa fa-angle-double-left\"></i>",
    'first-link'              => "<a href=\"{uri}\" rel=\"first\">{page}</a>",
    
    'first-inactive'          => "",
    'first-inactive-link'     => "",
    
    'previous'                => "\n\t\t<li>{link}</li>",
    'previous-marker'         => "<i class=\"fa fa-angle-left\"></i>",
    'previous-link'           => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'previous-inactive'       => "\n\t\t<li class=\"disabled\">{link}</li>",
    'previous-inactive-link'  => "<a href=\"#\" rel=\"prev\">{page}</a>",
    
    'regular'                 => "\n\t\t<li>{link}</li>",
    'regular-link'            => "<a href=\"{uri}\">{page}</a>",
    
    'active'                  => "\n\t\t<li class=\"active\">{link}</li>",
    'active-link'             => "<a href=\"#\">{page} <span class=\"sr-only\"></span></a>",
    
    'next'                    => "\n\t\t<li>{link}</li>",
    'next-marker'             => "<i class=\"fa fa-angle-right\"></i>",
    'next-link'               => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'next-inactive'           => "\n\t\t<li class=\"disabled\">{link}</li>",
    'next-inactive-link'      => "<a href=\"#\" rel=\"next\">{page}</a>",
    
    'last'                    => "\n\t\t<li>{link}</li>",
    'last-marker'             => "<i class=\"fa fa-double-angle-right\"></i>",
    'last-link'               => "<a href=\"{uri}\" rel=\"last\">{page}</a>",
    
    'last-inactive'           => "",
    'last-inactive-link'      => "",
));

\Config::set('pagination.todo-lg', array(
    'wrapper'                 => "<ul class=\"pagination pagination-lg m-t-none m-b-none\">\n\t{pagination}\n\t</ul>\n",
    
    'first'                   => "\n\t\t<li>{link}</li>",
    'first-marker'            => "<i class=\"fa fa-angle-double-left\"></i>",
    'first-link'              => "<a href=\"{uri}\" rel=\"first\">{page}</a>",
    
    'first-inactive'          => "",
    'first-inactive-link'     => "",
    
    'previous'                => "\n\t\t<li>{link}</li>",
    'previous-marker'         => "<i class=\"fa fa-angle-left\"></i>",
    'previous-link'           => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'previous-inactive'       => "\n\t\t<li class=\"disabled\">{link}</li>",
    'previous-inactive-link'  => "<a href=\"#\" rel=\"prev\">{page}</a>",
    
    'regular'                 => "\n\t\t<li>{link}</li>",
    'regular-link'            => "<a href=\"{uri}\">{page}</a>",
    
    'active'                  => "\n\t\t<li class=\"active\">{link}</li>",
    'active-link'             => "<a href=\"#\">{page} <span class=\"sr-only\"></span></a>",
    
    'next'                    => "\n\t\t<li>{link}</li>",
    'next-marker'             => "<i class=\"fa fa-angle-right\"></i>",
    'next-link'               => "<a href=\"{uri}\" rel=\"prev\">{page}</a>",
    
    'next-inactive'           => "\n\t\t<li class=\"disabled\">{link}</li>",
    'next-inactive-link'      => "<a href=\"#\" rel=\"next\">{page}</a>",
    
    'last'                    => "\n\t\t<li>{link}</li>",
    'last-marker'             => "<i class=\"fa fa-double-angle-right\"></i>",
    'last-link'               => "<a href=\"{uri}\" rel=\"last\">{page}</a>",
    
    'last-inactive'           => "",
    'last-inactive-link'      => "",
));
