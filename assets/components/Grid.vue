<template>
  <div class="grid">
    <v-grid
        theme="compact"
        :source="rows"
        :columns="columns"
        resize="true"
        filter="true"
        useClipboard="true"
        readonly="true"
    ></v-grid>
  </div>
</template>

<script>
import VGrid from "@revolist/vue-datagrid";

export default {
  name: "grid",
  components: {
    VGrid,
  },
  props: {
    columns: {
      type: Array,
      required: true
    },
    rows: {
      type: Array,
      required: true
    }
  }
};
</script>
<style>
.grid {
  height: 800px;
  width: 100%;
}
.grid .viewports {
  width:100%
}
</style>
