<?php

declare(strict_types=1);

final class BPMXML_XmlClient
{
    private string $host;
    private int $timeout;
    private bool $debug;
    private $debugCallback;

    public function __construct(string $host, int $timeout = 5, bool $debug = false, ?callable $debugCallback = null)
    {
        $this->host = preg_replace('#^https?://#', '', trim($host));
        $this->timeout = max(1, $timeout);
        $this->debug = $debug;
        $this->debugCallback = $debugCallback;
    }

    public function fetchRaw(int $type, int $id): string
    {
        $url = 'http://' . $this->host . '/cgi-bin/webgui.fcgi?xmlitem=' . $type . '.' . $id;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->timeout,
                'ignore_errors' => true,
                'header' => "Connection: close\r\nUser-Agent: IP-Symcon BayrolPoolmanagerXML\r\n"
            ]
        ]);

        $raw = @file_get_contents($url, false, $context);
        if ($raw === false || trim($raw) === '') {
            throw new RuntimeException('keine HTTP-Antwort');
        }

        return trim($raw);
    }

    public function fetchXml(int $type, int $id): SimpleXMLElement
    {
        return $this->parseXml($this->fetchRaw($type, $id));
    }

    public function parseXml(string $raw): SimpleXMLElement
    {
        $xmlStart = strpos($raw, '<');
        if ($xmlStart === false) {
            throw new RuntimeException('Antwort ist kein XML: ' . substr($raw, 0, 100));
        }

        if ($xmlStart > 0) {
            $raw = substr($raw, $xmlStart);
        }

        if ($this->debug && is_callable($this->debugCallback)) {
            call_user_func($this->debugCallback, 'XML', substr($raw, 0, 500));
        }

        $xml = @simplexml_load_string($raw);
        if (!$xml instanceof SimpleXMLElement) {
            throw new RuntimeException('ungueltiges XML: ' . substr($raw, 0, 120));
        }

        return $xml;
    }

    public function itemAttributes(int $type, int $id): array
    {
        $xml = $this->fetchXml($type, $id);
        if (!isset($xml->item)) {
            throw new RuntimeException('XML enthaelt kein item');
        }

        $result = [];
        foreach ($xml->item->attributes() as $key => $value) {
            $result[(string)$key] = (string)$value;
        }
        return $result;
    }
}
