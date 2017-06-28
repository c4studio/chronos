<script type="text/x-template" id="fieldset-template">
    <div class="fieldset" v-bind:class="{ minimized: isMinimized }">
        <div class="fieldset-name" v-html="name"></div>
        <a v-bind:class="{minimize: !isMinimized, maximize: isMinimized}" v-on:click="toggleMinimize"></a>
        <div class="fieldset-description" v-html="description" v-if="description != ''"></div>

        <div class="fieldset-repetition" v-for="(repetition, key) in repetitions">
            <div class="field-list">
                <field v-for="field in fields" v-bind:fieldset="field.fieldset" v-bind:fieldset-key="key" v-bind:field-data="field"></field>
            </div>
            <div class="repetition-meta" v-if="repeatable">
                <span class="repetition-key" v-html="key + 1"></span><a class="delete-repetition" v-on:click="deleteRepetition(key)" v-if="key > 0">{!! trans('chronos.content::forms.Remove repetition') !!}</a>
            </div>
        </div>
        <div class="fieldset-footer" v-if="repeatable">
            <a class="btn btn-action add-repetition" v-on:click="repeatFieldset">{!! trans('chronos.content::forms.Repeat fieldset') !!}</a>
        </div>
    </div>
</script>



<script type="text/x-template" id="field-template">
    <div class="field">
        <div class="form-group">
            <label class="control-label" for="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.0'" v-html="name"></label>
            <span class="help-block" v-if="helpText">@{{ helpText }}</span>
            <div class="field-repetition" v-for="(repetition, key) in repetitions">
                <!-- Autocomplete list -->
                <div class="field-autocomplete" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'autocomplete' && type == 'list'">
                    <autocomplete input-class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:src="listValuesParsed" v-bind:default-value="values[key]"></autocomplete>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Autocomplete entity -->
                <div class="field-autocomplete" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'autocomplete' && type == 'entity'">
                    <autocomplete input-class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:src="entityEndpoint" value-field="id" v-bind:label-field="labelField" search-field="filters[search]" v-bind:default-value="values[key]"></autocomplete>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Checkbox list -->
                <div class="field-checkbox" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'checkbox' && type == 'list'">
                    <div class="checkbox" v-for="(listLabel, listValue) in listValuesParsed">
                        <input v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + '][]'" type="checkbox" v-bind:value="listValue" v-model="values[key]" /> <span v-html="listLabel"></span>
                    </div>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Email -->
                <div class="field-email" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'email'">
                    <input class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" type="email" v-model="values[key]" />
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Media file -->
                <div class="field-media" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'media' && type == 'file'">
                    <media-file v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:default-value="values[key]"></media-file>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Media image -->
                <div class="field-media" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'media' && type == 'image'">
                    <media-file v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:default-value="values[key]" v-bind:enableAlt="true" v-bind:enableTitle="true" v-bind:imagesOnly="true"></media-file>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Number -->
                <div class="field-number" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'number'">
                    <input class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" type="number" v-bind:step="step" v-model="values[key]" />
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Radio list -->
                <div class="field-radio" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'radio' && type == 'list'">
                    <div class="radio" v-for="(listLabel, listValue) in listValuesParsed">
                        <input v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" type="radio" v-bind:value="listValue" v-model="values[key]" /> <span v-html="listLabel"></span>
                    </div>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Select list -->
                <div class="field-select" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'select' && type == 'list'">
                    <select class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-model="values[key]">
                        <option v-for="(listLabel, listValue) in listValuesParsed" v-bind:value="listValue" v-html="listLabel"></option>
                    </select>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Tagging list -->
                <div class="field-autocomplete" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'tagging' && type == 'list'">
                    <autocomplete input-class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:src="listValuesParsed" v-bind:default-value="values[key]" v-bind:multiple="true"></autocomplete>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Tagging entity -->
                <div class="field-autocomplete" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'tagging' && type == 'entity'">
                    <autocomplete input-class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:src="entityEndpoint" value-field="id" v-bind:label-field="labelField" search-field="filters[search]" v-bind:default-value="values[key]" v-bind:multiple="true"></autocomplete>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Text -->
                <div class="field-text" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'text'">
                    <input class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" type="text" v-model="values[key]" />
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Textarea -->
                <div class="field-textarea" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'textarea'">
                    <textarea class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" rows="5" v-model="values[key]"></textarea>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- URL -->
                <div class="field-url" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'url'">
                    <input class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" type="url" v-model="values[key]" />
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <!-- Wysiwyg -->
                <div class="field-wysiwyg" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key) }" v-if="widget == 'wysiwyg'">
                    <wysiwyg input-class="form-control" v-bind:id="'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key" v-bind:name="'fields[' + fieldset.id + '][' + fieldsetKey + '][' + id + '][' + key + ']'" v-bind:default-value="values[key]"></wysiwyg>
                    <span class="help-block" v-html="store.formErrors['fields'][fieldset.id][fieldsetKey][id][key][0]" v-if="Object.hasKey(store.formErrors, 'fields.' + fieldset.id + '.' + fieldsetKey + '.' + id + '.' + key)"></span>
                </div>
                <a class="delete-repetition" v-on:click="deleteRepetition(key)" v-if="repeatable && key != repetitions.length-1"></a>
                <a class="add-repetition" v-on:click="repeatField" v-if="repeatable && key == repetitions.length-1"></a>
            </div>
        </div>
    </div>
