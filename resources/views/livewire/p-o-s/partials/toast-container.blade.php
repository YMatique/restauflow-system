{{-- resources/views/livewire/pos/partials/toast-container.blade.php --}}
<div id="toast-container" class="fixed top-24 right-6 z-50 space-y-2">
    <!-- Toasts serão inseridos aqui via JavaScript -->
</div>

@push('scripts')
    <script>
        // Listener para toasts
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', (data) => {
                showToast(data[0]);
            });
        });

        function showToast(data) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                info: 'bg-blue-600'
            };

            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };

            toast.className = `${colors[data.type]} text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 flex items-center gap-3`;
            toast.innerHTML = `
                <span class="text-lg">${icons[data.type]}</span>
                <span>${data.message}</span>
                <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            container.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 300);
            }, 5000);
        }
    </script>
@endpush