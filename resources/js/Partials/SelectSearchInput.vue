<template>
    <div>
        <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
        <multiselect :id="id" ref="input" v-model="selected" :options="options"
                     :custom-label="customLabel" v-bind="$attrs"
                     :searchable="true" open-direction="bottom" :limit="5"
                     :placeholder="placeholder"
        ></multiselect>
        <div v-if="errors.length" class="form-error">{{ errors[0] }}</div>
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default() {
                    return `select-input-${this._uid}`
                },
            },
            value: [String, Number, Boolean, Object],
            label: String,
            options: Array,
            placeholder: String,
            customLabel: Function,
            valueParam: {
                type: String,
                default: 'id'
            },
            errors: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                selected: this.value,
            }
        },
        watch: {
            selected(selected) {
                this.$emit('input', selected instanceof Object ? selected[this.valueParam] : selected)
            },
        },
        methods: {
            focus() {
                this.$refs.input.focus()
            },
            select() {
                this.$refs.input.select()
            },
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>