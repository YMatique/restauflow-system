<?php

namespace App\Livewire;

use App\Traits\WithToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// #[Layout('components.layouts.app-main')]
#[Layout('layouts.shift-layout')]
#[Title('Testando o layout')]
class Teste extends Component
{

    use WithToast; //para notificação toast


    public function testFunction()
    {
        $this->toastSuccess('Sucesso!', 'Esta é uma notificação de sucesso.'); // se for toast de sucesso
        $this->toastError('Erro!', 'Esta é uma notificação de erro.'); // para erro
        $this->toastWarning('Atenção!', 'Esta é uma notificação de aviso.'); //para aviso
        $this->toast('Info', 'Esta é uma notificação informativa.', 'info'); // para informação
    }
    public function render()
    {
        return view('livewire.teste');
    }

    public function boot()
    {
        // Disponibilizar variáveis para o layout via ViewData
        // Por enquanto basta copiar apenas, depois vamos editar e tornar mais elegante
        view()->share([
            'pageTitle' => 'Testanto o Título',
            'pageDescription' => 'Subtítulo (se necessário)',
            'breadcrumbs' => [
                ['label' => 'Teste', 'url' => '#'],
                // ['label' => 'Planos', 'url' => '']
            ]
        ]);
    }
}
