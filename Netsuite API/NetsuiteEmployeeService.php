<?php

namespace App\Services\Netsuite;


use App\Models\Netsuite\NetsuiteEmployee;
use App\Models\Netsuite\NetsuiteInventorySnapshot;
use App\Models\Netsuite\NetsuiteTransaction;
use Illuminate\Support\Facades\DB;

class NetsuiteEmployeeService
{
    public function __construct(public NetsuiteService $netsuiteService)
    {
    }
    public function getEmployees($url, $q)
    {
        return $this->netsuiteService->makeNsQuery($q, $url);
    }

    public function saveEmployees($employees)
    {
        $upsertBody = [];
        $upsertFields = [
            'hire_date',
            'email',
            'first_name',
            'last_name',
            'is_sales_rep',
            'ns_employee_id',
            'is_inactive',
            'commission_rate'
        ];

        foreach ($employees as $employee) {
            $employee = (object) $employee;
            $upsertBody[] = [
                'hire_date' => date('Y-m-d', strtotime($employee->hiredate)),
                'email' => $employee->email ?? null,
                'first_name' => $employee->firstname ?? null,
                'last_name' => $employee->lastname ?? null,
                'is_sales_rep' => ($employee->issalesrep ?? null) === "T",
                'ns_employee_id' => $employee->id,
                'is_inactive' => ($employee->isinactive ?? null) === "T",
                'commission_rate' => $employee->custentitycommission_rate ?? null,
            ];
        }

        DB::transaction(function () use ($upsertBody, $upsertFields) {
            NetsuiteEmployee::upsert($upsertBody, ['ns_employee_id'], $upsertFields);
        }, 5);
        return true;
    }
}
