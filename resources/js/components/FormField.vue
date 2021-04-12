<template></template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ["resourceName", "resourceId", "field"],

  data() {
    return {
      observed: [],
      dependents: [],
    };
  },

  mounted() {
    this.field.options.map((option) => {
      // allow arrays [field = 'name'] or [field = ['name']]
      if (!Array.isArray(option.field)) {
        option.field = [option.field];
      }

      // add child(ren) to watcher
      option.field.forEach((item) => {
        this.addToObserver(item);
      });

      // add parent(s) to watcher
      option.conditions.forEach((condition) => {
        condition.forEach((item) => {
          this.addToObserver(item.field);

          option.field.forEach((field) => {
            this.dependents.push({ parent: item.field, child: field });
          });
        });
      });
      return option;
    });

    this.observed.forEach((component) => {
      let children = this.dependents
        .filter((dependent) => {
          return dependent.parent == component.name;
        })
        .map((dependent) => dependent.child);

      if (!children.length) return;

      let attribute = this.findComponentAttribute(component.component);
      component.component.$watch(
        attribute,
        () => {
          children.forEach((child) => {
            this.calculate(child);
          });
        },
        { immediate: true }
      );
    });
  },

  methods: {
    calculate(name) {
      let component = this.findObserved(name);
      let vue = component.component;

      console.log(this.field.options);
      // console.log(this.field.options.find((option) => option.field == name));
      let conditions = this.field.options.find((option) => {
        return option.field.includes(name);
      }).conditions;

      let value = !conditions
        .map((condition) => {
          return condition
            .map((item) => {
              return this.validate(
                this.findObserved(item.field).component.value,
                item.value,
                item.operator
              );
            })
            .includes(true);
        })
        .includes(false);

      if (!value) {
        this.$set(vue.field, "component", "hidden-field");
        this.$set(vue.field, "attribute", "");
      } else {
        this.$set(vue.field, "component", component.component_type);
        this.$set(vue.field, "attribute", component.name);
      }
    },

    validate(subject, value, operator) {
      var functions = {
        "<": function (a, b) {
          return a < b;
        },
        ">": function (a, b) {
          return a > b;
        },
        ">=": function (a, b) {
          return a >= b;
        },
        "<=": function (a, b) {
          return a <= b;
        },
        "=": function (a, b) {
          return a == b;
        },
        "==": function (a, b) {
          return a == b;
        },
        "!=": function (a, b) {
          return a != b;
        },
        "!==": function (a, b) {
          return a !== b;
        },
        "===": function (a, b) {
          return a === b;
        },
        includes: function (a, b) {
          return b.includes(a);
        },
        excludes: function (a, b) {
          return !b.includes(a);
        },
      };
      return functions[operator](subject, value);
    },

    findObserved(name) {
      return this.observed.find((observer) => observer.name == name);
    },

    findComponentAttribute(component) {
      let attribute;
      switch (component.field.component) {
        case "belongs-to-many-field":
        case "belongs-to-field":
          attribute = "selectedResource";
          break;
        case "morph-to-field":
          attribute = "fieldTypeName";
          break;
        default:
          attribute = "value";
      }
      return attribute;
    },

    addToObserver(name, root = this.$root) {
      root.$children.forEach((component) => {
        if ((component.field && component.field.attribute) == name) {
          !this.observed.find((component) => component.name == name)
            ? this.observed.push({
                name,
                component,
                component_type: component.field.component,
              })
            : null;
        } else {
          this.addToObserver(name, component);
        }
      });
    },
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || "";
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      // formData.append(this.field.attribute, this.value || "");
    },
  },
};
</script>
