<?php
/**
 * Created by PhpStorm.
 * User: Hassan Alaa
 * Date: 19/09/2017
 * Time: 01:41 م
 */

namespace Tests\Feature;


use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh');
    }

    /** @test */
    function guest_can_register_with_system()
    {
        $user = factory(User::class)->make();

        $response = $this->post('api/users/register', $user->makeVisible('password')->toArray());

        $response->assertStatus(201);

        unset($user->password);

        $this->assertDatabaseHas('users', $user->toArray());
    }

    /** @test */
    public function authorized_user_can_change_password()
    {
        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        Passport::actingAs($user);

        $response = $this->post('api/users/changePassword', ['old_password' => 'secret',
            'new_password' => 'secret2', 'new_password_confirmation' => 'secret2']);

        $response->assertStatus(200);

        $this->assertEquals(false, Hash::check('secret', $user->password));

        $this->assertEquals(true, Hash::check('secret2', $user->password));
    }

    /** @test */
    public function authorized_user_can_update_his_personal_info_and_his_avatar()
    {
        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        $fake_data = [
            'name' => 'Name',
            'email' => 'exapmle4@mail.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 60)
        ];

        Passport::actingAs(User::find($user->id));

        $response = $this->put('api/users/updatePersonalInfo', $fake_data);

        $response->assertStatus(200);

        $updated_user = User::find($user->id);

        $this->assertEquals($fake_data['name'], $updated_user->name);

        $this->assertEquals($fake_data['email'], $updated_user->email);

        Storage::disk('local')->assertExists($updated_user->avatar->avatar);
    }

    /** @test */
    public function any_authorized_user_can_search_for_certain_user()
    {
        $users = factory(User::class, 5)->create(['password' => bcrypt('secret')]);

        Passport::actingAs(User::find(1));

        $response = $this->get('api/users/search?query=example');

        $response->assertStatus(200);;
/*
        $response->assertJson(['success' => true, 'users' => $users->toArray()]);*/
    }
}