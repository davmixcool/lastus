<?php

namespace Nzesalem\Lastus\Tests;

use Carbon\Carbon;
use Nzesalem\Lastus\Lastus;
use Illuminate\Support\Facades\DB;
use Nzesalem\Lastus\Tests\Models\User;

/**
 * Class BaseTest
 *
 * @package Lastus
 */
class BaseTest extends TestCase
{
    /**
     * Test basic Lastus functionality.
     */
    public function testStatusMutatorAndAccessor()
    {
        $user = User::create([
            'name' => 'Salem Nzeukwu',
            'email' => 'email@domain.com',
            'password' => bcrypt('secret'),
            // And now Lastus!
            'status' => 'active',
        ]);
        
        $this->assertEquals('active', $user->status);
    }

    public function testModelStatusCodeMethod()
    {
        $now = Carbon::now();

        // Inserting data this way does not call eloquent mutators
        // so you need to use status code instead of status name
        DB::table('users')->insert([
            'name' => 'Fake Name',
            'email' => 'fake@example.com',
            'password' => bcrypt('secret'),
            'created_at' => $now,
            'updated_at' => $now,
            // Note: Adding a literal string such as 'suspended' here will work
            // this is due to sqlite 'dynamic typing system'
            // But thats definitely not what we want.
            'status' => User::statusCode('suspended'),
        ]);

        $user = User::whereRaw('status = ' . User::statusCode('suspended'))->first();

        $this->assertEquals('suspended', $user->status);
    }
}