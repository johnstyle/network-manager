<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class Whois
 */
class Whois
{
    private const CACHE_TTL = 3600 * 24 * 30;

    private CacheInterface $cache;
    private ?string $response = null;
    private array $data = [];

    /**
     * Whois constructor.
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * sendRequest
     *
     * @param string    $name
     * @param int|float $cacheTtl
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function sendRequest(string $name, int $cacheTtl = self::CACHE_TTL): bool
    {
        $command = sprintf('whois %s -h whois.arin.net', escapeshellarg($name));
        $hash = hash('sha1', $command);

        $this->response = $this->cache->get($hash, function (ItemInterface $item) use ($command, $cacheTtl) {
            $item->expiresAfter($cacheTtl);

            return shell_exec($command);
        });

        $this->prepareData();

        return '' !== $this->response;
    }

    /**
     * getResponse
     *
     * @return string|null
     */
    public function getResponse(): ?string
    {
        return $this->response;
    }

    /**
     * findValue
     *
     * @param array $names
     *
     * @return string|null
     */
    public function findValue(array $names): ?string
    {
        foreach ($names as $name) {
            foreach ($this->data as $data) {
                if (isset($data[$name])) {
                    return $data[$name];
                }
            }
        }

        return null;
    }

    /**
     * prepareData
     */
    private function prepareData(): void
    {
        if (!$this->response) {
            return;
        }

        $section = 0;
        $items = [];

        $response = $this->response;
        $partialResponse = preg_split('/This is the RIPE Database query service/', $response);
        if ($partialResponse && isset($partialResponse[1])) {
            $response = $partialResponse[1];
        }

        $response = preg_replace("/[\n]{2,}/", "\n__section__\n", $response);
        foreach (explode("\n", $response) as $line) {
            $line = trim($line);
            if (!$line || \in_array($line[0], [ '#', '%' ], true)) {
                continue;
            }
            if ('__section__' === $line) {
                $section++;
                continue;
            }
            if (!isset($items[$section])) {
                $items[$section] = [];
            }
            try {
                [ $name, $value ] = explode(':', $line, 2);
                $name = $this->getName($items[$section], str_replace([ ' ', '-' ], '', strtolower($name)));
                $items[$section][$name] = trim($value) ?: null;
            } catch (\Exception $exception) {}
        }

        $this->data = array_values($items);
    }

    /**
     * getName
     *
     * @param array  $items
     * @param string $originalName
     * @param int    $count
     *
     * @return string
     */
    private function getName(array $items, string $originalName, int $count = 0): string
    {
        $name = $count ? sprintf('%s_%s', $originalName, $count) : $originalName;

        return isset($items[$name]) ? $this->getName($items, $originalName, $count + 1) : $name;
    }
}
