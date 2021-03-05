<?php

namespace Packetery\SDK\Database;

use PDO;
use PacketeryDibi;


/**
 * Driver options:
 *   - dsn => driver specific DSN
 *   - username (or user)
 *   - password (or pass)
 *   - options (array) => driver specific options {@see PDO::__construct}
 *   - resource (PDO) => existing connection
 *   - version
 *   - lazy, profiler, result, substitutes, ... => see PacketeryDibi\Connection options
 */
class PdoDriver implements IDriver, IDriverResult
{
	/** @var PDO  Connection resource */
	protected $connection;

	/** @var \PDOStatement  Resultset resource */
	private $resultSet;

	/** @var int|FALSE  Affected rows */
	private $affectedRows = FALSE;

	/** @var string */
	private $driverName;

	/** @var string */
	private $serverVersion;


	/**
	 * @throws PacketeryDibi\NotSupportedException
	 */
	public function __construct()
	{
		if (!extension_loaded('pdo')) {
			throw new PacketeryDibi\NotSupportedException("PHP extension 'pdo' is not loaded.");
		}
	}


	/**
	 * Connects to a database.
	 * @return void
	 * @throws PacketeryDibi\Exception
	 */
	public function connect(array $config)
	{
		$foo = & $config['dsn'];
		$foo = & $config['options'];
		PacketeryDibi\Helpers::alias($config, 'resource', 'pdo');

		if ($config['resource'] instanceof PDO) {
			$this->connection = $config['resource'];
			unset($config['resource'], $config['pdo']);
		} else {
			try {
				$this->connection = new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
			} catch (\PDOException $e) {
				if ($e->getMessage() === 'could not find driver') {
					throw new PacketeryDibi\NotSupportedException('PHP extension for PDO is not loaded.');
				}
				throw new PacketeryDibi\DriverException($e->getMessage(), $e->getCode());
			}
		}

		$this->driverName = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
		$this->serverVersion = isset($config['version'])
			? $config['version']
			: @$this->connection->getAttribute(PDO::ATTR_SERVER_VERSION); // @ - may be not supported
	}


	/**
	 * Disconnects from a database.
	 * @return void
	 */
	public function disconnect()
	{
		$this->connection = NULL;
	}


	/**
	 * Executes the SQL query.
	 * @param  string      SQL statement.
	 * @return PacketeryDibi\ResultDriver|NULL
	 * @throws PacketeryDibi\DriverException
	 */
	public function query($sql)
	{
		// must detect if SQL returns result set or num of affected rows
		$cmd = strtoupper(substr(ltrim($sql), 0, 6));
		static $list = ['UPDATE' => 1, 'DELETE' => 1, 'INSERT' => 1, 'REPLAC' => 1];
		$this->affectedRows = FALSE;

		if (isset($list[$cmd])) {
			$this->affectedRows = $this->connection->exec($sql);
			if ($this->affectedRows !== FALSE) {
				return;
			}
		} else {
			$res = $this->connection->query($sql);
			if ($res) {
				return $this->createResultDriver($res);
			}
		}

		list($sqlState, $code, $message) = $this->connection->errorInfo();
		$message = "SQLSTATE[$sqlState]: $message";
		switch ($this->driverName) {
			case 'mysql':
				throw new DriverException($message, $code);

			case 'oci':
				throw new DriverException($message, $code);

			case 'pgsql':
				throw new DriverException($message);

			case 'sqlite':
				throw new DriverException($message, $code);

			default:
				throw new DriverException($message, $code);
		}
	}


	/**
	 * Gets the number of affected rows by the last INSERT, UPDATE or DELETE query.
	 * @return int|FALSE  number of rows or FALSE on error
	 */
	public function getAffectedRows()
	{
		return $this->affectedRows;
	}


	/**
	 * Retrieves the ID generated for an AUTO_INCREMENT column by the previous INSERT query.
	 * @return int|FALSE  int on success or FALSE on failure
	 */
	public function getInsertId($sequence)
	{
		return $this->connection->lastInsertId();
	}


