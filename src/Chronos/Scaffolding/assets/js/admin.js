/* globals Vue */

const vueStore = {
	state: {
		formErrors: []
	},
	updateFormErrors: function(formErrors) {
		if (!formErrors.status || formErrors.status != 200)
			this.state.formErrors = JSON.deflate(formErrors.errors);
		else
			this.state.formErrors = [];
	}
};



/*
 *  CLICK OUTSIDE
 */
Vue.directive('click-outside', {
	bind: function(el, binding, vNode) {
		// Provided expression must evaluate to a function.
		if (typeof binding.value !== 'function') {
			const compName = vNode.context.name;
			var warn = "[Vue-click-outside:] provided expression '${binding.expression}' is not a function, but has to be";
			if (compName)
				warn += "Found in component '${compName}'";

			console.warn(warn)
		}

		// Define Handler and cache it on the element
		const bubble = binding.modifiers.bubble;
		const handler = function(e) {
			if (bubble || (!el.contains(e.target) && el !== e.target))
				binding.value(e);
		};
		el.__vueClickOutside__ = handler;

		// add Event Listeners
		document.addEventListener('click', handler);
	},

	unbind: function(el, binding) {
		// Remove Event Listeners
		document.removeEventListener('click', el.__vueClickOutside__);
		el.__vueClickOutside__ = null;

	}
});


/*
 *  DATA TABLES
 */
