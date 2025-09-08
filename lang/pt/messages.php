<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Restflow System Messages
    |--------------------------------------------------------------------------
    | This file contains interface messages, feedbacks, and texts used in the
    | system in Portuguese. You can modify them as needed.
    |
    */

    // General
    'appName' => 'Restflow',
    'home' => 'Página Inicial',
    'welcome' => 'Bem-vindo ao Restflow, :NAME!',
    'success' => 'Operação concluída com sucesso.',
    'error'   => 'Ocorreu um erro. Por favor, tente novamente.',
    'nothing_found' =>  'Nenhum :record encontrado!',
    'product' => 'Produtos',

    // STATUS
    'status' => [
        'active'            => 'Ativo',
        'inactive'          => 'Inativo',
        'maintenance'       => 'Manutenção',

        'unavailable'       => 'Indisponível',
        'available'         => 'Disponível',
        'low-stock'         => 'Estoque baixo',
    ],

    // MODAL
    'modal' => [
        'save' => 'Salvar',
        'cancel' => 'Cancelar',
        'edit' => 'Editar',
        'create' => 'Criar'
    ],

    // FORMS
    'forms' => [
        'title' => [
            'see'       => 'Ver item',
            'delete'    => 'Excluir item',
            'edit'      => 'Editar item'
        ],
    ],

    // TOAST MESSAGES
    'toast' => [
        'success' => [
            'key'   => 'Sucesso!',
            'value' => ':verb :object com sucesso.',
        ],
        'error' => [
            'key'   => 'Erro!',
            'value' => 'Falha ao :verb :object.',
        ],
        'warning' => [
            'key'   => 'Atenção!',
            'value' => 'Aviso: :verb :object.',
        ],
    ],

    // MENU
    'dashboard' => [
        'title'     => 'Painel de Controle',
        'subtitle'  => 'Gerenciamento',
        'stoks'     => 'Estoques',
        'products'  => 'Produtos'
    ],

    // Reports
    'reports' => [
        'title'            => 'Relatórios',
        'subtitle'         => 'Resumos',
        'daily_report'     => 'Relatório Diário',
        'monthly_report'   => 'Relatório Mensal',
    ],

    // Authentication
    'login'          => 'Entrar',
    'logout'         => 'Sair',
    'register'       => 'Registrar',
    'email'          => 'Email',
    'password'       => 'Senha',
    'forgot_password'=> 'Esqueceu sua senha?',

    // Restaurant
    'menu'           => 'Cardápio',
    'order'          => 'Pedido',
    'table'          => 'Mesa',
    'reservation'    => 'Reserva',
    'kitchen'        => 'Cozinha',
    'bar'            => 'Bar',
    'waiter'         => 'Garçom',

    // Orders
    'order_created'   => 'Pedido criado com sucesso!',
    'order_updated'   => 'Pedido atualizado.',
    'order_delivered' => 'Pedido entregue.',
    'order_cancelled' => 'Pedido cancelado.',

    // Payment
    'payment'          => 'Pagamento',
    'payment_success'  => 'Pagamento concluído com sucesso.',
    'payment_error'    => 'Erro no pagamento. Por favor, verifique os detalhes.',

    // Products Management
    'product_management' => [
        'title'         => 'Gestão de Produtos',
    ],

    // Stock Management
    'stock_management' => [
        'title'         => 'Gestão de Estoque',
        'stock_low'     => 'Estoque baixo para :item.',
        'stock_updated' => 'Estoque atualizado.',
    ],

];
