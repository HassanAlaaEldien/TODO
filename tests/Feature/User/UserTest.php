<?php
/**
 * Created by PhpStorm.
 * User: Hassan Alaa
 * Date: 19/09/2017
 * Time: 01:41 م
 */

namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    function guest_can_register_with_system()
    {
        $user = factory(User::class)->make();

        $response = $this->post('api/users/register', $user->makeVisible('password')->toArray());

        $response->assertStatus(201);

        unset($user->password);

        $this->assertDatabaseHas('users', $user->toArray());
    }
}