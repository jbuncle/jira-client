<?php

namespace JBuncle\JiraClient;

use DateTime;
use stdClass;

/**
 * Worklog.
 */
class JiraWorklog {

    private $json;

    public function __construct(array $json) {
        $this->json = $json;
    }

    public function getDate(): DateTime {
        return new DateTime($this->json['started']);
    }

    public function getUser(): string {
        return $this->json['author']['name'];
    }

    public function getHours(): float {
        return $this->json['timeSpentSeconds'] / 3600;
    }

    public function __toString() {
        return "TimeEntry{" . $this->getDate()->format('Y-m-d H:i') . ' ' . $this->getUser() . ' ' . $this->getHours() . "}";
    }

}
