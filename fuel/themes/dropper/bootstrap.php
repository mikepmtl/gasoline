<?php

\Theme::instance()->asset->css('bootstrap.css', array(), 'header.css');
\Theme::instance()->asset->css(array('sticky-footer.css', 'app.css'), array('dependencies' => 'bootstrap.css'), 'header.css');
\Theme::instance()->asset->css('font-awesome.min.css', array(), 'header.css');

\Theme::instance()->asset->js('jquery.min.js', array(), 'footer.js');
\Theme::instance()->asset->js(array('html5shiv.js', 'respond.min.js'), array('dependencies' => 'jquery.min.js'), 'footer.js');
\Theme::instance()->asset->js('bootstrap.js', array('dependencies' => 'jquery.min.js'), 'footer.js');
\Theme::instance()->asset->js('dropper.input.js', array('dependencies' => 'bootstrap.js'), 'footer.js');
\Theme::instance()->asset->js('app.js', array('dependencies' => 'bootstrap.js'), 'footer.js');
