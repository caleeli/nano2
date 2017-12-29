
/*{$o->module()*/Module/*}*/./*{str_plural(camel_case(''.$o->name()))*/users/*}*/ = function () {
	Nano.Resources.apply(this, arguments);
	this.resource = /*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/,
	this.url = /*{json_encode($o->url())*/'/api/module/users'/*}*/,
	this.selection = /*{json_encode($o->selection())*/["username","role"]/*}*/;
}
Nano.extends(/*{$o->module()*/Module/*}*/./*{str_plural(camel_case(''.$o->name()))*/users/*}*/, Nano.Resources);

/*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/ = function () {
	Nano.Resource.apply(this, arguments);
}
Nano.extends(/*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/, Nano.Resource);
/*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/.prototype.type = /*{json_encode($o->type())*/'Module.User'/*}*/;
Nano.Resource.types[/*{$o->type()*/'Module.User'/*}*/] = /*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/;
/*{$o->module()*/Module/*}*/./*{$o->name()*/User/*}*/.definition = /*{$o->jsonDefinition()*/{}/*}*/;
