<?php namespace Gasoline\Orm;

interface Interface_Protect {
    
    public function is_protectable();
    
    public function is_protected();
    
    public function is_unprotectable();
    
    public function is_unprotected();
    
}
