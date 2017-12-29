var API_SERVER = 'http://localhost:7878';
function Element () {
}
var Nano = {};
/**
 * Extension function.
 */
Nano.extends = function (child, parent) {
	Object.assign(child, parent);
	child.parent = parent;
	child.prototype = Object.create(parent.prototype);
	child.prototype.constructor = child;
}

/**
 * Resource class.
 */
Nano.Resource = function () {
	var resources = this.constructor.resources;
	Element.apply(this, arguments);
	this.id = null;
	this.attributes = {};
	this.relationships = {};
	this.methods = {};
	this.getResources = function () {
		return resources;
	}
	this.setResources = function (rs) {
		resources = rs;
	}
};
Nano.extends(Nano.Resource, Element);
Nano.Resource.prototype.type = 'Nano.Resource';
Nano.Resource.prototype.encodeAttribute = function (attribute, value) {
		return value;
	};
Nano.Resource.prototype.encodeRelationship = function (relationship, value) {
		return value===null ? value : {data: value};
	};
Nano.Resource.prototype.jsonapiEncode = function () {
		var self = this;
		var attributes = {};
		var relationships = {};
		for(var a in self.constructor.definition.attributes) {
			var attribute = self.constructor.definition.attributes[a];
			attributes[a] = self.encodeAttribute(attribute, self.attributes[a]);
		}
		for(var a in self.constructor.definition.relationships) {
			var relationship = self.constructor.definition.relationships[a];
			relationships[a] = self.encodeRelationship(relationship, self.relationships[a]);
		}
		return {
			data: {
				type: self.type,
				attributes: attributes,
				relationships: relationships
			}
		};
	};
Nano.Resource.prototype.jsonapiDecode = function (data) {
		var self = this;
        self.originalData = data;
        Object.assign(self.attributes, self.originalData.attributes);
        self.id = self.originalData.id;
        if(typeof self.originalData.relationships==="object") {
            for(var a in self.originalData.relationships) {
                self.relationships[a] = self.constructor.jsonapiParse(self.originalData.relationships[a]);
            }
        }
	};
Nano.Resource.prototype.load = function (id) {
		this.getResources().load(
			typeof id==='undefined' ? this.id : id,
			this
		);
	};
Nano.Resource.prototype.save = function () {
		this.getResources().save(this);
	};
Nano.Resource.prototype.delete = function () {
		this.getResources().delete(this);
	};
Nano.Resource.prototype.reset = function (id) {
		this.jsonapiDecode(self.originalData);
	};
Nano.Resource.prototype.setRelationship = function (relationshipName, value) {
		var self = this;
		var definition = this.constructor.definition.relationships[relationshipName];
		var toObject = function (value) {
			if (value && typeof value==='object' && typeof value.id!=='undefined') {
				return value;
			}
			if (value===null) {
				return value;
			}
			if (typeof value!=='object') {
				return {id:value, type: definition.type};
			}
		}
		if (definition.isMultiple) {
			var ids=[];
			if (typeof value === 'string') {
				value.split(",").forEach(function (id) {
					ids.push(toObject(id));
				});
			} else if (value && typeof value.forEach==='function') {
				value.forEach(function (val) {
					ids.push(toObject(val));
				});
			} else if (value===null) {
				ids = [];
			} else {
				throw "Invalid relationship value";
			}
			self.relationships[relationshipName] = self.constructor.jsonapiParse(
				{
					data: ids
				}
			);
		} else {
			self.relationships[relationshipName] = self.constructor.jsonapiParse(
				{
					data: toObject(value)
				}
			);
		}
		return self.relationships[relationshipName];
	};
Nano.Resource.prototype.getListSelector = function (field) {
	var attribute = this.constructor.definition.attributes[field];
	var relationship = this.constructor.definition.relationships[field];
	return attribute ? attribute.list.resources() : relationship.list.resources();
};
Nano.Resource.types = {};
Nano.Resource.jsonapiParse = function (data, resourcesOwner, resourceBase) {
		var self = this;
		var result = null;
		if (data.data && typeof data.data.forEach==='function') {
			result = [];
			result.get = function(id){
				return this.find(function (item) {return item.id==id});
            };
			data.data.forEach(function (rowData) {
				var ResourceClass = self.getClassFor(rowData.type);
				var resource = new ResourceClass;
				resource.jsonapiDecode(rowData);
				resource.setResources(resourcesOwner);
				result.push(resource);
			});
		} else if (data.data) {
			var ResourceClass = self.getClassFor(data.data.type);
			result = resourceBase ? resourceBase : new ResourceClass;
			result.jsonapiDecode(data.data);
			result.setResources(resourcesOwner);
		}
		return result;
	};
