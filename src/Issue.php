<?php

namespace JiraClient;

use stdClass;

/**
 * Issue.
 */
class Issue
{
	private $json;

	/**
	 *
	 * @var Worklog[]
	 */
	private $worklogs;

	public function __construct(stdClass $json)
	{
		$this->json = $json;
	}

	public function getId()
	{
		return $this->json->id;
	}

	/**
	 * 
	 * @return Worklog[]
	 */
	public function getWorklogs()
	{
		if (!isset($this->worklogs)) {
			$worklogs = [];
			foreach ($this->json->fields->worklog->worklogs as $value) {
				$worklogs[] = new Worklog($value);
			}
			$this->worklogs = $worklogs;
		}
		return $this->worklogs;
	}

	public function getAssignee()
	{
		if (!isset($this->json->fields->assignee->name)) {
			return null;
		}
		return $this->json->fields->assignee->name;
	}

	public function getEpic()
	{
		return $this->json->fields->customfield_11100;
	}

	public function getKey()
	{
		return $this->json->key;
	}

	public function getSummary()
	{
		return $this->json->fields->summary;
	}

	public function getStatus()
	{
		return $this->json->fields->status->name;
	}

	public function getDescription()
	{
		return $this->json->fields->description;
	}

	public function getEpicKey()
	{
		return $this->json->fields->customfield_10800;
	}

	public function getType()
	{
		return $this->json->fields->issuetype->name;
	}

	public function isSubtask()
	{
		return $this->json->fields->issuetype->subtask === true;
	}

	public function getUpdated()
	{
		return new \DateTime($this->json->fields->updated);
	}
		public function getCreated()
	{
		return new \DateTime($this->json->fields->created);
	}
	
	public function getAttachmentUrls()
	{
		$urls = [];
		foreach ($this->json->fields->attachment as $attachment) {
			$urls[] = $attachment->content;
		}
		return $urls;
	}

	public function getParentKey()
	{
		if (!$this->isSubtask()) {
			return null;
		}
		return $this->json->fields->parent->key;
	}

	public function getSubtaskKeys()
	{
		if ($this->isSubtask()) {
			return null;
		}
		$keys = [];
		foreach ($this->json->fields->subtasks as $subtask) {
			$keys[] = $subtask->key;
		}
		return $keys;
	}
}
