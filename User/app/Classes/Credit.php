<?php

namespace App\Classes;

use App\Models\CreditLogs;
use App\Models\User;
use App\Repository\CreditRepositoryInterface;
use App\Repository\UserRepositoryInterface;

class Credit
{
    private UserRepositoryInterface $userRepository;
    private $creditRepository;

    public function __construct(UserRepositoryInterface $userRepository, CreditRepositoryInterface $creditRepository)
    {
        $this->userRepository = $userRepository;
        $this->creditRepository = $creditRepository;
    }

    public function create($data) {
        return $this->creditRepository->create($data);
    }

    public function getCredit(array $data)
    {
        $user_id = $data['user_id'];
        $company_id = $this->userRepository->findById($user_id)->company_id;
        $logs = $this->creditRepository->index(['company_id' => $company_id]);

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
        return $this->creditRepository->update($data['credit_log_id'], $data, ['type'=> 'lock']);
    }

    public function log(array $data)
    {
        return $this->creditRepository->index($data);
    }
}
