<?php
class UserInfo {
    public static function getClientInfo(): array {
        return [
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Неизвестно',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Неизвестно',
            'server_time' => date('Y-m-d H:i:s'),
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'Неизвестно'
        ];
    }

    public static function saveSubmissionTime(): void {
        setcookie('last_submission', date('Y-m-d H:i:s'), time() + 3600, '/');
    }

    public static function getLastSubmission(): string {
        return $_COOKIE['last_submission'] ?? 'Еще не было отправок';
    }

    // Метод строго по заданию
    public static function getInfo(): array {
        return [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'time' => date('Y-m-d H:i:s')
        ];
    }
}