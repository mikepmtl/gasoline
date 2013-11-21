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

class Create_modules {
	
	public function up()
	{
		\DBUtil::create_table('modules', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
			'name' => array('constraint' => 255, 'type' => 'varchar'),
			'slug' => array('constraint' => 255, 'type' => 'varchar'),
			'author' => array('constraint' => 255, 'type' => 'varchar'),
			'version' => array('constraint' => 50, 'type' => 'varchar'),
			'scope' => array('constraint' => 1, 'type' => 'tinyint', 'default' => 0),
			'status' => array('constraint' => 1, 'type' => 'tinyint', 'default' => 0),
			'protected' => array('constraint' => 1, 'type' => 'tinyint', 'default' => 0),
			'user_id' => array('constraint' => 16, 'type' => 'bigint', 'default' => 0, 'unsigned' => true),
			'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true, 'unsigned' => true),
			'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true, 'unsigned' => true),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table('modules');
	}
}

/* End of file 001_create_modules.php */
/* Location: ./fuel/gasoline/modules/modules/migrations/001_create_modules.php */
