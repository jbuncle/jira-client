<?php

namespace JiraClient;

/**
 * CachedConnection.
 */
class CachedConnection extends Connection
{
	private $requests = [];
	private $issues = [];

	protected function get($url, array $data = array())
	{
		// Unique ID for request
		$uid = md5(serialize(func_get_args()));

		if (!array_key_exists($uid, $this->requests)) {
			$uid = md5(serialize(func_get_args()));
			$response = parent::get($url, $data);
			$this->requests[$uid] = $response;
		}
		return $this->requests[$uid];
	}

	protected function createIssue(\stdClass $json)
	{
		$issue = parent::createIssue($json);
		$this->issues[$issue->getKey()] = $issue;
		return $issue;
	}

	public function getIssueByKey($issueKey)
	{
		if (!array_key_exists($issueKey, $this->issues)) {
			return parent::getIssueByKey($issueKey);
		}
		return $this->issues[$issueKey];
	}
}
