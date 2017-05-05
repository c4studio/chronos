<script type="text/x-template" id="ajax-upload-template">

    <div v-on:click.stop="startUpload()">
        <slot></slot>
        <input class="hidden" name="ajax-upload" type="file" v-on:change="validateUpload()" v-bind:multiple="multiple" />
    </div>

</script>

<script>
    var cropEventHub = new Vue();
    var uploadEventHub = new Vue();

    Vue.component('ajax-upload', {
        created: function() {
            // check for errors
            if (this.crop !== false && !/[0-9]+:[0-9]+/g.test(this.crop))
                console.error('Ajax upload: crop attribute must be either boolean false or a valid aspect ratio.');
            if (this.crop !== false && this.multiple === true)
                console.error('Ajax upload: crop & multiple attributes should not be used together.');
        },
        data: function() {
            return {
                fileData: [],
                fileNames: [],
                files: []
            }
        },
        methods: {
            convertAndUpload: function() {
                vm.$emit('show-loader');

                this.fileData = [];
                this.fileNames = [];

                var done = Array(this.files.length).fill(false);

                // convert files to base64
                [].forEach.call(this.files, function(file, key) {
                    this.fileNames.push(file.name);

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function (e) {
                        this.fileData.push(e.target.result);
                    }.bind(this);
                    reader.onloadend = function () {
                        done[key] = true;

                        // check if each item is done, then do upload. We do this by
                        // 1. checking if all values are the same
                        // 2. checking if one value is true
                        if (!!done.reduce(function(a, b){ return (a === b) ? a : NaN; }) && done[0] === true) {
                            this.doUpload();
                        }
                    }.bind(this);
                }.bind(this));
            },
            doUpload: function() {
                this.$http.post(this.action, {
                    files: this.fileData,
                    fileNames: this.fileNames
                }).then(function(response) {
                        vm.$emit('hide-loader');

                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }

                        uploadEventHub.$emit('upload-finished');
                }, function(response) {
                    if (response.body.alerts) {
                        response.body.alerts.forEach(function(alert) {
                            vm.$emit('add-alert', alert);
                        }.bind(this));
                    }
                    else {
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
                    }

                    vm.$emit('hide-loader');
                });
            },
            showCropDialog: function() {
                cropEventHub.$emit('show-crop-dialog', this.files[0], this.crop, this.action);
            },
            startUpload: function() {
                document.getElementsByName('ajax-upload')[0].click();
            },
            validateUpload: function() {
                // get files
                this.files = document.getElementsByName('ajax-upload')[0].files;

                // set types
                var types = this.allowedTypes;
                if (typeof types === 'undefined' && this.crop !== false)
                    types = ['gif', 'jpg', 'jpeg', 'png'];

                // validate file type
                if (typeof types !== 'undefined') {
                    for (var j = 0; j < this.files.length; j++) {
                        if (types.indexOf(this.files[j].type.split('/')[1]) == -1) {
                            vm.$emit('add-alert', {
                                type: 'error',
                                title: "{!! trans('chronos.scaffolding::alerts.Upload error.') !!}",
                                message: "{!! trans('chronos.scaffolding::alerts.Uploaded file must be of type image.') !!}"
                            });

                            return false;
                        }
                    }
                }

                // process to next action
                if (this.crop !== false)
                    this.showCropDialog();
                else
                    this.convertAndUpload();
            }
        },
        props: {
            action: {
                required: true,
                type: String
            },
            allowedTypes: Array,
            crop: {
                default: false,
                type: [Boolean, String]
            },
            maxSize: Number,
            multiple: {
                default: false,
                type: Boolean
            }
        },
        template: '#ajax-upload-template'
    });
</script>