<?php
/**
 * Mailer — envoi d'emails via SMTP, 100% PHP natif (sockets).
 * Pas de PHPMailer/SwiftMailer requis. Supporte AUTH PLAIN/LOGIN, STARTTLS, SSL implicite.
 *
 * Configuration via constantes (config/database.php) :
 *   SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE ('tls' | 'ssl' | ''),
 *   SMTP_FROM, SMTP_FROM_NAME
 *
 * Usage :
 *   $m = new Mailer();
 *   $m->send('a@b.com', 'Bienvenue', '<h1>Hello</h1>', 'Hello');
 */
class Mailer {
    private string $host;
    private int    $port;
    private string $user;
    private string $pass;
    private string $secure;     // 'tls' | 'ssl' | ''
    private string $fromAddr;
    private string $fromName;
    private $socket;

    public function __construct() {
        $this->host     = defined('SMTP_HOST') ? SMTP_HOST : 'localhost';
        $this->port     = defined('SMTP_PORT') ? (int)SMTP_PORT : 587;
        $this->user     = defined('SMTP_USER') ? SMTP_USER : '';
        $this->pass     = defined('SMTP_PASS') ? SMTP_PASS : '';
        $this->secure   = defined('SMTP_SECURE') ? strtolower(SMTP_SECURE) : 'tls';
        $this->fromAddr = defined('SMTP_FROM') ? SMTP_FROM : 'no-reply@creatorspeace.local';
        $this->fromName = defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'CreatorSpace';
    }

    /**
     * Envoie un email. Lance une RuntimeException en cas d'échec.
     * @param string $to       Destinataire
     * @param string $subject  Sujet
     * @param string $html     Corps HTML
     * @param string|null $text Version texte (auto-générée si null)
     */
    /**
     * @param array $attachments [['name'=>'file.pdf', 'mime'=>'application/pdf', 'data'=>$bin], ...]
     */
    public function send(string $to, string $subject, string $html, ?string $text = null, array $attachments = []): bool {
        $text = $text ?? trim(strip_tags($html));
        $altBoundary = 'CSPC-alt-' . bin2hex(random_bytes(6));
        $hasAttach   = !empty($attachments);
        $mixedBoundary = 'CSPC-mix-' . bin2hex(random_bytes(6));

        $headers  = "From: {$this->encodeHeader($this->fromName)} <{$this->fromAddr}>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: " . $this->encodeHeader($subject) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "Message-ID: <" . bin2hex(random_bytes(8)) . "@creatorspeace>\r\n";

        if ($hasAttach) {
            $headers .= "Content-Type: multipart/mixed; boundary=\"$mixedBoundary\"\r\n";
            $body  = "\r\n--$mixedBoundary\r\n";
            $body .= "Content-Type: multipart/alternative; boundary=\"$altBoundary\"\r\n\r\n";
        } else {
            $headers .= "Content-Type: multipart/alternative; boundary=\"$altBoundary\"\r\n";
            $body  = "";
        }

        $body .= "\r\n--$altBoundary\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $text . "\r\n";
        $body .= "--$altBoundary\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $html . "\r\n";
        $body .= "--$altBoundary--\r\n";

        if ($hasAttach) {
            foreach ($attachments as $att) {
                $name = $att['name'] ?? 'attachment';
                $mime = $att['mime'] ?? 'application/octet-stream';
                $data = chunk_split(base64_encode($att['data'] ?? ''));
                $body .= "\r\n--$mixedBoundary\r\n";
                $body .= "Content-Type: $mime; name=\"$name\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$name\"\r\n\r\n";
                $body .= $data . "\r\n";
            }
            $body .= "--$mixedBoundary--\r\n";
        }

        $msg = $headers . $body;

        $this->connect();
        $this->ehlo();
        if ($this->secure === 'tls') $this->startTls();
        if ($this->user !== '')      $this->authLogin();
        $this->cmd("MAIL FROM:<{$this->fromAddr}>", 250);
        $this->cmd("RCPT TO:<$to>", [250, 251]);
        $this->cmd("DATA", 354);
        fwrite($this->socket, $msg . "\r\n.\r\n");
        $this->expect(250);
        $this->cmd("QUIT", 221);
        fclose($this->socket);
        return true;
    }

    private function connect(): void {
        $host = ($this->secure === 'ssl') ? 'ssl://' . $this->host : $this->host;
        $errno = 0; $errstr = '';
        $this->socket = @stream_socket_client(
            "$host:{$this->port}", $errno, $errstr, 10, STREAM_CLIENT_CONNECT
        );
        if (!$this->socket) {
            throw new RuntimeException("[Mailer] Connexion impossible : $errstr ($errno)");
        }
        stream_set_timeout($this->socket, 10);
        $this->expect(220);
    }

    private function ehlo(): void {
        $this->cmd("EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost'), 250);
    }

    private function startTls(): void {
        $this->cmd("STARTTLS", 220);
        $ok = @stream_socket_enable_crypto(
            $this->socket, true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
            | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT
            | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
        );
        if (!$ok) throw new RuntimeException('[Mailer] STARTTLS échoué');
        $this->ehlo();
    }

    private function authLogin(): void {
        $this->cmd("AUTH LOGIN", 334);
        $this->cmd(base64_encode($this->user), 334);
        $this->cmd(base64_encode($this->pass), 235);
    }

    /** @param int|int[] $expected */
    private function cmd(string $command, $expected): string {
        fwrite($this->socket, $command . "\r\n");
        return $this->expect($expected);
    }

    /** @param int|int[] $expected */
    private function expect($expected): string {
        $expected = (array)$expected;
        $response = '';
        while ($line = fgets($this->socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break; // dernière ligne SMTP
        }
        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expected, true)) {
            throw new RuntimeException("[Mailer] SMTP attendu " . implode('/', $expected) . ", reçu : " . trim($response));
        }
        return $response;
    }

    private function encodeHeader(string $value): string {
        return preg_match('/[^\x20-\x7e]/', $value)
            ? '=?UTF-8?B?' . base64_encode($value) . '?='
            : $value;
    }
}