Vue.component('data-table', {
	created: function() {
		// set defaults
		this.sortField = this.defaultSortField;
		this.sortDesc = this.sortReverse;

		// get data
		this.getData();

		// add listeners
		this.$parent.$on('deleted-model-from-dialog', this.deleteModel);
		this.$parent.$on('perform-bulk-action', this.performBulkAction);
	},
	components: {
		sortable: {
			computed: {
				active: function() {
					return this.$parent.sortField == this.field;
				}
			},
			props: {
				field: {
					required: true,
					type: String
				}
			},
			template: '<a v-bind:class="{ active: active }" v-on:click="$parent.sort(field)"><slot></slot><span class="caret" v-show="!active"></span><span v-show="active" v-bind:class="{ \'caret-up\':$parent.sortDesc,\'caret-down\':!$parent.sortDesc }"></span></a>'
		}
	},
	data: function() {
		return {
			bulkSelector: false,
			data: null,
			dataLoader: false,
			deleteURL: null,
			filters: {
				search: ''
			},
			pagination: {},
			searchOn: false,
			selected: [],
			sortDesc: false,
			sortField: null,
			toggleOn: {}
		}
	},
	methods: {
		ajaxGet: function(src, reload) {

			if (reload === true)
				vm.$emit('ajax-get', src, this.getData);
			else
				vm.$emit('ajax-get', src);
		},
		clearSearch: function() {
			this.filters.search = '';

			this.searchOn = false;

			this.getData();
		},
		deleteModel: function(target) {
			vm.$emit('show-loader');

			var dialog = new Modal(target);
			dialog.close();

			this.$http.delete(this.deleteURL).then(function(response) {
				vm.$emit('hide-loader');

				if (response.body.alerts) {
					response.body.alerts.forEach(function(alert) {
						vm.$emit('add-alert', alert);
					}.bind(this));
				}

				this.getData();
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
		getData: function() {
			this.dataLoader = true;

			var params = {
				filters: this.filters,
				perPage: this.pagination.per_page,
				page: this.pagination.current,
				sortBy: this.sortField,
				sortOrder: this.sortDesc
			};

			if (this.withInactive)
				params.withInactive = 1;

			this.$http.get(this.src, {params: params}).then(function(response) {
				this.data = response.body.data;

				// assign pagination vars
				this.pagination.current = response.body.current_page;
				this.pagination.last = response.body.last_page;
				this.pagination.per_page = response.body.per_page;
				this.pagination.items = response.body.total;

				this.dataLoader = false;
			}.bind(this), function(response) {
				this.dataLoader = false;

				vm.$emit('add-alert', {
					type: 'error',
					title: 'AJAX error',
					message: response.statusText + ' (' + response.status + ')'
				});
			}.bind(this));
		},
		highlight: function(text, phrase) {
			if (phrase && this.searchOn)
				return text.replace(new RegExp('(' + phrase + ')', 'gi'), '<span class="highlighted">$1</span>');
			else
				return text;
		},
		paginate: function(page) {
			this.pagination.current = page;

			this.getData();
		},
		performBulkAction: function(url, method, arrayName, e) {
			vm.$emit('show-loader');

			// close modal
			if (e) {
				var target = e.target.closest('.modal');
				if (target) {
					var dialog = new Modal(target);
					dialog.close();
				}

				// close dropdown
				var dropdown = document.querySelector('.bulk-actions');
				if (dropdown)
					dropdown.classList.remove('open');
			}

			var params;

			if (method == 'DOWNLOAD') {
				params = Object.keys(this.selected).map(function(k) { return encodeURIComponent(arrayName  + '[' + k + ']') + '=' + encodeURIComponent(this.selected[k])}.bind(this)).join('&');

				window.location.href = url + '?' + params;

				this.bulkSelector = false;
				this.selected = [];

				vm.$emit('hide-loader');
			} else {
				params = {};
				params[arrayName] = this.selected;

				this.$http({
					method: method,
					params: params,
					url: url
				}).then(function (response) {
					vm.$emit('hide-loader');

					if (response.body.alerts) {
						response.body.alerts.forEach(function (alert) {
							vm.$emit('add-alert', alert);
						}.bind(this));
					}

					this.getData();

					this.bulkSelector = false;
					this.selected = [];
				}, function (response) {
					vm.$emit('hide-loader');

					if (response.body.alerts) {
						response.body.alerts.forEach(function (alert) {
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
		search: debounce(function() {
			this.getData();

			this.pagination.current = 1;
			this.searchOn = true;
		}, 500),
		selectAll: function() {
			if (this.selected.length != this.pagination.items) {
				this.selected = [];
				this.data.forEach(function(item) {
					this.selected.push(item.id);
				}.bind(this));
			} else {
				this.selected = [];
			}

		},
		setdeleteURL: function(deleteURL) {
			this.deleteURL = deleteURL;
		},
		setInnerHTML: function(target, html) {
			document.querySelector(target).innerHTML = html;
		},
		setModalURL: function(target, url) {
			document.querySelector(target + ' form').action = url;
		},
		setValue: function(target, value) {
			document.querySelector(target).value = value;
		},
		showAll: function() {
			this.pagination.current = 1;
			this.pagination.per_page = 0;

			this.getData();
		},
		sort: function(field) {
			if (this.sortField == field)
				this.sortDesc = !this.sortDesc;
			this.sortField = field;

			this.getData();
		},
		toggleFilter: function(filter) {
			this.toggleOn[filter] = !this.toggleOn[filter];
			this.filters[filter] = this.toggleOn[filter];

			this.getData();
		}
	},
	props: {
		defaultSortField: {
			required: true,
			type: String
		},
		sortReverse: {
			default: false,
			type: Boolean
		},
		src: {
			required: true,
			type: String
		},
		withInactive: {
			default: false,
			type: Boolean
		}
	},
	updated: function() {
		var Modals = document.querySelectorAll('.modal'), mdl = Modals.length, i = 0;
		for ( i;i<mdl;i++ ) {
			var modal = Modals[i], options = {};
			options.keyboard = modal.getAttribute('data-keyboard');
			options.backdrop = modal.getAttribute('data-backdrop');
			options.duration = modal.getAttribute('data-duration');
			new Modal(modal,options)
		}
	}
});



/*
 *  FLATPICKR
 */
Vue.directive('flatpickr', {
	bind: function(el, binding) {
		el._flatpickr = flatpickr(el, binding.value);
	},
	unbind: function(el) {
		el._flatpickr.destroy()
	}
});



/*
 *  VUE INSTANCE
 */
var vm = new Vue({
	el: '#chronos',
	created: function() {
		// check for alerts in session
		if (sessionStorage.getItem('alerts') && sessionStorage.getItem('alerts') !== 'undefined') {
			this.alerts = JSON.parse(sessionStorage.getItem('alerts'));
			sessionStorage.removeItem('alerts');
		}

		// add listeners
		this.$on('add-alert', this.addAlert);
		this.$on('ajax-get', this.ajaxGet);
		this.$on('hide-loader', this.hideLoader);
		this.$on('show-loader', this.showLoader);
	},
	data: {
		alerts: [],
		offcanvas: false,
		offcanvasOpen: '',
		loader: false
	},
	methods: {
		addAlert: function(alert) {
			this.alerts.push(alert)
		},
		ajaxGet: function(src, callback) {
			vm.$emit('show-loader');

			this.$http.get(src).then(function(response) {
				vm.$emit('hide-loader');

				if (response.body.alerts) {
					response.body.alerts.forEach(function(alert) {
						vm.$emit('add-alert', alert);
					}.bind(this));
				}

				if (typeof callback === 'function') {
					callback();
				}

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
		closeOffcanvas: function() {
			this.offcanvas = false;
		},
		deleteModelFromDialog: function(e) {
			this.$emit('deleted-model-from-dialog', e.target.closest('.modal'));
		},
		hideLoader: function() {
			this.loader = false;
		},
		isOffcanvasOpen: function(active) {
			return this.offcanvasOpen == active;
		},
		performBulkAction: function(url, method, arrayName, e) {
			this.$emit('perform-bulk-action', url, method, arrayName, e);
		},
		showLoader: function() {
			this.loader = true;
		},
		toggleOffcanvas: function() {
			this.offcanvas = !this.offcanvas;
		},
		toggleOffcanvasOpen: function(active) {
			if (this.isOffcanvasOpen(active))
				this.offcanvasOpen = '';
			else
				this.offcanvasOpen = active;
		}
	}
});



Vue.filter('strLimit', function(str, limit) {
	if (!str)
		return str;

	return str.limit(limit);
});
Vue.filter('strSlug', function(str, limit) {
	return str.slugify(limit);
});