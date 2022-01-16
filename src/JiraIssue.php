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

    public function getEpicKey() {
        return $this->json['fields']['customfield_10800'];
    }

    public function getType() {
        return $this->json['fields']['issuetype']['name'];
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

}
