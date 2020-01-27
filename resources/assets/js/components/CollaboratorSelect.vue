<template>
    <div>
        <v-select class="mb-4" :multiple="multiple" v-model="selected" :options="collaborators" label="labelName" inputId="collaborators"></v-select>
        <input v-if="multiple" v-for="collaborator in selected" type="hidden" :name="`${name}[]`" :value="collaborator.id" />
        <input v-if="! multiple" type="hidden" :name="`${name}`" :value="selected ? selected.id : null" />
    </div>
</template>

<script>
import vSelect from 'vue-select';

export default {
    components: {
        'v-select': vSelect,
    },
    props: {'name': {}, 'collaborators': {}, 'initialSelected': {}, 'multiple': Boolean},
    data: function() {
        return {
            selected: this.initialSelected || (this.multiple ? [] : null),
            useCollaborators: [],
        };
    },
    mounted: function() {
        this.useCollaborators = this.collaborators.map((collaborator) => {
            collaborator.labelName = `${collaborator.name} (${collaborator.github_username})`;

            return collaborator;
        });

        if (this.multiple) {
            this.selected = this.selected.map((collaborator) => {
                collaborator.labelName = `${collaborator.name} (${collaborator.github_username})`;

                return collaborator;
            });
        } else {
            this.$set(this.selected, 'labelName', `${this.selected.name} (${this.selected.github_username})`);
        }
    }
};
</script>
