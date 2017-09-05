<?php

namespace JiraClient;

use Zend_Http_Client;

/**
 * Connection.
 */
class Connection
{
	/**
	 * Jira username.
	 * 
	 * @var string 
	 */
	private $username;

	/**
	 * Jira password.
	 * 
	 * @var string 
	 */
	private $password;
	private $hostname;
	private $client;
	private static $fields = [
		'created',
		'updated',
		'attachment',
		'worklog',
		'status',
		'schema',
		'names',
		'summary',
		'comment',
		'parent',
		'subtasks',
		'issuetype',
		'assignee',
		'description',
		'customfield_10800', //Epic Link
	];

	public function __construct($hostname, $username, $password)
	{
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
	}

	public function board()
	{
		$boards = [];
		$results = $this->fetchJson('/rest/agile/1.0/board');
		foreach ($results->values as $value) {
			$boards[] = new Agile\Board($value);
		}
		return $boards;
	}

	/**
	 * 
	 * @param type $boardId
	 * @param type $sprintId
	 * @return \JiraClient\Agile\SprintReport
	 */
	public function sprintReport($boardId, $sprintId)
	{
		$path = '/rest/greenhopper/latest/rapid/charts/sprintreport';
		$args = [
			'rapidViewId' => $boardId,
			'sprintId' => $sprintId,
		];
		$results = $this->fetchJson($path, $args);
		return new Agile\SprintReport($results);
	}

	/**
	 * 
	 * @param Issue $issueKey
	 * @return type
	 */
	public function getIssueByKey($issueKey)
	{
		return $this->search("key = '$issueKey'")[0];
	}

	/**
	 * 
	 * @param type $jql
	 * @return \JiraClient\Issue[]
	 */
	public function search($jql)
	{

		$path = '/rest/api/2/search';
		$startAt = 0;
		$increment = 500;
		$issues = [];

		while (true) {

			$response = $this->fetchJson($path,
				[
				'jql' => $jql,
				'startAt' => $startAt,
				'maxResults' => $increment,
				'expand' => 'names,renderedFields',
				'fields' => implode(',', self::$fields),
//				'fields' => 'worklog,status,schema,names,summary,comment,parent,subtasks',
			]);
			foreach ($response->issues as $issue) {

				$issues[] = $this->createIssue($issue);
			}

			$startAt += $increment;

			if ($startAt > $response->total) {
				break;
			}
		}
		return $issues;
	}

	protected function createIssue(\stdClass $issue)
	{
		return new Issue($issue);
	}

	private function fetchJson($path, $data = [])
	{
		if (strpos($path, '/') !== 0) {
			$path = '/' . $path;
		}
		$url = $this->hostname . $path;
		$rawResponse = $this->get($url, $data);

		$response = json_decode($rawResponse);
		if (empty($response)) {
			throw new Exception("Bad response: from '$path' with '$rawResponse'");
		}
		if (isset($response->errorMessages)) {
			throw new Exception("Bad response: from '$path' with '{$response->errorMessages[0]}'");
		}

		return $response;
	}

	protected function get($url, array $data = [])
	{
		if (!isset($this->client)) {
			$this->client = new Zend_Http_Client();
			$this->client->setAdapter('\Zend_Http_Client_Adapter_Curl');
			$this->client->setAuth($this->username, $this->password);
			$this->client->setConfig(array('timeout' => 30));
		}
		$this->client->setUri($url);
		$this->client->setParameterGet($data);
		$rawResponse = $this->client->request(Zend_Http_Client::GET);
		$response = $this->client->getLastResponse();

		$status = $response->getStatus();
		if (!$response->isSuccessful()) {
			throw new Exception("Failed request to '$url' with $status");
		}

		return $rawResponse->getBody();
	}
}
