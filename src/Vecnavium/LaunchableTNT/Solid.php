<?php

declare(strict_types=1);

namespace Vecnavium\LaunchableTNT;

abstract class Solid extends Block{

	public function isSolid() : bool{
		return true;
	}
}