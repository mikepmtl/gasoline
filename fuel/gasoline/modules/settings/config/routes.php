<?php

die(microtime());

return array(
    'admin/settings/(:segment)/(:any)'  => '$1/admin/settings/$2',
    'admin/settings/(:segment)'         => '$1/admin/settings',
);
