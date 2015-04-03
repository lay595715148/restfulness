<?php
namespace Lay\Core;

use ArrayAccess;

interface InterfaceBean extends ArrayAccess {
	public abstract function toArray();
}