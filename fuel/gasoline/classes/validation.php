<?php namespace Gasoline;

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

class Validation extends \Fuel\Core\Validation {
    
    /**
     * Initialize this class by loading the validation language lines
     * 
     * @return  void
     */
    public static function _init()
    {
        \Lang::load('validation', true);
    }
    
    
    /**
     * Validate the given password to be the one of the currently logged in user
     * 
     * Also takes into account if the user is guest user (if guest login is enabled)
     * 
     * @param   string  $password   The given password to match
     * 
     * @return  bool                Returns true if the password matches the stored
     *                              password, otherwise false
     */
    public static function _validation_match_users_password($password = null)
    {
        if ( ! $password )
        {
            return false;
        }
        
        try
        {
            $user = \Auth::get_user();
            
            if ( ! $user->password )
            {
                return false;
            }
        }
        catch ( \Exception $e )
        {
            logger(\Fuel::L_WARNING, $e->getMessage(), __METHOD__);
            
            return false;
        }
        
        try
        {
            return \Auth::match_password($user->password, $password);
        }
        catch ( \Exception $e )
        {
            logger(\Fuel::L_DEBUG, $e->getMessage(), __METHOD__);
        }
        
        return false;
    }
    
    
    /**
     * Check whether the user is valid regarding the given field to match
     * 
     * Allows to check e.g., whether a given username or email is a valid one
     *  i.e., is stored inside the database
     * 
     * @param   string  $val        The value to match
     * @param   string  $field      Field to check against e.g., 'email' or 'username'
     * 
     * @return  boolean             Returns true if the user exists, otherwise false
     */
    public static function _validation_user_exists($val, $field)
    {
        if ( ! $val )
        {
            return false;
        }
        
        $result = \Model\Auth_User::query()
            ->where($field, '=', $val)
            ->get();
        
        return ( $result ? count($result) == 1 : false );
    }
    
    
    /**
     * Checks for uniqueness of the given value inside the database
     * 
     * @access  public
     * @static
     * @param   string  $value      The value to check for
     * @param   string  $tbl_field  Table name and field to check for value in
     *                              separated by a dot
     * @param   string  $model      Model to check with the PKs when excluding
     *                              data form the query
     * 
     * @return  boolean             Returns true if the value is unique
     */
    public static function _validation_unique($val, $tbl_field, $model = null)
    {
        if ( ! $val )
        {
            return true;
        }
        
        list($table, $field) = explode('.', $tbl_field, 2);
        
        $query = \DB::select($field)
            ->from($table)
            ->where($field, '=', $val);
        
        // Do we have a model?
        if ( isset($model) )
        {
            // Then get it's primary keys
            foreach ( $model::primary_key() as $pk )
            {
                // And see if there is a post value for these
                if ( $value = \Input::post($pk, false) )
                {
                    // Then exclude them from the query
                    $query->and_where($pk, '!=', $value);
                }
            }
        }
        
        // Execute query
        $result = $query->execute();
        
        // No results?
        if ( $result->count() == 0)
        {
            // Then it's unique
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Check whether the given value exists within the database
     * 
     * @access  public
     * @static
     * @param   mixed   $value      Value to check for existence
     * @param   array   $tbl_fld    Table name and field to check on separated by
     *                              a period e.g., "users.id"
     * 
     * @return  boolean             Returns true if the validation passed (i.e.,
     *                              item does _NOT_ exist in the DB), otherwise
     *                              returns false i.e., validation failed, if the
     *                              item(s) do 
     */
    public static function _validation_exists($val, $tbl_fld)
    {
        if ( is_null($val) )
        {
            return true;
        }
        
        // Get table and field from the options passed
        list($table, $field) = explode('.', $tbl_fld, 2);
        
        // Create a query and allow for multiple values to check for existence of all
        $query = \DB::select($field)
            ->from($table)
            ->where($field, 'IN', (array) $val);
        
        // Executei the query
        $result = $query->execute();
        
        // Check the count of results against the count of values to check for
        // existence. Found as many rows as we wanted to?
        if ( $result->count() == count($val) )
        {
            return true;
        }
        
        // If the count is not the same, then we're missing data and it did not
        // validate correctly
        return false;
    }
    
}
