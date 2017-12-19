<script type="text/x-template" id="autocomplete-template">

    <div class="form-autocomplete">
        <input v-bind:class="this.inputClass" type="text" v-on:focus="toggleList" v-on:keydown="handleEnter" v-on:keyup="toggleList" v-on:blur="closeList" v-model="search" />
        <ul class="suggestions" v-show="listOpen">
            <li v-for="(listLabel, listValue) in filteredValues" v-on:mousedown="selectValue(listValue)" v-html="listLabel"></li>
            <li class="no-results" v-if="!filteredValues || Object.keys(filteredValues).length == 0">{!! trans('chronos.scaffolding::interface.No results available') !!}</li>
        </ul>
        <ul class="tags" v-if="tags">
            <li v-for="(tag, key) in tags" v-on:click="deleteTag(key)">
                <span v-html="tag"></span>
                <input v-bind:id="id + '.' + key" v-bind:name="name + '[]'" type="hidden" v-bind:value="tagValues[key]" />
            </li>
        </ul>
        <input v-bind:id="id" v-bind:name="name" type="hidden" v-model="value" v-if="!multiple" />

    </div>

</script>

<script>
    Vue.component('autocomplete', {
        computed: {
            filteredValues: function() {
                if (this.search && this.search.length >= this.minLength) {
                    var ret = {};

                    Object.keys(this.values).forEach(function (key) {
                        if (this.values[key].toLocaleLowerCase().indexOf(this.search.toLocaleLowerCase()) > -1)
                            ret[key] = this.values[key];
                    }.bind(this));

                    return ret;
                }

                return {};
            }
        },
        created: function() {
            this.getValues();
        },
        data: function() {
            return {
                listOpen: false,
                search: '',
                tags: [],
                tagValues: [],
                value: null,
                values: {}
            }
        },
        methods: {
            closeList: function() {
                this.listOpen = false;
            },
            deleteTag: function(key) {
                this.tags.splice(key, 1);
                this.tagValues.splice(key, 1);
            },
            getValues: function() {
                // Local data source
                if (typeof this.src == 'object') {
                    this.values = this.src;

                    this.setDefaults();
                }
                // Remote data source
                else {
                    // show loader
                    if (typeof editorEventHub !== 'undefined') {
                        editorEventHub.$emit('show-data-loader');
                    }

                    params = {};
                    params.perPage = 0;
                    params.withInactive = 1;

                    var valueField = this.valueField;
                    var labelField = this.labelField;

                    var ret = {};

                    this.$http.get(this.src, { params: params }).then(function(response) {
                        if (response.body.data.length > 0) {
                            response.body.data.forEach(function(item) {
                                ret[item[valueField]] = item[labelField];
                            });
                        }

                        this.values = ret;

                        this.setDefaults();

                        // hide loader
                        if (typeof editorEventHub !== 'undefined') {
                            editorEventHub.$emit('hide-data-loader');
                        }
                    }.bind(this), function(response) {
                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }
                        else {
                            vm.$emit('add-alert', {
                                type: 'error',
                                title: 'AJAX error',
                                message: response.statusText + ' (' + response.status + ')'
                            });
                        }

                        // hide loader
                        if (typeof editorEventHub !== 'undefined') {
                            editorEventHub.$emit('hide-data-loader');
                        }
                    });
                }
            },
            handleEnter: function(e) {
                if (e.which == 13) {
                    if (this.multiple && typeof this.src == 'object') {
                        this.selectValue(e.target.value);
                    }

                    e.preventDefault();
                    return false;
                }
            },
            selectValue: function(value) {
                // Classic autocomplete
                if (!this.multiple) {
                    this.value = value;
                    this.search = this.values[this.value];
                }
                // Tagging autocomplete
                else {
                    if (this.tagValues.indexOf(value) == -1) {
                        this.tagValues.push(value);

                        // if value is not selectable, but using local data source then
                        // select anyway and add to selectable values
                        if (!this.values[value])
                            this.values[value] = value;

                        this.tags.push(this.values[value]);
                    }
                    this.search = '';
                }

                this.listOpen = false;
            },
            setDefaults: function() {
                if (this.defaultValue != null) {
                    this.value = this.defaultValue;

                    // Tagging autocomplete
                    if (this.multiple) {
                        if (Array.isArray(this.value)) {
                            this.value.forEach(function(value) {
                                this.tagValues.push(value);

                                if (!this.values[value])
                                    this.tags.push(value);
                                else
                                    this.tags.push(this.values[value]);

                            }.bind(this));
                        }
                        else {
                            this.tagValues.push(this.value);

                            if (!this.values[this.value])
                                this.tags.push(this.value);
                            else
                                this.tags.push(this.values[this.value]);
                        }
                    }
                    // Classic autocomplete
                    else {
                        if (this.values != null) {
                            this.search = this.values[this.value];
                        }
                    }
                }
            },
            toggleList: debounce(function() {
                this.listOpen = this.search && (this.search.length >= this.minLength);
            }, 100)
        },
        props: {
            defaultValue: {
                default: null
            },
            id: {
                default: '',
                type: String
            },
            inputClass: {
                default: '',
                type: String
            },
            labelField: {
                default: null,
                type: String
            },
            minLength: {
                default: 2,
                type: Number
            },
            multiple: {
                default: false,
                type: Boolean
            },
            name: {
                required: true,
                type: String
            },
            searchField: {
                default: null,
                type: String
            },
            src: {
                required: true,
                type: [Object, String]
            },
            valueField: {
                default: null,
                type: String
            }
        },
        template: '#autocomplete-template',
        watch: {
            defaultValue: function() {
                this.getValues();
            }
        }
    });
</script>