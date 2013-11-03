<?php namespace Gasoline;

/**
 * Part of the Gasoline framework
 *
 * @package     Gasoline
 * @version     1.0-dev
 * @author      Gasoline Development Teams
 * @author      Fuel Development Team
 * @license     MIT License
 * @copyright   2013 Gasoline Development Team
 * @copyright   2010 - 2013 Fuel Development Team
 * @link        http://hubspace.github.io/gasoline
 */

class Asset_Instance extends \Fuel\Core\Asset_Instance {
    
    /**
     * Extend the parent's render method to sort the assets before rendering
     * 
     * @access  public
     * @param   string  $group  Name of the group to render
     * @param   boolean $raw    Whether to return the raw file or not
     * 
     * @return  string          The output of the rendered group
     */
    public function render($group = null, $raw = false)
    {
        is_null($group) and $group = '_default_';
        
        $org_group = $group;

        if (is_string($group))
        {
            isset($this->_groups[$group]) and $group = $this->_groups[$group];
        }

        is_array($group) or $group = array();
        
        $this->_groups[$org_group] = $this->_sort_group($group);
        
        return parent::render($org_group, $raw);
    }
    
    /**
     * Parse an asset by adding it to the group
     * 
     * @access  protected
     * 
     * @access  private
     * @param   string  $type   The asset type
     * @param   mixed   $assets The file name, or an array files.
     * @param   array   $attr   An array of extra attributes
     * @param   string  $group  The asset group name
     * @param   boolean $raw    Whether to return the raw files or not
     * 
     * @return  string
     */
    protected function _parse_assets($type, $assets, $attr, $group, $raw = false)
    {
        if ( ! is_array($assets) )
        {
            $assets = array($assets);
        }
        
        foreach ( $assets as $key => $asset )
        {
            // Prevent duplicate files in a group.
            if ( \Arr::get($this->_groups, $group . $key . '.file' ) == $asset )
            {
                continue;
            }
            
            $attr = (array) $attr;
            $dependencies = array();
            
            // Check whether there are dependencies given
            if ( isset($attr['dependencies']) )
            {
                // Copy them and unset it
                $dependencies = (array) $attr['dependencies'];
                unset($attr['dependencies']);
            }
            
            // Append to local storage
            $this->_groups[$group][] = array(
                'type'          => $type,
                'file'          => $asset,
                'raw'           => $raw,
                'attr'          => $attr,
                'dependencies'  => $dependencies,
            );
        }
    }
    
    
    /**
     * Sort the given group to have dependent assets sorted after the dependecy
     * 
     * @author  laravel
     * 
     * @access  protected
     * @param   array       $group  Group of assets to sort
     * 
     * @return  array       Returns sorted array
     */
    protected function _sort_group($group = array())
    {
        list($original, $sorted) = array($group, array());
        // As long as there are assets in the original group
        while ( count($group) )
        {
            // Loop over each asset and evaluate its dependencies
            foreach ( $group as $k => $asset )
            {
                $this->_evaluate_asset($k, $asset, $original, $sorted, $group);
            }
        }
        
        return $sorted;
    }
    
    
    /**
     * Evaluate an assets dependencies
     * 
     * @author  laravel
     * 
     * @access  protected
     * 
     * @param  [type] $asset    [description]
     * @param  [type] $value    [description]
     * @param  [type] $original [description]
     * @param  [type] $sorted   [description]
     * @param  [type] $assets   [description]
     *
     * @return [type]           [description]
     */
    protected function _evaluate_asset($k, $asset, $original, &$sorted, &$assets)
    {
        // If the asset has no more dependencies, we can add it to the sorted list
        //  and remove it from the array of assets. Otherwise, we will not verify
        //  the asset's dependencies and determine if they've been sorted.
        if ( count($assets[$k]['dependencies']) == 0 )
        {
            $sorted[$k] = $asset;

            unset($assets[$k]);
        }
        else
        {
            foreach ( $assets[$k]['dependencies'] as $key => $dependency )
            {
                if ( ! $this->_dependency_is_valid($asset, $dependency, $original, $assets) )
                {
                    unset($assets[$k]['dependencies'][$key]);

                    continue;
                }

                // If the dependency has not yet been added to the sorted list, we can not
                //  remove it from this asset's array of dependencies. We'll try again on
                //  the next trip through the loop.
                if ( ! isset($sorted[$dependency]))
                {
                    continue;
                }

                unset($assets[$k]['dependencies'][$key]);
            }
        }       
    }
    
    
    /**
     * Verify that an asset's dependency is valid.
     *
     * A dependency is considered valid if it exists, is not a circular reference, and is
     * not a reference to the owning asset itself. If the dependency doesn't exist, no
     * error or warning will be given. For the other cases, an exception is thrown.
     * 
     * @author  laravel
     * 
     * @access  protected
     *
     * @param   string  $asset
     * @param   string  $dependency
     * @param   array   $original
     * @param   array   $assets
     * 
     * @return  boolean
     */
    protected function _dependency_is_valid($asset, $dependency, $original, $assets)
    {
        if ( ! isset($original[$dependency]) )
        {
            return FALSE;
        }
        elseif ( $dependency === $asset )
        {
            throw new \RuntimeException('Asset [' . $asset . '] is dependent on itself');
            
            return FALSE;
        }
        elseif ( isset($assets[$dependency]) && in_array($asset, $assets[$dependency]['dependencies']) )
        {
            throw new \RuntimeException('Assets [' . $asset . '] and [' . $dependency . '] have a circular dependency');
            
            return FALSE;
        }
        
        return TRUE;
    }
    
}

/* End of file instancephp */
/* Location: ./fuel/gasoline/classes/asset/instance.php */
