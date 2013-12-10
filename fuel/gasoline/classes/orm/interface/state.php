<?php namespace Gasoline\Orm;

interface Interface_State {
    
    public function is_activatable();
    
    public function is_activated();
    
    public function is_deactivatable();
    
    public function is_deactivated();
    
}
