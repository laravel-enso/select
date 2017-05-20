<template>
    <div :id="'bs-select-' + _uid">
        <i class="fa fa-times reset btn-box-tool"
           @click="removeSelection"
           v-if="!multiple && reset && selectedOptions">
        </i>
        <select v-model="selectedOptions"
                :multiple="multiple"
                :id="'select-' + _uid"
                :name="name"
                :disabled="disabled"
                class="form-control"
                @change="$emit('input', selectedOptions)">
            <option v-for="option in optionsList"
                    :value="option.key"
                    v-html="option.value">
            </option>
        </select>
    </div>
</template>

<script>

    export default {

        props: {
            name: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            source: {
                type: String,
                default: null
            },
            selected: {
                default: null
            },
            options: {
                type: Array,
                default: null
            },
            reset: {
                type: Boolean,
                default: false
            },
            customParams: {
                type: Object,
                default: null
            }
        },

        computed: {
            isServerSide() {
                return this.options ? false : true;
            }
        },

        watch: {
            customParams: {
                handler: 'getOptionsList',
                deep: true
            },
            selected: {
                handler: 'handleSelectedChange'
            },
            options: {
                handler: 'handleOptionsChange',
                deep: true
            }
        },

        data() {
            return {
                optionsList: this.options || [],
                selectedOptions: this.selected ? this.selected : (this.multiple ? [] : this.selected)
            };
        },

        methods: {
            getOptionsList() {
                let query = $('#bs-select-' + this._uid + ' input').val() || '', //we don't want undefined
                    params = {
                        customParams: this.customParams,
                        query: query,
                        selected: this.selectedOptions
                    };

                axios.get(this.source, {params: params}).then(response => {
                    this.optionsList = response.data;

                    if (this.multiple && this.optionsList.length === 0) {
                        this.optionsList = [ { key: null, value: ''} ];
                    }
                }).then(() => {
                        $('#select-' + this._uid).selectpicker('refresh');
                });
            },
            removeSelection() {
                this.selectedOptions = '';
                //we need next tick for the race condition when selectpicker
                //runs before vue finishes updating the DOM
                this.$nextTick(() => {
                    $('#select-' + this._uid).selectpicker('refresh');
                    this.$emit('input', this.selectedOptions);
                });
            },
            handleSelectedChange() {
                this.selectedOptions = this.selected;

                if (this.isServerSide) {
                    return this.getOptionsList();
                }

                $('#select-' + this._uid).selectpicker('val', this.selectedOptions);
                this.$emit('input', this.selectedOptions);
            },
            handleOptionsChange() {
                this.optionsList = this.options;
                this.removeSelection();
            },
            init() {
                $('#select-' + this._uid).selectpicker({
                    width: '100%',
                    liveSearch: true,
                    size: 5,
                    actionsBox: true,
                    title: $.fn.selectpicker.defaults.noneSelectedText
                });
            }
        },

        mounted() {
            this.init();

            if (this.selectedOptions) {
                this.$emit('input', this.selectedOptions);
                //necesary for using without server-side
                $('#select-' + this._uid).selectpicker('val', this.selectedOptions);
            }

            if (this.isServerSide) {
                this.getOptionsList();
                let self = this;
                $('#bs-select-' + this._uid + ' input').on('input', _.throttle(self.getOptionsList, 200));
            }
        },

        beforeDestroy() {
            $('#select-' + this._uid).selectpicker('destroy');
        }
    }

</script>

<style>

    i.reset {
        z-index: 10;
        position: absolute;
        right: 35px;
        bottom: 24px;
        cursor: pointer;
    }

</style>