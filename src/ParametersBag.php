<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock;

use Traversable;

class ParametersBag implements \Iterator, \Countable, \Serializable
{
	/** @var array|null  */
	protected $values = array();

	/**
	 * ParametersBag constructor.
	 *
	 * @param array|null $values
	 */
	public function __construct(array $values = null)
	{
		if ($values !== null){
			$this->values = $values;
		}

	}

	/**
	 * @method get
	 * @param bool|string $name
	 *
	 * @return mixed|null
	 */
	public function get($name = false)
	{
		if (isset($this->values[$name]) || array_key_exists($name, $this->values)){
			return $this->values[$name];
		}

		return null;
	}

	/**
	 * @method set
	 * @param bool $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function set($key = false, $value)
	{
		$this->values[$key] = $value;

		return $this;
	}

	/**
	 * @method setValues - set param Values
	 * @param array $values
	 */
	public function setValues(array $values)
	{
		$this->values = $values;
	}

	/**
	 * @method remove
	 * @param bool $name
	 *
	 * @return $this
	 */
	public function remove($name = false)
	{
		unset($this->values[$name]);

		return $this;
	}

	/**
	 * @method clear
	 */
	public function clear()
	{
		$this->values = array();
	}

	/**
	 * @method current
	 * @return mixed
	 */
	public function current()
	{
		return current($this->values);
	}

	/**
	 * @method next
	 * @return mixed
	 */
	public function next()
	{
		return next($this->values);
	}

	/**
	 * @method key
	 * @return int|mixed|null|string
	 */
	public function key()
	{
		return key($this->values);
	}

	/**
	 * @method valid
	 * @return bool
	 */
	public function valid()
	{
		return ($this->key() !== null);
	}

	/**
	 * @method rewind
	 * @return mixed
	 */
	public function rewind()
	{
		return reset($this->values);
	}

	/**
	 * @method count
	 * @return int
	 */
	public function count()
	{
		return count($this->values);
	}

	/**
	 * @method serialize
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->values);
	}

	/**
	 * @method unserialize
	 * @param string $serialized
	 *
	 * @return $this
	 */
	public function unserialize($serialized)
	{
		$this->values = unserialize($serialized);

		return $this;
	}

	/**
	 * @method has
	 * @param $key
	 *
	 * @return bool
	 */
	public function has($key)
	{
		return isset($this->values[$key]) || array_key_exists($key, $this->values);
	}

	/**
	 * @method isEmpty
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->values);
	}

	/**
	 * @method all
	 * @return array
	 */
	public function all()
	{
		return (array)$this->values;
	}

	/**
	 * @method getIterator
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->all());
	}

	/**
	 * @method getKeys
	 * @return array
	 */
	public function getKeys()
	{
		return array_keys($this->all());
	}

	/**
	 * @method getValues
	 * @return array
	 */
	public function getValues()
	{
		return array_values($this->all());
	}
}