	/**
	 * Begins a transaction (if supported).
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\DriverException
	 */
	public function begin($savepoint = NULL)
	{
		if (!$this->connection->beginTransaction()) {
			$err = $this->connection->errorInfo();
			throw new PacketeryDibi\DriverException("SQLSTATE[$err[0]]: $err[2]", $err[1]);
		}
	}


	/**
	 * Commits statements in a transaction.
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\DriverException
	 */
	public function commit($savepoint = NULL)
	{
		if (!$this->connection->commit()) {
			$err = $this->connection->errorInfo();
			throw new PacketeryDibi\DriverException("SQLSTATE[$err[0]]: $err[2]", $err[1]);
		}
	}


	/**
	 * Rollback changes in a transaction.
	 * @param  string  optional savepoint name
	 * @return void
	 * @throws PacketeryDibi\DriverException
	 */
	public function rollback($savepoint = NULL)
	{
		if (!$this->connection->rollBack()) {
			$err = $this->connection->errorInfo();
			throw new PacketeryDibi\DriverException("SQLSTATE[$err[0]]: $err[2]", $err[1]);
		}
	}


	/**
	 * Returns the connection resource.
	 * @return PDO
	 */
	public function getResource()
	{
		return $this->connection;
	}


	/**
	 * Returns the connection reflector.
	 * @return PacketeryDibi\Reflector
	 */
	public function getReflector()
	{
		switch ($this->driverName) {
			case 'mysql':
				return new MySqlReflector($this);

			case 'sqlite':
				return new SqliteReflector($this);

			default:
				throw new PacketeryDibi\NotSupportedException;
		}
	}


	/**
	 * Result set driver factory.
	 * @param  \PDOStatement
	 * @return PacketeryDibi\ResultDriver
	 */
	public function createResultDriver(\PDOStatement $resource)
	{
		$res = clone $this;
		$res->resultSet = $resource;
		return $res;
	}


	/********************* SQL ****************d*g**/


	/**
	 * Encodes data for use in a SQL statement.
	 * @param  mixed     value
	 * @return string    encoded value
	 */
	public function escapeText($value)
	{
		if ($this->driverName === 'odbc') {
			return "'" . str_replace("'", "''", $value) . "'";
		} else {
			return $this->connection->quote($value, PDO::PARAM_STR);
		}
	}


	public function escapeBinary($value)
	{
		if ($this->driverName === 'odbc') {
			return "'" . str_replace("'", "''", $value) . "'";
		} else {
			return $this->connection->quote($value, PDO::PARAM_LOB);
		}
	}


	public function escapeIdentifier($value)
	{
		switch ($this->driverName) {
			case 'mysql':
				return '`' . str_replace('`', '``', $value) . '`';

			case 'oci':
			case 'pgsql':
				return '"' . str_replace('"', '""', $value) . '"';

			case 'sqlite':
				return '[' . strtr($value, '[]', '  ') . ']';

			case 'odbc':
			case 'mssql':
				return '[' . str_replace(['[', ']'], ['[[', ']]'], $value) . ']';

			case 'dblib':
			case 'sqlsrv':
				return '[' . str_replace(']', ']]', $value) . ']';

			default:
				return $value;
		}
	}


	public function escapeBool($value)
	{
		if ($this->driverName === 'pgsql') {
			return $value ? 'TRUE' : 'FALSE';
		} else {
			return $value ? 1 : 0;
		}
	}


	public function escapeDate($value)
	{
		if (!$value instanceof \DateTime && !$value instanceof \DateTimeInterface) {
			$value = new PacketeryDibi\DateTime($value);
		}
		return $value->format($this->driverName === 'odbc' ? '#m/d/Y#' : "'Y-m-d'");
	}


	public function escapeDateTime($value)
	{
		if (!$value instanceof \DateTime && !$value instanceof \DateTimeInterface) {
			$value = new PacketeryDibi\DateTime($value);
		}
		return $value->format($this->driverName === 'odbc' ? "#m/d/Y H:i:s#" : "'Y-m-d H:i:s'");
	}


