# Graphs to track the spread of COVID-19

## Updating data files
Export new CSV files from **Mathematica** and place them in **/rawdata/**.

Generate the JSON files.
```
php generate-json.php 
```
These will be placed in **/public/data/** and in **/dist/data/**

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
