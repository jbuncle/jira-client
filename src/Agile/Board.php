<?php

namespace JiraClient\Agile;

/**
 * Board.
 */
class Board
{
	private $json;

	public function __construct(\stdClass $json)
	{
		$this->json = $json;
	}

	public function getId()
	{
		return $this->json->id;
	}

	public function getName()
	{
		return $this->json->name;
	}

	public function getType()
	{
		return $this->json->type;
	}
}
