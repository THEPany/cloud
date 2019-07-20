<?php

namespace Tests\Feature\Application;

use Bouncer;
use Tests\TestCase;
use App\Organization;
use Laravel\Cashier\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignPermissionUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_assign_permission_to_user()
    {
        $subscriptionA = factory(Subscription::class)->state('active')->create();
        $subscriptionB = factory(Subscription::class)->state('active')->create();

        $organization = factory(Organization::class)->create([
            'user_id' => $subscriptionA->user->id
        ]);

        $organization->addContributor($subscriptionB->user);

        $response = $this->withoutExceptionHandling()->actingAs($subscriptionA->user)->post(route('apps.collaborator.permissions.store', [
            'slug' => $organization->slug,
            'user' => $subscriptionB->user
        ]), [
            'permissions' => [
                $permission_1 = Bouncer::ability()->create(['name' => 'permission-1'])->id,
                $permission_2 = Bouncer::ability()->create(['name' => 'permission-2'])->id,
            ]
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas(['success' => 'Permisos asignados correctamente.']);

        $this->assertDatabaseHas('permissions', [
            'ability_id' => $permission_1,
            'scope' => $organization->id
        ]);

        $this->assertDatabaseHas('permissions', [
            'ability_id' => $permission_2,
            'scope' => $organization->id
        ]);
    }
}
