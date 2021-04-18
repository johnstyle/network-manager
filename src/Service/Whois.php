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
     * findFromArin
     *
     * @param string    $name
     * @param array     $values
     * @param int|float $cacheTtl
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function findFromArin(string $name, array $values = [], int $cacheTtl = self::CACHE_TTL): array
    {
        $command = sprintf('whois -h whois.arin.net %s', escapeshellarg($name));
        $hash = hash('sha1', $command);

        $response = $this->cache->get($hash, function (ItemInterface $item) use ($command, $cacheTtl) {
            $item->expiresAfter($cacheTtl);

            return shell_exec(sprintf('%s', $command));
        });

        return $this->prepareResponse($response, $values);
    }

    /**
     * findFromCymru
     *
     * @param string    $name
     * @param array     $values
     * @param int|float $cacheTtl
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function findFromCymru(string $name, array $values, int $cacheTtl = self::CACHE_TTL): array
    {
        $command = sprintf('whois -h whois.cymru.com %s', escapeshellarg(sprintf(' -v %s', $name)));
        $hash = hash('sha1', $command);

        $response = $this->cache->get($hash, function (ItemInterface $item) use ($command, $cacheTtl) {
            $item->expiresAfter($cacheTtl);

            return shell_exec(sprintf('%s', $command));
        });

        if (!$response) {
            return [];
        }

        $data = [];

        $items = array_map('trim', explode('|', explode("\n", $response)[1]));
        $items = array_combine([ 'asn', 'ip', 'route', 'country', 'registry', 'allocatedAt', 'organization' ], $items);
        $items['organization'] = preg_replace('/, [A-Z]{2}$/', '', $items['organization']);

        foreach (array_keys($values) as $property) {
            if (isset($items[$property])) {
                $data[$property] = $items[$property];
            }
        }

        return $data;
    }

    /**
     * prepareResponse
     *
     * @param string|null $response
     * @param array       $values
     *
     * @return array
     */
    private function prepareResponse(?string $response, array $values): array
    {
        if (!$response) {
            return [];
        }

        $section = 0;
        $items = [];

        $partialResponse = explode('Renvoi trouvé vers', $response);
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

        $data = [];
        $items = array_values($items);

        foreach ($values as $property => $names) {
            foreach ($names as $name) {
                foreach ($items as $item) {
                    if (isset($item[$name])) {
                        $data[$property] = $item[$name];
                    }
                }
            }
        }

        return \count($values) ? $data : $items;
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
