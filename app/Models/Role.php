<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
      use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',       // nome do papel, ex: super_admin, company_admin, vendedor
        'description' // descrição opcional
    ];

    // 🔹 Relacionamentos

    // Usuários que possuem esta role
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    // Vendedores que possuem esta role
    public function vendedores()
    {
        return $this->belongsToMany(User::class, 'role_vendedor');
    }


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');

    }

     /**
     * Cria roles padrão se não existirem
     */
    public static function seedDefaultRoles()
    {
        $roles = [
            'super_admin' => 'Super administrador do sistema',
            'company_admin' => 'Administrador da empresa',
            'company_user'  => 'Usuário comum da empresa',
            'vendedor'      => 'Vendedor'
        ];

        foreach ($roles as $name => $description) {
            self::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
