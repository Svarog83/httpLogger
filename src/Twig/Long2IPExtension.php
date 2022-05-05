<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Long2IPExtension extends AbstractExtension {
	public function getFilters(): array {
		return [ new TwigFilter( 'long2ip', [ $this, 'long2ip' ] ), ];
	}

	public function long2ip( int $ip ): string {
		return long2ip( $ip );
	}
}