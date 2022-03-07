<?PHP

namespace Entity;

use Knight\Configuration;

use Entity\Map as Entity;

/* The Adapter class is an abstract class that is used to create a new adapter */
abstract class Adapter
{
    use Configuration;

    const CONFIGURATION_NAMESPACE = 0xa0d0c;
    const CONFIGURATION_NAMESPACE_DEFAULT = 'extensions\\entity';

    const CONFIGURATION_DIRECTORY = 0xa0d16;
    const CONFIGURATION_DIRECTORY_DEFAULT = 'adapters';

   /**
    * Returns the namespace of the adapter
    * 
    * @return The namespace of the adapter.
    */

    public static function getAdapterNamespace() : string
    {
        return static::getConfiguration(static::CONFIGURATION_NAMESPACE) ?? static::CONFIGURATION_NAMESPACE_DEFAULT;
    }

    /**
     * Returns the directory where the adapter files are stored
     * 
     * @return The value of the configuration directory.
     */
    
    public static function getAdapterDirectory() : string
    {
        return static::getConfiguration(static::CONFIGURATION_DIRECTORY) ?? static::CONFIGURATION_DIRECTORY_DEFAULT;
    }
}