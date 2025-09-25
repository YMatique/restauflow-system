{{-- resources/views/livewire/pos/pos-component.blade.php --}}
<div>
    <!-- Sidebar Esquerdo -->
    <x-slot name="sidebar">
        @include('livewire.p-o-s.partials.sidebar')
    </x-slot>

    <!-- Área Central -->
    @if($currentView === 'tables')
        @include('livewire.p-o-s.partials.tables-view')
    @elseif($currentView === 'products')
        @include('livewire.p-o-s.partials.products-view')
    @endif

    <!-- Painel Direito -->
    <x-slot name="rightPanel">
        @include('livewire.p-o-s.partials.order-summary')
    </x-slot>

    <!-- Toast Notifications -->
    @include('livewire.p-o-s.partials.toast-container')
</div>