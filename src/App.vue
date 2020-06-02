<template>
  <div id="app">
    <div class="container">
      <h1>Tracking the spread of COVID-19</h1>
      <WorldMap title="World" />
    </div>
  </div>
</template>

<script>
import WorldMap from "./components/WorldMap.vue";
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";
import "./assets/bootstrap.css";
import "jquery";
import "popper.js";
import "bootstrap/dist/js/bootstrap.bundle";

export default {
  name: "App",
  components: {
    WorldMap
  }
};
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: left;
  margin-top: 20px;
}
h1 {
  font-size: 2rem;
}
</style>
