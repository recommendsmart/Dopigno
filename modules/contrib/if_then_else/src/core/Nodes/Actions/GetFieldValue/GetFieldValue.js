// New add action for setting default values of fields
var VueGetFieldValueControl = {
  components: {
    // Component included for Multiselect.
    Multiselect: window.VueMultiselect.default
  },
  props: ['getData', 'putData', 'emitter', 'onChange'],
  provide() {
    return {
      emitter: this.emitter
    }
  },
  data(){
    return{
      type: drupalSettings.if_then_else.nodes.get_form_field_value_action.type,
      class: drupalSettings.if_then_else.nodes.get_form_field_value_action.class,
      name: drupalSettings.if_then_else.nodes.get_form_field_value_action.name,
      classArg: drupalSettings.if_then_else.nodes.get_form_field_value_action.classArg,
      options: [],
      form_fields: [],
      entities: [],
      selected_entity: '',
      field_bundles: [],
      selected_bundle: '',
      field_type: '',
      field_cardinality: 1,
      value: [],
      bundles: [],
    }
  },
  template: `<div class="fields-container">
    <div class="entity-select">
      <label class="typo__label">Entity</label>
      <multiselect @wheel.native.stop="wheel" v-model="selected_entity" :options="entities" @input="entityFieldValueChanged" label="name" track-by="code"
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select an Entity">
      </multiselect>
      <label v-if="selected_entity" class="typo__label">Bundle</label>
      <multiselect @wheel.native.stop="wheel" v-if="selected_entity" v-model="selected_bundle" :options="bundles" @input="bundleFieldValueChanged" label="name" track-by="code"
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select a Bundle">
      </multiselect>
      <label v-if="selected_entity && selected_bundle" class="typo__label">Field</label>
      <multiselect v-if="selected_entity && selected_bundle" @wheel.native.stop="wheel" v-model="value" :options="options" @input="fieldValueChanged" label="name" track-by="code"
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select a field">
      </multiselect>
    </div>
  </div>`,

  methods: {
    entityFieldValueChanged(value){
      if (value !== undefined && value !== null && value !== '') {
        var selectedentity = [];
        selectedentity = {
          name: value.name,
          code: value.code
        };

        //check if selected entity value is changed.
        prevSelectedEntity = this.getData('selected_entity');
        if (typeof prevSelectedEntity != 'undefined' && prevSelectedEntity.code != value.code) {
          this.selected_bundle = '';
          this.value = '';
          this.putData('selected_bundle', '');
          this.putData('value', '');
        }
        //Triggered when selecting an field.
        let bundle_list = drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info[selectedentity.code]['bundles'];
        this.bundles = [];
        Object.keys(bundle_list).forEach(itemKey => {
          this.bundles.push({
            name: bundle_list[itemKey].label,
            code: bundle_list[itemKey].bundle_id
          });
        });
        var field_cardinality = drupalSettings.if_then_else.nodes.get_form_field_value_action.form_fields_cardinality[selectedentity.code][this.value.code];
	      this.putData('field_cardinality', field_cardinality);
        this.putData('selected_entity', selectedentity);
        editor.trigger('process');
      } else {
        this.bundles = [];
        this.selected_bundle = '';
        this.value = '';
        this.options = [];
        this.putData('value', '');
        this.putData('selected_entity', '');
        this.putData('selected_bundle', '');
      }
    },
    bundleFieldValueChanged(value){
      if (value !== undefined && value !== null && value !== '') {
        //check if selected entity value is changed.
        prevSelectedBundle = this.getData('selected_bundle');
        if (typeof prevSelectedBundle != 'undefined' && prevSelectedBundle.code != value.code) {
          this.value = '';
          this.options = [];
          this.putData('value', '');
        }
        var selectedbundle = [];
        selectedbundle = {
          name: value.name,
          code: value.code
        };
        this.putData('selected_bundle', selectedbundle);
        var selectedEntity = this.getData('selected_entity');
        if (this.selected_entity != undefined && typeof this.selected_entity != 'undefined' && this.selected_entity.value !== '' && this.selected_bundle != undefined && typeof this.selected_bundle != 'undefined' && this.selected_bundle !== '') {
          var options = drupalSettings.if_then_else.nodes.get_form_field_value_action.form_fields[selectedEntity.code][selectedbundle.code]['fields'];
          this.options = [];
          Object.keys(options).forEach(itemKey => {
            this.options.push({
              name: options[itemKey].name,
              code: options[itemKey].code
            });
          });
        }
        editor.trigger('process');
      }else{
        this.value = '';
        this.options = [];
        this.putData('value', '');
      }
    },
    fieldValueChanged(value){
      if (value !== undefined && value !== null && value !== '') {
        //Triggered when selecting an field.
        var selectedOptions = [];

        selectedOptions = {
          name: value.name,
          code: value.code
        };

        this.putData('form_fields', selectedOptions);
        var selectedentity = this.getData('selected_entity');
        var field_type = drupalSettings.if_then_else.nodes.get_form_field_value_action.form_fields_type[selectedentity.code][this.value.code];
        this.putData('field_type', field_type);
        if (this.selected_entity != undefined && typeof this.selected_entity != 'undefined' && this.selected_entity.value !== '' && this.selected_bundle != undefined && typeof this.selected_bundle != 'undefined' && this.selected_bundle !== '') {
          this.onChange(field_type, this.selected_entity.code, this.selected_bundle.code);
        }
        editor.trigger('process');
      } else {
        this.putData('value', '');
        this.value = '';
      }
    }
  },

  mounted(){
    // initialize variable for data
    this.putData('type',drupalSettings.if_then_else.nodes.get_form_field_value_action.type);
    this.putData('class',drupalSettings.if_then_else.nodes.get_form_field_value_action.class);
    this.putData('name', drupalSettings.if_then_else.nodes.get_form_field_value_action.name);
    this.putData('classArg', drupalSettings.if_then_else.nodes.get_form_field_value_action.classArg);

    //setting values of selected fields when rule edit page loads.
    var get_form_fields = this.getData('form_fields');
    if(typeof get_form_fields != 'undefined'){
      this.value = get_form_fields;

      var field_entity = drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info;

      //setting value for selected entity
      var get_selected_entity = this.getData('selected_entity');
      if(typeof get_selected_entity != 'undefined'){
        //setting entity list
        this.selected_entity = get_selected_entity;

        var field_type = this.getData('field_type');
        if(typeof field_type != 'undefined'){
          this.onChange(field_type);
          var field_cardinality = drupalSettings.if_then_else.nodes.get_form_field_value_action.form_fields_cardinality[this.selected_entity.code][this.value.code];
          this.putData('field_cardinality', field_cardinality);
        }

        //setting value for selected bundle
        var get_selected_bundle = this.getData('selected_bundle');
        if(typeof get_selected_bundle != 'undefined'){
          //setting bundle list
          this.selected_bundle = get_selected_bundle;
          var options = drupalSettings.if_then_else.nodes.get_form_field_value_action.form_fields[this.selected_entity.code][this.selected_bundle.code]['fields'];
          this.options = [];
          Object.keys(options).forEach(itemKey => {
            this.options.push({
              name: options[itemKey].name,
              code: options[itemKey].code
            });
          });
          if (typeof field_type != 'undefined') {
            this.onChange(field_type, this.selected_entity.code, this.selected_bundle.code);
          }
        }
      }
    }else{
      this.putData('form_fields',[]);
    }
  },
  created() {
    //Fetching values of fields when editing rule page loads
    if (drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info) {
      var entities_list = drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info;
      Object.keys(entities_list).forEach(itemKey => {
        this.entities.push({
          name: entities_list[itemKey].label,
          code: entities_list[itemKey].entity_id
        });
      });

      // Load the bundle list when form loads for edit
      this.selected_entity = this.getData('selected_entity');
      if (this.selected_entity != undefined && typeof this.selected_entity != 'undefined' && this.selected_entity !== '') {
        let selected_entity = this.selected_entity.code;
        if (drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info) {
          let bundle_list = drupalSettings.if_then_else.nodes.get_form_field_value_action.entity_info[selected_entity]['bundles'];
          Object.keys(bundle_list).forEach(itemKey => {
            this.bundles.push({
              name: bundle_list[itemKey].label,
              code: bundle_list[itemKey].bundle_id
            });
          });
        }
      }
    }
  }
};

