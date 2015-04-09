<?php
namespace Lay\DB;

interface Integratable {
    /**
     * 
     * @return boolean
     */
	public function integrate();
    /**
     * 
     * @return array
     */
	public function principle();
}
// PHP END