<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\Productpolicy;
use App\Policies\TransactionPolicy;
use App\Policies\Userolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => Userolicy::class,
        Category::class => CategoryPolicy::class,
        Product::class => Productpolicy::class,
        Transaction::class => TransactionPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
