<script type="text/x-template" id="fieldset-template">
    <div class="fieldset" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + order), minimized: isMinimized }">
        <div class="reorder" v-on:mousedown="dragStart" v-on:mouseout="dragEnd"><div class="drag"></div><span v-html="name" v-if="name" v-on:click="focusNameField('fieldsets.' + order + '.name')"></span><em v-if="!name" v-on:click="focusNameField('fieldsets.' + order + '.name')">{!! trans('chronos.content::forms.Unnamed fieldset') !!}</em></div>
        <a v-bind:class="{minimize: !isMinimized, maximize: isMinimized}" v-on:click="toggleMinimize"></a>
        <a class="delete" v-on:click="deleteFieldset"></a>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + order + '.name') }">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + order + '.name'">{!! trans('chronos.content::forms.Fieldset name') !!}</label>
                    <input class="form-control" v-bind:id="'fieldsets.' + order + '.name'" v-bind:name="'fieldsets[' + order + '][name]'" v-on:blur="updateMachineName" v-model="name" type="text" />
                    <span class="help-block" v-html="store.formErrors['fieldsets'][order]['name'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + order + '.name')"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + order + '.fieldset_machine') }" v-show="(machineName != '' && !machineNameChanged) || machineNameChanged || Object.hasKey(store.formErrors, 'fieldsets.' + order + '.fieldset_machine')">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + order + '.fieldset_machine'">{!! trans('chronos.content::forms.Machine name') !!}</label>
                    <div class="machine-name" v-if="!machineNameChanged && !Object.hasKey(store.formErrors, 'fieldsets.' + order + '.fieldset_machine')"><strong v-html="machineName"></strong><a v-on:click="changeMachineName('fieldsets.' + order + '.fieldset_machine')">change</a></div>
                    <input class="form-control" v-bind:id="'fieldsets.' + order + '.fieldset_machine'" v-bind:name="'fieldsets[' + order + '][fieldset_machine]'" v-on:keyup="changeMachineName('fieldsets.' + order + '.fieldset_machine')" v-show="machineNameChanged || Object.hasKey(store.formErrors, 'fieldsets.' + order + '.fieldset_machine')" type="text" v-model="machineName" />
                    <span class="help-block" v-html="store.formErrors['fieldsets'][order]['fieldset_machine'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + order + '.fieldset_machine')"></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" v-bind:for="'fieldsets.' + order + '.description'">{!! trans('chronos.content::forms.Description') !!}</label>
            <textarea class="form-control" v-bind:id="'fieldsets.' + order + '.description'" v-bind:name="'fieldsets[' + order + '][description]'" v-model="description" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label class="control-label" v-bind:for="'fieldsets.' + order + '.repeatable'">{!! trans('chronos.content::forms.Repeatable?') !!}</label>
            <div class="checkbox">
                <label>
                    <input v-bind:id="'fieldsets.' + order + '.repeatable'" v-bind:name="'fieldsets[' + order + '][repeatable]'" v-model="repeatable" type="checkbox" />
                    {!! trans('chronos.content::forms.Yes') !!}
                </label>
            </div>
        </div>

        <label class="control-label">{!! trans('chronos.content::forms.Fields') !!}</label>
        <div class="field-list">
            <field v-for="field in fields" v-bind:key="field.key" v-bind:order="field.order" v-bind:fieldset="field.fieldset" v-bind:field-data="field"></field>
        </div>

        <input v-bind:id="'fieldsets.' + order + '.id'" v-bind:name="'fieldsets[' + order + '][id]'" v-model="id" v-if="id" type="hidden" />

        <a class="btn btn-action add-field" v-on:click="addField">{!! trans('chronos.content::forms.Add field') !!}</a>

        <div class="modal fade" v-bind:id="'delete-fieldset-dialog-' + id" tabindex="-1" role="dialog" v-if="id">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-danger">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete fieldset') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.content::interface.WARNING! All content of belonging to this fieldset will be deleted as well. This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                        <button class="btn btn-danger" type="button" v-on:click="deleteFieldset(true)">{!! trans('chronos.content::interface.Delete') !!}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>



