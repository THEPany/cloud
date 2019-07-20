<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Model\Invoice\{Client, Article, Bill, Payment};

class BouncerSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try{
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            DB::table('abilities')->truncate();
        }catch (Exception $e) {
            dd($e->getMessage());
        }

        // Permisos | Habilidades
        $this->allAbility();
        $this->clientAbilities();
        $this->articlesAbilities();
        $this->billsAbilities();

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
    | Clientes Habilidades
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

    /*
    |--------------------------------------------------------------------------
    | Clientes Habilidades
    |--------------------------------------------------------------------------
    |
    | Todas la habilidades para la gestion del crud de articulos
    */
    protected function articlesAbilities(): void
    {
        Bouncer::ability()->createForModel(Article::class, [
            'name' => 'view',
            'title' => 'Ver articulos'
        ]);
        Bouncer::ability()->createForModel(Article::class, [
            'name' => 'create',
            'title' => 'Crear articulo'
        ]);
        Bouncer::ability()->createForModel(Article::class, [
            'name' => 'update',
            'title' => 'Actualizar articulo'
        ]);
        Bouncer::ability()->createForModel(Article::class, [
            'name' => 'delete',
            'title' => 'Eliminar articulo'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Facturas Habilidades
    |--------------------------------------------------------------------------
    |
    | Todas la habilidades para la gestion del crud de facturas
    */
    protected function billsAbilities(): void
    {
        Bouncer::ability()->createForModel(Bill::class, [
            'name' => 'view',
            'title' => 'Ver facturas'
        ]);
        Bouncer::ability()->createForModel(Bill::class, [
            'name' => 'create',
            'title' => 'Crear factura'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pagos Habilidades
    |--------------------------------------------------------------------------
    |
    | Todas la habilidades para la gestion del crud de pagos
    */
    protected function PaymentsAbilities(): void
    {
        Bouncer::ability()->createForModel(Payment::class, [
            'name' => 'view',
            'title' => 'Ver pagos'
        ]);
        Bouncer::ability()->createForModel(Payment::class, [
            'name' => 'create',
            'title' => 'Crear pago'
        ]);
    }

}
