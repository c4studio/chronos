<script>
    Vue.component('role-editor', {
        data: function() {
            return {
                name: '',
                store: vueStore.state
            }
        },
        methods: {
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
                    vueStore.updateFormErrors([]);

                    // refresh page
                    sessionStorage.setItem('alerts', JSON.stringify(response.body.alerts));
                    window.location.reload();
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
        }
    });
</script>