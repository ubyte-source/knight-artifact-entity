<?PHP

namespace Entity;

use Knight\Configuration;

use Entity\Map as Entity;

abstract class Adapter
{
    use Configuration;

    const CONFIGURATION_NAMESPACE = 0xa0d0c;
    const CONFIGURATION_NAMESPACE_DEFAULT = 'extensions\\entity';

    const CONFIGURATION_DIRECTORY = 0xa0d16;
    const CONFIGURATION_DIRECTORY_DEFAULT = 'adapters';

    public static function getAdapterNamespace() : string
    {
        return static::getConfiguration(static::CONFIGURATION_NAMESPACE) ?? static::CONFIGURATION_NAMESPACE_DEFAULT;
    }

    public static function getAdapterDirectory() : string
    {
        return static::getConfiguration(static::CONFIGURATION_DIRECTORY) ?? static::CONFIGURATION_DIRECTORY_DEFAULT;
    }
}