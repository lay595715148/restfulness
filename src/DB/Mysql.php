<?php
namespace Lay\DB;

use Lay\DB\DataBase;
use Lay\DB\Relatable;

class Mysql extends DataBase implements Relatable {
	public function connect() {
		//mysql_connect();
	}
	public function close() {
		//mysql_connect();
	}
    public final function query($sql, $encoding = 'UTF8', array $option = array()) {
    }
    public final function get($id) {
    }
    public final function add(array $info) {
    }
    public final function del($id) {
    }
    public final function upd($id, array $info) {
    }
    public final function count(array $info = array()) {
    }
}
// PHP END