</script>



<script>
    Vue.component('content-editor', {
        components: {
            set: {
                components: {
                    field: {
                        created: function() {
                            // populate with data
                            if (this.fieldData.id) {
                                this.enableAlt = this.fieldData.enable_alt;
                                this.enableTitle = this.fieldData.enable_title;
                                this.helpText = this.fieldData.help_text;
                                this.id = this.fieldData.id;
                                this.name = this.fieldData.name;
                                this.repeatable = this.fieldData.repeatable;
                                this.repetitions = this.fieldData.repetitions;
                                this.rules = this.fieldData.rules;
                                this.step = this.fieldData.step;
                                this.type = this.fieldData.type;
                                this.listValues = this.fieldData.values;
                                this.listValuesParsed = this.fieldData.valuesParsed;
                                this.widget = this.fieldData.widget;

                                if (this.fieldData.entity_endpoints != null) {
                                    switch (this.fieldData.entity_model) {
                                        case '\\Chronos\\Scaffolding\\Models\\Role':
                                            this.entityEndpoint = this.fieldData.entity_endpoints.users;
                                            this.labelField = 'name';

                                            break;
                                        case '\\App\\Models\\User':
                                            this.entityEndpoint = this.fieldData.entity_endpoints.index;
                                            this.labelField = 'name';

                                            break;
                                        default:
                                            this.entityEndpoint = this.fieldData.entity_endpoints.index;
                                    }
                                }
                                else
                                    this.entityEndpoint = null;

                                if (this.fieldData.value && this.fieldData.value.length > 0)
                                    this.values = this.fieldData.value[this.fieldsetKey];
                                else if (this.fieldData.default)
                                    this.values = [this.fieldData.default];
                                else
                                    this.values = [null];
                            }
                        },
                        data: function() {
                            return {
                                entityEndpoint: null,
                                enableAlt: false,
                                enableTitle: false,
                                helpText: '',
                                id: null,
                                labelField: 'title',
                                listValues: [],
                                listValuesParsed: [],
                                name: '',
                                repeatable: false,
                                repetitions: [],
                                rules: '',
                                step: 1,
                                store: vueStore.state,
                                type: '',
                                values: [],
                                widget: ''
                            }
                        },
                        methods: {
                            deleteRepetition: function(key) {
                                // delete repetition
                                this.repetitions.splice(key, 1);

                                // delete associated errors
                                if (Object.hasKey(this.store.formErrors, 'fields.' + this.fieldset.id + '.' + this.fieldsetKey + '.' + this.id + '.' + key))
                                    delete this.store.formErrors['fields'][this.fieldset.id][this.fieldsetKey][this.id][key];

                                // restore values order
                                this.values.splice(key, 1);
                            },
                            repeatField: function() {
                                var field = Object.clone(this);
                                delete field.repetitions;
                                this.repetitions.push(field);
                                if (this.widget != 'checkbox')
                                    this.values.push(this.fieldData.default);
                                else
                                    this.values.push([this.fieldData.default]);
                            },
                        },
                        props: {
                            fieldData: {
                                type: Object
                            },
                            fieldset: {
                                type: Object
                            },
                            fieldsetKey: {
                                type: Number
                            }
                        },
                        template: '#field-template'
                    }
                },
                created: function() {
                    // populate with data
                    if (this.fieldsetData.id) {
                        this.description = this.fieldsetData.description;
                        this.fields = this.fieldsetData.fields;
                        this.id = this.fieldsetData.id;
                        this.name = this.fieldsetData.name;
                        this.repeatable = this.fieldsetData.repeatable;
                        this.repetitions = this.fieldsetData.repetitions;
                    }
                },
                data: function() {
                    return {
                        description: '',
                        fields: null,
                        id: null,
                        isMinimized: false,
                        name: '',
                        repeatable: false,
                        repetitions: [],
                        store: vueStore.state
                    }
                },
                methods: {
                    deleteRepetition: function(key) {
                        this.repetitions.splice(key, 1);
                    },
                    repeatFieldset: function() {
                        var fieldset = Object.clone(this);
                        delete fieldset.repetitions;

                        fieldset.fields.forEach(function(field) {
                            field.value = null;
                        });


                        this.repetitions.push(fieldset);
                    },
                    toggleMinimize: function() {
                        this.isMinimized = !this.isMinimized;
                    }
                },
                props: {
                    fieldsetData: {
                        type: Object
                    }
                },
                template: '#fieldset-template'
            }
        },
        computed: {
            typeHierarchyFlattened: function() {
                var hierarchy = [];
                var key = 0;

                return this.flattenHierarchy(hierarchy, this.typeHierarchy, 0);
            }
        },
        created: function() {
            // get languages
            if (this.contentId == null) {
                this.getLanguages();
            }

            // populate data
            this.getData();
        },
        data: function() {
            return {
                dataLoader: false,
                fieldsets: [],
                languages: [],
                languageSelected: '{{ Config::get('app.locale') }}',
                lockDelete: 0,
                order: 0,
                parentId: 0,
                slug: '',
                slugChanged: false,
                status: 1,
                store: vueStore.state,
                title: '',
                typeHierarchy: null
            }
        },
        methods: {
            changeSlug: function(target) {
                this.slugChanged = true;

                setTimeout(function() {
                    document.getElementById(target).focus();
                }, 100);
            },
            flattenHierarchy: function(hierarchy, items, depth) {
                if (items) {
                    items.forEach(function(item) {
                        if (this.contentId == null || this.contentId != item.id) {
                            item.depth = depth;
                            hierarchy.push(item);

                            if (item.children)
                                hierarchy = this.flattenHierarchy(hierarchy, item.children, depth + 1);
                        }
                    }.bind(this));
                }

                return hierarchy;
            },
            getData: function() {
                this.dataLoader = true;

                // add
                if (this.contentId == null) {
                    this.$http.get('/api/content/types/' + this.typeId + '?load=fieldsets').then(function(response) {
                        var fieldsets = [];
                        response.body.fieldsets.forEach(function(fieldset) {
                            var fields = [];
                            fieldset.fields.forEach(function(field) {
                                field.fieldset = fieldset;

                                var field_repetitions = [];
                                field_repetitions.push(Object.clone(field));
                                field.repetitions = field_repetitions;

                                fields.push(field);
                            });
                            fieldset.fields = fields;

                            var fieldset_repetitions = [];
                            fieldset_repetitions.push(Object.clone(fieldset));
                            fieldset.repetitions = fieldset_repetitions;

                            fieldsets.push(fieldset);
                        });
                        this.fieldsets = fieldsets;

                        this.dataLoader = false;
                    }, function(response) {
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

                        this.dataLoader = false;
                    });

                }
                // edit
                else {
                    this.$http.get('/api/content/manage/' + this.typeId + '/' + this.contentId + '?load=allFieldsets').then(function(response) {
                        var content = response.body;
                        if (content) {
                            this.parentId = content.parent_id ? content.parent_id : 0;
                            this.slug = content.slug;
                            this.title = content.title;
                            this.slugChanged = true;
                            this.order = content.order;
                            this.status = content.status;
                            this.lockDelete = content.lock_delete;

                            if (content.allFieldsets)
                                content.allFieldsets.forEach(function(fieldset) {
                                    fieldset.fields.forEach(function(field) {
                                        field.fieldset = fieldset;
                                    });
                                });

                            this.fieldsets = content.allFieldsets;
                        }

                        this.dataLoader = false;
                    }, function(response) {
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

                        this.dataLoader = false;
                    });
                }

                // get type hierarchy
                var params = {
                    hierarchy: 1,
                    perPage: 0,
                    withInactive: 1
                };

                this.$http.get('/api/content/manage/' + this.typeId, {params: params}).then(function(response) {
                    this.typeHierarchy = response.body.data;
                }, function(response) {
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
                });

            },
            getLanguages: function() {
                this.$http.get('/api/settings/languages').then(function(response) {
                    this.languages = response.body.data;
                }, function(response) {
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
                });
            },
            saveContent: function(event, update) {
                vm.$emit('show-loader');

                var form = event.target;

                var action = form.getAttribute('action');
                var data = new FormData(form);
                var method = form.getAttribute('method').toUpperCase();

                this.$http({
                    body: data,
                    method: method,
                    url: action
                }).then(function(response) {
                    // redirect to edit page if new content has been created
                    if (response.body.content && !update) {
                        sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));
                        window.location = response.body.content.admin_urls.edit;
                    }
                    else {
                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }
                    }

                    vueStore.updateFormErrors(response.body);

                    vm.$emit('hide-loader');
                }, function(response) {
                    vueStore.updateFormErrors(response.body);

                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }

                    vm.$emit('hide-loader');
                });
            },
            updateSlug: function() {
                if (this.slugChanged)
                    return;

                this.slug = this.title.slugify();
            }
        },
        props: {
            contentId: {
                default: null,
                type: Number
            },
            typeId: {
                default: null,
                type: Number
            }
        }
    });
</script>