Nano.Resource.getClassFor = function (type) {
		return typeof Nano.Resource.types[type]==='undefined' ? this : Nano.Resource.types[type];
	};

/**
 * Resources, collection class
 */
Nano.Resources = function () {
	Element.apply(this, arguments);
	this.url = null;
	this.resource = null;
	this.include = [];
	this.selection = [];
}
Nano.extends(Nano.Resources, Element);
Nano.Resources.prototype.makeUrl = function (base, path, query) {
		for(var name in query) {
			if (query[name]===undefined) {
				delete query[name];
			}
		}
		var params = $.param(query);
		return base + path + (params ? '?' + params : '');
	};
Nano.Resources.prototype.getUrl = function () {
		return this.url;
	};
Nano.Resources.prototype.getSelectUrl = function (id) {
		var self = this;
		var fields = [];
		var include = [];
		self.selection.forEach(function (field) {
			var attribute = self.resource.definition.attributes[field];
			var relationship = self.resource.definition.relationships[field];
			if (attribute) {
				fields.push(field);
			}
			if (relationship) {
				include.push(field);
			}
		});
		return self.makeUrl(
				API_SERVER,
				this.getUrl() +	(typeof id === 'undefined' ? '' : id ? '/' + id : '/create'),
				{
					fields: fields.length>0 ? fields.join(',') : undefined,
					include: include.length>0 ? include.join(',') : undefined
				}
			);
	};
Nano.Resources.prototype.getResourceUrl = function (id) {
		var self = this;
		return self.makeUrl(
				API_SERVER,
				this.getUrl() +	(typeof id === 'undefined' ? '' : '/'+id),
				{}
			);
	};
Nano.Resources.prototype.ajax = function(config, always) {
		return new Promise(function (resolve, reject) {
			$.ajax(config).done(resolve).fail(reject).always(always);
		});
	};
Nano.Resources.prototype.select = function () {
		var self = this;
		return self.ajax({
            method: "GET",
            url: self.getSelectUrl(),
            dataType: 'json',
		}).then(function (data) {
			return self.resource.jsonapiParse(data, self);
		});
	};
Nano.Resources.prototype.load = function (id, resource) {
		var self = this;
		return self.ajax({
            method: "GET",
            url: self.getSelectUrl(id),
            dataType: 'json',
		}).then(function(data){
			return self.resource.jsonapiParse(data, self, resource);
		});
	};
Nano.Resources.prototype.save = function (resource) {
		var self = this;
		var method = resource.id ? 'PUT' : 'POST';
		var url = self.getResourceUrl(resource.id?resource.id:undefined);
        return self.ajax({
            method: method,
            url: url,
            contentType: "application/json;charset=utf-8",
            dataType: 'json',
            data: JSON.stringify(resource.jsonapiEncode())
        }).then(function (data) {
        	if (data.data) {
	        	resource.jsonapiDecode(data.data);
        	}
        	return resource;
        });
	};
Nano.Resources.prototype.delete = function (resource) {
        var self = this;
        var method = 'DELETE';
        var url = self.getResourceUrl(resource.id);
        if (!resource.id) {
            throw "resource does not have id";
        }
        return self.ajax({
            method: method,
            url: url,
            contentType: "application/json;charset=utf-8",
            dataType: 'json'
        }).then(function () {
        	resource.id = 0;
        	return resource;
        });
    };
Nano.Resources.prototype.call = function (resource, method, params) {
        var self = this;
        var method = 'POST';
        var url = self.getResourceUrl(resource.id);
        return self.ajax({
            method: method,
            url: url,
            contentType: "application/json;charset=utf-8",
            dataType: 'json',
            data: JSON.stringify({
            	method: method,
            	arguments: params
            })
        }).then(function (response) {
            if (response.success) {
                return response.response;
            } else {
                throw new Exception(response.error);
            }
        });
    };
Nano.Resources.definition = {};