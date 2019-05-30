<template>
    <div>
        <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
        <multiselect :id="id" ref="input" v-model="selected" :options="options"
                     :custom-label="customLabel" v-bind="$attrs"
                     :searchable="true" :limit="2"
                     :placeholder="placeholder"
        >
            <template slot="singleLabel" slot-scope="props">
                <span class="option__desc">
                    <slot name="singleLabel" :slot-scope="props.option"></slot>
                </span>
            </template>
            <template slot="option" slot-scope="props">
                <div class="option__desc">
                    <slot name="multipleLabel" :slot-scope="props.option"></slot>
                </div>
            </template>
        </multiselect>
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
            returnObject: {
              type: Boolean,
              default() {
                return false;
              }
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
                if(this.returnObject === false) {
                  this.$emit('input', selected instanceof Object ? selected[this.valueParam] : selected);
                }else {
                  this.$emit('input', selected);
                }
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