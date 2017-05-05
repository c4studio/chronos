<?php

namespace Chronos\Scaffolding\Policies;

use Illuminate\Support\Facades\Auth;

class DashboardPolicy {

    public function index() {
        return Auth::user()->role()->hasPermission('view_dashboard');
    }

}