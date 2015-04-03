<?php
namespace Lay\Core;

use ArrayAccess;

interface InterfaceBean extends ArrayAccess {
	public function toArray();
}