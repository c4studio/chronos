<script>
    Vue.component('content-type-editor', {
        created: function() {
            // populate data
            this.getData();
        },
        data: function() {
            return {
                name: '',
                notes: '',
                store: vueStore.state,
                titleLabel: 'Title',
                translatable: true
            }
        },
        methods: {
            getData: function() {
                if (!this.typeId)
                    return;

                this.$http.get('/api/content/types/' + this.typeId).then(function(response) {
                    var content = response.body;

                    if (content) {
                        this.name = content.name;
                        this.notes = content.notes;
                        this.titleLabel = content.title_label;
                        this.translatable = content.translatable;
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
                    if (response.body.type && !this.typeId) {
                        sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));
                        window.location = response.body.type.admin_urls.edit;
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
            typeId: {
                default: null,
                type: Number
            }
        }
    });
</script>