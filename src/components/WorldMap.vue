<template>
  <div>
    <div class="row">
      <div class="col-md-2">
        <ul class="nav nav-pills flex-column">
          <li><a href="#" class="nav-link active" @click="worldRe">Re</a></li>            
          <li><a href="#" class="nav-link" @click="worldActive">Active cases</a></li>
          <li><a href="#" class="nav-link" @click="worldCumulative">Total cases</a></li>
          <li><a href="#" class="nav-link" @click="worldDeaths">Deaths</a></li>
          <li><a href="#" class="nav-link disabled" @click="worldRecovered">% Recovered</a></li>
          <li><a href="#" class="nav-link disabled">CFR</a></li>
          <li><a href="#" class="nav-link disabled">IFR</a></li>
        </ul>
      </div>
      <div class="col-md-10">
        <div class="worldmap" ref="worldmap"></div>
      </div>
    </div>
    <div ref="chartdiv" style="width: 100%; height: 500px;"></div>
  </div>
</template>

<script>
import axios from 'axios';

import * as am4core from '@amcharts/amcharts4/core';
import * as am4maps from "@amcharts/amcharts4/maps";
import am4geodata_worldLow from "@amcharts/amcharts4-geodata/worldLow";

import * as am4charts from '@amcharts/amcharts4/charts';
import am4themes_animated from "@amcharts/amcharts4/themes/dark";
am4core.useTheme(am4themes_animated);


export default {
  name: 'WorldMap',
  props: {
    title: String,
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
    
    // Default color of countries
    polygonTemplate.fill =  am4core.color("#d0d0e0");

    // Create hover state and set alternative fill color
    let hs = polygonTemplate.states.create("hover");
    hs.properties.fill = am4core.color("#7777DD");
    
    // Add heat rule
    polygonSeries.heatRules.push({
      "property": "fill",
      "target": polygonTemplate,
      "min": am4core.color("#66FF66"),
      "max": am4core.color("#FF6666"),
      "minValue": 0,
      "maxValue": 2,
    });

    let heatLegend = map.createChild(am4charts.HeatLegend);
    heatLegend.series = polygonSeries;
    heatLegend.maxValue = 2;
    heatLegend.width = am4core.percent(20);
    heatLegend.valueAxis.renderer.labels.template.fontSize = 12;
    heatLegend.valueAxis.renderer.minGridDistance = 20;
  

    // Change data for some countries from the default
    axios
      .get('./data/world-R0.json')
      .then(response => (polygonSeries.data = response.data))
    
    this.polygonSeries = polygonSeries;
    this.polygonTemplate = polygonTemplate;
    this.heatLegend = heatLegend;
    this.map = map;




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
    if (this.map) {
      this.map.dispose();
    }
  },
  methods: {
    worldRe: function () {
      axios
        .get('./data/world-R0.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#66FF66"),
        "max": am4core.color("#FF6666"),
        "minValue": 0,
        "maxValue": 2,
      });
      this.polygonTemplate.tooltipText = "{name}: {value}";
      this.heatLegend.maxValue = 2;
      this.heatLegend.minColor = am4core.color("#66FF66");
      this.heatLegend.maxColor = am4core.color("#FF6666");
    },
    worldActive: function () {
      axios
        .get('./data/world-active.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    worldCumulative: function () {
      axios
        .get('./data/world-cumulative.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    worldDeaths: function () {
      axios
        .get('./data/world-deaths.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    worldRecovered: function () {
      axios
        .get('./data/world-recovered.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#0000FF"),
        "minvalue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    worldActiveDiff: function () {
      axios
        .get('./data/world-active-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#FF0000"),
        "minvalue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    worldDeathsDiff: function () {
      axios
        .get('./data/world-deaths-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#FF0000"),
        "minvalue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
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
.worldmap {
  width: 100%;
  height: 500px;
  border-style: solid;
  border-width: 1px;
  border-color: #cccccc;
}

</style>
