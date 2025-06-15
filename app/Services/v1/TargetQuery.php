<?php

namespace App\Services\v1;

use Illuminate\Http\Request;

class TargetQuery
{
    protected $safeParms = [
        'name' => ['like', 'eq'],
        'amountNeeded' => ['eq', 'gt', 'lt'],
        'amountCollected' => ['eq', 'gt', 'lt'],
        'deadline' => ['eq', 'gt', 'lt'],
        'status' => ['eq'],
    ];

    protected $collumsMap = [
        'name' => 'name',
        'amountNeeded' => 'amount_needed',
        'amountCollected' => 'amount_collected',
        'deadline' => 'deadline',
        'status' => 'status',
    ];

    protected $operatorMap = [
        'like' => 'like',
        'eq' => '=',
        'gt' => '>=',
        'lt' => '<=',
    ];

    public function transform(Request $request)
    {
        $eloQuery = [];
        $hasQuery = !empty($request->query());

        foreach ($this->safeParms as $parm => $operators){
            $query = $request->query($parm);

            if(!isset($query)){
                continue;
            }

            $column = $this->collumsMap[$parm] ?? $parm;

            foreach($operators as $operator) {
                if (isset($query[$operator])) {
                    if ($operator === 'like') {
                        $query[$operator] = '%' . $query[$operator] . '%';
                    }
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }

        if ($hasQuery && count($eloQuery) === 0) {
            return false;
        }

        return $eloQuery;
    }
}