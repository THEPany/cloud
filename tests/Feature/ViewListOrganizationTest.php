<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\{Organization, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewListOrganizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_only_organization_view_organization_which_it_belongs()
    {
        $user = factory(User::class)->create();

        factory(Organization::class)->times(4)->create([
            'user_id' => $user->id
        ]);

        factory(Organization::class)->create();

        $this->actingAs($user)->get(route('organizations.index'))
            ->assertPropCount('organizations.data', 4)
            ->assertPropValue('organizations.data', function ($organizations) {
                $this->assertEquals(
                    ['id', 'user_id', 'name', 'slug', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code', 'deleted_at'],
                    array_keys($organizations[0])
                );
            });
    }

    /** @test */
    function user_can_view_organization_its_a_contributor()
    {
        $user = factory(User::class)->create();

        $organization = factory(Organization::class)->create();

        $organization->addContributor($user);

        factory(Organization::class)->times(4)->create();

        $this->actingAs($user)->get(route('organizations.index'))
            ->assertPropCount('organizations.data', 1);
    }

    /** @test */
    function user_cannot_view_organization_its_not_a_contributor()
    {
        $user = factory(User::class)->create();

        factory(Organization::class)->create();

        factory(Organization::class)->times(4)->create();

        $this->actingAs($user)->get(route('organizations.index'))
            ->assertPropCount('organizations.data', 0);
    }

    /** @test */
    public function can_search_for_organizations()
    {
        $user = factory(User::class)->create();

        factory(Organization::class)->times(4)->create([
            'user_id' => $user->id
        ]);

        factory(Organization::class)->create([
            'user_id' => $user->id,
            'name' => 'Some Big Fancy Company Name'
        ]);

        $this->actingAs($user)
            ->get('/organizations?search=Some Big Fancy Company Name')
            ->assertStatus(200)
            ->assertPropValue('filters.search', 'Some Big Fancy Company Name')
            ->assertPropCount('organizations.data', 1)
            ->assertPropValue('organizations.data', function ($organizations) {
                $this->assertEquals('Some Big Fancy Company Name', $organizations[0]['name']);
            });
    }

    /*public function test_cannot_view_deleted_organizations()
    {
        $this->user->account->organizations()->saveMany(
            factory(Organization::class, 5)->make()
        )->first()->delete();
        $this->actingAs($this->user)
            ->get('/organizations')
            ->assertStatus(200)
            ->assertPropCount('organizations.data', 4);
    }
    public function test_can_filter_to_view_deleted_organizations()
    {
        $this->user->account->organizations()->saveMany(
            factory(Organization::class, 5)->make()
        )->first()->delete();
        $this->actingAs($this->user)
            ->get('/organizations?trashed=with')
            ->assertStatus(200)
            ->assertPropValue('filters.trashed', 'with')
            ->assertPropCount('organizations.data', 5);
    }*/
}
