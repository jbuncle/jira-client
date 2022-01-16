<?php

namespace JBuncle\JiraClient;

class JiraConnection {

    private JiraConnectionInfo $jiraConnectionInfo;

    public function __construct(JiraConnectionInfo $jiraConnectionInfo) {
        $this->jiraConnectionInfo = $jiraConnectionInfo;
    }

    public function get(string $path, array $params = []): ?array {

        $queryString = http_build_query($params);
        $url = $this->getUrl($path) . '?' . $queryString;

        $ch = curl_init();
        if ($ch === false) {
            throw new JiraException("Failed to initialise curl");
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_USERPWD, $this->jiraConnectionInfo->getUsername() . ':' . $this->jiraConnectionInfo->getPassword());

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new JiraException('CURL error:' . curl_error($ch));
        }
        if (!is_string($result)) {
            throw new JiraException('Failed to get CURL response');
        }
        curl_close($ch);

        $decoded = json_decode($result, true);
        if (!is_array($decoded)) {
            throw new JiraException("Failed to decode response");
        }
        return $decoded;
    }

    private function getUrl(string $path): string {
        $baseUrl = $this->jiraConnectionInfo->getHostname();
        return 'https://' . $baseUrl . '/' . $path;
    }

}
