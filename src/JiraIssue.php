<?php

namespace JBuncle\JiraClient;

use DateTime;

/**
 * JiraIssue.
 */
class JiraIssue {

    private $json;

    /**
     *
     * @var JiraWorklog[]
     */
    private $worklogs;

    public function __construct(array $json) {
        $this->json = $json;
    }

    public function getId() {
        return $this->json['id'];
    }

    /**
     * 
     * @return JiraWorklog[]
     */
    public function getWorklogs() {
        if (!isset($this->worklogs)) {
            $worklogs = [];
            foreach ($this->json['fields']['worklog']['worklogs'] as $value) {
                $worklogs[] = new JiraWorklog($value);
            }
            $this->worklogs = $worklogs;
        }
        return $this->worklogs;
    }

    public function getAssignee() {
        if (!isset($this->json['fields']['assignee']['name'])) {
            return null;
        }
        return $this->json['fields']['assignee']['name'];
    }

    public function getChangelog(): array {
        if ($this->json['changelog']['total'] != $this->json['changelog']['maxResults']) {
            fwrite(STDERR, "Changelog incomplete\n");
        }
        return $this->json['changelog']['histories'];
    }
    
    public function getEpic() {
        return $this->json['fields']['customfield_11100'];
    }

    public function getKey() {
        return $this->json['key'];
    }

    public function getSummary() {
        return $this->json['fields']['summary'];
    }

    public function getStatus() {
        return $this->json['fields']['status']['name'];
    }

    public function getDescription() {
        return $this->json['fields']['description'];
    }

    public function getLabels(): array {
        return $this->json['fields']['labels'];
    }

    public function getEpicKey() {
        return $this->json['fields']['customfield_10800'];
    }

    public function getIssueType(): string {
        return $this->json['fields']['issuetype']['name'];
    }

    public function getIssueLinks(): ?array {
        if (!isset($this->json['fields']['issuelinks'])) {
            return null;
        }
        return array_map(function (array $issueLink) {
            if (isset($issueLink['outwardIssue'])) {
                return [
                    'type' => $issueLink['type']['name'],
                    'description' => $issueLink['type']['outward'],
                    'issue' => [
                        'key' => $issueLink['outwardIssue']['key'],
                        'title' => $issueLink['outwardIssue']['fields']['summary'],
                        'type' => $issueLink['outwardIssue']['fields']['issuetype']['name'],
                    ],
                ];
            } else {
                return [
                    'type' => $issueLink['type']['name'],
                    'description' => $issueLink['type']['inward'],
                    'issue' => [
                        'key' => $issueLink['inwardIssue']['key'],
                        'title' => $issueLink['inwardIssue']['fields']['summary'],
                        'type' => $issueLink['inwardIssue']['fields']['issuetype']['name'],
                    ],
                ];
            }
        }, $this->json['fields']['issuelinks']);
    }

    public function isSubtask(): bool {
        return $this->json['fields']['issuetype']['subtask'] === true;
    }

    public function getUpdated(): ?DateTime {
        if (!isset($this->json['fields']['updated'])) {
            return null;
        }
        return new DateTime((string) $this->json['fields']['updated']);
    }

    public function getCreated(): ?DateTime {
        if (!isset($this->json['fields']['created'])) {
            return null;
        }
        return new DateTime((string) $this->json['fields']['created']);
    }

    public function getAttachmentUrls() {
        $urls = [];
        foreach ($this->json['fields']['attachment'] as $attachment) {
            $urls[] = $attachment['content'];
        }
        return $urls;
    }

    public function getParentKey() {
        if (!$this->isSubtask()) {
            return null;
        }
        return $this->json['fields']['parent']['key'];
    }

    public function getSubtaskKeys(): ?array {
        if ($this->isSubtask()) {
            return null;
        }
        $keys = [];
        foreach ($this->json['fields']['subtasks'] as $subtask) {
            $keys[] = $subtask->key;
        }
        return $keys;
    }

    public function getField(string $fieldName): ?array {
        if (isset($this->json['fields'][$fieldName])) {
            return $this->json['fields'][$fieldName];
        }

        return null;
    }

}