<script type="text/x-template" id="field-template">
    <div class="field" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order), minimized: isMinimized }">
        <div class="reorder" v-on:mousedown="dragStart" v-on:mouseout="dragEnd"><div class="drag"></div><span v-html="name" v-if="name" v-on:click="focusNameField('fieldsets.' + fieldset.order + '.fields.' + order + '.name')"></span><em v-if="!name" v-on:click="focusNameField('fieldsets.' + fieldset.order + '.fields.' + order + '.name')">{!! trans('chronos.content::forms.Unnamed field') !!}</em></div>
        <a v-bind:class="{minimize: !isMinimized, maximize: isMinimized}" v-on:click="toggleMinimize"></a>
        <a class="delete" v-on:click="deleteField"></a>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.name') }">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.name'">{!! trans('chronos.content::forms.Field name') !!}</label>
                    <input class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.name'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][name]'" type="text" v-model="name" v-on:blur="updateMachineName" />
                    <span class="help-block" v-html="store.formErrors['fieldsets'][fieldset.order]['fields'][order]['name'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.name')"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine') }" v-show="(machineName != '' && !machineNameChanged) || machineNameChanged || Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine'">{!! trans('chronos.content::forms.Machine name') !!}</label>
                    <div class="machine-name" v-if="!machineNameChanged && !Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')"><strong v-html="machineName"></strong><a v-on:click="changeMachineName('fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')">change</a></div>
                    <input class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][field_machine]'" v-on:keyup="changeMachineName('fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')" v-show="machineNameChanged || Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')" type="text" v-model="machineName" />
                    <span class="help-block" v-html="store.formErrors['fieldsets'][fieldset.order]['fields'][order]['field_machine'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.field_machine')"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.type') }">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.type'">{!! trans('chronos.content::forms.Type') !!}</label>
                    <select class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.type'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][type]'" v-model="type">
                        <option v-for="type in fieldTypes" v-bind:value="type" v-html="type"></option>
                    </select>
                    <span class="help-block" v-html="store.formErrors['fieldsets'][fieldset.order]['fields'][order]['type'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.type')"></span>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.widget') }">
                    <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.widget'">{!! trans('chronos.content::forms.Widget') !!}</label>
                    <select class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.widget'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][widget]'" v-bind:disabled="!type" v-model="widget">
                        <option v-for="widget in fieldWidgets" v-bind:value="widget" v-html="widget"></option>
                    </select>
                    <span class="help-block" v-html="store.formErrors['fieldsets'][fieldset.order]['fields'][order]['widget'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.widget')"></span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-6" v-if="['file', 'image'].indexOf(type) == -1">
                <div class="form-group">
                    <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.default'">{!! trans('chronos.content::forms.Default value') !!}</label>
                    <input class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.default'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][default]'" v-model="defaultValue" type="text" />
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.repeatable'">{!! trans('chronos.content::forms.Repeatable?') !!}</label>
                    <div class="checkbox">
                        <label>
                            <input v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.repeatable'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][repeatable]'" v-model="repeatable" type="checkbox" />
                            {!! trans('chronos.content::forms.Yes') !!}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="type == 'image'">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.enable_alt'">{!! trans('chronos.content::forms.Enable "alt" field?') !!}</label>
                    <div class="checkbox">
                        <label>
                            <input v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.enable_alt'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][enable_alt]'" v-model="enableAlt" type="checkbox" />
                            {!! trans('chronos.content::forms.Yes') !!}
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.enable_title'">{!! trans('chronos.content::forms.Enable "title" field?') !!}</label>
                    <div class="checkbox">
                        <label>
                            <input v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.enable_title'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][enable_title]'" v-model="enableTitle" type="checkbox" />
                            {!! trans('chronos.content::forms.Yes') !!}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.entity_type') }" v-if="type == 'entity'">
            <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.entity_type'">{!! trans('chronos.content::forms.Entity type') !!}</label>
            <select class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.entity_type'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][entity_type]'" v-model="entityType">
                <option></option>
                <optgroup label="{!! trans('chronos.content::interface.Content types') !!}" v-if="entityTypes.contentTypes">
                    <option v-for="entity in entityTypes.contentTypes" v-bind:value="entity.model + ':' + entity.id" v-html="entity.name"></option>
                </optgroup>
                <optgroup label="{!! trans('chronos.content::interface.Users') !!}" v-if="entityTypes.userRoles">
                    <option value="\App\Models\User">{!! trans('chronos.content::interface.All users') !!}</option>
                    <option v-for="entity in entityTypes.userRoles" v-bind:value="entity.model + ':' + entity.id" v-html="entity.name"></option>
                </optgroup>
            </select>
            <span class="help-block" v-html="store.formErrors['fieldsets'][fieldset.order]['fields'][order]['entity_type'][0]" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.entity_type')"></span>
        </div>
        <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.values') }" v-if="type == 'list'">
            <label class="control-label label-req" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.values'">{!! trans('chronos.content::forms.Values') !!}</label>
            <textarea class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.values'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][values]'" placeholder="{!! trans('key => value') !!}" rows="2" v-model="values"></textarea>
            <span class="help-block" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.values')">@{{ store.formErrors['fieldsets'][fieldset.order]['fields'][order]['values'][0] }}</span>
            <span class="help-block" v-else>{!! trans('chronos.content::forms.List of possible values, one per line, in "key => value" format.') !!}</span>
        </div>
        <div class="row" v-if="type == 'number'">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group" v-bind:class="{ 'has-error': Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.step') }">
                    <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.step'">{!! trans('chronos.content::forms.Step') !!}</label>
                    <input class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.step'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][step]'" min="0,0001" step="1" type="number" v-model="step" />
                    <span class="help-block" v-if="Object.hasKey(store.formErrors, 'fieldsets.' + fieldset.order + '.fields.' + order + '.step')">@{{ store.formErrors['fieldsets'][fieldset.order]['fields'][order]['step'][0] }}</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.help_text'">{!! trans('chronos.content::forms.Help text') !!}</label>
            <textarea class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.values'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][help_text]'" rows="2" v-model="helpText"></textarea>
            <span class="help-block">{!! trans('chronos.content::forms.Instructions to be presented to the user on the content editing form.') !!}</span>
        </div>
        <div class="form-group" v-if="widget != 'media'">
            <label class="control-label" v-bind:for="'fieldsets.' + fieldset.order + '.fields.' + order + '.rules'">{!! trans('chronos.content::forms.Validation rules') !!}</label>
            <input class="form-control" v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.rules'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][rules]'" type="text" v-model="rules" />
            <span class="help-block">{!! trans('chronos.content::forms.Enter validation rules separated by a "|" (pipe) character.') !!}</span>
        </div>

        <input v-bind:id="'fieldsets.' + fieldset.order + '.fields.' + order + '.id'" v-bind:name="'fieldsets[' + fieldset.order + '][fields][' + order + '][id]'" v-model="id" v-if="id" type="hidden" />

        <div class="modal fade" v-bind:id="'delete-field-dialog-' + id" tabindex="-1" role="dialog" v-if="id">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-danger">
                    <div class="modal-header">
                        <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                        <h4 class="modal-title">{!! trans('chronos.content::interface.Delete field') !!}</h4>
                    </div>
                    <div class="modal-body">
                        <p class="marginT15 text-center"><strong>{!! trans('chronos.content::interface.WARNING! All content of belonging to this field will be deleted as well. This action is irreversible.') !!}</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">{!! trans('chronos.content::interface.Close') !!}</button>
                        <button class="btn btn-danger" type="button" v-on:click="deleteField(true)">{!! trans('chronos.content::interface.Delete') !!}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>



<script>
    var dragFieldEventHub = new Vue();
    var dragFieldsetEventHub = new Vue();
    var dragType = null;
    var editorEventHub = new Vue();

    const fieldsetEditorStore = {
        state: {
            entityTypes: {
                contentTypes: [],
                userRoles: []
            }
        }
    };

    Vue.component('fieldset-editor', {
        components: {
            set: {
                components: {
                    field: {
                        beforeCreate: function() {
                            entityTypes = {
                                contentTypes: [],
                                userRoles: []
                            };
                            if (Object.keys(fieldsetEditorStore.state.entityTypes.contentTypes.length == 0)) {
                                Vue.http.get('/api/content/types').then(function (response) {
                                    entityTypes['contentTypes'] = [];
                                    response.body.data.forEach(function (contentType) {
                                        entityTypes['contentTypes'].push({
                                            id: contentType.id,
                                            model: '\\Chronos\\Content\\Models\\ContentType',
                                            name: contentType.name
                                        });
                                    });

                                    if (entityTypes['userRoles'].length > 0) {
                                        fieldsetEditorStore.state.entityTypes = entityTypes;
                                        this.entityTypes = fieldsetEditorStore.state.entityTypes;
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
                                });
                            }

                            if (Object.keys(fieldsetEditorStore.state.entityTypes.userRoles.length == 0)) {
                                Vue.http.get('/api/users/roles/').then(function (response) {
                                    entityTypes['userRoles'] = [];
                                    response.body.data.forEach(function(role) {
                                        entityTypes['userRoles'].push({
                                            id:  role.id,
                                            model: '\\Chronos\\Scaffolding\\Models\\Role',
                                            name: role.name
                                        });
                                    });

                                    if (entityTypes['contentTypes'].length > 0) {
                                        fieldsetEditorStore.state.entityTypes = entityTypes;
                                        this.entityTypes = fieldsetEditorStore.state.entityTypes;
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
                                });
                            }
                        },
                        computed: {
                            fieldWidgets: function() {
                                if (this.type === null)
                                    return null;
                                else {
                                    if (this.allWidgets[this.type].length == 2)
                                        this.widget = this.allWidgets[this.type][1];

                                    return this.allWidgets[this.type];
                                }
                            }
                        },
                        created: function() {
                            // populate with data
                            if (this.fieldData.id) {
                                this.defaultValue = this.fieldData.default;
                                this.entityType = this.fieldData.entity_id ? this.fieldData.entity_model + ':' + this.fieldData.entity_id : this.fieldData.entity_model;
                                this.enableAlt = this.fieldData.enable_alt;
                                this.enableTitle = this.fieldData.enable_title;
                                this.helpText = this.fieldData.help_text;
                                this.id = this.fieldData.id;
                                this.machineName = this.fieldData.machine_name;
                                this.machineNameChanged = true;
                                this.name = this.fieldData.name;
                                this.repeatable = this.fieldData.repeatable;
                                this.rules = this.fieldData.rules;
                                this.step = this.fieldData.step;
                                this.type = this.fieldData.type;
                                this.values = this.fieldData.values;
                                this.widget = this.fieldData.widget;
                            }
                        },
                        data: function() {
                            return {
                                allWidgets: {
                                    email: ['', 'email'],
                                    entity: ['', 'autocomplete', 'tagging'],
                                    file: ['', 'media'],
                                    image: ['', 'media'],
                                    list: ['', 'autocomplete', 'checkbox', 'select', 'radio', 'tagging'],
                                    number: ['', 'number'],
                                    text: ['', 'text', 'textarea', 'wysiwyg'],
                                    url: ['', 'url']
                                },
                                dragCounter: 0,
                                defaultValue: '',
                                enableAlt: false,
                                enableTitle: false,
                                entityType: '',
                                entityTypes: fieldsetEditorStore.state.entityTypes,
                                fieldTypes: ['', 'email', 'entity', 'file', 'image',  'list', 'number', 'text', 'url'],
                                helpText: '',
                                id: null,
                                isMinimized: false,
                                machineName: '',
                                machineNameChanged: false,
                                name: '',
                                repeatable: false,
                                rules: '',
                                step: 1,
                                store: vueStore.state,
                                type: null,
                                values: '',
                                value: null,
                                widget: null
                            }
                        },
                        methods: {
                            changeMachineName: function(target) {
                                this.machineNameChanged = true;

                                setTimeout(function() {
                                    document.getElementById(target).focus();
                                }, 100);
                            },
                            deleteField: function(confirmed) {
                                if (this.id === null) {
                                    editorEventHub.$emit('delete-field', this.order);
                                } else {
                                    var modal = document.querySelector('#delete-field-dialog-' + this.id);
                                    var dialog = new Modal(modal);

                                    if (confirmed === true) {
                                        editorEventHub.$emit('delete-field', this.order, this.id);
                                        dialog.close();
                                    } else {
                                        dialog.open();
                                    }
                                }
                            },
                            dragDrop: function(event) {
                                if (dragType != 'field')
                                    return;

                                event.stopPropagation();

                                this.dragCounter = 0;
                                dragType = null;

                                var target = event.target.closest('.field');
                                dragFieldEventHub.$emit('reorder-elements', this.fieldset.order, Array.prototype.indexOf.call(target.parentNode.childNodes, target));
                            },
                            dragEnd: function(event) {
                                var target = event.target.parentElement.parentElement;

                                target.removeAttribute('draggable');
                                target.classList.remove('dragged');

                                this.dragCounter = 0;
                                dragType = null;

                                [].forEach.call(document.querySelectorAll('.field'), function(field) {
                                    field.classList.remove('dragover');
                                });
                            },
                            dragEnter: function(event) {
                                if (dragType != 'field')
                                    return;

                                event.preventDefault();

                                this.dragCounter++;

                                if (this.dragCounter === 1)
                                    event.target.classList.add('dragover');
                            },
                            dragLeave: function(event) {
                                this.dragCounter--;

                                if (this.dragCounter === 0)
                                    event.target.classList.remove('dragover');
                            },
                            dragOver: function(event) {
                                event.preventDefault();

                                return false;
                            },
                            dragStart: function(event) {
                                var target = event.target.parentElement.parentElement;

                                if (!target.classList.contains('field'))
                                    return;

                                target.setAttribute('draggable', 'true');
                                target.classList.add('dragged');

                                dragType = 'field';

                                dragFieldEventHub.$emit('set-drag-element', Array.prototype.indexOf.call(target.parentNode.childNodes, target));
                            },
                            focusNameField: function(target) {
                                document.getElementById(target).focus();
                            },
                            toggleMinimize: function() {
                                this.isMinimized = !this.isMinimized;
                            },
                            updateMachineName: function() {
                                if (this.machineNameChanged)
                                    return;

                                this.machineName = this.name.slugify({ delimiter: '_' });
                            }
                        },
                        mounted: function() {
                            this.$el.addEventListener('dragend', this.dragEnd, false);
                            this.$el.addEventListener('dragenter', this.dragEnter, false);
                            this.$el.addEventListener('dragleave', this.dragLeave, false);
                            this.$el.addEventListener('dragover', this.dragOver, false);
                            this.$el.addEventListener('dragstart', this.dragStart, false);
                            this.$el.addEventListener('drop', this.dragDrop, false);
                        },
                        props: {
                            fieldData: {
                                type: Object
                            },
                            fieldset: {
                                type: Object
                            },
                            order: {
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
                        this.machineName = this.fieldsetData.machine_name;
                        this.machineNameChanged = true;
                        this.name = this.fieldsetData.name;
                        this.repeatable = this.fieldsetData.repeatable;
                    }

                    // add listeners
                    dragFieldEventHub.$on('reorder-elements', this.reorderElements);
                    dragFieldEventHub.$on('set-drag-element', this.setDragElement);

                    editorEventHub.$off('delete-field');
                    editorEventHub.$on('delete-field', this.deleteField);
                },
                data: function() {
                    return {
                        description: '',
                        dragCounter: 0,
                        id: null,
                        isMinimized: false,
                        fields: [],
                        machineName: '',
                        machineNameChanged: false,
                        name: '',
                        store: vueStore.state,
                        repeatable: false
                    }
                },
                methods: {
                    addField: function() {
                        var length = this.fields.length;
                        this.fields.push({
                            fieldset: this,
                            key: length + 1,
                            order: length + 1
                        });
                    },
                    changeMachineName: function(target) {
                        this.machineNameChanged = true;

                        setTimeout(function() {
                            document.getElementById(target).focus();
                        }, 100);
                    },
                    deleteField: function(order, id) {
                        for (var key in this.fields) {
                            if (this.fields[key].order == order) {
                                if (Number.isInteger(id)) {
                                    vm.$emit('show-loader');
                                    this.$http.delete('/api/content/types/field/' + id).then(function(response) {
                                        if (response.body.alerts) {
                                            response.body.alerts.forEach(function(alert) {
                                                vm.$emit('add-alert', alert);
                                            }.bind(this));
                                        }

                                        vm.$emit('hide-loader');
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

                                        vm.$emit('hide-loader');
                                    });
                                }

                                this.fields.splice(key, 1);
                            }
                        }
                    },
                    deleteFieldset: function(confirmed) {
                        if (this.id === null)
                            editorEventHub.$emit('delete-fieldset', this.order);
                        else {
                            var modal = document.querySelector('#delete-fieldset-dialog-' + this.id);
                            var dialog = new Modal(modal);

                            if (confirmed === true) {
                                editorEventHub.$emit('delete-fieldset', this.order, this.id);
                                dialog.close();
                            } else {
                                dialog.open();
                            }
                        }
                    },
                    dragDrop: function(event) {
                        if (dragType != 'fieldset')
                            return;

                        event.stopPropagation();

                        this.dragCounter = 0;
                        dragType = null;

                        var target = event.target.closest('.fieldset');
                        dragFieldsetEventHub.$emit('reorder-elements', Array.prototype.indexOf.call(target.parentNode.childNodes, target));
                    },
                    dragEnd: function(event) {
                        var target = event.target.parentElement.parentElement;

                        target.removeAttribute('draggable');
                        target.classList.remove('dragged');

                        this.dragCounter = 0;
                        dragType = null;

                        [].forEach.call(document.querySelectorAll('.fieldset'), function(fieldset) {
                            fieldset.classList.remove('dragover');
                        });
                    },
                    dragEnter: function(event) {
                        if (dragType != 'fieldset')
                            return;

                        event.preventDefault();

                        this.dragCounter++;

                        if (this.dragCounter === 1)
                            event.target.classList.add('dragover');
                    },
                    dragLeave: function(event) {
                        this.dragCounter--;

                        if (this.dragCounter === 0)
                            event.target.classList.remove('dragover');
                    },
                    dragOver: function(event) {
                        event.preventDefault();

                        return false;
                    },
                    dragStart: function(event) {
                        var target = event.target.parentElement.parentElement;

                        if (!target.classList.contains('fieldset'))
                            return;

                        target.setAttribute('draggable', 'true');
                        target.classList.add('dragged');

                        dragType = 'fieldset';

                        dragFieldsetEventHub.$emit('set-drag-element', Array.prototype.indexOf.call(target.parentNode.childNodes, target));
                    },
                    focusNameField: function(target) {
                        document.getElementById(target).focus();
                    },
                    reorderElements: function(order, dropElement) {
                        if (this.order !== order)
                            return;

                        this.fields.move(this.dragElement, dropElement);

                        this.fields[dropElement].order = dropElement + 1;
                        this.fields[this.dragElement].order = this.dragElement + 1;

                        this.dragElement = null;
                    },
                    setDragElement: function(dragElement) {
                        this.dragElement = dragElement;
                    },
                    toggleMinimize: function() {
                        this.isMinimized = !this.isMinimized;
                    },
                    updateMachineName: function() {
                        if (this.machineNameChanged)
                            return;

                        this.machineName = this.name.slugify({ delimiter: '_' });
                    }
                },
                mounted: function() {
                    this.$el.addEventListener('dragenter', this.dragEnter, false);
                    this.$el.addEventListener('dragleave', this.dragLeave, false);
                    this.$el.addEventListener('dragover', this.dragOver, false);
                    this.$el.addEventListener('drop', this.dragDrop, false);
                },
                props: {
                    fieldsetData: {
                        type: Object
                    },
                    formErrors: {
                        type: Array
                    },
                    order: {
                        type: Number
                    }
                },
                template: '#fieldset-template'
            }
        },
        created: function() {
            // populate fieldsets
            this.getParent();

            // add listeners
            dragFieldsetEventHub.$on('reorder-elements', this.reorderElements);
            dragFieldsetEventHub.$on('set-drag-element', this.setDragElement);

            editorEventHub.$off('delete-fieldset');
            editorEventHub.$on('delete-fieldset', this.deleteFieldset);
            editorEventHub.$on('set-fieldsets', this.setFieldsets);
        },
        data: function() {
            return {
                dataLoader: false,
                dragElement: null,
                fieldsets: []
            }
        },
        methods: {
            addFieldset: function() {
                var length = this.fieldsets.length;
                this.fieldsets.push({
                    key: length + 1,
                    order: length + 1
                });
            },
            deleteFieldset: function(order, id) {
                for (var key in this.fieldsets) {
                    if (this.fieldsets[key].order == order) {
                        if (Number.isInteger(id)) {
                            vm.$emit('show-loader');
                            this.$http.delete('/api/content/types/fieldset/' + id).then(function(response) {
                                if (response.body.alerts) {
                                    response.body.alerts.forEach(function(alert) {
                                        vm.$emit('add-alert', alert);
                                    }.bind(this));
                                }

                                vm.$emit('hide-loader');
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

                                vm.$emit('hide-loader');
                            });
                        }

                        this.fieldsets.splice(key, 1);
                    }
                }
            },
            getParent: function() {
                this.dataLoader = true;

                $url = this.parentType == 'ContentType' ? '/api/content/types/' + this.parentId + '?load=fieldsets' : '/api/content/manage/' + this.typeId + '/' + this.parentId + '?load=fieldsets';
                this.$http.get($url).then(function(response) {
                    var fieldsets = [];

                    response.body.fieldsets.forEach(function(fieldset, fieldset_key) {
                        fieldset.key = fieldset_key + 1;
                        fieldset.order = fieldset_key + 1;

                        var fields = [];
                        fieldset.fields.forEach(function(field, field_key) {
                            field.fieldset = fieldset;
                            field.key = field_key + 1;
                            field.order = field_key + 1;
                            fields.push(field);
                        });
                        fieldset.fields = fields;
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
            },
            reorderElements: function(dropElement) {
                this.fieldsets.move(this.dragElement, dropElement);

                this.fieldsets[dropElement].order = dropElement + 1;
                this.fieldsets[this.dragElement].order = this.dragElement + 1;

                this.dragElement = null;
            },
            saveFieldsets: function(event) {
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
                    vueStore.updateFormErrors([]);

                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }

                    this.getParent();

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
            setDragElement: function(dragElement) {
                this.dragElement = dragElement;
            }
        },
        props: {
            parentId: {
                required: true,
                type: Number
            },
            parentType: {
                required: true,
                type: String
            },
            typeId: {
                required: true,
                type: Number
            }
        }
    });
</script>