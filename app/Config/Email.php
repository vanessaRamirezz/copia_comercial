<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'jaimepalacios400@gmail.com'; // Tu dirección de correo
    public string $fromName   = 'Pruebas'; // Nombre del remitente
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';

    public string $protocol = 'smtp'; // Usar SMTP

    public string $mailPath = '/usr/sbin/sendmail';

    public string $SMTPHost = 'smtp.gmail.com'; // Servidor SMTP
    public string $SMTPUser = 'palaciosjaime400@gmail.com'; // Usuario SMTP
    public string $SMTPPass = 'xywuvmnrypecfdqf'; // Contraseña SMTP
    public int $SMTPPort = 587; // Puerto SMTP
    public int $SMTPTimeout = 5;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'tls'; // O 'ssl' si usas SSL
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html'; // O 'text' si prefieres texto plano
    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;
}
