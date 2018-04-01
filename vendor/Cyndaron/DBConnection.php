<?php
namespace Cyndaron;

use PDO;
use PDOStatement;

ini_set('memory_limit', '96M');

/**
 * Zorgt voor verbinding met de database.
 * @author Michael Steenbeek
 */
class DBConnection
{
    /** @var PDO $pdo */
    private static $pdo;

    private function __construct() {}

    public static function connect(string $host, string $database, string $user, string $pass): bool
    {
        try
        {
            static::$pdo = @new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', $user, $pass);
            static::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return true;
        }
        catch (\Throwable $e)
        {
            error_log($e->getMessage());
            return false;
        }
    }

    public static function doQuery($query, $vars = [])
    {
        $prep = static::$pdo->prepare($query);
        $result = $prep->execute($vars);

        if ($result == false)
        {
            error_log(implode(',', $prep->errorInfo()));
        }

        return $result == false ? $result : static::$pdo->lastInsertId();
    }

    public static function doQueryAndReturnFetchable(string $query, array $vars = []): PDOStatement
    {
        $prep = static::$pdo->prepare($query);

        if ($prep === false)
        {
            throw new \Exception('Query mislukt!');
        }

        $prep->execute($vars);
        return $prep;
    }

    public static function doQueryAndFetchAll(string $query, array $vars = [])
    {
        $prep = static::$pdo->prepare($query);
        $prep->execute($vars);
        return $prep->fetchAll();
    }

    public static function doQueryAndFetchFirstRow(string $query, array $vars = [])
    {
        $prep = static::$pdo->prepare($query);
        $prep->execute($vars);
        return $prep->fetch();
    }

    public static function doQueryAndFetchOne(string $query, array $vars = [])
    {
        $prep = static::$pdo->prepare($query);
        $prep->execute($vars);
        return $prep->fetchColumn();
    }

    public static function getPdo()
    {
        return static::$pdo;
    }

    public static function errorInfo()
    {
        return static::$pdo->errorCode();
    }
}
