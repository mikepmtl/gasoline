<?php namespace Gasoline;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     0.1-dev
 * @author      Gasoline Development Teams
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @copyright   2010 - 2013 Fuel Development Team
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
     * @param   array   $options    Option to use for matching. Currently, this is
     *                              just the field to match.
     * 
     * @return  boolean             Returns true if the user exists, otherwise false
     */
    public static function _validation_user_exists($val, $options = array())
    {
        if ( ! $val )
        {
            return false;
        }
        
        $field = ( is_array($options) ? reset($options) : $options );
        
        $result = \Model\Auth_User::query()
            ->where($field, '=', $val)
            ->get();
        
        return ( $result ? count($result) == 1 : false );
    }
    
}
