<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocType extends Model
{
    // Optional if table name follows convention
    protected $table = 'doc_types';


    //NAVES SPACES SALES, STOCKMOVEMENT ETC>>>
    const Inventory = [
       'namespace' =>  Inventory::class,
    ];

    // Fillable fields for mass assignment
    protected $fillable = [
        'description',
        'sigla',
        'namespace',
        'numerator',
        'company_id',
    ];


    /*
     * Get the next numerator
     */
    public function nextNumerator(): int
    {
        $this->increment('numerator');
        
        // Refresh the model to get the latest value
        $this->refresh();

        return $this->numerator;
    }

    /**
     * Increment numerator
     */
    // public function incrementNumerator(): void
    // {
    //     $this->increment('numerator');
    // }

    // public static function getFirstByCompanyAndNamespace($companyId, $namespace)
    // {
    //     return self::where('company_id', $companyId)
    //                ->where('namespace', $namespace)
    //                ->first(); // retorna o primeiro registro encontrado
    // }
}
