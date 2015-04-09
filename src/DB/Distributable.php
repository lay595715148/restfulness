<?php
namespace Lay\DB;

interface Distributable {
	public function execute(array $cmd, array $option = array());
}
// PHP END