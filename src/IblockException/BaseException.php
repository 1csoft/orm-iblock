<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 12.07.2018
 */

namespace Soft1c\OrmIblock\IblockException;

use Throwable;

class BaseException extends \Exception
{
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 *
	 * @param string|array $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param Throwable $previous [optional] The previous throwable used for the exception chaining.
	 *
	 * @since 5.1.0
	 */
	public function __construct($message = "", int $code = 0, Throwable $previous = null)
	{
		if(is_array($message)){
			$message = implode(', ', $message);
		}
		parent::__construct($message, $code, $previous);
	}

	/**
	 * String representation of the exception
	 * @link http://php.net/manual/en/exception.tostring.php
	 * @return string the string representation of the exception.
	 * @since 5.1.0
	 */
	public function __toString()
	{
		return '['.$this->code.'] '.$this->message;
	}

	/**
	 * @method trace
	 * @return string
	 */
	public function trace()
	{
		return parent::__toString();
	}
}