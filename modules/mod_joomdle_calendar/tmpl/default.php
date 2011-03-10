<?php 
// no direct access
	defined('_JEXEC') or die('Restricted access');

	$path = JURI::root()."modules/mod_joomdle_calendar/";

	$itemid = JoomdleHelperContent::getMenuItem();

	if ($linkstarget == "new")
		$target = " target='_blank'";
	else $target = "";

	if ($linkstarget == 'wrapper')
	        $open_in_wrapper = 1;
	else
		$open_in_wrapper = 0;


	if (!$user->guest) 
	{ 
		$n_events = Array();
		$f_events = Array();
		$t_events = Array();
		$i = 0;
		foreach ($eventos as $evento) {
			$n_events[$i] = $evento["name"];
			$f_events[$i] = date("Ymd", $evento["timestart"]);
			if ($evento["courseid"] > 0) {
				if ($evento["courseid"] == 1) {
					$t_events[$i] = "s";
				} else {
					$t_events[$i] = "c";
				}
			} else if ($evento["courseid"]) {
				$t_events[$i] = "u";
			}
			$i++;
		}
	} else if ($guests_see_global)
	{
		$n_events = Array();
		$f_events = Array();
		$t_events = Array();
		$i = 0;
		foreach ($eventos as $evento) {
			if ($evento["courseid"] != 1)
				continue;

			$n_events[$i] = $evento["name"];
			$f_events[$i] = date("Ymd", $evento["timestart"]);
			$t_events[$i] = "s";
			$i++;
		}

	}
//print_r ($n_events);

//$start_monday = 0; //XXX
?>
<script type="text/javascript" language="javascript" src="<?php echo $path; ?>cal_functions.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $path; ?>cal_events.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $path; ?>cal_styles.js"></script>

<script  type="text/javascript" language="javascript">
var todayMonth;
var todayDay;
var todayDate;
var todayYear;
var today;
var txtCommands;

var months = new Array("<?php echo JText::_('JANUARY'); ?>", "<?php echo JText::_('FEBRUARY'); ?>", "<?php echo JText::_('MARCH'); ?>", "<?php echo JText::_('APRIL'); ?>", "<?php echo JText::_('MAY'); ?>", "<?php echo JText::_('JUNE'); ?>", "<?php echo JText::_('JULY'); ?>", "<?php echo JText::_('AUGUST'); ?>", "<?php echo JText::_('SEPTEMBER'); ?>", "<?php echo JText::_('OCTOBER'); ?>", "<?php echo JText::_('NOVEMBER'); ?>", "<?php echo JText::_('DECEMBER'); ?>");
var feb = (((todayYear % 4 == 0) && (todayYear % 100 != 0)) || (todayYear % 400 == 0)) ? 29 : 28;
var monthDays = new Array(31,feb,31,30,31,30,31,31,30,31,30,31);
<?php if ((!$user->guest) || ($guests_see_global)){ ?>
var myEventsNames = new Array('<?php echo implode("', '", $n_events); ?>');
var myEventsDates = new Array('<?php echo implode("', '", $f_events); ?>');
var myEventsTypes = new Array('<?php echo implode("', '", $t_events); ?>');
<?php } ?>

function renderCalendar() {
	document.getElementById("month_text").innerHTML = months[today.getMonth()] + " " + today.getFullYear();
	
	txtCommands = "";
	var row = parseInt(today.getDate()/7) + <?php echo $start_monday; ?>; 
	//+1
	if(today.getMonth() == todayMonth) {
		todayIndex = (row*7)+today.getDay() - <?php echo $start_monday; ?>;
	} else {
		todayIndex = -1;
	}
	
	//var id = today.getDate() - ((row*7)+today.getDay()-1);
	//var id = today.getDate() - ((row*7)+today.getDay() - <?php echo $start_monday; ?>);
	var id = today.getDate() - ((row*7)+today.getDay() - <?php echo $start_monday; ?>);
	if (id < -7)
	{
		id = id % 7;
		todayIndex = todayIndex - 7;
	}
//	alert (<?php echo $start_monday; ?>);
//	var id = today.getDate() - ((row*7)+today.getDay()) - <?php echo $start_monday; ?>;
	
	for(var x = 0;x<42;x++) {
		var thisMonthDays = monthDays[today.getMonth()];
		var thisText = "";
		var thisURL = "";
		var thisTarget = "<?php echo $target; ?>";
		var fecha = today.getFullYear();
		var mes = today.getMonth()+1;
		if (mes < 10) { fecha = fecha.toString() + '0' + mes; } else { fecha = fecha.toString() + mes; }
		if (id < 10) { fecha = fecha.toString() + '0' + id; } else { fecha = fecha.toString() + id; }
		
	<?php if (($user->guest) && (!$guests_see_global)){ ?>
		if((id <= 0) || (id > thisMonthDays)) {
			thisText = "";
			thisURL = "#";
		} else {
			thisText = id;
			thisURL = "#";
		}
	<?php } else { ?>
		if((id <= 0) || (id > thisMonthDays)) {
			thisText = "";
			thisURL = "#";
		} else {
			thisURL = "<?php echo $moodle_auth_land_url; ?>?username=<?php echo $username; ?>&token=<?php echo $token; ?>&mtype=event&id=<?php echo $id; ?>&use_wrapper=<?php echo $open_in_wrapper; ?>&Itemid=<?php echo $itemid; ?>&day=" + id + "&mon=" + mes + "&year=" + today.getFullYear();
			thisText = id;
		}
	<?php } ?>
		
		document.getElementById("link_" + x).href = thisURL; 
		if (thisTarget != "") { document.getElementById("link_" + x).target = thisTarget; } 
		document.getElementById("link_" + x).innerHTML = thisText; 
		
	<?php if ((!$user->guest) || ($guests_see_global)) { ?>
		for (idx=0; idx<myEventsDates.length; idx++) {
			if (myEventsDates[idx] == fecha) {
				if (myEventsTypes[idx] == "s") {
					txtCommands += "document.getElementById(\"day_" + x + "\").className = \"evGlobal\"|";
				} else if (myEventsTypes[idx] == "c") {
					txtCommands += "document.getElementById(\"day_" + x + "\").className = \"evCurso\"|";
				} else {
					txtCommands += "document.getElementById(\"day_" + x + "\").className = \"evUsuario\"|";
				}
				document.getElementById("link_" + x).title = myEventsNames[idx];
			}
		}
	<?php } ?>
		id++;
	}
	
	if (txtCommands != "") { txtCommands = txtCommands.substr(0, txtCommands.length-1); }
	renderFunctions();
}

