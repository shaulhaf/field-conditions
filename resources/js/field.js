Nova.booting((Vue, router, store) => {
  Vue.component('index-field-conditions', require('./components/IndexField'))
  Vue.component('detail-field-conditions', require('./components/DetailField'))
  Vue.component('form-field-conditions', require('./components/FormField'))
})
