<?php

namespace JiraClient\Agile;

/**
 * SprintReportIssue.
 */
class SprintReportIssue
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

	public function getKey()
	{
		return $this->json->key;
	}
}
