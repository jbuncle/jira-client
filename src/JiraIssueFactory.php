<?php

namespace JBuncle\JiraClient;

/**
 * JiraIssueFactory
 *
 * @author jbuncle
 */
class JiraIssueFactory {

    public function createFromJson(array $arr): JiraIssue {
        return new JiraIssue($arr);
    }

}
