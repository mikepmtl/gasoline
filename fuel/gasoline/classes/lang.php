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

class Lang extends \Fuel\Core\Lang {
    
    public static function load($file, $group = null, $language = null, $overwrite = false, $reload = false)
    {
        // get the active language and all fallback languages
        $language OR $language = static::get_lang();
        $languages = static::$fallback;
        
        // make sure we don't have the active language in the fallback array
        if ( in_array($language, $languages) )
        {
            unset($languages[array_search($language, $languages)]);
        }
        
        // stick the active language to the front of the list
        array_unshift($languages, $language);
        
        if ( ! $reload &&
             ! is_array($file) &&
             ! is_object($file) &&
            array_key_exists($file, static::$loaded_files) )
        {
            $group === true && $group = $file;
            if ( $group === null OR $group === false OR ! isset(static::$lines[$language][$group]) )
            {
                return false;
            }
            return static::$lines[$language][$group];
        }
        
        $lang = array();
        if ( is_array($file) )
        {
            $lang = $file;
        }
        elseif ( is_string($file) )
        {
            $info = pathinfo($file);
            $type = 'php';
            if ( isset($info['extension']) )
            {
                $type = $info['extension'];
                // Keep extension when it's an absolute path, because the finder won't add it
                if ( $file[0] !== '/' && $file[1] !== ':' )
                {
                    $file = substr($file, 0, -(strlen($type) + 1));
                }
            }
            
            $class = '\\Lang_'.ucfirst($type);
            
            if ( class_exists($class) )
            {
                static::$loaded_files[$file] = true;
                $file = new $class($file, $languages);
            }
            else
            {
                throw new \FuelException(sprintf('Invalid lang type "%s".', $type));
            }
        }
        
        if ( $file instanceof \Fuel\Core\Lang_Interface )
        {
            try
            {
                $lang = $file->load($overwrite);
            }
            catch ( \LangException $e )
            {
                $lang = array();
            }
            
            $group = $group === true ? $file->group() : $group;
        }
        
        if ( $group === null )
        {
            isset(static::$lines[$language]) OR static::$lines[$language] = array();
            static::$lines[$language] = $overwrite ? array_merge(static::$lines[$language], $lang) : \Arr::merge(static::$lines[$language], $lang);
        }
        else
        {
            $group = ( $group === true ) ? $file : $group;
            isset(static::$lines[$language]) OR static::$lines[$language] = array();
            
            $group = ( strpos($group, DS) === false ? str_replace(DS, '.', $group) : $group );
            
            if ( $overwrite )
            {
                \Arr::delete(static::$lines[$language], $group);
                \Arr::set(static::$lines[$language], $group, $lang);
            }
            else
            {
                $tmp = array();
                \Arr::set($tmp, $group, $lang);
                
                static::$lines[$language] = \Arr::merge(static::$lines[$language], \Arr::merge(array(), $tmp));
            }
            
            // if ( strpos($group, DS) !== false OR strpos($group, '.') !== false )
            // {
            //     $sections = explode( strpos($group, DS) !== false ? DS : '.', $group);
                
            //     $lines =& static::$lines[$language];
                
            //     while ( count($sections) )
            //     {
            //         $section = array_shift($sections);
                    
            //         isset($lines[$section]) OR $lines[$section] = array();
                    
            //         $lines =& $lines[$section];
            //     }
                
            //     $lines = $overwrite ? $lang : \Arr::merge($lines, $lang);
            // }
            // else
            // {
            //     isset(static::$lines[$language][$group]) or static::$lines[$language][$group] = array();
            //     static::$lines[$language][$group] = $overwrite ? array_merge(static::$lines[$language][$group], $lang) : \Arr::merge(static::$lines[$language][$group], $lang);
            // }
        }
        
        return $lang;
    }
    
}

/* End of file lang.php */
/* Location: ./fuel/gasoline/classes/lang.php */
