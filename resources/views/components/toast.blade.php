<div
    x-data="{
        toasts: [],
        counter: 0,
        init() {
            const status = this.$el.dataset.flashStatus;
            const error = this.$el.dataset.flashError;
            if (status) this.add(status, 'success');
            if (error) this.add(error, 'error');
        },
        add(message, type = 'success') {
            const id = ++this.counter;
            this.toasts.push({ id, message, type, visible: true });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) toast.visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
        },
    }"
    @if(session('status')) data-flash-status="{{ session('status') }}" @endif
    @if(session('error')) data-flash-error="{{ session('error') }}" @endif
    @toast.window="add($event.detail.message, $event.detail.type || 'success')"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium max-w-sm"
            :class="toast.type === 'error'
                ? 'bg-red-600 text-white'
                : 'bg-green-600 text-white'"
        >
            <span x-text="toast.message"></span>
            <button @click="remove(toast.id)" class="ml-2 opacity-70 hover:opacity-100 text-white font-bold">&times;</button>
        </div>
    </template>
</div>
