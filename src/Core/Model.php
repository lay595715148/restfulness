<?php
namespace Lay\Core;

use Lay\Core\AbstractModel;
use Lay\Core\InterfaceModel;

use Lay\Traits\Singleton;

abstract class Model extends Bean implements InterfaceModel {
    use Singleton;
    protected $db;
    public function save() {
        // TODO
        $pk = $this->primary();
        $data = $this->toArray();
        $data = array_filter($data, function ($var) {
            return $var !== null;
        });
        if ($this->$pk) {
            unset($data[$pk]);
            $this->upd($this->$pk, $data);
        } else {
            $last_id = $this->add($data);
            if ($last_id) {
                $this->$pk = $last_id;
            }
        }
    }
}
// PHP END
 