class GetFieldValueControl extends Rete.Control {
  constructor(emitter, key, onChange) {
    super(key);
    this.component = VueGetFieldValueControl;
    this.props = { emitter, ikey: key, onChange, };
  }
}

class GetFieldValueActionComponent extends Rete.Component {
  constructor(){
    var nodeName = 'get_form_field_value_action';
    var node = drupalSettings.if_then_else.nodes[nodeName];
    super(jsUcfirst(node.type) + ": " + node.label);
  }

  //Event node builder
  builder(eventNode) {

    var node_outputs = [];
    var nodeName = 'get_form_field_value_action';
    var node = drupalSettings.if_then_else.nodes[nodeName];

    node_outputs['success'] = new Rete.Output('success', 'Success', sockets['bool']);
    node_outputs['success']['description'] = node.outputs['success'].description;

    node_outputs['field_value'] = new Rete.Output('field_value', 'Field Value', sockets['string']);
    node_outputs['field_value']['description'] = node.outputs['field_value'].description;

    eventNode.addOutput(node_outputs['success']);
    eventNode.addOutput(node_outputs['field_value']);


    function handleInput(){
    	return function (value, field_cardinality) {
        let socket_out = eventNode.outputs.get('field_value');

        if(typeof window[value+'_type_field_socket'] == 'function'){
          socket_out.socket = window[value+'_type_field_socket'](field_cardinality);
        }

        eventNode.outputs.set('field_value',socket_out);
        eventNode.update();
        editor.view.updateConnections({node: eventNode});
        editor.trigger('process');
      }
    }

    eventNode.addControl(new GetFieldValueControl(this.editor, nodeName,handleInput()));
    for (let name in node.inputs) {
      let inputLabel = node.inputs[name].label + (node.inputs[name].required ? ' *' : '');
      if (node.inputs[name].sockets.length === 1) {
        let  inputObject = new Rete.Input(name, inputLabel, sockets[node.inputs[name].sockets[0]]);
        inputObject['description'] = node.inputs[name].description;
        eventNode.addInput(inputObject);
      }
    }
    eventNode['description'] = node.description;
  }
  worker(eventNode, inputs, outputs) {
    //outputs['form'] = eventNode.data.event;
  }
}
