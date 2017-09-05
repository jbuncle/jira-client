<?php
namespace JiraClient\Agile;

use stdClass;

/**
 * SprintReport.
 */
class SprintReport
{
	private $json;

	public function __construct(stdClass $json)
	{
		$this->json = $json;
	}

	public function getSprintStart()
	{
		return new \DateTime($this->json->sprint->startDate);
	}

	public function getSprintEnd()
	{
		return new \DateTime($this->json->sprint->endDate);
	}

	public function getSprintId()
	{
		return (int) $this->json->sprint->id;
	}

	public function getSprintName()
	{
		return (string) $this->json->sprint->name;
	}

	public function getPuntedIssues()
	{
		return $this->processIssues($this->json->contents->puntedIssues);
	}

	public function getIssuesNotCompletedInCurrentSprint()
	{
		return $this->processIssues($this->json->contents->issuesNotCompletedInCurrentSprint);
	}

	public function getCompletedIssues()
	{
		return $this->processIssues($this->json->contents->completedIssues);
	}

	public function getIssuesCompletedInAnotherSprint()
	{
		return $this->processIssues($this->json->contents->issuesCompletedInAnotherSprint);
	}

	private function processIssues($json)
	{
		$issues = [];
		foreach ($json as $issueJson) {
			$issues[] = new SprintReportIssue($issueJson);
		}
		return $issues;
	}
}
