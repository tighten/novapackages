@props(['collaborators', 'selected' => null, 'name', 'multiple' => false])

<div
    x-data="{
        multiple: @js($multiple),
        selected: @js($selected),
        init() {
            let options = @js($collaborators->map(fn($c) => ['value' => $c->id, 'text' => $c->name_with_username]));

            let config = {
                options: options,
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                placeholder: 'Select...',
            };

            if (this.multiple) {
                config.maxItems = null;
                config.plugins = ['remove_button'];
            } else {
                config.maxItems = 1;
            }

            this.tomSelect = new TomSelect(this.$refs.select, config);

            if (this.selected) {
                if (this.multiple && Array.isArray(this.selected)) {
                    this.selected.forEach(item => {
                        let val = typeof item === 'object' ? item.id : item;
                        this.tomSelect.addItem(val, true);
                    });
                } else if (!this.multiple) {
                    let val = typeof this.selected === 'object' ? this.selected.id : this.selected;
                    this.tomSelect.addItem(val, true);
                }
            }
        }
    }"
    class="mb-4"
    wire:ignore
>
    @if ($multiple)
        <select x-ref="select" name="{{ $name }}[]" multiple>
        </select>
    @else
        <select x-ref="select" name="{{ $name }}">
            <option value="">Select...</option>
        </select>
    @endif
</div>
