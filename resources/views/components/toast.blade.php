<!-- Toast System Styles and JS Component -->
<style>
    @keyframes toast-slide-in {
        0% { transform: translateX(120%); opacity: 0; }
        100% { transform: translateX(0); opacity: 1; }
    }
    @keyframes toast-slide-out {
        0% { transform: translateX(0); opacity: 1; }
        100% { transform: translateX(120%); opacity: 0; }
    }
    @keyframes toast-progress {
        0% { width: 100%; }
        100% { width: 0%; }
    }
    .animate-toast-in {
        animation: toast-slide-in 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    .animate-toast-out {
        animation: toast-slide-out 0.3s cubic-bezier(0.6, -0.28, 0.735, 0.045) forwards;
    }
    .animate-toast-progress {
        animation: toast-progress 4000ms linear forwards;
    }
</style>

<!-- Fixed Toast Container -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 w-full max-w-[350px] pointer-events-none px-4 sm:px-0"></div>

<script>
    (function () {
        // SVG Icons dictionary
        const icons = {
            success: `<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            error: `<svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>`,
            warning: `<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
            </svg>`,
            info: `<svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path>
            </svg>`
        };

        const borderClasses = {
            success: 'border-l-[4px] border-emerald-500 shadow-emerald-100/50',
            error: 'border-l-[4px] border-rose-500 shadow-rose-100/50',
            warning: 'border-l-[4px] border-amber-500 shadow-amber-100/50',
            info: 'border-l-[4px] border-sky-500 shadow-sky-100/50'
        };

        const progressBgClasses = {
            success: 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]',
            error: 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]',
            warning: 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]',
            info: 'bg-sky-500 shadow-[0_0_8px_rgba(14,165,233,0.5)]'
        };

        // Exposed global toast function
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            // Normalize type
            type = (type in icons) ? type : 'success';

            // Create toast div
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto w-full bg-white/95 backdrop-blur-md border border-slate-100 shadow-xl rounded-xl p-4 flex gap-3 relative overflow-hidden animate-toast-in ${borderClasses[type]}`;

            // Set HTML content
            toast.innerHTML = `
                <div class="flex-shrink-0">
                    ${icons[type]}
                </div>
                <div class="flex-grow pr-4">
                    <p class="text-sm font-bold text-slate-800">${message}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 transition flex-shrink-0 self-start p-0.5 leading-none" onclick="this.parentElement.closeToast()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <!-- Shrinking progress bar -->
                <div class="absolute bottom-0 left-0 h-1 animate-toast-progress ${progressBgClasses[type]}"></div>
            `;

            // Function to close/dismiss this specific toast
            let isClosing = false;
            toast.closeToast = function () {
                if (isClosing) return;
                isClosing = true;
                toast.classList.remove('animate-toast-in');
                toast.classList.add('animate-toast-out');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            };

            // Auto close after 4000ms
            const autoCloseTimeout = setTimeout(() => {
                toast.closeToast();
            }, 4000);

            // Add to container
            container.appendChild(toast);
        };

        // Read Laravel Session data on Page Load
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                window.showToast("{{ session('success') }}", 'success');
            @endif

            @if(session('error'))
                window.showToast("{{ session('error') }}", 'error');
            @endif

            @if(session('warning'))
                window.showToast("{{ session('warning') }}", 'warning');
            @endif

            @if(session('info') || session('status'))
                window.showToast("{{ session('info') ?? session('status') }}", 'info');
            @endif
        });
    })();
</script>
