<?php namespace Fuel\Migrations;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @link        http://hubspace.github.io/gasoline
 */

class Add_website_to_modules {
    
    public function up()
    {
        \DBUtil::add_fields('modules', array(
            'website' => array('constraint' => 255, 'type' => 'varchar', 'default' => '', 'null' => true, 'after' => 'author'),
        ));
    }

    public function down()
    {
        \DBUtil::drop_fields('modules', array(
            'website'
        ));
    }
}

/* End of file 002_add_website_to_modules.php */
/* Location: ./fuel/gasoline/modules/modules/migrations/002_add_website_to_modules.php */
