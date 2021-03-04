<?php

/**
 * This file is part of the Dibi, smart database abstraction layer (https://dibiphp.com)
 * Copyright (c) 2005 David Grudl (https://davidgrudl.com)
 */

use PacketeryDibi\Type;


/**
 * Static container class for Dibi connections.
 */
class packetery_dibi
{
	use PacketeryDibi\Strict;

	const
		AFFECTED_ROWS = 'a',
		IDENTIFIER = 'n';

	/** version */
	const
		VERSION = '3.2.4',
		REVISION = 'released on 2020-03-26';

	/** sorting order */
	const
		ASC = 'ASC',
		DESC = 'DESC';

	/** @deprecated */
	const
		TEXT = Type::TEXT,
		BINARY = Type::BINARY,
		BOOL = Type::BOOL,
		INTEGER = Type::INTEGER,
		FLOAT = Type::FLOAT,
		DATE = Type::DATE,
		DATETIME = Type::DATETIME,
		TIME = Type::TIME,
		FIELD_TEXT = Type::TEXT,
		FIELD_BINARY = Type::BINARY,
		FIELD_BOOL = Type::BOOL,
		FIELD_INTEGER = Type::INTEGER,
		FIELD_FLOAT = Type::FLOAT,
		FIELD_DATE = Type::DATE,
		FIELD_DATETIME = Type::DATETIME,
		FIELD_TIME = Type::TIME;

	/** @var string|null  Last SQL command @see dibi::query() */
	public static $sql;

	/** @var float|null  Elapsed time for last query */
	public static $elapsedTime;

	/** @var float  Elapsed time for all queries */
	public static $totalTime;

	/** @var int  Number or queries */
	public static $numOfQueries = 0;

	/** @var string  Default dibi driver */
	public static $defaultDriver = 'mysqli';

	/** @var PacketeryDibi\Connection[]  Connection registry storage for PacketeryDibi\Connection objects */
	private static $registry = [];

	/** @var PacketeryDibi\Connection  Current connection */
	private static $connection;


	/**
	 * Static class - cannot be instantiated.
	 */
	final public function __construct()
	{
		throw new LogicException('Cannot instantiate static class ' . get_class($this));
	}


	/********************* connections handling ****************d*g**/


	/**
	 * Creates a new Connection object and connects it to specified database.
	 * @param  array|string   connection parameters
	 * @param  string  connection name
	 * @return PacketeryDibi\Connection
	 * @throws PacketeryDibi\Exception
	 */
	public static function connect($config = [], $name = '0')
	{
		return self::$connection = self::$registry[$name] = new PacketeryDibi\Connection($config, $name);
	}


	/**
	 * Disconnects from database (doesn't destroy Connection object).
	 * @return void
	 */
	public static function disconnect()
	{
		self::getConnection()->disconnect();
	}


	/**
	 * Returns true when connection was established.
	 * @return bool
	 */
	public static function isConnected()
	{
		return (self::$connection !== null) && self::$connection->isConnected();
	}


	/**
	 * Retrieve active connection.
	 * @param  string   connection registy name
	 * @return PacketeryDibi\Connection
	 * @throws PacketeryDibi\Exception
	 */
	public static function getConnection($name = null)
	{
		if ($name === null) {
			if (self::$connection === null) {
				throw new PacketeryDibi\Exception('Dibi is not connected to database.');
			}

			return self::$connection;
		}

		if (!isset(self::$registry[$name])) {
			throw new PacketeryDibi\Exception("There is no connection named '$name'.");
		}

		return self::$registry[$name];
	}


	/**
	 * Sets connection.
	 * @param  PacketeryDibi\Connection
	 * @return PacketeryDibi\Connection
	 */
	public static function setConnection(PacketeryDibi\Connection $connection)
	{
		return self::$connection = $connection;
	}


	/**
	 * @deprecated
	 */
	public static function activate($name)
	{
		trigger_error(__METHOD__ . '() is deprecated.', E_USER_DEPRECATED);
		self::$connection = self::getConnection($name);
	}


	/********************* monostate for active connection ****************d*g**/


	/**
	 * Generates and executes SQL query - Monostate for PacketeryDibi\Connection::query().
	 * @param  array|mixed      one or more arguments
	 * @return PacketeryDibi\Result|int   result set or number of affected rows
	 * @throws PacketeryDibi\Exception
	 */
	public static function query($args)
	{
		$args = func_get_args();
		return self::getConnection()->query($args);
	}


	/**
	 * Executes the SQL query - Monostate for PacketeryDibi\Connection::nativeQuery().
	 * @param  string           SQL statement.
	 * @return PacketeryDibi\Result|int   result set or number of affected rows
	 */
	public static function nativeQuery($sql)
	{
		return self::getConnection()->nativeQuery($sql);
	}


	/**
	 * Generates and prints SQL query - Monostate for PacketeryDibi\Connection::test().
	 * @param  array|mixed  one or more arguments
	 * @return bool
	 */
	public static function test($args)
	{
		$args = func_get_args();
		return self::getConnection()->test($args);
	}


	/**
	 * Generates and returns SQL query as DataSource - Monostate for PacketeryDibi\Connection::test().
	 * @param  array|mixed      one or more arguments
	 * @return PacketeryDibi\DataSource
	 */
	public static function dataSource($args)
	{
		$args = func_get_args();
		return self::getConnection()->dataSource($args);
	}


	/**
	 * Executes SQL query and fetch result - Monostate for PacketeryDibi\Connection::query() & fetch().
	 * @param  array|mixed    one or more arguments
	 * @return PacketeryDibi\Row
	 * @throws PacketeryDibi\Exception
	 */
	public static function fetch($args)
	{
		$args = func_get_args();
		return self::getConnection()->query($args)->fetch();
	}


