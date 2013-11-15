<?php

\Theme::instance()->asset->css('bootstrap.css', array(), 'header.css');
\Theme::instance()->asset->css(array('animate.css', 'font-awesome.min.css', 'font.css', 'plugin.css', 'todo.css'), array('dependencies' => 'bootstrap.css'), 'header.css');

\Theme::instance()->asset->js('jquery.min.js', array(), 'footer.js');
\Theme::instance()->asset->js('bootstrap.js', array('dependencies' => 'jquery.min.js'), 'footer.js');
\Theme::instance()->asset->js(array('todo.js', 'todo.plugin.js', 'todo.data.js', 'fuelux/fuelux.js'), array('dependencies' => 'bootstrap.js'), 'footer.js');
