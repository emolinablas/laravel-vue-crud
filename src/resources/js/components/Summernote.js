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
            tabsize: 2,
            height: 120,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['fontname', ['fontname']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
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

$(this.$el).summernote();

},

}
