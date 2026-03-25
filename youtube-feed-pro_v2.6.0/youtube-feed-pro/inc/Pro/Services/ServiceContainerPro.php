<?php

namespace SmashBalloon\YouTubeFeed\Pro\Services;

use SmashBalloon\YouTubeFeed\Services\ServiceContainer;
use SmashBalloon\YouTubeFeed\Pro\Services\AdminAjaxServicePro;

class ServiceContainerPro extends ServiceContainer
{

	protected $services = [
		AdminAjaxServicePro::class,
	];
}
