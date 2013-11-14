<?php

\Theme::instance()->asset->css('bootstrap.css', array(), 'header.css');
\Theme::instance()->asset->css(array('animate.css', 'font-awesome.min.css', 'font.css', 'plugin.css', 'app.css'), array('dependencies' => 'bootstrap.css'), 'header.css');

\Theme::instance()->asset->js('jquery.min.js', array(), 'footer.js');
\Theme::instance()->asset->js('bootstrap.js', array('dependencies' => 'jquery.min.js'), 'footer.js');
\Theme::instance()->asset->js(array('app.js', 'app.plugin.js', 'app.data.js', 'fuelux/fuelux.js'), array('dependencies' => 'bootstrap.js'), 'footer.js');
