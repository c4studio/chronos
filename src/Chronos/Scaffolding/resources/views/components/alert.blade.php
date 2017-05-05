<script type="text/x-template" id="alerts-template">

    <div v-bind:class="classes" v-if="show">
        <span class="alert-title" v-html="alertTitle"></span>
        <span class="alert-message" v-html="alertMessage"></span>
        <a class="alert-close" v-on:click="closeAlert()"></a>
    </div>

</script>

<script>
    Vue.component('alert', {
        computed: {
            classes: function() {
                return {
                    'alert': true,
                    'alert-dismissible': true,
                    'alert-info': this.alertType == 'info',
                    'alert-error': this.alertType == 'error',
                    'alert-success': this.alertType == 'success',
                    'alert-warning': this.alertType == 'warning',
                    'out': !this.out
                }
            }
        },
        created: function() {
            setTimeout(function() {
                this.closeAlert();
            }.bind(this), "{{ Config::get('chronos.alerts.auto_dismiss') }}");
        },
        data: function() {
            return {
                out: true,
                show: true
            }
        },
        methods: {
            closeAlert: function() {
                this.out = false;
                setTimeout(function() {
                    this.show = false;
                }.bind(this), 450);
            }
        },
        props: ['alertType', 'alertTitle', 'alertMessage'],
        template: '#alerts-template'
    });
</script>