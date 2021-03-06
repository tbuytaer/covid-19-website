<template>
  <div>
    <div class="row">
      <div class="col-lg-3">
        <nav class="navbar navbar-expand-lg navbar-dark world">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#worldnav" aria-controls="worldnav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <span class="maptitle d-inline d-lg-none">{{mapTitle}} ({{regionTitle}})</span>

          <div class="collapse navbar-collapse" id="worldnav">
            <div class="btn-group btn-group-md regionbuttons" role="group" aria-label="Choose region">
              <button type="button" class="btn btn-secondary" :class="{active: isWorldMap}" @click="WorldMap">World</button>
              <button type="button" class="btn btn-secondary" :class="{active: isUSMap}" @click="USMap">USA</button>
            </div>
            <div class="menu">
              <ul class="nav nav-pills">
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isRisk}" @click="mapRisk">Risk</a></li>
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isRe}" @click="mapRe">Re</a></li>            
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isActive}" @click="mapActive">Active cases</a></li>
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isTotal}" @click="mapCumulative">Total cases</a></li>
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isDeaths}" @click="mapDeaths">Deaths</a></li>
                <li class="nav-item"><a href="#" class="nav-link disabled" @click="mapRecovered">% Recovered</a></li>
                <li class="nav-item"><a href="#" class="nav-link" :class="{active: isCFR}" @click="mapCFR">Case Fatality Rate</a></li>
              </ul>
            </div>
          </div>
        </nav>
        <div class="explanation world"><p v-html="mapExplanation"></p></div>
        <div></div>
      </div>
      <div class="col-lg-9">
        <div class="worldmap" ref="worldmap"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3">
        <h2>{{countryName}}</h2>
        <div class="explanation">
          <p>Use the slider to select a time period.</p>
          <p>Dots are data from <em>Johns Hopkins</em>.<br/>
          Solid lines are calculated curves.</p>
          <p>The projection for the next 30 days uses the last calculated R<sub>e</sub>.<p/>
          <p class="red"><span>Predicting the future is hard, and depends on a lot of factors that evolve over time. So take this forecast with lots of grains of salt.</span></p>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="countrygraph" ref="chartdiv"></div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3">
        <h2>Disclaimer</h2>
      </div>
      <div class="col-lg-9"> 
        <div class="explanation disclaimer">
          <p class="red">Predicting the future is hard, and depends on a lot of factors that evolve over time. So take these calculations and forecasts with lots of grains of salt.</p>
          <p>These graphs and some of the numbers on them are based on a model. All models are just approximations of real life, are based on a set of assumptions, and have their limits.</p>
          <p>The level of testing differs per country and also evolves over time. Countries differ in how they count cases. This makes it difficult to compare numbers directly.</p>
          <p>Calculated values are based on a <a href="https://github.com/tbuytaer/seird">SEIR model</a> that uses data from <em><a href="https://github.com/CSSEGISandData/COVID-19">Johns Hopkins University</a></em> to estimate R<sub>e</sub> and other values.</p>
          <p><span>One of the assumptions of this model is that all new infections are caused by people inside this state. If most infections come from abroad, this will cause a (fictitious) spike in Re values.</span></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

