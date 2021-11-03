module.exports = {

template: '<textarea :name="name"></textarea>',

props: {
    valor: {
        type: String,
        default: ''
    },
    model: {
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    config: {
        type: Object,
        default: {
        },
    },
},

watch: {
  valor: function(){
      $(this.$el).summernote('code', this.valor);
  }
},
mounted() {
let vm = this;
let config = this.config;

config.callbacks = {

onInit: function () {
    $(vm.$el).summernote("code", vm.model);
},

onChange: function () {
    vm.$emit('change', $(vm.$el).summernote('code'));
},

onBlur: function () {
    vm.$emit('change', $(vm.$el).summernote('code'));
}
};

$(this.$el).summernote(config);

},

}
