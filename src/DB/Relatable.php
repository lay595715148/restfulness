<?php
namespace Lay\DB;

interface Relatable {
	public function query($sql, $encoding = 'UTF8', array $option = array());
}
// PHP END