	var head = document.getElementsByTagName('head')[0];
	var style = document.createElement('style');
	
	var ruleText = '';
	ruleText += '.hour_line{ border-style: none none solid;border-width: 1px; vertical-align:top;}';
	ruleText += '	.non_business_hour_line{ border-style: none none solid; border-width: 1px; vertical-align:top; background-color:#D6D6D6;}';
	ruleText += '	.D_Calendar {font-family: Arial, Verdana, Helvetica; font-size:12px;font-weight: normal;margin:10px auto 10px auto;padding:3px; width: 200px; horizontal-align:center; vertical-align:top; background-color:#f7f7f7; border-width:0; border-style:none}';
	ruleText += '	.year_down {text-align:center;width:15px}';
	ruleText += '	.year_up {text-align:center;width:15px}';
	ruleText += '	.month_up {text-align:center;width:25px}';
	ruleText += '	.month_down {text-align:center;width:25px}';
	ruleText += '	.month_text {text-align:center;width:200px;font-weight:bold}';
	ruleText += '	.footer {text-align:center;}';
	ruleText += '	.day_table{border-collapse:separate;border-spacing:1px;}';
	ruleText += '	.day_name {text-align:center; text-align: center;font-size: 11px; background-color:#FFFFFF}';
	ruleText += '	.day {text-align:center; text-align: center;font-size: 11px;width:30px;border-width:1px;border-style:solid;border-color:#f7f7f7; }';
	ruleText += '	.evGlobal {text-align:center; text-align: center;font-size: 11px;width:30px;border-width:1px;border-style:solid;border-color:#f7f7f7; background-color:#D6F8CD; font-weight: bold; }';
	ruleText += '	.evCurso {text-align:center; text-align: center;font-size: 11px;width:30px;border-width:1px;border-style:solid;border-color:#f7f7f7; background-color:#FFD3BD; font-weight: bold; }';
	ruleText += '	.evUsuario {text-align:center; text-align: center;font-size: 11px;width:30px;border-width:1px;border-style:solid;border-color:#f7f7f7;background-color:#DCE7EC;font-weight:bold; }';
	ruleText += '	.activeDay {font-weight:bold;text-align:center; text-align: center;font-size: 11px;width:30px;border-color:#f7f7f7;border-width:1px;border-style:solid; }';
	ruleText += '	.today {text-align:center; text-align: center;font-size: 11px;width:30px; border-color:red;border-width:1px;border-style:solid; }';
	ruleText += '	.selected {text-align:center; text-align: center;font-size: 11px;width:30px; border-color:blue;border-width:1px;border-style:solid; }';
	ruleText += '	.activeToday {font-weight:bold;text-align:center; text-align: center;font-size: 11px;width:30px; border-color:red;border-width:1px;border-style:solid; }';
	ruleText += '	.activeSelected {font-weight:bold;text-align:center; text-align: center;font-size: 11px;width:30px; border-color:blue;border-width:1px;border-style:solid; }';
	ruleText += '	a.linkActive {font-weight:bold}';
	
	var rules = document.createTextNode(ruleText);

	style.type = 'text/css';
	if(style.styleSheet)
    		style.styleSheet.cssText = rules.nodeValue;
	else style.appendChild(rules);
	head.appendChild(style);