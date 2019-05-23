<template>
    <div>
        <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
        <input :id="id" ref="input" v-bind="$attrs" :class="[inputClass ? inputClass : 'form-input', { error: errors.length }]" :type="type" :value="value" @input="$emit('input', $event.target.value)">
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
                    return `text-input-${this._uid}`
                },
            },
            type: {
                type: String,
                default: 'text',
            },
            value: [String, Number],
            label: String,
            inputClass: String,
            errors: {
                type: Array,
                default: () => [],
            },
        },
        methods: {
            focus() {
                this.$refs.input.focus()
            },
            select() {
                this.$refs.input.select()
            },
            setSelectionRange(start, end) {
                this.$refs.input.setSelectionRange(start, end)
            },
        },
    }
</script>