	/**
	 * Encodes string for use in a LIKE statement.
	 * @param  string
	 * @param  int
	 * @return string
	 */
	public function escapeLike($value, $pos)
	{
		switch ($this->driverName) {
			case 'mysql':
				$value = addcslashes(str_replace('\\', '\\\\', $value), "\x00\n\r\\'%_");
				return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'");

			case 'oci':
				$value = addcslashes(str_replace('\\', '\\\\', $value), "\x00\\%_");
				$value = str_replace("'", "''", $value);
				return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'");

			case 'pgsql':
				$bs = substr($this->connection->quote('\\', PDO::PARAM_STR), 1, -1); // standard_conforming_strings = on/off
				$value = substr($this->connection->quote($value, PDO::PARAM_STR), 1, -1);
				$value = strtr($value, ['%' => $bs . '%', '_' => $bs . '_', '\\' => '\\\\']);
				return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'");

			case 'sqlite':
				$value = addcslashes(substr($this->connection->quote($value, PDO::PARAM_STR), 1, -1), '%_\\');
				return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'") . " ESCAPE '\\'";

			case 'odbc':
			case 'mssql':
			case 'dblib':
			case 'sqlsrv':
				$value = strtr($value, ["'" => "''", '%' => '[%]', '_' => '[_]', '[' => '[[]']);
				return ($pos <= 0 ? "'%" : "'") . $value . ($pos >= 0 ? "%'" : "'");

			default:
				throw new PacketeryDibi\NotImplementedException;
		}
	}


	/**
	 * Decodes data from result set.
	 * @param  string
	 * @return string
	 */
	public function unescapeBinary($value)
	{
		return $value;
	}


	/** @deprecated */
	public function escape($value, $type)
	{
		trigger_error(__METHOD__ . '() is deprecated.', E_USER_DEPRECATED);
		return PacketeryDibi\Helpers::escape($this, $value, $type);
	}


	/**
	 * Injects LIMIT/OFFSET to the SQL query.
	 * @return void
	 */
	public function applyLimit(& $sql, $limit, $offset)
	{
		if ($limit < 0 || $offset < 0) {
			throw new PacketeryDibi\NotSupportedException('Negative offset or limit.');
		}

		switch ($this->driverName) {
			case 'mysql':
				if ($limit !== NULL || $offset) {
					// see http://dev.mysql.com/doc/refman/5.0/en/select.html
					$sql .= ' LIMIT ' . ($limit === NULL ? '18446744073709551615' : (int) $limit)
						. ($offset ? ' OFFSET ' . (int) $offset : '');
				}
				break;

			case 'pgsql':
				if ($limit !== NULL) {
					$sql .= ' LIMIT ' . (int) $limit;
				}
				if ($offset) {
					$sql .= ' OFFSET ' . (int) $offset;
				}
				break;

			case 'sqlite':
				if ($limit !== NULL || $offset) {
					$sql .= ' LIMIT ' . ($limit === NULL ? '-1' : (int) $limit)
						. ($offset ? ' OFFSET ' . (int) $offset : '');
				}
				break;

			case 'oci':
				if ($offset) {
					// see http://www.oracle.com/technology/oramag/oracle/06-sep/o56asktom.html
					$sql = 'SELECT * FROM (SELECT t.*, ROWNUM AS "__rnum" FROM (' . $sql . ') t '
						. ($limit !== NULL ? 'WHERE ROWNUM <= ' . ((int) $offset + (int) $limit) : '')
						. ') WHERE "__rnum" > '. (int) $offset;

				} elseif ($limit !== NULL) {
					$sql = 'SELECT * FROM (' . $sql . ') WHERE ROWNUM <= ' . (int) $limit;
				}
				break;

			case 'mssql':
			case 'sqlsrv':
			case 'dblib':
				if (version_compare($this->serverVersion, '11.0') >= 0) { // 11 == SQL Server 2012
					// requires ORDER BY, see https://technet.microsoft.com/en-us/library/gg699618(v=sql.110).aspx
					if ($limit !== NULL) {
						$sql = sprintf('%s OFFSET %d ROWS FETCH NEXT %d ROWS ONLY', rtrim($sql), $offset, $limit);
					} elseif ($offset) {
						$sql = sprintf('%s OFFSET %d ROWS', rtrim($sql), $offset);
					}
					break;
				}
				// intentionally break omitted

			case 'odbc':
				if ($offset) {
					throw new PacketeryDibi\NotSupportedException('Offset is not supported by this database.');

				} elseif ($limit !== NULL) {
					$sql = 'SELECT TOP ' . (int) $limit . ' * FROM (' . $sql . ') t';
					break;
				}
				// intentionally break omitted

			default:
				throw new PacketeryDibi\NotSupportedException('PDO or driver does not support applying limit or offset.');
		}
	}


