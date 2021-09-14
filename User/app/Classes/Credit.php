<?php

namespace App\Classes;

use App\Models\CreditLogs;
use App\Models\User;

class Credit
{
    public function create($data) {
        $credit_log = new CreditLogs();
        $credit_log->user_id = $data['user_id'];
        $credit_log->amount = $data['amount'];
        $credit_log->type = $data['type'];
        $credit_log->admin_id = $data['admin_id'] ?? null;
        $credit_log->save();

        return $credit_log;
    }

    public function getCredit(array $data)
    {
        $user_id = $data['user_id'];
        $company_id = User::where('id', $user_id)->first()->company_id;
        $logs = CreditLogs::whereHas('user.company', function ($q) use ($company_id) {
            $q->where('id', $company_id);
        })->get();

        $credit = 0;
        foreach ($logs as $log) {
            switch ($log->type) {
                case 'increase':
                    $credit += $log->amount;
                    break;
                case 'lock':
                case 'decrease':
                    $credit -= $log->amount;
                    break;
            }
        }

        return $credit;
    }

    public function update(array $data)
    {
        $credit_log = CreditLogs::where('id', $data['credit_log_id'])->where('type', 'lock')->first();
        $credit_log->update($data);
        $credit_log->refresh();

        return $credit_log;
    }

    public function log(array $data)
    {
        $credit = new CreditLogs();

        if (isset($data['company_id']) && $data['company_id'] != '')
            $credit = $credit->whereHas('user.company', function ($q) use ($data) {
                $q->where('id', $data['company_id']);
            });

        if (isset($data['seller_id']) && $data['seller_id'] != '')
            $credit = $credit->where('user_id', $data['seller_id']);

        return $credit->get();
    }
}
