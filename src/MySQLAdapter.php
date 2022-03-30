<?php namespace Leven\DBA\MySQL;
// by Leon, MIT License

use PDO, PDOException;
use Leven\DBA\Common\Exception\{
    EmptyResultException,
    ArgumentValidationException,
    Driver\DriverException,
    Driver\TxnNotActiveException
};
use Leven\DBA\Common\{DatabaseAdapterInterface, DatabaseAdapterResponse};

final class MySQLAdapter implements DatabaseAdapterInterface
{

    private PDO $driver;
    private int $transactionDepth = 0;

    /**
     * @throws DriverException
     */
    function __construct(
        string         $database,
        string         $user = '',
        string         $password = '',
        string         $host = '127.0.0.1',
        int            $port = 3306,
        string         $charset = 'UTF8',
        private string $prefix = '',
    )
    {
        try {
            $this->driver = new PDO(
                dsn: "mysql:host=$host;port=$port;dbname=$database;charset=$charset",
                username: $user,
                password: $password,
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw new DriverException(message: 'PDO init failed', previous: $e);
        }
    }

    public function escapeName(string $string): string
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }

    public function escapeValue(string $string): string
    {
        return $this->driver->quote($string);
    }

    /**
     * @throws DriverException
     */
    public function schema(string $table): array
    {
        $query =
            'DESCRIBE ' .
            $this->escapeName($this->prefix . $table);

        try {
            $result = $this->driver->query($query, PDO::FETCH_ASSOC);

            $data = [];
            $id = 0;
            while ($data[$id] = $result->fetch()) $id++;
            unset($data[$id]);
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }

        return $data;
    }

    /**
     * @throws DriverException
     */
    public function count(string $table): int
    {
        // TODO
        throw new DriverException('method work in progress');
    }

    /**
     * @throws ArgumentValidationException
     * @throws DriverException
     * @throws EmptyResultException
     */
    public function get(string $table, array|string $columns = '*', array $conditions = [], array $options = []): DatabaseAdapterResponse
    {
        $conditions = $this->parseConditions($conditions);

        $query =
            'SELECT ' . $this->parseColumns($columns) .
            ' FROM ' . $this->escapeName($this->prefix . $table) .
            $conditions['query'] .
            $this->parseOptions($options);
        try {
            $stat = $this->driver->prepare($query);

            foreach ($conditions['params'] as $index => $param)
                $stat->bindParam($index + 1, $param[0], $param[1]);

            $stat->execute();
            $count = $stat->rowCount();

            $data = [];
            $id = 0;
            if ($count > 0) {
                while ($data[$id] = $stat->fetch()) $id++;
                unset($data[$id]);
            }
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }


        $response = new DatabaseAdapterResponse(
            query: $query,
            count: $count,
            rows: $data,
        );

        if (($options['single'] ?? false)) {
            if ($count === 0) throw new EmptyResultException;
            $response->row = $data[0];
        }

        return $response;
    }

    /**
     * @throws ArgumentValidationException
     * @throws DriverException
     */
    public function insert(string $table, array $data): DatabaseAdapterResponse
    {
        $data = $this->parseData($data);

        $query =
            'INSERT INTO ' .
            $this->escapeName($this->prefix . $table) .
            $data['query'];

        try {
            $stat = $this->driver->prepare($query);

            foreach ($data['params'] as $row) {
                foreach ($row as $index => [$param, $type]) {
                    $stat->bindValue($index + 1, $param, $type);
                    //print_r($index); echo PHP_EOL; print_r($param); echo PHP_EOL;
                }
                //var_dump($stat);
                $stat->execute();
            }
            $count = $stat->rowCount();
            $lastID = $this->driver->lastInsertId();
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }

        return new DatabaseAdapterResponse(
            query: $query,
            count: $count,
            lastID: $lastID
        );
    }

    /**
     * @throws ArgumentValidationException
     * @throws DriverException
     */
    public function update(string $table, array $data, array $conditions = [], array $options = []): DatabaseAdapterResponse
    {
        $data = $this->parseData($data);
        $conditions = $this->parseConditions($conditions);

        $query =
            'UPDATE ' .
            $this->escapeName($this->prefix . $table) .
            $data['query'] .
            $conditions['query'] .
            $this->parseOptions($options);

        try {
            $stat = $this->driver->prepare($query);

            foreach ([... $data['params'][0], ...$conditions['params']] as $index => [$param, $type])
                $stat->bindValue($index + 1, $param, $type);

            $stat->execute();
            $count = $stat->rowCount();
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }

        return new DatabaseAdapterResponse(
            query: $query,
            count: $count
        );
    }

