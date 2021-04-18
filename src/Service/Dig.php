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
     * find
     *
     * @param string    $name
     * @param int|float $cacheTtl
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function find(string $name, int $cacheTtl = self::CACHE_TTL): array
    {
        $command = sprintf('dig +noall +answer -x %s', escapeshellarg($name));
        $hash = hash('sha1', $command);

        $response = $this->cache->get($hash, function (ItemInterface $item) use ($command, $cacheTtl) {
            $item->expiresAfter($cacheTtl);

            return shell_exec($command);
        });

        return $this->prepareResponse($response);
    }

    /**
     * prepareResponse
     *
     * @param string|null $response
     *
     * @return array
     */
    private function prepareResponse(?string $response): array
    {
        if (!$response) {
            return [];
        }

        $items = [];
        foreach (explode("\n", $response) as $line) {
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
