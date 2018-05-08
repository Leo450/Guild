<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class History
{

	private $session;
	private $history;

	public function __construct()
	{

		$this->session = new Session();

		$this->history = $this->session->get('history');

		if($this->history === null){
			$this->history = [];
			$this->persist();
		}

	}

	public function persist()
	{

		$this->session->set('history', $this->history);

	}

	public function add(array $route)
	{

		$this->history[] = $route;

		$this->persist();
	}

	public function clear()
	{

		if($this->history === null || count($this->history) < 2){
			return;
		}

		$this->history = [$this->history[0]];

		$this->persist();

	}

	public function getByIndex(int $index)
	{

		if(!isset($this->history[$index])){
			return null;
		}

		return $this->history[$index];

	}

	public function getLast()
	{

		return $this->getByIndex(count($this->history) - 1);

	}

	public function getPrevious()
	{

		return $this->getByIndex(count($this->history) - 2);

	}

	public function rollback(int $number = 1)
	{

		for($i = 0; $i < $number; $i++){
			array_pop($this->history);
		}

		$this->persist();

	}

}