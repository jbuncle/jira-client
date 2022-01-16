<?php

namespace JBuncle\JiraClient;

use JBuncle\JiraClient\Agile\SprintBoard;
use JBuncle\JiraClient\Agile\SprintReport;

class JiraLookup {

    private JiraConnection $jiraConnection;
    private JiraIssueFactory $jiraIssueFactory;
    private array $customFields;

    private const FIELDS = [
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
    private const EXPAND = [
        'renderedFields',
        'names',
        'fields',
        'changelog',
    ];

    public function __construct(
            JiraConnection $jiraConnection,
            JiraIssueFactory $jiraIssueFactory,
            array $customFields
    ) {
        $this->jiraConnection = $jiraConnection;
        $this->jiraIssueFactory = $jiraIssueFactory;
        $this->customFields = $customFields;
    }

    public function board() {
        $results = $this->jiraConnection->get('/rest/agile/1.0/board');
        if ($results === null) {
            return [];
        }
        $boards = [];
        foreach ($results['values'] as $value) {
            $boards[] = new SprintBoard($value);
        }
        return $boards;
    }

    public function sprintReport(int $boardId, int $sprintId): ?SprintReport {
        $path = '/rest/greenhopper/latest/rapid/charts/sprintreport';
        $args = [
            'rapidViewId' => $boardId,
            'sprintId' => $sprintId,
        ];
        $results = $this->jiraConnection->get($path, $args);
        if ($results === null) {
            return null;
        }
        return new SprintReport($results);
    }

    public function getIssueByKey(string $issueKey): JiraIssue {
        return $this->search("key = '$issueKey'")[0];
    }

    public function search(string $jql): array {

        $path = '/rest/api/3/search';
        $startAt = 0;
        $increment = 500;
        $issues = [];

        $fields = array_merge(self::FIELDS, $this->customFields);

        while (true) {

            $response = $this->jiraConnection->get($path,
                    [
                        'jql' => $jql,
                        'startAt' => $startAt,
                        'maxResults' => $increment,
                        'expand' => implode(',', self::EXPAND),
                        'fields' => implode(',', $fields),
//				'fields' => 'worklog,status,schema,names,summary,comment,parent,subtasks',
            ]);
            if ($response === null) {
                throw new JiraException("Bad response");
            }
            $issuesArr = $response['issues'];
            foreach ($issuesArr as $issue) {
                $issues[] = $this->jiraIssueFactory->createFromJson($issue);
            }
            $startAt += $increment;

            if ($startAt > $response['total']) {
                break;
            }
        }
        return $issues;
    }

}
