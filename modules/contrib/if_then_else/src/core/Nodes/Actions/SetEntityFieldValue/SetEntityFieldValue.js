// New add action for setting default values of fields
var VueSetEntityFieldValueControl = {
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
  data() {
    return {
      type: drupalSettings.if_then_else.nodes.set_entity_field_value_action.type,
      class: drupalSettings.if_then_else.nodes.set_entity_field_value_action.class,
      name: drupalSettings.if_then_else.nodes.set_entity_field_value_action.name,
      classArg: drupalSettings.if_then_else.nodes.set_entity_field_value_action.classArg,
      options: [],
      form_fields: [],
      field_entities: [],
      selected_entity: '',
      field_bundles: [],
      selected_bundle: '',
      field_type: '',
      value: [],
    }
  },
  template: `<div class="fields-container">
    <div class="entity-select">
      <label class="typo__label">Field</label>
      <multiselect @wheel.native.stop="wheel" v-model="value" :options="options" @input="fieldValueChanged" label="name" track-by="code" 
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select a field">
      </multiselect>
      <label v-if="value != ''" v-model="selected_entity" class="typo__label">Entity</label>
      <multiselect @wheel.native.stop="wheel" v-if="value != ''" v-model="selected_entity" :options="field_entities" @input="entityFieldValueChanged" label="name" track-by="code" 
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select an Entity">
      </multiselect>
      <label v-if="value != '' && selected_entity" class="typo__label">Bundle</label>
      <multiselect  @wheel.native.stop="wheel" v-if="value != '' && selected_entity" v-model="selected_bundle" :options="field_bundles" @input="bundleFieldValueChanged" label="name" track-by="code" 
      :searchable="false" :close-on-select="true" :show-labels="false" placeholder="Select a Bundle">
      </multiselect>  
    </div>
  </div>`,

  methods: {
    fieldValueChanged(value) {
      if (value !== undefined && value !== null && value !== '') {
        //Triggered when selecting an field.
        var selectedOptions = [];

        selectedOptions = {
          name: value.name,
          code: value.code
        };

        //check if selected field value is changed.
        var prevSelectedField = this.getData('form_fields');
        if (typeof prevSelectedField != 'undefined' && prevSelectedField.code != value.code) {
          this.selected_entity = '';
          this.selected_bundle = '';
          this.putData('selected_entity', '');
          this.putData('selected_bundle', '');
        }

        var field_entity = drupalSettings.if_then_else.nodes.set_entity_field_value_action.field_entity_bundle;
        this.field_entities = field_entity[value.code]['entity'];
        this.putData('form_fields', selectedOptions);
        editor.trigger('process');
      } else {
        this.putData('form_fields', '');
        this.putData('selected_entity', '');
        this.putData('selected_bundle', '');
        this.value = '';
        this.field_bundles = [];
      }
    },
    entityFieldValueChanged(value) {
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
          this.putData('selected_bundle', '');
        }
        //Triggered when selecting an field.
        var field_entity = drupalSettings.if_then_else.nodes.set_entity_field_value_action.field_entity_bundle;
        this.field_bundles = field_entity[this.value.code][value.code]['bundle'];
        this.putData('selected_entity', selectedentity);
        var field_type = drupalSettings.if_then_else.nodes.set_entity_field_value_action.form_fields_type[selectedentity.code][this.value.code];
        this.putData('field_type', field_type);
        editor.trigger('process');
      } else {
        this.field_bundles = [];
        this.selected_bundle = '';
        this.putData('selected_entity', '');
        this.putData('selected_bundle', '');
      }
    },
    bundleFieldValueChanged(value) {
      var selectedbundle = [];
      selectedbundle = {
        name: value.name,
        code: value.code
      };
      this.putData('selected_bundle', selectedbundle);
      if (this.selected_entity != undefined && typeof this.selected_entity != 'undefined' && this.selected_entity.value !== '' && this.selected_bundle != undefined && typeof this.selected_bundle != 'undefined' && this.selected_bundle !== '') {
        this.onChange(this.getData('field_type'), this.selected_entity.code, this.selected_bundle.code);
      }
      editor.trigger('process');
    }
  },

  mounted() {
    // initialize variable for data
    this.putData('type', drupalSettings.if_then_else.nodes.set_entity_field_value_action.type);
    this.putData('class', drupalSettings.if_then_else.nodes.set_entity_field_value_action.class);
    this.putData('name', drupalSettings.if_then_else.nodes.set_entity_field_value_action.name);
    this.putData('classArg', drupalSettings.if_then_else.nodes.set_entity_field_value_action.classArg);
    
    //setting values of selected fields when rule edit page loads.
    var get_form_fields = this.getData('form_fields');
    if (typeof get_form_fields != 'undefined') {
      this.value = get_form_fields;

      var field_entity = drupalSettings.if_then_else.nodes.set_entity_field_value_action.field_entity_bundle;

      //setting value for selected entity
      var get_selected_entity = this.getData('selected_entity');
      if (typeof get_selected_entity != 'undefined') {
        //setting entity list
        this.field_entities = field_entity[get_form_fields.code]['entity'];
        this.selected_entity = get_selected_entity;

        var field_type = this.getData('field_type');

        //setting value for selected bundle
        var get_selected_bundle = this.getData('selected_bundle');
        if (typeof get_selected_bundle != 'undefined') {
          //setting bundle list
          this.field_bundles = field_entity[get_form_fields.code][this.selected_entity.code]['bundle'];
          this.selected_bundle = get_selected_bundle;
          if (typeof field_type != 'undefined') {
            this.onChange(field_type, this.selected_entity.code, this.selected_bundle.code);
          }
        }
      }
    } else {
      this.putData('form_fields', []);
    }
  },
  created() {
    if (drupalSettings.if_then_else.nodes.set_entity_field_value_action.form_fields) {
      //setting list of all fields for a form when rule edit page loads.
      this.options = drupalSettings.if_then_else.nodes.set_entity_field_value_action.form_fields;
    }
  }
};

