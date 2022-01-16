<?php

namespace JBuncle\JiraClient;

/**
 * JiraConnectionInfo
 *
 * @author jbuncle
 */
class JiraConnectionInfo {

    private string $hostname;
    private string $username;
    private string $password;

    public function __construct(string $hostname, string $username, string $password) {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getHostname(): string {
        return $this->hostname;
    }

}
