<?php

namespace App\Policies;

use App\customer;
use Illuminate\Auth\Access\HandlesAuthorization;
use UserInterface;

class customerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any customers.
     *
     * @param  \App\customer  $user
     * @return mixed
     */
    public function viewAny(customer $user)
    {
        //
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param  \App\customer  $user
     * @param  \App\customer  $customer
     * @return mixed
     */
    public function view(customer $user, customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param  \App\customer  $user
     * @return mixed
     */
    public function create(customer $user)
    {
        //
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param  \App\customer  $user
     * @param  \App\customer  $customer
     * @return mixed
     */
    public function update(customer $user, customer $customer)
    {
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @param  \App\customer  $user
     * @param  \App\customer  $customer
     * @return mixed
     */
    public function delete(customer $user, customer $customer)
    {
    }

    /**
     * Determine whether the user can restore the customer.
     *
     * @param  \App\customer  $user
     * @param  \App\customer  $customer
     * @return mixed
     */
    public function restore(customer $user, customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer.
     *
     * @param  \App\customer  $user
     * @param  \App\customer  $customer
     * @return mixed
     */
    public function forceDelete(customer $user, customer $customer)
    {
        //
    }
}
