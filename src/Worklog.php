<?php

namespace JiraClient;

use DateTime;
use stdClass;

/**
 * Worklog.
 */
class Worklog
{
	private $json;

	public function __construct(stdClass $json)
	{
		$this->json = $json;
	}

	public function getDate()
	{
		return new DateTime($this->json->started);
	}

	public function getUser()
	{
		return $this->json->author->name;
	}

	public function getHours()
	{
		return $this->json->timeSpentSeconds / 3600;
	}

	public function __toString()
	{
		return "TimeEntry{" . $this->getDate()->format('Y-m-d H:i') . ' ' . $this->getUser() . ' ' . $this->getHours() . "}";
	}
}
