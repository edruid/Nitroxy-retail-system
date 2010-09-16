Sort = function() {
	this.defaultComparator = this.stringInsensitiveComparator;
	this.initial = '';
	this.desc = false;
	this.table = null;
};

Sort.prototype = {
	sort: function(c, comparator) {
		if(comparator == null) {
			this.comparator = this.defaultComparator;
		} else {
			this.comparator = comparator;
		}
		this.desc = (this.initial == c) && !this.desc;
		this.initial = c;
		var tbody = this.table.getElementsByTagName('tbody')[0];
		var length = tbody.childNodes.length;
		var trs = new Array();
		for(var i=0; i < length; i++) {
			var node = tbody.removeChild(tbody.firstChild);
			if(node.nodeType == 1) {
				trs.push(node);
			}
		}
		trs = this.merge_sort(trs);
		for(var i=0; i < trs.length; i++) {
			tbody.appendChild(trs[i]);
		}
	},

	merge_sort: function(array)
	{
		if(array.length < 2)
			return array;
		var middle = Math.ceil(array.length/2);
		return this.merge(this.merge_sort(array.slice(0,middle)),
				this.merge_sort(array.slice(middle)));
	},

	merge: function(left,right)
	{
		var result = new Array();
		while((left.length > 0) && (right.length > 0))
		{
			if(this.comparator(left[0],right[0])) {
				result.push(left.shift());
			} else {
				result.push(right.shift());
			}
		}
		while(left.length > 0)
			result.push(left.shift());
		while(right.length > 0)
			result.push(right.shift());
		return result;
	},

	stringComparator: function(node1, node2) {
		var node1_value = node1.getElementsByTagName('td')[this.initial].innerHTML;
		var node2_value = node2.getElementsByTagName('td')[this.initial].innerHTML;
		if(this.desc) {
			return node1_value >= node2_value;
		} else {
			return node1_value <= node2_value;
		}
	},

	stringInsensitiveComparator: function(node1, node2) {
		var node1_value = node1.getElementsByTagName('td')[this.initial].innerHTML;
		var node2_value = node2.getElementsByTagName('td')[this.initial].innerHTML;
		node1_value = node1_value.toLowerCase();
		node2_value = node2_value.toLowerCase();
		if(this.desc) {
			return node1_value >= node2_value;
		} else {
			return node1_value <= node2_value;
		}
	},

	tagInsensitiveComparator: function(node1, node2) {
		var node1_value = node1.getElementsByTagName('td')[this.initial].innerHTML;
		var node2_value = node2.getElementsByTagName('td')[this.initial].innerHTML;
		node1_value = node1_value.replace(/<[^>]*>/g, '').toLowerCase();
		node2_value = node2_value.replace(/<[^>]*>/g, '').toLowerCase();
		node1_value = node1_value.replace(/^\s*|\s*$/g, '');
		node2_value = node2_value.replace(/^\s*|\s*$/g, '');
		
		if(this.desc) {
			return node1_value >= node2_value;
		} else {
			return node1_value <= node2_value;
		}
	},

	numericComparator: function(node1, node2) {
		var node1_value = node1.getElementsByTagName('td')[this.initial].innerHTML;
		var node2_value = node2.getElementsByTagName('td')[this.initial].innerHTML;
		node1_value = node1_value.replace(/[^0-9.-]/g, '') * 1;
		node2_value = node2_value.replace(/[^0-9.-]/g, '') * 1;
		
		if(this.desc) {
			return node1_value >= node2_value;
		} else {
			return node1_value <= node2_value;
		}
	},
};

