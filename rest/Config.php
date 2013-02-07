<?php

/**
 * Config class file . 
 * 
 * This class loads the the configuration file and allows access to the 
 * configuration values with Config->myParameter. It replaces the old class 
 * RestConfig and uses a config file instead of hard coding the parameters in 
 * the class.
 *
 * @package   FedoraProxy
 * @author    Franck Borel <franck.borel@ub.uni-freiburg.de>
 * @copyright 2012 Freiburg University Library
 * @license   GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @version   Release: <package_version>
 */
class Config implements ArrayAccess, Countable, IteratorAggregate
{

    protected static $_configFile = '/../config/main.php';
    private $_values = array();

    public function __construct()
    {
        $values = @include( __DIR__ . self::$_configFile );
        if (is_array($values)) {
            $this->_values = &$values;
        }
    }

    /**
     * enables to set values using the object notation i.e. 
     * $config->myValue = 'something';
     * 
     * @param type $key
     * @param type $value 
     */
    public function __set($key, $value)
    {
        $this->_values[$key] = $value;
    }

    /**
     * enables to get values using the object notation i.e. $config->myValue; 
     *
     * @param type $key
     * @return type 
     */
    public function __get($key)
    {
        return $this->_values[$key];
    }

    /**
     * returns number of elements iside config
     *
     * @return integer number of elements inside config
     */
    public function count()
    {
        return sizeof($this->_values);
    }

    /**
     * checks if a given key exists
     *
     * @param mixed $offset key of item to check
     * @return boolean true if key exisits, false otherwise
     */
    public function offsetExists($offset)
    {
        return isset($this->_values[$offset]);
    }

    /**
     * retreive the value of a given key
     *
     * @param mixed $offset key of item to fetch
     * @return mixed value of the matched element
     */
    public function offsetGet($offset)
    {
        return $this->_values[$offset];
    }

    /**
     * assigns a new value to a key
     *
     * @param mixed $offset key of the element to set
     * @param mixed $value value to assign
     */
    public function offsetSet($offset, $value)
    {
        $this->_values[$offset] = $value;
    }

    /**
     * removes an item from the config
     *
     * @param mixed $offset key of the elment to remove
     */
    public function offsetUnset($offset)
    {
        unset($this->_values[$offset]);
    }

    /**
     * retrive an iterator for config values
     *
     * @return Iterator iterator of config values
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_values);
    }

}

