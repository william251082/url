<?php

namespace App\Providers;

use Exception;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

class UrlShortenerServiceProvider
{
    private string $salt = '';
    private string $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private array $whitelist = [];
    private int $padding = 1;

    public function shortenUrl(string $hostName, string $longUrl, int $idToEncode): string
    {
        $url = urldecode($longUrl);
        if (!empty($url)) {
            if (!empty($this->whitelist) && !in_array($hostName, $this->whitelist)) {
                $this->error();
            }
            if (preg_match('/^http[s]?\:\/\/[\w]+/', $url)) {
                $url = $hostName.'/'.$this->encode($idToEncode);
            }
            else {
                $this->error();
            }
        }
        return $url;
    }

    /**
     * @throws Exception
     */
    public function setChars(string $chars) {
        if (!is_string($chars) || empty($chars)) {
            throw new Exception('Invalid input.');
        }
        $this->chars = $chars;
    }

    public function setSalt(string $salt) {
        $this->salt = $salt;
    }

    #[NoReturn]
    private function error(): void
    {
        exit("Not allowed.");
    }

    private function encode(int $idToEncode): string
    {
        if (!empty($this->salt)) {
            $seed = $this->getSeed($idToEncode, $this->salt, $this->padding);
            $idToEncode = (int)($seed.$idToEncode);
        }

        return $this->numToAlpha($idToEncode, $this->chars);
    }

    #[Pure]
    private function decode(string $s): float|int|string
    {
        $n = $this->alphaToNum($s, $this->chars);

        return (!empty($this->salt)) ? substr($n, $this->padding) : $n;
    }

    private function getSeed($idToEncode, $salt, $padding): int
    {
        $hash = md5($idToEncode.$salt);
        $dec = hexdec(substr($hash, 0, $padding));

        $num = $dec % pow(10, $padding);
        if ($num === 0) {
            $num = 1;
        }
        return (int)str_pad($num, $padding, '0');
    }

    private function numToAlpha($n, $s): string
    {
        $b = strlen($s);
        $m = $n % $b;
        if ($n - $m == 0) return substr($s, $n, 1);
        $a = '';

        while ($m > 0 || $n > 0) {
            $a = substr($s, $m, 1).$a;
            $n = ($n - $m) / $b;
            $m = $n % $b;
        }

        return $a;
    }

    private function alphaToNum($a, $s): float|int
    {
        $b = strlen($s);
        $l = strlen($a);

        for ($n = 0, $i = 0; $i < $l; $i++) {
            $n += strpos($s, substr($a, $i, 1)) * pow($b, $l - $i - 1);
        }

        return $n;
    }
}
