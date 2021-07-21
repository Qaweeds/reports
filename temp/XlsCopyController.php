<?php

namespace App\Http\Controllers;

use Adldap\Adldap;
use Adldap\Schemas\ActiveDirectory;
use Carbon\Carbon;

class XlsCopyController extends Controller
{
    public function saveCopy()
    {
        $date = Carbon::now()->format('Y-m-d H:i');
        $success = \File::copy(base_path('storage/app/public/dist/Distributors.xlsx'), base_path('storage/app/public/distCopy/' . $date . '.xlsx'));
    }

    public function test()
    {
        $serverName = "DW1C.olko.local";
        $uid = "Reports";
        $pwd = "Report@s";
        $databaseName = "DWH";

        $connectionInfo = array("UID" => $uid,
            "PWD" => $pwd,
            "Database" => $databaseName);

        $conn = sqlsrv_connect($serverName, $connectionInfo);
        if ($conn) {
            echo 'OK';
        } else {
            echo '<pre>';
            print_r(sqlsrv_errors());
            echo '</pre>';
        }

        $sql = "SELECT a.[Date],
                        Container = isnull(cast(stores.StoreNo as nvarchar(128)),stores.Name),
                        employees.Name,
                        a.[SaleSum],
                        a.[Qty],
                        a.[SebSum],
                        a.[Inserted],
                        a.[Updated]
                FROM [DWH].[Fact].[SalesRR_rr] as a
                inner join [DWH].[Dim].[StoresRR] as stores on a.[ContainerIDx] = stores.id
                inner join [DWH].[Dim].[EmployeesRR] as employees on a.[PersonIDx] = employees.id";


        $stmt = sqlsrv_query( $conn, $sql );


        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arr[] = $row;
        }

        dd($arr[0]['Name']);
    }
}

