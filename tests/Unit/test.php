<?php

use App\Models\User;

it('creates user', function () {
    $user = User::factory()->create();
    expect($user)->not()->toBeNull();
});