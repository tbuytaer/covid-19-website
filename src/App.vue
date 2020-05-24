<template>
  <div id="app">
    <WorldMap msg="Worldmap"/>
  </div>
</template>

<script>
import WorldMap from './components/WorldMap.vue'

export default {
  name: 'App',
  components: {
    WorldMap
  }
}
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