function loadCalendar() {
	loadToday();
	renderCalendar();
}
 
function minusMonth() {
	today.setMonth(today.getMonth()-1);
	selectedIndex = -1;
	clearLinkFormatting();
	renderCalendar();
	monthChanged();
}

function plusMonth() {
	today.setMonth(today.getMonth()+1);
	selectedIndex = -1;
	clearLinkFormatting();
	renderCalendar();
	monthChanged();
}

function loadToday(){
	today = new Date();
	todayMonth = today.getMonth();
//	todayDay = today.getDay() - <?php echo $start_monday; ?>;
	todayDay = today.getDay() - 1;
//	alert (todayDay);
	todayDate = today.getDate();
	todayYear = today.getFullYear();
}
</script>

<div id="D_Calendar" class="D_Calendar">
	<table width="100%" align="center">
		<tr>
			<td id="year_down" class="year_down"></td>
			<td id="month_down" class="month_down"><a href="#IsRet" onclick="minusMonth();">◄</a></td>
			<td id="month_text" class="month_text">December</td>
			<td id="month_up" class="month_up"><a href="#IsRet" onclick="plusMonth();">►</a></td>
			<td id="year_up" class="year_up"></td>
		</tr>
		<tr><td colspan="5">
			<table width="100%" id="day_table" class="day_table">
				<tr>
				<? if ($start_monday) : ?>
					<td id="day_name_0" class="day_name"><?php echo JText::_('MON'); ?></td>
					<td id="day_name_1" class="day_name"><?php echo JText::_('TUE'); ?></td>
					<td id="day_name_2" class="day_name"><?php echo JText::_('WED'); ?></td>
					<td id="day_name_3" class="day_name"><?php echo JText::_('THU'); ?></td>
					<td id="day_name_4" class="day_name"><?php echo JText::_('FRI'); ?></td>
					<td id="day_name_5" class="day_name"><?php echo JText::_('SAT'); ?></td>
					<td id="day_name_6" class="day_name"><?php echo JText::_('SUN'); ?></td>
				<? else : ?>
					<td id="day_name_0" class="day_name"><?php echo JText::_('SUN'); ?></td>
					<td id="day_name_1" class="day_name"><?php echo JText::_('MON'); ?></td>
					<td id="day_name_2" class="day_name"><?php echo JText::_('TUE'); ?></td>
					<td id="day_name_3" class="day_name"><?php echo JText::_('WED'); ?></td>
					<td id="day_name_4" class="day_name"><?php echo JText::_('THU'); ?></td>
					<td id="day_name_5" class="day_name"><?php echo JText::_('FRI'); ?></td>
					<td id="day_name_6" class="day_name"><?php echo JText::_('SAT'); ?></td>
				<? endif; ?>
				</tr>
		<?php for ($i=0; $i<6; $i++) { ?>
				<tr>
			<?php for ($j=$i*7; $j<($i*7)+7; $j++) { ?>
					<td id="day_<?php echo $j; ?>" class="day"><a id="link_<?php echo $j; ?>" href="#" onclick="setSelectedDay(<?php echo $j; ?>); return false;">0</a></td>
			<?php } ?>
				</tr>
		<?php } ?>
			</table>
		</td></tr>
	<?php if (!$user->guest) { ?>
		<tr><td colspan="5">
			<table width="100%">
				<tr>
					<td colspan="3" style="font-weight: bold; border-top-width: 1px; border-top-style: solid"><?= JText::_('CJ EVENT KEY'); ?>:</td>
				</tr>
				<tr>
					<td style="background-color: #D6F8CD; text-align: center"><?= JText::_('CJ GLOBAL'); ?></td>
					<td style="background-color: #FFD3BD; text-align: center"><?= JText::_('CJ COURSE'); ?></td>
					<td style="background-color: #DCE7EC; text-align: center"><?= JText::_('CJ USER'); ?></td>
				</tr>
			</table>
		</td></tr>
	<?php } ?>
	</table>
</div>
<script  type="text/javascript" language="javascript">loadCalendar();</script>