import * as am4core from '@amcharts/amcharts4/core';
import * as am4maps from "@amcharts/amcharts4/maps";
import am4geodata_worldLow from "@amcharts/amcharts4-geodata/worldLow";
import am4geodata_USLow from "@amcharts/amcharts4-geodata/usaLow";
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
      isWorldMap: true,
      isUSMap: false,
      isRisk: true,
      isRe: false,
      isActive: false,
      isTotal: false,
      isDeaths: false,
      isCFR: false,
      regionTitle: 'World',
      mapTitle: 'Risk',
      countryName: 'Belgium',
      mapExplanation: 'This gives a rough indication of how \'bad\' the situation is.<br/>Calculated by multiplying <em>Re</em> with <em>active cases</em> (per 100 000).<br/>High levels mean lots of new infections to be expected in the following days.'
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
    // Allow saving as image
    map.exporting.menu = new am4core.ExportMenu();
    map.exporting.formatOptions.getKey("print").disabled = true;
    map.exporting.useRetina = true;
    map.exporting.menu.align = "bottom";
    map.exporting.menu.verticalAlign = "right";
    map.exporting.formatOptions.getKey("png").scale = 2;
    map.exporting.formatOptions.getKey("jpg").scale = 2;
    map.exporting.formatOptions.getKey("pdf").scale = 2;

    let polygonTemplate = polygonSeries.mapPolygons.template;
    // Show country name when hovering
    polygonTemplate.tooltipText = "{name}";
    
    // Default color of countries
    //polygonTemplate.fill =  am4core.color("#d0d0e0");
    polygonTemplate.fill =  am4core.color("#777790");

    let that=this;
    let prefix = 'world';
    polygonTemplate.events.on("hit", function(event){
      // When country or state is clicked, change the data for country/state graph
      that.countryName = event.target.dataItem.dataContext.name;
      id = event.target.dataItem.dataContext.nr;
      if (that.isWorldMap) {
        prefix = 'world';
      } else if (that.isUSMap) {
        prefix = 'USA';
      }
      axios
      .get('./data/' + prefix + '-' + id + '-r0.json')
      .then(response => {
          seriesr0.data = response.data;
        })
      axios
        .get('./data/' + prefix + '-' + id + '-jh-confirmed.json')
        .then(response => {
            series.data = response.data;
          })
      axios
        .get('./data/' + prefix + '-' + id + '-c.json')
        .then(response => {
            series2.data = response.data;
          })
      axios
        .get('./data/' + prefix + '-' + id + '-c-range.json')
        .then(response => {
            series2range.data = response.data;
          })
      axios
        .get('./data/' + prefix + '-' + id + '-m.json')
        .then(response => {
            series3.data = response.data;
          })
      axios
        .get('./data/' + prefix + '-' + id + '-jh-deaths.json')
        .then(response => {
            series4.data = response.data;
          })
      axios
        .get('./data/' + prefix + '-' + id + '-i.json')
        .then(response => {
            series5.data = response.data;
          })
    })

    // Create hover state and set alternative fill color
    let hs = polygonTemplate.states.create("hover");
    //hs.properties.fill = am4core.color("#777799");
    hs.properties.fill = am4core.color("#777790");
    
    // Add heat rule
    polygonSeries.heatRules.push({
      "property": "fill",
      "target": polygonTemplate,
      "min": am4core.color("#CCCCCC"),
      "max": am4core.color("#FF0000"),
      "minValue": 0,
      "maxValue": 200,
    });

    let heatLegend = map.createChild(am4charts.HeatLegend);
    heatLegend.series = polygonSeries;
    heatLegend.disabled = true;
    heatLegend.maxValue = 200;
    heatLegend.width = am4core.percent(20);
    heatLegend.valueAxis.renderer.labels.template.fontSize = 12;
    heatLegend.valueAxis.renderer.minGridDistance = 20;

    // Load the default map
    axios
      .get('./data/world-risk.json')
      .then(response => (polygonSeries.data = response.data))
    
    this.polygonSeries = polygonSeries;
    this.polygonTemplate = polygonTemplate;
    this.heatLegend = heatLegend;
    this.map = map;

    // Default country: Belgium
    let id = 16;

    let chart = am4core.create(this.$refs.chartdiv, am4charts.XYChart);
    chart.paddingRight = 20;
    chart.cursor = new am4charts.XYCursor();
    let dateAxis = chart.xAxes.push(new am4charts.DateAxis());

    // Add scrollbar chart for R
    let r0scrollbar = new am4charts.XYChartScrollbar();    
    r0scrollbar.scrollbarChart.xAxes.push(new am4charts.DateAxis());
    r0scrollbar.scrollbarChart.yAxes.push(new am4charts.ValueAxis());
    let seriesr0 = r0scrollbar.scrollbarChart.series.push(new am4charts.LineSeries());
    seriesr0.dataFields.dateX = "date";
    seriesr0.dataFields.valueY = "value";
    seriesr0.tooltipText = "{valueY.value}";

    //let scrollbarSeries = r0scrollbar.scrollbarChart.series.getIndex(0);
    seriesr0.fillOpacity = 0.5;
    seriesr0.fill = am4core.color('#FFFFFF');
    seriesr0.strokeOpacity = 0.5;
    seriesr0.stroke = am4core.color('#5684d0');
    seriesr0.tooltipText = "{valueY.value}";

    r0scrollbar.scrollbarChart.plotContainer.filters.clear();
    r0scrollbar.background.fillOpacity = 0;
    r0scrollbar.thumb.background.fill = am4core.color('#ffffff');
    r0scrollbar.thumb.background.fillOpacity = 0;
    r0scrollbar.thumb.background.strokeOpacity = 0;
    r0scrollbar.unselectedOverlay.fill = am4core.color('#000000');
    r0scrollbar.unselectedOverlay.fillOpacity = 0.4;

    let scrollAxis = r0scrollbar.scrollbarChart.yAxes.getIndex(0);
    scrollAxis.min = 0;
    scrollAxis.max = 4;

    // Red color for high Re
    let rangeRed = scrollAxis.createSeriesRange(seriesr0);
    rangeRed.value = 1.2;
    rangeRed.endValue = 10;
    rangeRed.contents.stroke = am4core.color('#ff4444');
    rangeRed.contents.fill = am4core.color('#FF4444');
    rangeRed.contents.fillOpacity = 0.3;
    // Orange color for Medium Re
    let rangeOrange = scrollAxis.createSeriesRange(seriesr0);
    rangeOrange.value = 0.8;
    rangeOrange.endValue = 1.2;
    rangeOrange.contents.stroke = am4core.color('#FFFF44');
    rangeOrange.contents.fill = am4core.color('#FFFF44');
    rangeOrange.contents.fillOpacity = 0.3;
    // Green color for Medium Re
    let rangeGreen = scrollAxis.createSeriesRange(seriesr0);
    rangeGreen.value = 0.02;
    rangeGreen.endValue = 0.8;
    rangeGreen.contents.stroke = am4core.color('#44FF44');
    rangeGreen.contents.fill = am4core.color('#44FF44');
    rangeGreen.contents.fillOpacity = 0.3;
    // Show a line for R = 1
    let rangeLine = scrollAxis.axisRanges.create();
    rangeLine.value = 1;
    rangeLine.grid.stroke = am4core.color('#FF4444');
    rangeLine.grid.strokeOpacity = 0.7;

    let rangeLine3 = scrollAxis.axisRanges.create();
    rangeLine3.value = 3;
    rangeLine3.grid.stroke = am4core.color('#ffffff');
    rangeLine3.grid.strokeOpacity = 0.1;

    chart.scrollbarX = r0scrollbar;
    chart.scrollbarX.parent = chart.bottomAxesContainer;  
    //chart.scrollbarX.start = 0.15;
    //chart.scrollbarX.end = 0.9;
    chart.scrollbarX.startGrip.background.fill = am4core.color('#EEEEFF');
    chart.scrollbarX.startGrip.background.fillOpacity = 1;
    chart.scrollbarX.endGrip.background.fill = am4core.color('#EEEEFF');
    chart.scrollbarX.endGrip.background.fillOpacity = 1;
    chart.scrollbarX.minHeight = 150;
    chart.colors.list = [
      am4core.color("#5684d0"),
      am4core.color("#5684d0"),
      am4core.color('#5684d0'),
      am4core.color("#dd3333"),
      am4core.color("#dd3333"),
      am4core.color("#bbbbee"),
    ];
    // Allow saving as image
    chart.exporting.menu = new am4core.ExportMenu();
    chart.exporting.formatOptions.getKey("print").disabled = true;
    chart.exporting.useRetina = true;
    chart.exporting.menu.align = "right";
    chart.exporting.menu.verticalAlign = "bottom";
    chart.exporting.formatOptions.getKey("png").scale = 2;
    chart.exporting.formatOptions.getKey("jpg").scale = 2;
    chart.exporting.formatOptions.getKey("pdf").scale = 2;
    this.chart = chart;

    
    dateAxis.renderer.grid.template.location = 0;

    let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.tooltip.disabled = true;
    valueAxis.renderer.minWidth = 35;
    valueAxis.min = 0;

    let series = chart.series.push(new am4charts.LineSeries());
    var bullet = series.bullets.push(new am4charts.CircleBullet());
    bullet.circle.strokeWidth = 0;
    bullet.circle.maxWidth = 1;
    bullet.circle.radius = 3;
    series.strokeOpacity = 0;
    series.dataFields.dateX = "date";
    series.dataFields.valueY = "value";
    series.tooltipText = "{valueY.value}";
    // Don't include tooltip in saved images
    series.tooltip.exportable = false;
    
    let series2 = chart.series.push(new am4charts.LineSeries());
    series2.dataFields.dateX = "date";
    series2.dataFields.valueY = "value";
    series2.strokeWidth = 2;
    series2.strokeOpacity = 0.7;

    let series2range = chart.series.push(new am4charts.LineSeries());
    series2range.dataFields.dateX = "date";
    series2range.dataFields.openValueY = "minvalue";
    series2range.dataFields.valueY = "maxvalue";
    series2range.sequencedInterpolation = true;
    series2range.fillOpacity = 0.3;
    series2range.strokeOpacity = 0;
    series2range.tensionX = 0.8;

    let series3 = chart.series.push(new am4charts.LineSeries());
    series3.dataFields.dateX = "date";
    series3.dataFields.valueY = "value";
    series3.strokeWidth = 2;
    series3.strokeOpacity = 0.7;

    let series4 = chart.series.push(new am4charts.LineSeries());
    var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
    bullet4.circle.strokeWidth = 0;
    bullet4.circle.maxWidth = 1;
    bullet4.circle.radius = 3;
    series4.strokeOpacity = 0;
    series4.dataFields.dateX = "date";
    series4.dataFields.valueY = "value";
    series4.tooltipText = "{valueY.value}";
    // Don't include tooltip in saved images
    series4.tooltip.exportable = false;

    let series5 = chart.series.push(new am4charts.LineSeries());
    series5.dataFields.dateX = "date";
    series5.dataFields.valueY = "value";
    series5.baseAxis = dateAxis;
    series5.fillOpacity = 0.3;
    series5.tooltipText = "{valueY.value}";
    // Don't include tooltip in saved images
    series5.tooltip.exportable = false;

    axios
      .get('./data/world-' + id + '-r0.json')
      .then(response => {
          seriesr0.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-jh-confirmed.json')
      .then(response => {
          series.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-c.json')
      .then(response => {
          series2.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-c-range.json')
      .then(response => {
          series2range.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-m.json')
      .then(response => {
          series3.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-jh-deaths.json')
      .then(response => {
          series4.data = response.data;
        })
    axios
      .get('./data/world-' + id + '-i.json')
      .then(response => {
          series5.data = response.data;
        })
  },
  beforeDestroy() {
    if (this.chart) {
      this.chart.dispose();
    }
    if (this.chartr0) {
      this.chartr0.dispose();
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
      this.isRisk = false;
      this.isCFR = false;
    },
    WorldMap: function() {
      this.isWorldMap = true;
      this.isUSMap = false;
      this.regionTitle = 'World';
      this.mapMode();
    },
    USMap: function() {
      this.isWorldMap = false;
      this.isUSMap = true;
      this.regionTitle = 'USA';
      this.mapMode();
    },
    mapMode: function() {
      console.log(this.isRe);
      console.log(this.isActive);
      if (this.isRe) {
        this.mapRe();
      } else if (this.isActive) {
        this.mapActive();
      } else if (this.isTotal) {
        this.mapCumulative();
      } else if (this.isDeaths) {
        this.mapDeaths();
      } else if (this.isRisk) {
        this.mapRisk();
      } else if (this.isCFR) {
        this.mapCFR();
      }
    },   
    mapRe: function () {
      this.inactivateMenuOptions();
      this.isRe = true;
      this.mapTitle = 'Re';
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-R0.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-R0.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#66FF66"),
        "max": am4core.color("#FF6666"),
        "minValue": 0,
        "maxValue": 2,
      });
      this.polygonTemplate.tooltipText = "{name}: {value}";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = 2;
      this.heatLegend.minColor = am4core.color("#66FF66");
      this.heatLegend.maxColor = am4core.color("#FF6666");
      this.mapExplanation = 'The average amount of new people infected by an infectious person.<br/>If Re is above 1, there will be an exponential increase in infections.<br/>If it\'s below 1, there will be a decrease.';
    },
    mapActive: function () {
      this.inactivateMenuOptions();
      this.isActive = true;
      this.mapTitle = 'Active cases';
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-active.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-active.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
        "maxValue": 200,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = 200;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'Number of active cases (per 100 000).<br/>This is not the total amount of people who are currently infected, but the <em>officially diagnosed</em> amount.';
    },
    mapCumulative: function () {
      this.inactivateMenuOptions();
      this.isTotal = true;
      this.mapTitle = 'Total cases';
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-cumulative.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-cumulative.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
        "maxValue": 1000,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = 1000;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'Total number of cases (per 100 000).<br/>This is not the total amount of people who have ever caught the disease, but the <em>officially diagnosed</em> amount.';
    },
    mapDeaths: function () {
      this.inactivateMenuOptions();
      this.isDeaths = true;
      this.mapTitle = 'Deaths';
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-deaths.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-deaths.json')
        .then(response => (this.polygonSeries.data = response.data));
      }
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'Number of deaths (per 100 000).<br/>Countries differ in their estimation of how much of their overmortality they attribute to COVID-19.';
    },
    mapRecovered: function () {
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-recovered.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-recovered.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#0000FF"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'Number of officially recovered cases (per 100 000).';
    },
    mapActiveDiff: function () {
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-active-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-active-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    mapDeathsDiff: function () {
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-deaths-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-deaths-diff.json')
        .then(response => (this.polygonSeries.data = response.data));
      } 
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#cccccc"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
      });
      this.polygonTemplate.tooltipText = "{name}: {value} / 100 000";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = this.polygonSeries.heatRules.maxValue;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
    },
    mapRisk: function () {
      this.inactivateMenuOptions();
      this.isRisk = true;
      this.mapTitle = 'Risk';
      if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-risk.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-risk.json')
        .then(response => (this.polygonSeries.data = response.data));
      }
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
        "maxValue": 200,
      });
      this.polygonTemplate.tooltipText = "{name}";
      this.heatLegend.disabled = true;
      this.heatLegend.maxValue = 200;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'This gives a rough indication of how \'bad\' the situation is.<br/>Calculated by multiplying <em>Re</em> with <em>active cases</em> (per 100 000).<br/>High levels mean lots of new infections to be expected in the following days.';
    },
    mapCFR: function () {
      this.inactivateMenuOptions();
      this.isCFR = true;
      this.mapTitle = 'Case Fatality Rate';
            if (this.isUSMap) {
        this.map.geodata = am4geodata_USLow;
        axios
        .get('./data/USA-CFR.json')
        .then(response => (this.polygonSeries.data = response.data));
      } else {
        this.map.geodata = am4geodata_worldLow;
        axios
        .get('./data/world-CFR.json')
        .then(response => (this.polygonSeries.data = response.data));
      }
      this.polygonSeries.heatRules.push({
        "property": "fill",
        "target": this.polygonTemplate,
        "min": am4core.color("#CCCCCC"),
        "max": am4core.color("#FF0000"),
        "minValue": 0,
        "maxValue": 20,
      });
      this.polygonTemplate.tooltipText = "{name}: {value}";
      this.heatLegend.disabled = false;
      this.heatLegend.maxValue = 20;
      this.heatLegend.minColor = am4core.color("#CCCCCC");
      this.heatLegend.maxColor = am4core.color("#FF0000");
      this.mapExplanation = 'The percentage of <em>confirmed cases</em> with a fatal outcome.<br/>This is <b><u>not</u></b> the chance of dying when you are infected.<br/>Countries differ in their estimation of how much of their overmortality they attribute to COVID-19.';
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang="scss">
h2 {
  font-size: 1.5rem;
  margin: 20px 0 0;
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
  color: #78b8f9;
}
.worldmap {
  width: 100%;
  height: 500px;
  border-style: none;
  border-width: 1px;
  border-color: #cccccc;
  background-color: #233648;
}
@media screen and (max-width: 800px){
  .worldmap {
    height: 350px;
  }
}
@media screen and (max-width: 600px){
  .worldmap {
    height: 250px;
  }
}
@media screen and (max-width: 500px){
  .worldmap {
    height: 200px;
  }
}
.countrygraph {
  padding-top: 10px;
  width: 100%;
  height: 600px;
  border-style: none;
  border-width: 1px;
  border-color: #cccccc;
  background-color: #233648;
}
.explanation {  
  font-size: 85%;
  padding: 0.7em;
}
.maptitle {
  font-size: 1.5rem;
  margin-left: 2em;
}
.explanation.world {
  border-style: dotted;
  border-width: 1px;
  border-color: rgba(255,255,255,0.2);
  margin-top: 1em;
  padding-top: 1em;
}
.explanation.disclaimer {
  margin-top: 20px;
}
.navbar.world {
  padding: 0;
}
.navbar.world .navbar-collapse, .navbar.world .navbar-expand-lg {
  flex-direction: column;
}
.navbar.world ul.nav li.nav-item {
  width: 100%;
  margin: 0;
}
.regionbuttons {
  width: 100%;
  margin-bottom: 0.7em;
}
.regionbuttons .btn-secondary {
  background-color: unset;
}
.red {
  border-style: dotted;
  border-color:rgba(255,0,0,0.6);
  border-width: 1px;
  background-color: rgba(255,68,68,0.3);
  padding: 0.4em;
}
</style>
