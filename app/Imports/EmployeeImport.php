<?php

namespace App\Imports;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Rate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel, WithHeadingRow
{
    private $companies;
    private $departments;
    private $rates;

    public function __construct()
    {
        $this->companies = Company::select('id', 'name')->get();
        $this->departments = Department::select('id', 'name')->get();
        $this->rates = Rate::select('id', 'rate')->get();
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $company = $this->companies->where('name', $row['company_name'])->first();
        $department = $this->departments->where('name', $row['department_name'])->first();
        $rate = $this->rates->where('rate', $row['rate'])->first();

        return new Employee([
            'company_id' => $company->id ?? NULL,
            'department_id' => $department->id ?? NULL,
            'rate_id' => $rate->id ?? NULL,
            'staff_no' => $row['staff_no'],
            'employee_name' => $row['employee_name'],
        ]);
    }
}
