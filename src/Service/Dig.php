<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Class Dig
 */
class Dig
{
    private const CACHE_TTL = 3600 * 24 * 30;

    private CacheInterface $cache;
    private ?string $response = null;

    /**
     * Dig constructor.
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
     * @return string|null
     *
     * @throws InvalidArgumentException
     */
    public function sendRequest(string $name, int $cacheTtl = self::CACHE_TTL): ?string
    {
        $command = sprintf('dig +noall +answer -x %s', escapeshellarg($name));
        $hash = hash('sha1', $command);

        $this->response = $this->cache->get($hash, function (ItemInterface $item) use ($command, $cacheTtl) {
            $item->expiresAfter($cacheTtl);

            return shell_exec($command);
        });

        return $this->response;
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
     * getData
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getData(): array
    {
        $items = [];
        foreach (explode("\n", $this->response) as $line) {
            $line = trim($line);
            if (!$line) {
                continue;
            }

            $linePart = preg_split('/[[:space:]]+/', $line);
            if (!$linePart) {
                continue;
            }

            $items[] = [
                'name' => trim($linePart[0] ?? '', '.'),
                'ttl' => (int) ($linePart[1] ?? 0),
                'class' => $linePart[2] ?? '',
                'type' => $linePart[3] ?? '',
                'record' => trim($linePart[4] ?? '', '.'),
            ];
        }

        return $items;
    }
}
