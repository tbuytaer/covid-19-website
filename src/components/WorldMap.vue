<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <div class="world" ref="worldmap"></div>
    <div ref="chartdiv" style="width: 100%; height: 500px;"></div>
  </div>
</template>

<script>
import axios from 'axios';

import * as am4core from '@amcharts/amcharts4/core';
import * as am4maps from "@amcharts/amcharts4/maps";
import am4geodata_worldLow from "@amcharts/amcharts4-geodata/worldLow";

import * as am4charts from '@amcharts/amcharts4/charts';
import am4themes_animated from "@amcharts/amcharts4/themes/animated";
am4core.useTheme(am4themes_animated);


export default {
  name: 'WorldMap',
  props: {
    msg: String
  },
  data: function() {
    return {
      currentJsonFile: '',
    }
  },
  mounted() {
    let map = am4core.create(this.$refs.worldmap, am4maps.MapChart);
    // low quality map
    map.geodata = am4geodata_worldLow;
    // map projection
    map.projection = new am4maps.projections.Miller();
    let polygonSeries = new am4maps.MapPolygonSeries();
    // use the geodata defined above
    polygonSeries.useGeodata = true;
    map.series.push(polygonSeries);

    // Hide Antarctica
    polygonSeries.exclude = ["AQ"];

    let polygonTemplate = polygonSeries.mapPolygons.template;
    // Show country name when hovering
    polygonTemplate.tooltipText = "{name}: {value}";
    // Color of countries
    //polygonTemplate.fill = am4core.color("#74B266");
    // Create hover state and set alternative fill color
    let hs = polygonTemplate.states.create("hover");
    hs.properties.fill = am4core.color("#7777DD");

    // Add heat rule
    polygonSeries.heatRules.push({
      "property": "fill",
      "target": polygonTemplate,
      "min": am4core.color("#00FF00"),
      "max": am4core.color("#FF0000"),
      "minvalue": 0,
      "maxValue": 2,
    });

    // Change data for some countries from the default
    axios
      .get('./data/worldR0.json')
      .then(response => (polygonSeries.data = response.data))
    





    let chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart);
    chart.paddingRight = 20;

    let data = [];
    let visits = 10;
    for (let i = 1; i < 366; i++) {
      visits += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
      data.push({ date: new Date(2018, 0, i), name: "name" + i, value: visits });
    }
    chart.data = data;

    let dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.renderer.grid.template.location = 0;

    let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.tooltip.disabled = true;
    valueAxis.renderer.minWidth = 35;

    let series = chart.series.push(new am4charts.LineSeries());
    series.dataFields.dateX = "date";
    series.dataFields.valueY = "value";

    series.tooltipText = "{valueY.value}";
    chart.cursor = new am4charts.XYCursor();

    let scrollbarX = new am4charts.XYChartScrollbar();
    scrollbarX.series.push(series);
    chart.scrollbarX = scrollbarX;

    this.chart = chart;



  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.dispose();
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
.world {
  width: 100%;
  height: 500px;
}
</style>
