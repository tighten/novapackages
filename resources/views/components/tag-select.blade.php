@props(['tags', 'selected' => [], 'name'])

<div
    x-data="{
        init() {
            let options = @js($tags->map(fn($t) => ['value' => $t->id, 'text' => $t->name]));
            let initialSelected = @js($selected);

            let hiddenContainer = this.$refs.hiddenInputs;
            let fieldName = @js($name);

            this.tomSelect = new TomSelect(this.$refs.select, {
                options: options,
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                placeholder: 'Select tags...',
                maxItems: null,
                create: true,
                plugins: ['remove_button'],
                onItemAdd: () => this.updateHiddenInputs(),
                onItemRemove: () => this.updateHiddenInputs(),
            });

            if (initialSelected && Array.isArray(initialSelected)) {
                initialSelected.forEach(item => {
                    let val = typeof item === 'object' ? item.id : item;
                    this.tomSelect.addItem(val, true);
                });
            }

            this.updateHiddenInputs();
        },
        updateHiddenInputs() {
            let container = this.$refs.hiddenInputs;
            let fieldName = @js($name);
            container.innerHTML = '';

            let items = this.tomSelect.items;
            items.forEach(val => {
                let input = document.createElement('input');
                input.type = 'hidden';

                let option = this.tomSelect.options[val];
                if (option && option.value && !isNaN(option.value)) {
                    input.name = fieldName + '[]';
                    input.value = option.value;
                } else {
                    input.name = fieldName + '-new[]';
                    input.value = val;
                }
                container.appendChild(input);
            });
        }
    }"
    class="mb-4"
    wire:ignore
>
    <select x-ref="select" multiple>
    </select>
    <div x-ref="hiddenInputs"></div>
</div>
