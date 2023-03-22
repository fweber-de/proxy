<?php

namespace fweber\Proxy;

use Exception;
use Symfony\Component\Yaml\Yaml;

class Host
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @var string
     */
    public string $target;

    /**
     * @param string $id
     * @return Host|null
     * @throws Exception
     */
    public static function fromConfig(string $id): ?Host
    {
        $conf = __DIR__.'/../config/proxy.yaml';
        $data = Yaml::parse(file_get_contents($conf));

        if(!isset($data['hosts'][$id])) {
            return null;
        }

        $host = new Host();
        $host->id = $id;
        $host->target = $data['hosts'][$id]['target'];

        return $host;
    }

    /**
     * @return Host[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        $conf = __DIR__.'/../config/proxy.yaml';
        $data = Yaml::parse(file_get_contents($conf));

        $hosts = [];

        foreach ($data['hosts'] as $id => $_host) {
            $hosts[] = self::fromConfig($id);
        }

        return $hosts;
    }
}
