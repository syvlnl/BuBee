<?php

namespace App\Services\v1;

use Illuminate\Http\Request;

class TransactionQuery
{
    protected $safeParms = [
        'name' => ['like', 'eq'],
        'isSaving' => ['eq'],
        'dateTransaction' => ['eq', 'gt', 'lt'],
        'amount' => ['eq', 'gt', 'lt'],
        'note' => ['like'],
    ];

    protected $collumsMap = [
        'name' => 'name',
        'isSaving' => 'is_saving',
        'dateTransaction' => 'date_transaction',
        'amount' => 'amount',
        'note' => 'note',
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