var gulp = require("gulp");
var elixir = require('laravel-elixir');
var shell = require("gulp-shell")
var del = require("del");

var destination = "public/pina-colada/assets";
var tempPath = "tmp/asset";

elixir.config.publicPath = destination;

gulp.task("delete-public", function() {
  return del([destination]);
});

elixir(function(mix) {
    mix.task("delete-public");
});

elixir(function(mix) {
    mix.sass('application.scss', tempPath + "/application.css");
});

elixir(function(mix) {
    mix.browserify("application.js", tempPath + "/application.js");
});

elixir(function(mix) {
    mix.copy("resources/assets/images", tempPath);
});

elixir(function(mix) {
    mix.copy(tempPath, destination);
});

elixir(function(mix) {
    mix.version([
      "application.js",
      "application.css",
      "**/*.gif",
      "**/*.jpg",
      "**/*.png",
      "**/*.svg"
    ], destination);
});

