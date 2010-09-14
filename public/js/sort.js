var column = '';
var desc = false;
function sort(table, c) {
	desc = (column == c) && !desc;
	column = c;
	var tbody = table.getElementsByTagName('tbody')[0];
	var length = tbody.childNodes.length;
	var trs = new Array();
	for(var i=0; i < length; i++) {
		var node = tbody.removeChild(tbody.firstChild);
		if(node.nodeType == 1) {
			trs.push(node);
		}
	}
	trs = merge_sort(trs, tableColumnComparator);
	for(var i=0; i < trs.length; i++) {
		tbody.appendChild(trs[i]);
	}
}

function merge_sort(array,comparison)
{
	if(array.length < 2)
		return array;
	var middle = Math.ceil(array.length/2);
	return merge(merge_sort(array.slice(0,middle),comparison),
			merge_sort(array.slice(middle),comparison),
			comparison);
}

function merge(left,right,comparison)
{
	var result = new Array();
	while((left.length > 0) && (right.length > 0))
	{
		if(comparison(left[0],right[0])) {
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
}


function tableColumnComparator(node1, node2) {
	var node1_value = node1.getElementsByTagName('td')[column].innerHTML;
	var node2_value = node2.getElementsByTagName('td')[column].innerHTML;
	node1_value = node1_value.replace(/<[^>]*>/g, '').toLowerCase();
	node2_value = node2_value.replace(/<[^>]*>/g, '').toLowerCase();
	node1_value = node1_value.replace(/^\s*|\s*$/g, '').toLowerCase();
	node2_value = node2_value.replace(/^\s*|\s*$/g, '').toLowerCase();
	
	if(desc) {
		return node1_value >= node2_value;
	} else {
		return node1_value <= node2_value;
	}
}

