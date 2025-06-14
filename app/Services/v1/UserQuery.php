<?php

namespace App\Services\v1;

use Illuminate\Http\Request;

class UserQuery
{
    protected $safeParms = [
        'name' => ['like', 'eq'],
        'email' => ['like', 'eq'],
    ];

    protected $collumsMap = [];

    protected $operatorMap = [
        'like' => 'like',
        'eq' => '=',
    ];

    public function transform(Request $request)
    {
        $eloQuery = [];

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

        return $eloQuery;
    }
}