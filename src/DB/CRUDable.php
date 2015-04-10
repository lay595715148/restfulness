<?php
namespace Lay\DB;

use ArrayAccess;
use Iterator;
use JsonSerializable;

interface CRUDable {
    public function get($id);
    public function add(array $info);
    public function del($id);
    public function upd($id, array $info);
    public function count(array $info = array());
}
// PHP END