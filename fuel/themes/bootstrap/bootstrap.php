<?php

\Theme::instance()->asset->css('bootstrap.css', array(), 'header.css');
\Theme::instance()->asset->css(array('sticky-footer.css', 'application.css'), array('dependencies' => 'bootstrap.css'), 'header.css');
\Theme::instance()->asset->css('font-awesome.min.css', array(), 'header.css');

\Theme::instance()->asset->js('bootstrap.js', array(), 'footer.js');
\Theme::instance()->asset->js('application.js', array('dependencies' => 'bootstrap.js'), 'footer.js');
