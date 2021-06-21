<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MsConnectController extends Controller
{
    public static function msQuery($sql)
    {
        $serverName = config('mssql.srv');
        $uid = config('mssql.uid');
        $pwd = config('mssql.pwd');
        $databaseName = config('mssql.db');
        $connectionInfo = array(
            "UID" => $uid,
            "PWD" => $pwd,
            "Database" => $databaseName,
            "CharacterSet" => 'UTF-8',
        );
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        if (!$conn) {
            dd(sqlsrv_errors());
        }

        $stmt = sqlsrv_query($conn, $sql);


        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $arr[] = $row;
        }

        sqlsrv_close($conn);

        return $arr;
    }

}
