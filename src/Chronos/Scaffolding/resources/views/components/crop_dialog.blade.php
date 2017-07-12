<script type="text/x-template" id="crop-template">
    <div class="modal fade" id="crop-dialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="modal-close" data-dismiss="modal"><span class="icon c4icon-cross-2"></span></button>
                    <h4 class="modal-title">{!! trans('chronos.scaffolding::interface.Crop picture') !!}</h4>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <img class="img-responsive" id="crop-image" src="" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{!! trans('chronos.scaffolding::interface.Close') !!}</button>
                    <button type="button" class="btn btn-primary" v-on:click="doUpload">{!! trans('chronos.scaffolding::forms.Crop and save') !!}</button>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
    Vue.component('crop-dialog', {
        created: function() {
            // add listeners
            cropEventHub.$on('show-crop-dialog', this.showCropDialog);
        },
        data: function() {
            return {
                action: null,
                aspectRatio: null,
                cropData: null,
                cropper: null,
                dialog: null,
                file: null,
                fileData: null
            }
        },
        methods: {
            doUpload: function() {
                vm.$emit('show-loader');

                this.$http.post(this.action, {
                    cropData: this.cropData,
                    file: this.fileData
                }).then(function(response) {
                    if (!response.body.redirect) {
                        vm.$emit('hide-loader');

                        return false;
                    }

                    window.location = response.body.redirect;
                }, function(response) {
                    vm.$emit('hide-loader');

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
            showCropDialog: function(file, aspectRatio, action) {
                // set defaults
                this.setFile(file);
                this.setAspectRatio(aspectRatio);
                this.setAction(action);

                var containerWidth, height, width;

                var image = document.getElementById('crop-image');
                var modal = document.getElementById('crop-dialog');

                // initialize image
                var reader = new FileReader();
                reader.readAsDataURL(this.file);
                reader.onload = function(e) {
                    image.setAttribute('src', e.target.result);
                    this.fileData = e.target.result;
                }.bind(this);

                // set up cropper
                var self = this;
                reader.onloadend = function () {
                    // initialize dialog
                    if (!this.dialog) {
                        options = {};
                        options.keyboard = modal.getAttribute('data-keyboard');
                        options.backdrop = modal.getAttribute('data-backdrop');
                        options.duration = modal.getAttribute('data-duration');
                        this.dialog = new Modal(modal, options);
                    }

                    // get image sizes
                    height = image.height;
                    width = image.width;

                    // initialize cropper
                    modal.addEventListener('shown.bs.modal', function() {
                        // get container width
                        var container = document.querySelector('#crop-dialog .modal-content');
                        containerWidth = container.clientWidth - 30;

                        // compute canvas dimension
                        var canvasHeight = containerWidth * height / width;
                        var canvasWidth = containerWidth;

                        self.cropper = new Cropper(image, {
                            aspectRatio: self.aspectRatio,
                            crop: function(e) {
                                self.cropData = e.detail;
                            },
                            minCanvasHeight: canvasHeight,
                            minCanvasWidth: canvasWidth,
                            minContainerHeight: canvasHeight,
                            minContainerWidth: canvasWidth,
                            movable: false,
                            rotatable: false,
                            scalable: false,
                            zoomable: false,
                            viewMode: 3
                        });
                    });

                    // destroy cropper
                    modal.addEventListener('hidden.bs.modal', function() {
                        self.cropper.destroy();
                    });

                    // open dialog
                    this.dialog.open();
                };
            },
            setAction: function(action) {
                this.action = action;
            },
            setAspectRatio: function(aspectRatio) {
                // check for errors
                if (!/[0-9]+:[0-9]+/g.test(aspectRatio)) {
                    console.error('Crop dialog: crop attribute must be either boolean false or a valid aspect ratio.');

                    return false;
                }

                var ratio = aspectRatio.split(':').map(function(num) {
                    return parseInt(num);
                });

                this.aspectRatio = ratio[0] / ratio[1];
            },
            setFile: function(file) {
                this.file = file;
            }
        },
        template: '#crop-template'
    });
</script>