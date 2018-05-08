<?php

namespace App\EventListener;

use App\Service\History;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class LastRouteListener
{

	private $history;

	public function __construct(History $history)
	{
		$this->history = $history;
	}

	public function onKernelRequest(GetResponseEvent $event)
	{
		// Do not save subrequests
		if($event->getRequestType() !== HttpKernel::MASTER_REQUEST){
			return;
		}

		$request = $event->getRequest();

		$routeName = $request->get('_route');
		$routeParams = $request->get('_route_params');
		if($routeName[0] == '_'){
			return;
		}
		$routeData = ['name' => $routeName, 'params' => $routeParams];

		// Do not save same matched route twice
		$lastRoute = $this->history->getLast();
		if($lastRoute == $routeData){
			return;
		}

		// If same route as previous
		$previousRoute = $this->history->getPrevious();
		if($previousRoute == $routeData){
			$this->history->rollback();
			return;
		}

		$this->history->add($routeData);
	}
}