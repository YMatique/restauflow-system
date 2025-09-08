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
    'home' => 'Home Page',
    'welcome' => 'Welcome to Restflow, :NAME!',
    'success' => 'Operation completed successfully.',
    'error'   => 'An error occurred. Please try again.',
    'nothing_found' =>  'No :record found!',
    'product' => 'Products',

    //STATUS
    'status' => [
        'active'            => 'Active',
        'inactive'          => 'Inactive',
        'maintenance'       => 'Maintenance',

        'unavailable'       => 'Unavailable',
        'available'         => 'Available',
        'low-stock'         => 'Low stock',
    ],

    //MODAL
    'modal' => [
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'create' => 'Create'
    ],


    //FORMS
    'forms' => [
        'title' => [
            'see'       => 'See item',
            'delete'    => 'Delete item',
            'edit'      => 'Edit item'
        ],
    ],

    // TOAST MESSAGES
    'toast' => [
        'success' => [
            'key'   => 'Success!',
            'value' => 'Successfully :verb :object.',
        ],
        'error' => [
            'key'   => 'Error!',
            'value' => 'Failed to :verb :object.',
        ],
        'warning' => [
            'key'   => 'Warning!',
            'value' => 'Warning: :verb :object.',
        ],
    ],

    //MENU
    'dashboard' => [
        'title'     => 'Dashboard',
        'subtitle'  => 'Manangment',
        'stoks'     => 'Stocks',
        'products'  => 'Products'
    ],

    // Reports
    'reports' => [
        'title'            => 'Reports',
        'subtitle'         => 'Summaries',
        'daily_report'     => 'Daily Report',
        'monthly_report'   => 'Monthly Report',
    ],


    // Authentication
    'login'          => 'Login',
    'logout'         => 'Logout',
    'register'       => 'Register',
    'email'          => 'Email',
    'password'       => 'Password',
    'forgot_password'=> 'Forgot your password?',

    // Restaurant
    'menu'           => 'Menu',
    'order'          => 'Order',
    'table'          => 'Table',
    'reservation'    => 'Reservation',
    'kitchen'        => 'Kitchen',
    'bar'            => 'Bar',
    'waiter'         => 'Waiter',

    // Orders
    'order_created'   => 'Order created successfully!',
    'order_updated'   => 'Order updated.',
    'order_delivered' => 'Order delivered.',
    'order_cancelled' => 'Order cancelled.',

    // Payment
    'payment'          => 'Payment',
    'payment_success'  => 'Payment completed successfully.',
    'payment_error'    => 'Payment error. Please check the details.',

    //Products Managment
    'product_management' => [
        'title'         => 'Products Management',
    ],


    // Stock Managment
    'stock_management' => [
        'title'         => 'Stock Management',
        'stock_low'     => 'Low stock for :item.',
        'stock_updated' => 'Stock updated.',
    ],



];
