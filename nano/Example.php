<?xml version="1.0" encoding="UTF-8"?>
<root xmlns:v-bind='http://nano.com/vue'>
<script type="Model">
{
	"module": "Example",
	"name": "example",
	"table": "example",
	"extends": "\\Illuminate\\Foundation\\Auth\\User",
	"events": {
		"saving": <?php
			function (\App\Events\Example\ExampleSaving $event) {

			}
		?>
	},

	"fields": [
		{"name":"name","type":"string","nullable":false,"default":"","label":"Nombre"},
		{"name":"address","type":"string","nullable":true,"label":"Dirección"},
		{"name":"avatar","type":"array","nullable":true,"label":"Avatar","ui":"file"}
	],

	"relationships": [
		{"type":"hasMany","name":"logins","model":"login"},
		{"type":"belongsTo","name":"role","model":"role"}
	],

	"methods": []
}	
</script>
<component name="abm-example">
	<abm model="Example.Example" v-bind:fields='[
		{"name":"name","label":"Nombre"},
		{"name":"address","label":"Dirección"},
		{"name":"avatar","label":"Avatar","ui":"file","list":false}
	]'/>
	<!-- script type="Vue">
		{
			"data": function () {

			}
		}
	</script -->
</component>
</root>