class SetEntityFieldValueControl extends Rete.Control {
  constructor(emitter, key, onChange) {
    super(key);
    this.component = VueSetEntityFieldValueControl;
    this.props = {
      emitter,
      ikey: key,
      onChange,
    };
  }
}

class SetEntityFieldValueActionComponent extends Rete.Component {
  constructor() {
    var nodeName = 'set_entity_field_value_action';
    var node = drupalSettings.if_then_else.nodes[nodeName];
    super(jsUcfirst(node.type) + ": " + node.label);
  }

  //Event node builder
  builder(eventNode) {

    var node_outputs = [];
    var nodeName = 'set_entity_field_value_action';
    var node = drupalSettings.if_then_else.nodes[nodeName];

    node_outputs['success'] = new Rete.Output('success', 'Success', sockets['bool']);
    node_outputs['success']['description'] = node.outputs['success'].description;

    node_outputs['entity'] = new Rete.Output('entity', 'Entity', sockets['object.entity']);
    node_outputs['entity']['description'] = node.outputs['entity'].description;

    eventNode.addOutput(node_outputs['success']);
    eventNode.addOutput(node_outputs['entity']);


    function handleInput() {
      return function(value, entityValue = null, bundleValue = null) {
        let socket_out = eventNode.outputs.get('entity');
        let socket_in = eventNode.inputs.get('entity');
        let socket_field_value = eventNode.inputs.get('field_value');
        if (value == 'email' || value == 'list_string' || value == 'datetime' || value == 'string' || value == 'string_long') {
          socket_field_value.socket = sockets['string'];
        } else if (value == 'entity_reference' || value == 'list_integer' || value == 'list_float' || value == 'decimal' || value == 'float' || value == 'integer') {
          socket_field_value.socket = sockets['number'];
        } else if (value == 'boolean') {
          socket_field_value.socket = sockets['bool'];
        } else if (value == 'text_with_summary') {
          socket_field_value.socket = sockets['object.field.text_with_summary'];
          makeCompatibleSocketsByName('object.field.text_with_summary');
        } else if (value == 'image') {
          socket_field_value.socket = sockets['object.field.image'];
          makeCompatibleSocketsByName('object.field.image');
        } else if (value == 'link') {
          socket_field_value.socket = sockets['object.field.link'];
          makeCompatibleSocketsByName('object.field.link');
        } else if (value == 'text' || value == 'text_long') {
          socket_field_value.socket = sockets['object.field.text_long'];
          makeCompatibleSocketsByName('object.field.text_long');
        }


        if (entityValue != undefined && typeof entityValue != 'undefined' && entityValue !== '' && bundleValue != undefined && typeof bundleValue != 'undefined' && bundleValue !== '') {
          socket_out.socket = sockets['object.entity.' + entityValue + '.' + bundleValue];
          socket_in.socket = sockets['object.entity.' + entityValue + '.' + bundleValue];
          makeCompatibleSocketsByName('object.entity.' + entityValue + '.' + bundleValue);
        }
        eventNode.outputs.set('entity', socket_out);
        eventNode.inputs.set('entity', socket_in);
        eventNode.inputs.set('field_value', socket_field_value);

        eventNode.update();
        editor.view.updateConnections({
          node: eventNode
        });
        editor.trigger('process');
      }
    }

    eventNode.addControl(new SetEntityFieldValueControl(this.editor, nodeName, handleInput()));
    for (let name in node.inputs) {
      let inputLabel = node.inputs[name].label + (node.inputs[name].required ? ' *' : '');
      if (node.inputs[name].sockets.length === 1) {
        let  inputObject = new Rete.Input(name, inputLabel, sockets[node.inputs[name].sockets[0]]);
        inputObject['description'] = node.inputs[name].description;
        eventNode.addInput(inputObject);
      }
      else if (node.inputs[name].sockets.length > 1) {
        let socketNames = [];
        let socketLabels = [];
        for (let idx in node.inputs[name].sockets) {
          socketNames.push(node.inputs[name].sockets[idx]);
          socketLabels.push(sockets[node.inputs[name].sockets[idx]].name);
        }
        socketNames.sort();
        socketLabels.sort();
        let socketLabel = socketLabels.join(', ');
        let socketName = socketNames.join(', ');

        if (!(socketName in sockets)) {
          sockets[socketName] = new Rete.Socket(socketLabel);
        }

        for (let idx in node.inputs[name].sockets) {
          if (!sockets[node.inputs[name].sockets[idx]].compatibleWith(sockets[socketName])) {
            sockets[node.inputs[name].sockets[idx]].combineWith(sockets[socketName]);
            if (typeof compatibleSockets[node.inputs[name].sockets[idx]] === "undefined") {
              compatibleSockets[node.inputs[name].sockets[idx]] = [];
            }
            compatibleSockets[node.inputs[name].sockets[idx]].push(socketName);
          }
        }
        let inputObject = new Rete.Input(name, inputLabel, sockets[socketName]);
        inputObject['description'] =  node.inputs[name].description;
        eventNode.addInput(inputObject);
      }
    }
    eventNode['description'] = node.description;
  }
  worker(eventNode, inputs, outputs) {
    //outputs['form'] = eventNode.data.event;
  }
}