    /**
     * @throws ArgumentValidationException
     * @throws DriverException
     */
    public function delete(string $table, array $conditions = [], array $options = []): DatabaseAdapterResponse
    {
        $conditions = $this->parseConditions($conditions);

        $query =
            'DELETE FROM ' .
            $this->escapeName($this->prefix . $table) .
            $conditions['query'] .
            $this->parseOptions($options);

        try {
            $stat = $this->driver->prepare($query);

            foreach ($conditions['params'] as $index => [$param, $type])
                $stat->bindParam($index + 1, $param, $type);

            $stat->execute();
            $count = $stat->rowCount();
        } catch (PDOException $e) {
            throw new DriverException(message: 'query failed', previous: $e);
        }

        return new DatabaseAdapterResponse(
            query: $query,
            count: $count
        );
    }

    /**
     * @throws DriverException
     */
    public function txnBegin(): MySQLAdapter
    {
        if ($this->transactionDepth) {
            $this->transactionDepth++;
            return $this;
        }

        try {
            $this->driver->beginTransaction();
            $this->transactionDepth = 1;
        } catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnCommit(): MySQLAdapter
    {
        if (!$this->transactionDepth) throw new TxnNotActiveException;

        try {
            if ($this->transactionDepth === 1) $this->driver->commit();
            $this->transactionDepth--;
        } catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

    /**
     * @throws TxnNotActiveException
     * @throws DriverException
     */
    public function txnRollback(): MySQLAdapter
    {
        if (!$this->transactionDepth) throw new TxnNotActiveException;

        try {
            $this->driver->rollBack();
            $this->transactionDepth = 0;
        } catch (PDOException $e) {
            throw new DriverException(message: 'txn failed', previous: $e);
        }

        return $this;
    }

    // INTERNAL METHODS

    /**
     * @throws ArgumentValidationException
     */
    private function parseColumns(array|string $columns): string
    {
        if (empty($columns)) return '';
        if (is_string($columns)) return $columns;

        foreach ($columns as $index => $column) {
            if (!is_string($column) && !is_numeric($column) || empty($column))
                throw new ArgumentValidationException("invalid column name with index " . $index);


            $columns[$index] = $this->escapeName((string)$column);
        }

        return implode(', ', $columns);
    }

    /**
     * @throws ArgumentValidationException
     */
    private function parseConditions(array $conditions): array
    {
        $output = ['query' => '', 'params' => []];

        if (empty($conditions)) return $output;

        $index = 0;
        foreach ($conditions as $column => $value) {
            $output['params'][] = $this->generateParam($column, $value, $index);

            if (empty($output['query'])) $output['query'] = ' WHERE ';
            else $output['query'] .= ' AND ';

            $output['query'] .= $this->escapeName($column) . ' <=> ?';
            $index++;
        }

        return $output;
    }

    /**
     * @throws ArgumentValidationException
     */
    private function parseOptions(array $options): string
    {
        $query = '';

        if (isset($options['order'])) {
            if (!is_string($options['order']))
                throw new ArgumentValidationException("invalid options[order] format");
            if ($options['order']) $query .= ' ORDER BY ' . $options['order'];
        }

        if (isset($options['single']) && $options['single']) $options['limit'] = 1;

        if (isset($options['limit'])) {
            if (!is_numeric($options['limit']) || $options['limit'] < 1)
                throw new ArgumentValidationException("invalid options[limit] format");

            $query .= ' LIMIT ' . $options['limit'];

            if (isset($options['offset'])) {
                if (!is_numeric($options['offset']) || $options['offset'] < 0)
                    throw new ArgumentValidationException("invalid options[offset] format");

                $query .= ' OFFSET ' . $options['offset'];
            }
        }

        return $query;
    }

    /**
     * @throws ArgumentValidationException
     */
    private function parseData(array $data): array
    {
        if (empty($data)) throw new ArgumentValidationException('data must not be empty');

        $output = ['query' => '', 'params' => []];

        $index = 0;
        foreach ($data as $column => $value) {
            $output['params'][0][] = $this->generateParam($column, $value, $index++);

            if (empty($output['query']))    $output['query']    =   ' SET ';
            else                            $output['query']    .=  ', ';

            $output['query'] .= $this->escapeName($column) . ' = ?';
        }

        return $output;
    }

    /**
     * @throws ArgumentValidationException
     */
    private function generateParam($column, $value, $index): array
    {
        if (!is_string($column) && !is_numeric($column) || empty($column))
            throw new ArgumentValidationException("invalid column name with index " . $index);

        if (is_bool($value)) return [$value, PDO::PARAM_BOOL];
        else if (is_null($value) || is_numeric($value)) return [$value, PDO::PARAM_INT];
        else if (is_string($value)) return [$value, PDO::PARAM_STR];

        else throw new ArgumentValidationException("invalid value for column " . $column);
    }

}
