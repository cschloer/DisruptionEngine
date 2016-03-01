<?php 
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<?php
$slider_size = 150;
if (isset($_GET['slider_size'])) {
    $slider_size = $_GET['slider_size'];
}
$slider_start = 50;
if (isset($_GET['slider_start'])) {
    $slider_start = $_GET['slider_start'];
}
$slider_step = 10;
if (isset($_GET['slider_step'])) {
    $slider_step = $_GET['slider_step'];
}

// Start the slider in the middle
$init_slider_position = round(($slider_start + $slider_size) / 2);

$eq1LHS = preg_replace('/\s+/', '', "
( (S1 + S2) * (nN / ( 1 + nN) ) ) * 
( 
    (S1 * (uN1) + S2 * uN2) / (S1 + S2) -
    cN1 -
    ((nE / (1 + nE)) * S1 * (uE1 - cEi) ) / (S1 + S2)
)");
$eq1RHS = preg_replace('/\s+/', '', "
S2 * 
(
 (nE / (1 + nE)) * (uE1 - cEi) -
 (uN1 - uN2) 
)");
$eq1Sym = ">=";

$eq2LHS = "uN2";
$eq2RHS = "10";
$eq2Sym = "<";

$eq3LHS = "uN1";
$eq3RHS = "uE1";
$eq3Sym = "<=";

$eq4LHS = "uN2";
$eq4RHS = "uN1";
$eq4Sym = ">=";


# size of mainstream market segment
$S1 = 150000;
# marginal cost of production for new technology firm 1
$cN1 = .210;
# utility from consumption of established technology for mainstream consumers
$uE1 = 1.052;
# Marginal cost of production for established technology firm i
$cEi = .21819;
# number of new technology firms
$nN = 1;
# number of established technology firms
$nE = 4;
if (isset($_GET['S1'])) {
    $S1 = $_GET['S1'];
}
if (isset($_GET['cN1'])) {
    $cN1 = $_GET['cN1'];
}
if (isset($_GET['uE1'])) {
    $uE1 = $_GET['uE1'];
}
if (isset($_GET['cEi'])) {
    $cEi = $_GET['cEi'];
}
if (isset($_GET['nN'])) {
    $nN = $_GET['nN'];
}
if (isset($_GET['nE'])) {
    $nE = $_GET['nE'];
}

//RHS and LHS of 1th equation
if (isset($_GET['eq1LHS'])) {
    $eq1LHS = $_GET['eq1LHS'];
}
if (isset($_GET['eq1RHS'])) {
    $eq1RHS = $_GET['eq1RHS'];
}
if (isset($_GET['eq1Sym'])) {
    $eq1Sym = $_GET['eq1Sym'];
}
//RHS and LHS of 2th equation
if (isset($_GET['eq2LHS'])) {
    $eq2LHS = $_GET['eq2LHS'];
}
if (isset($_GET['eq2RHS'])) {
    $eq2RHS = $_GET['eq2RHS'];
}
if (isset($_GET['eq2Sym'])) {
    $eq2Sym = $_GET['eq2Sym'];
}
//RHS and LHS of 3th equation
if (isset($_GET['eq3LHS'])) {
    $eq3LHS = $_GET['eq3LHS'];
}
if (isset($_GET['eq3RHS'])) {
    $eq3RHS = $_GET['eq3RHS'];
}
if (isset($_GET['eq3Sym'])) {
    $eq3Sym = $_GET['eq3Sym'];
}
//RHS and LHS of 4th equation
if (isset($_GET['eq4RHS'])) {
    $eq4LHS = $_GET['eq4LHS'];
}
if (isset($_GET['eq4RHS'])) {
    $eq4RHS = $_GET['eq4RHS'];
}
if (isset($_GET['eq4Sym'])) {
    $eq4Sym = $_GET['eq4Sym'];
}
$command = escapeshellcmd('python calc.py ' . 
    "$slider_size $slider_start $slider_step
    $S1 $cN1 $uE1 $cEi $nN $nE " .
    preg_replace('/\s+/', '', $eq1LHS) . " " .
    preg_replace('/\s+/', '', $eq1RHS) . " " .
    $eq1Sym . " " .
    preg_replace('/\s+/', '', $eq2LHS) . " " .
    preg_replace('/\s+/', '', $eq2RHS) . " " .
    $eq2Sym . " " .
    preg_replace('/\s+/', '', $eq3LHS) . " " .
    preg_replace('/\s+/', '', $eq3RHS) . " " .
    $eq3Sym . " " .
    preg_replace('/\s+/', '', $eq4LHS) . " " .
    preg_replace('/\s+/', '', $eq4RHS) . " " .
    $eq4Sym

);
//echo $command;
$output = shell_exec($command);
//echo $output;
$result = json_decode($output);
//print_r($result);
?>
<script>
var slider_position = <?php echo $init_slider_position?>;
var slider_size = <?php echo $slider_size?>;
var slider_start = <?php echo $slider_start?>;
var slider_step = <?php echo $slider_step?>;
var quad_points_list = <?php 
        echo json_encode($result) 
?>; 

// Populate the min and max values with a real value
var placeholder = quad_points_list[0][0];
var min_x = placeholder[0];
var max_x = placeholder[0];
var min_y = placeholder[1];
var max_y = placeholder[1];
for (var i = 0; i < quad_points_list.length; i++) {
    var quad_points =  quad_points_list[i];
    if (quad_points != null) {
        for (var j = 0; j < quad_points.length; j++) {
            var point = quad_points[j];
            if (point[0] < min_x) {
                min_x = point[0];
            }
            if (point[0] > max_x) {
                max_x = point[0];
            }
            if (point[1] < min_y) {
                min_y = point[1];
            }
            if (point[1] > max_y) {
                max_y = point[1];
            }
        }
    }
}
// The amount of extra space on either side of the shape, based on the size of the shape
var extra_space_x = (max_x - min_x) / 5;
var extra_space_y = (max_y - min_y) / 5;
min_x -= extra_space_x;
max_x += extra_space_x;
min_y -= extra_space_y;
max_y += extra_space_y;
</script>

<html>
<head>
<title>LaunchU</title>
  <link rel="stylesheet" href="d3.slider.css" />  
</head>
<!DOCTYPE html>
<meta charset="utf-8">
<style>
.axis path, .axis line {
    fill: none;
    stroke: #000;
    shape-rendering: crispEdges;
}
path.line {
    fill: none;
    stroke-width: 1px;
}
.zoomOut {
    fill: #66a;
    cursor: pointer;
}
.zoomOutText {
    pointer-events: none;
    fill : #ccc;
}
.zoomOverlay {
    pointer-events: all;
    fill:none;
}
.band {
    fill : none;
    stroke-width: 1px;
    stroke: red;
}

.crossover {
    fill : none;
    stroke-width: 1px;
    stroke: red;
}

body {
    font-family: Verdana,Arial,sans-serif;
}

h2 {
    font-size: 1.2em;
    margin: 60px 0 5px 0;
}

.wrapper {
    width: 500px;
    margin-left: 10%;
}

.wrapper > div {
    margin: 35px 0;
}

#slider1 {
/* height: 250px; */
}
</style>
<body>
<div id="graph"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="d3.slider.js"></script>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script>
var bandPos = [-1, -1];
var pos;
var xdomain = max_x;
var ydomain = max_y;

var margin = {
    top: 40,
    right: 40,
    bottom: 50,
    left: 60
}
var width = 760 - margin.left - margin.right;
var height = 450 - margin.top - margin.bottom;
var zoomArea = {
    x1: min_x,
    y1: min_y,
    x2: xdomain,
    y2: ydomain
};
var drag = d3.behavior.drag();

var svg = d3.select("body").append("svg")
.attr("width", width + margin.left + margin.right)
.attr("height", height + margin.top + margin.bottom)
.append("g")
.attr("transform", "translate(" + margin.left + "," + margin.top + ")");


var x = d3.scale.linear()
    .range([0, width]).domain([0, xdomain]);

var y = d3.scale.linear()
    .range([height, 0]).domain([0, ydomain]);

    var xAxis = d3.svg.axis()
.scale(x)
    .orient("bottom");

    var yAxis = d3.svg.axis()
.scale(y)
    .orient("left");


var band = svg.append("rect")
.attr("width", 0)
.attr("height", 0)
.attr("x", 0)
.attr("y", 0)
.attr("class", "band");

var quad_points_string = x(0) + "," + y(0) + " " + x(0) + "," + y(0) + " " + x(0) + "," + y(0) + " " + x(0) + "," + y(0);
var quad = svg.append("polygon")
.attr("points", quad_points_string)
.attr("fill", 'blue')
.attr("stroke", 'black')
.attr("stroke-width", 1)
.attr("class", "quad");

svg.append("g")
    .attr("class", "x axis")
.call(xAxis)
    .attr("transform", "translate(0," + height + ")");

    svg.append("g")
    .attr("class", "y axis")
.call(yAxis)

    svg.append("clipPath")
    .attr("id", "clip")
    .append("rect")
    .attr("width", width)
    .attr("height", height);

var zoomOverlay = svg.append("rect")
.attr("width", width - 10)
.attr("height", height)
.attr("class", "zoomOverlay")
.call(drag);

var zoomout = svg.append("g");

zoomout.append("rect")
.attr("class", "zoomOut")
.attr("width", 75)
.attr("height", 40)
.attr("x", -12)
.attr("y", height + (margin.bottom - 20))
.on("click", function () {
        zoomOut();
        });

zoomout.append("text")
.attr("class", "zoomOutText")
.attr("width", 75)
.attr("height", 30)
.attr("x", -10)
.attr("y", height + (margin.bottom - 5))
.text("Zoom Out");

zoom();

drag.on("dragend", function () {
        var pos = d3.mouse(this);
        var x1 = x.invert(bandPos[0]);
        var x2 = x.invert(pos[0]);

        if (x1 < x2) {
        zoomArea.x1 = x1;
        zoomArea.x2 = x2;
        } else {
        zoomArea.x1 = x2;
        zoomArea.x2 = x1;
        }

        var y1 = y.invert(pos[1]);
        var y2 = y.invert(bandPos[1]);

        if (x1 < x2) {
        zoomArea.y1 = y1;
        zoomArea.y2 = y2;
        } else {
        zoomArea.y1 = y2;
        zoomArea.y2 = y1;
        }

        bandPos = [-1, -1];

        d3.select(".band").transition()
            .attr("width", 0)
            .attr("height", 0)
            .attr("x", bandPos[0])
            .attr("y", bandPos[1]) ;

        zoom();

});

drag.on("drag", function () {

        var pos = d3.mouse(this);

        if (pos[0] < bandPos[0]) {
        d3.select(".band").
        attr("transform", "translate(" + (pos[0]) + "," + bandPos[1] + ")");
        }
        if (pos[1] < bandPos[1]) {
        d3.select(".band").
        attr("transform", "translate(" + (pos[0]) + "," + pos[1] + ")");
        }
        if (pos[1] < bandPos[1] && pos[0] > bandPos[0]) {
        d3.select(".band").
        attr("transform", "translate(" + (bandPos[0]) + "," + pos[1] + ")");
        }

        //set new position of band when user initializes drag
        if (bandPos[0] == -1) {
        bandPos = pos;
        d3.select(".band").attr("transform", "translate(" + bandPos[0] + "," + bandPos[1] + ")");
        }

        d3.select(".band").transition().duration(1)
            .attr("width", Math.abs(bandPos[0] - pos[0]))
            .attr("height", Math.abs(bandPos[1] - pos[1]));
});

function zoom() {
    //recalculate domains
    if(zoomArea.x1 > zoomArea.x2) {
        x.domain([zoomArea.x2, zoomArea.x1]);
    } else {
        x.domain([zoomArea.x1, zoomArea.x2]);
    }

    if(zoomArea.y1 > zoomArea.y2) {
        y.domain([zoomArea.y2, zoomArea.y1]);
    } else {
        y.domain([zoomArea.y1, zoomArea.y2]);
    }

    //update axis and redraw lines
    var t = svg.transition().duration(750);
    t.select(".x.axis").call(xAxis);
    t.select(".y.axis").call(yAxis);

    var slider_position_index = Math.round((slider_position - slider_start) / slider_step) * slider_step;
    var quad_points = quad_points_list[slider_position_index];

    var quad_points_string = "";
    for (var i = 0; i < quad_points.length; i++) {
        point = quad_points[i];
        quad_points_string += x(point[0]) + "," + y(point[1]) + " ";
    } 
    d3.select(".quad")
    .transition()
    .attr("points", quad_points_string)

}

var zoomOut = function () {
    x.domain([min_x, xdomain]);
    y.domain([min_y, ydomain]);

    var t = svg.transition().duration(750);
    t.select(".x.axis").call(xAxis);
    t.select(".y.axis").call(yAxis);

    var slider_position_index = Math.round((slider_position - slider_start) / slider_step) * slider_step;
    var quad_points = quad_points_list[slider_position_index];

    var quad_points_string = "";
    for (var i = 0; i < quad_points.length; i++) {
        point = quad_points[i];
        quad_points_string += x(point[0]) + "," + y(point[1]) + " ";
    } 
    d3.select(".quad")
    .transition()
    .attr("points", quad_points_string)

}

</script>

<div class="wrapper">
    <center><h2> S2 Value </h2></center>
    <div id="slider1"></div>
</div>
<script>

d3.select("#slider1").call(d3.slider().value(slider_position).axis(true).min(slider_start).max(slider_start + slider_size).on("slide", function(evt, value) {
    slider_position = Math.round(value);
    // Round to nearest "slider_step" relative to where the slider starts
    var slider_position_index = Math.round((slider_position - slider_start) / slider_step) * slider_step;
    var quad_points = quad_points_list[slider_position_index];
    // The edge case where the rounding might give a value that is greater than slider_start + slider_size
    if (quad_points == null) {
        quad_points = quad_points_list[slider_position_index - slider_step];
    }

    var quad_points_string = "";
    for (var i = 0; i < quad_points.length; i++) {
        point = quad_points[i];
        quad_points_string += x(point[0]) + "," + y(point[1]) + " ";
    } 

    d3.select(".quad")
    .transition()
    .attr("points", quad_points_string)


}));
</script>


<form method="GET">
S1: <input type="text" name="S1" value="<?php echo $S1 ?>">
<br>
cN1: <input type="text" name="cN1" value="<?php echo $cN1 ?>">
<br>
uE1: <input type="text" name="uE1" value="<?php echo $uE1 ?>">
<br>
cEi: <input type="text" name="cEi" value="<?php echo $cEi ?>">
<br>
nN: <input type="text" name="nN" value="<?php echo $nN ?>">
<br>
nE: <input type="text" name="nE" value="<?php echo $nE ?>">
<br>



<br>
inequality 1: <input type="text" name="eq1LHS" value="<?php echo $eq1LHS ?>">
<select name='eq1Sym'>
    <option value='>=' <?php if (strcmp($eq1Sym, '>=') == 0) echo 'selected'?>>&#8805</option>
    <option value='>' <?php if (strcmp($eq1Sym, '>') == 0) echo 'selected'?>>&#62</option>
    <option value='<=' <?php if (strcmp($eq1Sym, '<=') == 0) echo 'selected'?>>&#8804</option>
    <option value='<' <?php if (strcmp($eq1Sym, '<') == 0) echo 'selected'?>>&#60</option>
</select>
<input type="text" name="eq1RHS" value="<?php echo $eq1RHS ?>"> 
<br>
inequality 2: <input type="text" name="eq2LHS" value="<?php echo $eq2LHS ?>">
<select name='eq2Sym'>
    <option value='>=' <?php if (strcmp($eq2Sym, '>=') == 0) echo 'selected'?>>&#8805</option>
    <option value='>' <?php if (strcmp($eq2Sym, '>') == 0) echo 'selected'?>>&#62</option>
    <option value='<=' <?php if (strcmp($eq2Sym, '<=') == 0) echo 'selected'?>>&#8804</option>
    <option value='<' <?php if (strcmp($eq2Sym, '<') == 0) echo 'selected'?>>&#60</option>
</select>
<input type="text" name="eq2RHS" value="<?php echo $eq2RHS ?>"> 
<br>
inequality 3: <input type="text" name="eq3LHS" value="<?php echo $eq3LHS ?>">
<select name='eq3Sym'>
    <option value='>=' <?php if (strcmp($eq3Sym, '>=') == 0) echo 'selected'?>>&#8805</option>
    <option value='>' <?php if (strcmp($eq3Sym, '>') == 0) echo 'selected'?>>&#62</option>
    <option value='<=' <?php if (strcmp($eq3Sym, '<=') == 0) echo 'selected'?>>&#8804</option>
    <option value='<' <?php if (strcmp($eq3Sym, '<') == 0) echo 'selected'?>>&#60</option>
</select>

<input type="text" name="eq3RHS" value="<?php echo $eq3RHS ?>"> 
<br>
inequality 4: <input type="text" name="eq4LHS" value="<?php echo $eq4LHS ?>">
<select name='eq4Sym'>
    <option value='>=' <?php if (strcmp($eq4Sym, '>=') == 0) echo 'selected'?>>&#8805</option>
    <option value='>' <?php if (strcmp($eq4Sym, '>') == 0) echo 'selected'?>>&#62</option>
    <option value='<=' <?php if (strcmp($eq4Sym, '<=') == 0) echo 'selected'?>>&#8804</option>
    <option value='<' <?php if (strcmp($eq4Sym, '<') == 0) echo 'selected'?>>&#60</option>
</select>
<input type="text" name="eq4RHS" value="<?php echo $eq4RHS ?>"> 
<br>



<br>
slider start: <input type="text" name="slider_start" value="<?php echo $slider_start ?>">
<br>
slider size: <input type="text" name="slider_size" value="<?php echo $slider_size ?>">
<br>
slider step: <input type="text" name="slider_step" value="<?php echo $slider_step ?>">
<br>
<br>
<input type="submit" value="Submit">
</form>


</body>
</html>