	/********************* result set ****************d*g**/


	/**
	 * Returns the number of rows in a result set.
	 * @return int
	 */
	public function getRowCount()
	{
		return $this->resultSet->rowCount();
	}


	/**
	 * Fetches the row at current position and moves the internal cursor to the next position.
	 * @param  bool     TRUE for associative array, FALSE for numeric
	 * @return array    array on success, nonarray if no next record
	 */
	public function fetch($assoc)
	{
		return $this->resultSet->fetch($assoc ? PDO::FETCH_ASSOC : PDO::FETCH_NUM);
	}


	/**
	 * Moves cursor position without fetching row.
	 * @param  int   the 0-based cursor pos to seek to
	 * @return bool  TRUE on success, FALSE if unable to seek to specified record
	 */
	public function seek($row)
	{
		throw new PacketeryDibi\NotSupportedException('Cannot seek an unbuffered result set.');
	}


	/**
	 * Frees the resources allocated for this result set.
	 * @return void
	 */
	public function free()
	{
		$this->resultSet = NULL;
	}


	/**
	 * Returns metadata for all columns in a result set.
	 * @return array
	 * @throws PacketeryDibi\Exception
	 */
	public function getResultColumns()
	{
		$count = $this->resultSet->columnCount();
		$columns = [];
		for ($i = 0; $i < $count; $i++) {
			$row = @$this->resultSet->getColumnMeta($i); // intentionally @
			if ($row === FALSE) {
				throw new PacketeryDibi\NotSupportedException('Driver does not support meta data.');
			}
			$row = $row + [
				'table' => NULL,
				'native_type' => 'VAR_STRING',
			];

			$columns[] = [
				'name' => $row['name'],
				'table' => $row['table'],
				'nativetype' => $row['native_type'],
				'type' => $row['native_type'] === 'TIME' && $this->driverName === 'mysql' ? PacketeryDibi\Type::TIME_INTERVAL : NULL,
				'fullname' => $row['table'] ? $row['table'] . '.' . $row['name'] : $row['name'],
				'vendor' => $row,
			];
		}
		return $columns;
	}

	/**
	 * Returns the result set resource.
	 * @return \PDOStatement
	 */
	public function getResultResource()
	{
		return $this->resultSet;
	}

    /** @var int $key The cursor pointer */
    protected $key;
    /** @var  bool|\stdClass The resultset for a single row */
    protected $result;
    /** @var  bool $valid Flag indicating there's a valid resource or not */
    protected $valid = true;

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->result;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $this->key++;
        $this->result = $this->resultSet->fetch(
            \PDO::FETCH_ASSOC,
            \PDO::FETCH_ORI_ABS,
            $this->key
        );
        if (false === $this->result) {
            $this->valid = false;
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->valid;
    }

    /** todo fix probably throw
     * @inheritDoc
     */
    public function rewind()
    {
        $this->key = -1;
        $this->next();
    }

    public function count()
    {
        return $this->resultSet->rowCount();
    }

    public function isConnected()
    {
        return $this->connection !== null;
    }
}
