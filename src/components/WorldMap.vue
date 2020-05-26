<template>
  <div>
    <div class="row">
      <div class="col-md-2">
        <ul class="nav nav-pills flex-column">
          <li><a href="#" class="nav-link" :class="{active: isRe}" @click="worldRe">Re</a></li>            
          <li><a href="#" class="nav-link" :class="{active: isActive}" @click="worldActive">Active cases</a></li>
          <li><a href="#" class="nav-link" :class="{active: isTotal}" @click="worldCumulative">Total cases</a></li>
          <li><a href="#" class="nav-link" :class="{active: isDeaths}" @click="worldDeaths">Deaths</a></li>
          <li><a href="#" class="nav-link disabled" @click="worldRecovered">% Recovered</a></li>
          <li><a href="#" class="nav-link disabled">CFR</a></li>
          <li><a href="#" class="nav-link disabled">IFR</a></li>
        </ul>
      </div>
      <div class="col-md-10">
        <div class="worldmap" ref="worldmap"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h2>{{countryName}}</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <div class="explanation">Click on a country to get more detailed information.</div>
      </div>
      <div class="col-md-10">
        <div class="countrymap" ref="chartdiv"></div>
      </div>
    </div>
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
      isRe: true,
      isActive: false,
      isTotal: false,
      isDeaths: false,
      countryName: 'Belgium',
    }
  },
  mounted() {
    //let countryName = 'Belgium';
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

    let that=this;
    polygonTemplate.events.on("hit", function(event){
      // When country is clicked, change the data for country graph
      console.log(event.target.dataItem.dataContext.id);
      console.log(event.target.dataItem.dataContext.name);
      console.log(event.target.dataItem.dataContext.nr);
      that.countryName = event.target.dataItem.dataContext.name;
      id = event.target.dataItem.dataContext.nr;
      axios
        .get('./data/country-' + id + '-jh-confirmed.json')
        .then(response => {
            //chart.data = response.data;
            series.data = response.data;
          })
      axios
        .get('./data/country-' + id + '-c.json')
        .then(response => {
            //chart.data = response.data;
            series2.data = response.data;
          })
      axios
        .get('./data/country-' + id + '-m.json')
        .then(response => {
            //chart.data = response.data;
            series3.data = response.data;
          })
      axios
        .get('./data/country-' + id + '-jh-deaths.json')
        .then(response => {
            //chart.data = response.data;
            series4.data = response.data;
          })
      axios
        .get('./data/country-' + id + '-i.json')
        .then(response => {
            //chart.data = response.data;
            series5.data = response.data;
          })
    })

    // Create hover state and set alternative fill color
    let hs = polygonTemplate.states.create("hover");
    hs.properties.fill = am4core.color("#777799");
    
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

    //let countryList = [];
    // Get country IDs
    axios
    .get('./data/country-ID.json')
    .then(response => {
        console.log(response.data);
        //console.log(response.data[16].id);
       // countryList = response.data;
      })
   // console.log(countryList[16].id);

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
    chart.colors.list = [
      am4core.color("#5555ff"),
      am4core.color("#5555ff"),
      am4core.color("#FF4444"),
      am4core.color("#FF4444"),
      am4core.color("#bbbbee"),
    ];

    let id = 17;
    
    let dateAxis = chart.xAxes.push(new am4charts.DateAxis());
    dateAxis.renderer.grid.template.location = 0;

    let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.tooltip.disabled = true;
    valueAxis.renderer.minWidth = 35;

    let series = chart.series.push(new am4charts.LineSeries());
    var bullet = series.bullets.push(new am4charts.CircleBullet());
  
    bullet.circle.strokeWidth = 0;
    bullet.circle.maxWidth = 1;
    series.dataFields.dateX = "date";
    series.dataFields.valueY = "value";
    

    let series2 = chart.series.push(new am4charts.LineSeries());
    series2.dataFields.dateX = "date";
    series2.dataFields.valueY = "value";

    let series3 = chart.series.push(new am4charts.LineSeries());
    series3.dataFields.dateX = "date";
    series3.dataFields.valueY = "value";

    let series4 = chart.series.push(new am4charts.LineSeries());
    var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
    bullet4.circle.strokeWidth = 0;
    bullet.circle.maxWidth = 1;
    series4.dataFields.dateX = "date";
    series4.dataFields.valueY = "value";

    let series5 = chart.series.push(new am4charts.LineSeries());
    series5.dataFields.dateX = "date";
    series5.dataFields.valueY = "value";

    axios
      .get('./data/country-' + id + '-jh-confirmed.json')
      .then(response => {
          //chart.data = response.data;
          series.data = response.data;
        })
    axios
      .get('./data/country-' + id + '-c.json')
      .then(response => {
          //chart.data = response.data;
          series2.data = response.data;
        })
    axios
      .get('./data/country-' + id + '-m.json')
      .then(response => {
          //chart.data = response.data;
          series3.data = response.data;
        })
    axios
      .get('./data/country-' + id + '-jh-deaths.json')
      .then(response => {
          //chart.data = response.data;
          series4.data = response.data;
        })
    axios
      .get('./data/country-' + id + '-i.json')
      .then(response => {
          //chart.data = response.data;
          series5.data = response.data;
        })

    series.tooltipText = "{valueY.value}";
    chart.cursor = new am4charts.XYCursor();

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
    inactivateMenuOptions: function () {
      this.isRe = false;
      this.isActive = false;
      this.isTotal = false;
      this.isDeaths = false;
    },
    worldRe: function () {
      this.inactivateMenuOptions();
      this.isRe = true;
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
      this.inactivateMenuOptions();
      this.isActive = true;
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
      this.inactivateMenuOptions();
      this.isTotal = true;
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
      this.inactivateMenuOptions();
      this.isDeaths = true;
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
  background-color: #233648;
}
.countrymap {
  width: 100%;
  height: 500px;
  border-style: solid;
  border-width: 1px;
  border-color: #cccccc;
  background-color: #233648;
}
.explanation {  
  font-size: 90%;
  padding: 0.7em;
}
</style>
