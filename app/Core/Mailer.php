<?php

namespace App\Core;

abstract class Mailer
{
    protected string $host;
    protected int $port;
    protected string $from;
    protected string $username;
    protected string $password;
    protected ?string $encryption;
    protected int $timeout = 5;
    protected string $newLine = "\r\n";
    protected array $responses = [];
    protected bool $isSuccessful = true;
    protected $connection;

    public function __construct()
    {
        $this->host = config('mail.host');
        $this->port = config('mail.port');
        $this->from = config('mail.from');
        $this->username = config('mail.username');
        $this->password = config('mail.password');
        $this->encryption = config('mail.encryption');
    }

    public function send(string $to, string $name, string $subject, string $message): bool
    {
        $this->connect();

        $this->encrypt();

        $this->authenticate();

        return $this->sendMail($to, $name, $subject, $message);
    }

    private function encrypt(): void
    {
        if ($this->encryption === 'tls') {
            $response = $this->sendCommand("STARTTLS");
            if (!$this->checkError($response, "220")) {
                $this->isSuccessful = false;
            }
            stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        }
    }

    protected function connect(): void
    {
        $this->connection = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

        if (empty($this->connection)) {
            logger("Failed to connect: {$errno} {$errstr}", 'mailer');
            $this->isSuccessful = false;
        }

        stream_set_timeout($this->connection, $this->timeout);
        $response = fgets($this->connection, 515);

        $info = stream_get_meta_data($this->connection);
        if ($info['timed_out']) {
            logger("Timeout: {$response}", 'mailer');
            fclose($this->connection);
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand("HELO {$this->host}");
        if (!$this->checkError($response, "250")) {
            $this->isSuccessful = false;
        }

        $this->checkForMoreResponses("HELO {$this->host}");
    }

    protected function authenticate(): void
    {
        $response = $this->sendCommand("AUTH LOGIN");
        if (!$this->checkError($response, "334")) {
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand(base64_encode($this->username));
        if (!$this->checkError($response, "334")) {
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand(base64_encode($this->password));
        if (!$this->checkError($response, "235")) {
            $this->isSuccessful = false;
        }
    }

    protected function sendMail($to, $name, $subject, $message): bool
    {

        $response = $this->sendCommand("MAIL FROM: <" . $this->from . ">");
        if(!$this->checkError($response, "250")) {
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand("RCPT TO: <" . $to . ">");
        if(!$this->checkError($response, "250")) {
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand("DATA");
        if(!$this->checkError($response, "354")) {
            $this->isSuccessful = false;
        }

        $headers = "MIME-Version: 1.0" . $this->newLine;
        $headers .= "X-Mailer: PHP/" . PHP_VERSION . $this->newLine;
        $headers .= "Content-type: text/html; charset=iso-8859-1" . $this->newLine;
        $headers .= "Message-Id: <" . time() . "." . getmypid() . "." . config('app.url') . "@" . $this->host . ">" . $this->newLine;
        $headers .= "Received: from " . gethostname() . " (" . gethostbyname(gethostname()) . ") by " . gethostname() . $this->newLine;
        $headers .= "Date: " . date("r") . $this->newLine;
        $headers .= "To: " . $name . " <" . $to . ">" . $this->newLine;
        $headers .= "From: " . config('app.name') . " <" . $this->from . ">" . $this->newLine;
        $headers .= "Subject: " . $subject . $this->newLine;

        $message = str_replace("\n.", "\n..", $message);

        $response = $this->sendCommand($headers . $this->newLine . $message . $this->newLine . ".");
        if(!$this->checkError($response, "250")) {
            $this->isSuccessful = false;
        }

        $response = $this->sendCommand("QUIT");
        if(!$this->checkError($response, "221")) {
            $this->isSuccessful = false;
        }

        fclose($this->connection);

        if (!$this->isSuccessful) {
            return false;
        }

        return true;
    }

    protected function checkError($response, $code): bool
    {
        if (!str_contains($response, $code)) {
            $this->mailLogger();
            return false;
        }
        return true;
    }

    protected function sendCommand($command): string
    {

        fwrite($this->connection, $command . $this->newLine);

        $this->responses[$command][] = fgets($this->connection, 515);

        if (str_contains(end($this->responses[$command]), "421")) {
            $this->mailLogger();
        }

        return end($this->responses[$command]);
    }

    protected function checkForMoreResponses($command): void
    {
        $info = stream_get_meta_data($this->connection);
        $i = 0;
        while (!($info['timed_out'] || $info['unread_bytes'] === 0)) {
            $this->responses[$command][] = fgets($this->connection, 515);
            $info = stream_get_meta_data($this->connection);
            $i++;
        }
        if ($i = 0) {
            $this->mailLogger();
        }
    }

    private function mailLogger(): void
    {
        $this->responses['SUCCESS'] = "FALSE";
        logger(
            message: 'failed to send command',
            level: 'mailer',
            context: $this->responses,
        );
    }

}