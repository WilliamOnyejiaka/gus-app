<?php

declare(strict_types=1);

namespace Lib;

class Request
{
    public  $payload;
    public  $params = []; // Added to store route parameters

    public function __construct() {}

    // public function body($key, $default = null)
    // {
    //     $body = json_decode(file_get_contents("php://input"));
    //     if (!empty($body->{$key})) {
    //         return $body->{$key};
    //     } else {
    //         return $default;
    //     }
    // }

    // public function body(string $key, $default = null)
    // {
    //     $body = json_decode(file_get_contents('php://input'));
    //     return $body && property_exists($body, $key) ? $body->$key : $default;
    // }

    public function body(string $key, $default = null)
    {
        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
            if ($this->notFormDataLike()) {
                $body = json_decode(file_get_contents('php://input'));
                return $body && property_exists($body, $key) ? $body->$key : $default;
            } else {
                $parsedData = $this->parseText();
                return isset($parsedData[$key]) ? $parsedData[$key] : $default;
            }
        } else {
            if ($this->notFormDataLike()) {
                $body = json_decode(file_get_contents('php://input'));
                return $body && property_exists($body, $key) ? $body->$key : $default;
            } else {
                return $_POST[$key] ?? $default;
            }
        }
    }

    /**
     * Parse text fields from multipart/form-data
     * @return array<string, string>
     */
    public static function parseText(): array {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return [];
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!str_contains($contentType, 'multipart/form-data')) {
            return [];
        }
        preg_match('/boundary=(.+)$/', $contentType, $matches);
        $boundary = $matches[1] ?? null;
        if (!$boundary) {
            return [];
        }

        $parts = array_filter(explode("--$boundary", $input));
        $data = [];

        foreach ($parts as $part) {
            if (trim($part) === '' || trim($part) === '--') continue;

            list($headersRaw, $content) = explode("\r\n\r\n", $part, 2);
            $headers = [];
            foreach (explode("\r\n", trim($headersRaw)) as $header) {
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $headers[trim($key)] = trim($value);
                }
            }

            $disposition = $headers['Content-Disposition'] ?? '';
            preg_match('/name="([^"]+)"/', $disposition, $nameMatch);
            $name = $nameMatch[1] ?? '';
            $filename = null;
            if (preg_match('/filename="([^"]+)"/', $disposition, $fileMatch)) {
                $filename = $fileMatch[1];
            }

            $content = trim($content, "\r\n");
            if (!$filename) {
                $data[$name] = $content; // Only text fields
            }
        }

        return $data;
    }

    /**
     * Parse file fields from multipart/form-data
     * @param bool $allowFiles Whether to process files
     * @return array<string, array>
     */
    public static function parseFiles(): array {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return [];
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!str_contains($contentType, 'multipart/form-data')) {
            return [];
        }
        preg_match('/boundary=(.+)$/', $contentType, $matches);
        $boundary = $matches[1] ?? null;
        if (!$boundary) {
            return [];
        }

        $parts = array_filter(explode("--$boundary", $input));
        $data = [];

        foreach ($parts as $part) {
            if (trim($part) === '' || trim($part) === '--') continue;

            list($headersRaw, $content) = explode("\r\n\r\n", $part, 2);
            $headers = [];
            foreach (explode("\r\n", trim($headersRaw)) as $header) {
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $headers[trim($key)] = trim($value);
                }
            }

            $disposition = $headers['Content-Disposition'] ?? '';
            preg_match('/name="([^"]+)"/', $disposition, $nameMatch);
            $name = $nameMatch[1] ?? '';
            $filename = null;
            if (preg_match('/filename="([^"]+)"/', $disposition, $fileMatch)) {
                $filename = $fileMatch[1];
            }

            $content = trim($content, "\r\n");
            if ($filename) {
                $tmpName = tempnam(sys_get_temp_dir(), 'upload_');
                file_put_contents($tmpName, $content);

                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $destination = $uploadDir . basename($filename);
                if (move_uploaded_file($tmpName, $destination)) {
                    $data[$name] = [
                        'name' => $filename,
                        'tmp_name' => $tmpName,
                        'full_path' => realpath($destination),
                        'type' => $headers['Content-Type'] ?? 'application/octet-stream',
                        'size' => strlen($content),
                        'error' => 0
                    ];
                } else {
                    $data[$name] = [
                        'name' => $filename,
                        'tmp_name' => $tmpName,
                        'full_path' => null,
                        'type' => $headers['Content-Type'] ?? 'application/octet-stream',
                        'size' => strlen($content),
                        'error' => 1
                    ];
                }
            }
        }

        return $data;
    }

    public static function parse(bool $allowFiles = true): array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return [];
        }

        // Get boundary from Content-Type header
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (!str_contains($contentType, 'multipart/form-data')) {
            return []; // Only parse multipart/form-data
        }
        preg_match('/boundary=(.+)$/', $contentType, $matches);
        $boundary = $matches[1] ?? null;
        if (!$boundary) {
            return [];
        }

        // Split parts by boundary
        $parts = array_filter(explode("--$boundary", $input));
        $data = [];

        foreach ($parts as $part) {
            if (trim($part) === '' || trim($part) === '--') continue;

            // Split headers and content
            list($headersRaw, $content) = explode("\r\n\r\n", $part, 2);
            $headers = [];
            foreach (explode("\r\n", trim($headersRaw)) as $header) {
                if (strpos($header, ':') !== false) {
                    list($key, $value) = explode(':', $header, 2);
                    $headers[trim($key)] = trim($value);
                }
            }

            // Extract name and filename from Content-Disposition
            $disposition = $headers['Content-Disposition'] ?? '';
            preg_match('/name="([^"]+)"/', $disposition, $nameMatch);
            $name = $nameMatch[1] ?? '';
            $filename = null;
            if (preg_match('/filename="([^"]+)"/', $disposition, $fileMatch)) {
                $filename = $fileMatch[1];
            }

            // Handle files or text
            $content = trim($content, "\r\n");
            if ($filename && $allowFiles) {
                $tmpName = tempnam(sys_get_temp_dir(), 'upload_');
                file_put_contents($tmpName, $content);

                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $destination = $uploadDir . basename($filename); // Use unique names in production
                if (move_uploaded_file($tmpName, $destination)) {
                    $data[$name] = [
                        'name' => $filename,
                        'tmp_name' => $tmpName,
                        'full_path' => realpath($destination), // Permanent path
                        'type' => $headers['Content-Type'] ?? 'application/octet-stream',
                        'size' => strlen($content),
                        'error' => 0
                    ];
                } else {
                    $data[$name] = [
                        'name' => $filename,
                        'tmp_name' => $tmpName,
                        'full_path' => null,
                        'type' => $headers['Content-Type'] ?? 'application/octet-stream',
                        'size' => strlen($content),
                        'error' => 1 // Indicate move failed
                    ];
                }
            } elseif (!$filename) {
                $data[$name] = $content; // Text field
            }
        }

        return $data;
    }

    public static function notFormDataLike(bool $allowArrays = true): bool
    {
        $input = file_get_contents('php://input');
        $body = json_decode($input);

        if ($body === null || !is_object($body)) {
            return false;
        }

        $data = (array) $body;
        foreach ($data as $value) {
            if (is_object($value)) {
                return false; // Nested objects not allowed
            }
            if (is_array($value) && !$allowArrays) {
                return false; // Arrays only allowed if specified
            }
        }

        return true;
    }

    public function args($key, $default = null)
    {
        if (isset($_GET[$key]) && !empty($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    public function file($key)
    {
        if (in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
            $parsedData = $this->parseFiles();
            return isset($parsedData[$key]) ? $parsedData[$key] : null;
        }else {
            if (isset($_FILES[$key])) {
                return $_FILES[$key];
            }
            return null;
        }
    }

    public function authorization(string $name)
    {
        if ($name == "email") {
            return $_SERVER['PHP_AUTH_USER'] ?? null;
        } elseif ($name == "password") {
            return $_SERVER['PHP_AUTH_PW'] ?? null;
        } else {
            return null;
        }
    }

    public function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    public function set_header($header_name, $value)
    {
        header("$header_name: $value");
    }

    public function locals($key, $value = null)
    {
        if (!isset($_SERVER['locals'])) {
            $_SERVER['locals'] = [];
        }

        if (!isset($value)) {
            return $_SERVER['locals'][$key] ?? null;
        } else {
            $_SERVER['locals'][$key] = $value;
        }
    }

    public function get_header($key)
    {
        return (getallheaders())[$key] ?? null;
    }

    // Added method to access route parameters
    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
}