	/**
	 * Executes SQL query and fetch results - Monostate for PacketeryDibi\Connection::query() & fetchAll().
	 * @param  array|mixed    one or more arguments
	 * @return PacketeryDibi\Row[]
	 * @throws PacketeryDibi\Exception
	 */
	public static function fetchAll($args)
	{
		$args = func_get_args();
		return self::getConnection()->query($args)->fetchAll();
	}


	/**
	 * Executes SQL query and fetch first column - Monostate for PacketeryDibi\Connection::query() & fetchSingle().
	 * @param  array|mixed    one or more arguments
	 * @return mixed
	 * @throws PacketeryDibi\Exception
	 */
	public static function fetchSingle($args)
	{
		$args = func_get_args();
		return self::getConnection()->query($args)->fetchSingle();
	}


	/**
	 * Executes SQL query and fetch pairs - Monostate for PacketeryDibi\Connection::query() & fetchPairs().
	 * @param  array|mixed    one or more arguments
	 * @return array
	 * @throws PacketeryDibi\Exception
	 */
	public static function fetchPairs($args)
	{
		$args = func_get_args();
		return self::getConnection()->query($args)->fetchPairs();
	}


	/**
	 * Gets the number of affected rows.
	 * Monostate for PacketeryDibi\Connection::getAffectedRows()
	 * @return int  number of rows
	 * @throws PacketeryDibi\Exception
	 */
	public static function getAffectedRows()
	{
		return self::getConnection()->getAffectedRows();
	}


	/**
	 * @deprecated
	 */
	public static function affectedRows()
	{
		trigger_error(__METHOD__ . '() is deprecated, use getAffectedRows()', E_USER_DEPRECATED);
		return self::getConnection()->getAffectedRows();
	}


	/**
	 * Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
	 * Monostate for PacketeryDibi\Connection::getInsertId()
	 * @param  string     optional sequence name
	 * @return int
	 * @throws PacketeryDibi\Exception
	 */
	public static function getInsertId($sequence = null)
	{
		return self::getConnection()->getInsertId($sequence);
	}


	/**
	 * @deprecated
	 */
	public static function insertId($sequence = null)
	{
		trigger_error(__METHOD__ . '() is deprecated, use getInsertId()', E_USER_DEPRECATED);
		return self::getConnection()->getInsertId($sequence);
	}


	/**
	 * Begins a transaction - Monostate for PacketeryDibi\Connection::begin().
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\Exception
	 */
	public static function begin($savepoint = null)
	{
		self::getConnection()->begin($savepoint);
	}


	/**
	 * Commits statements in a transaction - Monostate for PacketeryDibi\Connection::commit($savepoint = null).
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\Exception
	 */
	public static function commit($savepoint = null)
	{
		self::getConnection()->commit($savepoint);
	}


	/**
	 * Rollback changes in a transaction - Monostate for PacketeryDibi\Connection::rollback().
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\Exception
	 */
	public static function rollback($savepoint = null)
	{
		self::getConnection()->rollback($savepoint);
	}


	/**
	 * Gets a information about the current database - Monostate for PacketeryDibi\Connection::getDatabaseInfo().
	 * @return PacketeryDibi\Reflection\Database
	 */
	public static function getDatabaseInfo()
	{
		return self::getConnection()->getDatabaseInfo();
	}


	/**
	 * Import SQL dump from file - extreme fast!
	 * @param  string  filename
	 * @return int  count of sql commands
	 */
	public static function loadFile($file)
	{
		return PacketeryDibi\Helpers::loadFromFile(self::getConnection(), $file);
	}


	/********************* fluent SQL builders ****************d*g**/


	/**
	 * @return PacketeryDibi\Fluent
	 */
	public static function command()
	{
		return self::getConnection()->command();
	}


	/**
	 * @param  mixed    column name
	 * @return PacketeryDibi\Fluent
	 */
	public static function select($args)
	{
		$args = func_get_args();
		return call_user_func_array([self::getConnection(), 'select'], $args);
	}


	/**
	 * @param  string   table
	 * @param  array
	 * @return PacketeryDibi\Fluent
	 */
	public static function update($table, $args)
	{
		return self::getConnection()->update($table, $args);
	}


	/**
	 * @param  string   table
	 * @param  array
	 * @return PacketeryDibi\Fluent
	 */
	public static function insert($table, $args)
	{
		return self::getConnection()->insert($table, $args);
	}


	/**
	 * @param  string   table
	 * @return PacketeryDibi\Fluent
	 */
	public static function delete($table)
	{
		return self::getConnection()->delete($table);
	}


	/********************* substitutions ****************d*g**/


	/**
	 * Returns substitution hashmap - Monostate for PacketeryDibi\Connection::getSubstitutes().
	 * @return PacketeryDibi\HashMap
	 */
	public static function getSubstitutes()
	{
		return self::getConnection()->getSubstitutes();
	}


	/********************* misc tools ****************d*g**/


	/**
	 * Prints out a syntax highlighted version of the SQL command or Result.
	 * @param  string|Result
	 * @param  bool  return output instead of printing it?
	 * @return string|null
	 */
	public static function dump($sql = null, $return = false)
	{
		return PacketeryDibi\Helpers::dump($sql, $return);
	}


	/**
	 * Strips microseconds part.
	 * @param  \DateTime|\DateTimeInterface
	 * @return \DateTime|\DateTimeInterface
	 */
	public static function stripMicroseconds($dt)
	{
		$class = get_class($dt);
		return new $class($dt->format('Y-m-d H:i:s'), $dt->getTimezone());
	}
}
