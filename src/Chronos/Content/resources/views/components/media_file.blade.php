<script type="text/x-template" id="media-file-template">
    <div class="media-file">
        <div class="clearfix" v-if="src">
            <div class="pull-left marginR15">
                <img v-bind:src="src" alt="" v-if="isImage" />
                <span class="icon c4icon-5x c4icon-file-2" v-else></span>
            </div>
            <div class="pull-left" v-bind:class="{ paddingT30: !isImage }">
                <strong class="display-block" v-html="basename"></strong>
                <a class="marginR15" v-on:click="openMediaDialog">{!! trans('chronos.content::forms.Change file') !!}</a>
                <a class="text-danger" v-on:click="removeFile">{!! trans('chronos.content::forms.Remove file') !!}</a>
                <div class="form-group marginT15" v-if="isImage && enableAlt">
                    <label class="control-label">{!! trans('chronos.content::forms.Alt tag') !!}<input class="form-control" v-bind:name="name + '[alt]'" type="text" v-model="alt" /></label>
                </div>
                <div class="form-group" v-if="isImage && enableTitle">
                    <label class="control-label">{!! trans('chronos.content::forms.Title tag') !!}<input class="form-control" v-bind:name="name + '[title]'" type="text" v-model="title" /></label>
                </div>
            </div>
        </div>

        <a data-toggle="modal" data-target="#select-file-dialog" v-on:click="openMediaDialog(true)" v-if="!src">{!! trans('chronos.content::forms.Select file') !!}</a>

        <input v-bind:name="name + '[media_id]'" type="hidden" v-model="value" />
    </div>
</script>


<script>
    Vue.component('media-file', {
        created: function() {
            if (this.defaultValue) {
                if (this.defaultValue.media_id) {
                    this.value = this.defaultValue.media_id;
                    this.getData();
                }

                if (this.defaultValue.alt)
                    this.alt = this.defaultValue.alt;
                if (this.defaultValue.title)
                    this.title = this.defaultValue.title;
            }

            // add listeners
            mediaEventHub.$on('select-from-media-dialog', this.selectFileFromMediaDialog);
        },
        data: function() {
            return {
                alt: '',
                basename: '',
                isImage: false,
                src: '',
                title: '',
                value: null
            }
        },
        methods: {
            getData: function() {
                this.$http.get('/api/content/media/' + this.value).then(function(response) {
                    this.basename = response.body.basename;
                    this.isImage = response.body.is_image;
                    this.src = response.body.file;
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
            openMediaDialog: function() {
                mediaEventHub.$emit('open-media-dialog', this.imagesOnly, this.name);
            },
            removeFile: function() {
                this.alt = '';
                this.basename = '';
                this.src = '';
                this.title = '';
                this.value = null;
            },
            selectFileFromMediaDialog: function(file, name) {
                if (this.name != name)
                    return;

                this.alt = '';
                this.basename = file.basename;
                this.isImage = file.is_image;
                this.src = file.file;
                this.title = '';
                this.value = file.id;
            }
        },
        props: {
            defaultValue: {
                default: null
            },
            enableAlt: {
                default: false,
                type: Boolean
            },
            enableTitle: {
                default: false,
                type: Boolean
            },
            imagesOnly: {
                default: false,
                type: Boolean
            },
            name: {
                required: true,
                type: String
            }
        },
        template: '#media-file-template',
        watch: {
            defaultValue: function() {
                if (this.defaultValue.media_id && !this.src) {
                    this.value = this.defaultValue.media_id;
                    this.getData();
                }

                if (this.defaultValue.alt)
                    this.alt = this.defaultValue.alt;
                if (this.defaultValue.title)
                    this.title = this.defaultValue.title;
            }
        }
    });
</script>