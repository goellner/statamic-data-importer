Vue.component('data_importer-fieldtype', {

    mixins: [Fieldtype],
    props: ['data', 'config', 'options', 'name'],


    data: function() {
        return {
            mapping: {}
        };
    },

    computed: {
        fieldDefinitions: function() {
            var self = this;
            // add title field, since it is not defined in fieldset
            this.config.fieldset_content.title = {display: 'Title', key: 'title', type: 'text'};
            return Object.keys(this.config.fieldset_content).map(function (key) {
                self.config.fieldset_content[key]["key"] = key;
                return self.config.fieldset_content[key];
            });
        },
    },

    methods: {
        autoSelectOption: function(key, option) {
            if(option === key) {
                return 'selected';
            } else {
                return '';
            }
        }
    },

    ready: function() {

            var self = this;

            $(this.$el).find('select').each(function() {
                var selfSelect = this;
                $(this).selectize({
                    allowEmptyOption: true,
                    plugins: ['drag_drop', 'remove_button'],
                    onChange: function(value) {
                        if(value == '') {
                            self.mapping[selfSelect.name] = undefined;
                        }
                        else {
                            self.mapping[selfSelect.name] = value;
                        }
                    }
                });
            });
    },

    mounted: function() {
    },

    template:`
    <div class="publish-fields" style="width: 100%;">
        <div v-for="item in fieldDefinitions" class="form-group">
            <div class="field-inner">
            <label class="block">{{ item.display || item.key }}</label>
            <select name="mapping[{{ item.key }}]">
                <option value="">&nbsp;</option>
                <option v-for="option in config.uploaded_data_keys" :value="option" :selected="autoSelectOption(item.key, option)">{{ option }}</option>
            </select>
            </div>
        </div>
    </div>`

});
