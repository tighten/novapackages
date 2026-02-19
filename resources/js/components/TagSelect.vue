<template>
    <div>
        <v-select taggable class="mb-4" multiple v-model="selected" :options="tags" label="name" inputId="tags"></v-select>
        <input v-for="tag in selectedOld" type="hidden" :name="`${name}[]`" :value="tag.id" />
        <input v-for="tag in selectedNew" type="hidden" :name="`${name}-new[]`" :value="tag.name" />
    </div>
</template>

<script>
import vSelect from 'vue-select';
import _ from 'lodash';

export default {
    components: {
        'v-select': vSelect
    },
    props: ['name', 'tags', 'initialSelected'],
    data: function() {
        return {
            selected: this.initialSelected ? this.initialSelected : []
        };
    },
    computed: {
        selectedOld: function () {
            return this.selected.filter((tag) => {
                return _.has(tag, 'id');
            });
        },
        selectedNew: function () {
            return this.selected.filter((tag) => {
                return ! _.has(tag, 'id');
            });
        },
    }
};
</script>
