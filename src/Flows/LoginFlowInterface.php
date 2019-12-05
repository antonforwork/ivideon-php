<?php

namespace IVideon\Flows;

use IVideon\Account;

interface LoginFlowInterface
{
    public function login(Account $account, $forceLogin = false);
}
