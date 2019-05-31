<?php

use App\Model\Invoice\Client;
use Illuminate\Database\Seeder;

class BouncerSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Permisos | Habilidades
        $this->allAbility();
        $this->clientAbilities();

        // Roles
        $this->createRoles();
    }

    protected function createRoles()
    {
        /*
        |--------------------------------------------------------------------------
        | Rol de administrador
        |--------------------------------------------------------------------------
        */
        $admin = Bouncer::role()->create([
            'name' => 'admin',
            'title' => 'Administrador',
        ]);
        Bouncer::allow($admin)->everything();
    }

    /*
      |--------------------------------------------------------------------------
      | Todas las habilidades
      |--------------------------------------------------------------------------
      |
      | Estas habilidades solo las puede tener un rol de administrador, ya que tienen autorizacion total sobre
      | los distintos modulos del sistema.
      */
    protected function allAbility(): void
    {
        Bouncer::ability()->create([
            'name' => '*',
            'title' => 'Todas las habilidades',
            'entity_type' => '*',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Productos Habilidades
    |--------------------------------------------------------------------------
    |
    | Todas la habilidades para la gestion del crud de productos
    */
    protected function clientAbilities(): void
    {
        Bouncer::ability()->createForModel(Client::class, [
            'name' => 'view',
            'title' => 'Ver clientes'
        ]);
        Bouncer::ability()->createForModel(Client::class, [
            'name' => 'create',
            'title' => 'Crear cliente'
        ]);
        Bouncer::ability()->createForModel(Client::class, [
            'name' => 'update',
            'title' => 'Actualizar cliente'
        ]);
        Bouncer::ability()->createForModel(Client::class, [
            'name' => 'delete',
            'title' => 'Eliminar cliente'
        ]);
    }

}
