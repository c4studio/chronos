<script>
    Vue.component('image-style-editor', {
        created: function() {
            // populate data
            this.getData();
        },
        data: function() {
            return {
                anchor: null,
                crop_height: null,
                crop_type: null,
                crop_width: null,
                greyscale: false,
                height: null,
                name: '',
                rotate: null,
                store: vueStore.state,
                upsizing: false,
                width: null
            }
        },
        methods: {
            getData: function() {
                if (!this.styleId)
                    return;

                this.$http.get('/api/settings/image-styles/' + this.styleId).then(function(response) {
                    var content = response.body;

                    if (content) {
                        this.anchor = content.anchor_h + '-' + content.anchor_v;
                        this.crop_height = content.crop_height;
                        this.crop_type = content.crop_type;
                        this.crop_width = content.crop_width;
                        this.greyscale = content.greyscale;
                        this.height = content.height;
                        this.name = content.name;
                        this.rotate = content.rotate;
                        this.upsizing = content.upsizing;
                        this.width = content.width;
                    }
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
            saveForm: function(event) {
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
                    if (response.body.style && !this.styleId) {
                        sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));
                        window.location = response.body.style.admin_urls.edit;
                    }
                    else {
                        if (response.body.alerts) {
                            response.body.alerts.forEach(function(alert) {
                                vm.$emit('add-alert', alert);
                            }.bind(this));
                        }
                    }

                    vueStore.updateFormErrors([]);

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

            }
        },
        props: {
            styleId: {
                default: null,
                type: Number
            }
        }
    });
</script>