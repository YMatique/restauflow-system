@props([
    'padding' => 'md', // sm, md, lg, none
    'shadow' => true,
    'border' => false
])

@php
$paddings = [
    'none' => '',
    'sm' => 'p-4',
    'md' => 'p-6',
    'lg' => 'p-8'
];

$classes = 'bg-white rounded-xl';
if($shadow) $classes .= ' shadow-lg hover:shadow-xl transition-shadow duration-200';
if($border) $classes .= ' border border-gray-200';
$classes .= ' ' . $paddings[$padding];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
