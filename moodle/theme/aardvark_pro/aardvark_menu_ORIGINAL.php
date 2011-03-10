<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE abiword PUBLIC "-//ABISOURCE//DTD AWML 1.0 Strict//EN" "http://www.abisource.com/awml.dtd">
<abiword template="false" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:math="http://www.w3.org/1998/Math/MathML" xid-max="146" xmlns:dc="http://purl.org/dc/elements/1.1/" fileformat="1.1" xmlns:svg="http://www.w3.org/2000/svg" xmlns:awml="http://www.abisource.com/awml.dtd" xmlns="http://www.abisource.com/awml.dtd" xmlns:xlink="http://www.w3.org/1999/xlink" version="2.8.1" xml:space="preserve" props="dom-dir:ltr; document-footnote-restart-section:0; document-endnote-type:numeric; document-endnote-place-enddoc:1; document-endnote-initial:1; lang:en-US; document-endnote-restart-section:0; document-footnote-restart-page:0; document-footnote-type:numeric; document-footnote-initial:1; document-endnote-place-endsection:0">
<!-- ======================================================================== -->
<!-- This file is an AbiWord document.                                        -->
<!-- AbiWord is a free, Open Source word processor.                           -->
<!-- More information about AbiWord is available at http://www.abisource.com/ -->
<!-- You should not edit this file by hand.                                   -->
<!-- ======================================================================== -->

<metadata>
<m key="dc.format">application/x-abiword</m>
<m key="abiword.generator">AbiWord</m>
</metadata>
<history version="1" edit-time="31" last-saved="1270138315" uid="37e58c20-3da9-11df-9dce-d545e05b7753">
<version id="1" started="1270138315" uid="4a8fe7b2-3da9-11df-9dce-d545e05b7753" auto="0" top-xid="146"/>
</history>
<styles>
<s type="P" name="Normal" basedon="" followedby="Current Settings" props="font-family:Liberation Serif; margin-top:0pt; font-variant:normal; margin-left:0pt; text-indent:0in; widows:2; font-style:normal; font-weight:normal; text-decoration:none; color:000000; line-height:1.0; text-align:left; margin-bottom:0pt; text-position:normal; margin-right:0pt; bgcolor:transparent; font-size:12pt; font-stretch:normal"/>
</styles>
<pagesize pagetype="A4" orientation="portrait" width="210.000000" height="297.000000" units="mm" page-scale="1.000000"/>
<section xid="1">
<p style="Normal" xid="2" props="text-align:left; dom-dir:ltr">&lt;div id="aardvark_menu_date"&gt;</p>
<p style="Normal" xid="3" props="text-align:left; dom-dir:ltr">&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/calendar/view.php' ?&gt;"&gt;&lt;script language="Javascript" type="text/javascript"&gt;</p>
<p style="Normal" xid="4" props="text-align:left; dom-dir:ltr">//&lt;![CDATA[</p>
<p style="Normal" xid="5">&lt;!--</p>
<p style="Normal" xid="6"><c></c></p>
<p style="Normal" xid="7" props="text-align:left; dom-dir:ltr">// Get today's current date.</p>
<p style="Normal" xid="8" props="text-align:left; dom-dir:ltr">var now = new Date();</p>
<p style="Normal" xid="9"><c></c></p>
<p style="Normal" xid="10" props="text-align:left; dom-dir:ltr">// Array list of days.</p>
<p style="Normal" xid="11" props="text-align:left; dom-dir:ltr">var days = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');</p>
<p style="Normal" xid="12"><c></c></p>
<p style="Normal" xid="13" props="text-align:left; dom-dir:ltr">// Array list of months.</p>
<p style="Normal" xid="14" props="text-align:left; dom-dir:ltr">var months = new Array('January','February','March','April','May','June','July','August','September','October','November','December');</p>
<p style="Normal" xid="15"><c></c></p>
<p style="Normal" xid="16" props="text-align:left; dom-dir:ltr">// Calculate the number of the current day in the week.</p>
<p style="Normal" xid="17" props="text-align:left; dom-dir:ltr">var date = ((now.getDate()&lt;10) ? "0" : "")+ now.getDate();</p>
<p style="Normal" xid="18"><c></c></p>
<p style="Normal" xid="19" props="text-align:left; dom-dir:ltr">// Calculate four digit year.</p>
<p style="Normal" xid="20" props="text-align:left; dom-dir:ltr">function fourdigits(number)     {</p>
<p style="Normal" xid="21" props="text-align:left; dom-dir:ltr">        return (number &lt; 1000) ? number + 1900 : number;</p>
<p style="Normal" xid="22">                                                                }</p>
<p style="Normal" xid="23"><c></c></p>
<p style="Normal" xid="24" props="text-align:left; dom-dir:ltr">// Join it all together</p>
<p style="Normal" xid="25" props="text-align:left; dom-dir:ltr">today =  days[now.getDay()] + " " +</p>
<p style="Normal" xid="26" props="text-align:left; dom-dir:ltr">              date + " " +</p>
<p style="Normal" xid="27" props="text-align:left; dom-dir:ltr">                          months[now.getMonth()] + " " +               </p>
<p style="Normal" xid="28" props="text-align:left; dom-dir:ltr">                (fourdigits(now.getYear())) ;</p>
<p style="Normal" xid="29"><c></c></p>
<p style="Normal" xid="30" props="text-align:left; dom-dir:ltr">// Print out the data.</p>
<p style="Normal" xid="31" props="text-align:left; dom-dir:ltr">document.write("" +today+ " ");</p>
<p style="Normal" xid="32">  </p>
<p style="Normal" xid="33">//--&gt;</p>
<p style="Normal" xid="34">//]]&gt;</p>
<p style="Normal" xid="35" props="text-align:left; dom-dir:ltr">&lt;/script&gt;&lt;/a&gt;</p>
<p style="Normal" xid="36">	</p>
<p style="Normal" xid="37" props="text-align:left; dom-dir:ltr">	&lt;/div&gt;</p>
<p style="Normal" xid="38">    </p>
<p style="Normal" xid="39" props="text-align:left; dom-dir:ltr">&lt;ul&gt;</p>
<p style="Normal" xid="40">     </p>
<p style="Normal" xid="41" props="text-align:left; dom-dir:ltr">       &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;&lt;img width="18" height="17" src="&lt;?php echo $CFG-&gt;httpswwwroot.'/theme/'.current_theme() ?&gt;/images/menu/home_icon.png" alt=""/&gt;&lt;/a&gt;&lt;/div&gt;</p>
<p style="Normal" xid="42" props="text-align:left; dom-dir:ltr">       &lt;/li&gt; </p>
<p style="Normal" xid="43"> </p>
<p style="Normal" xid="44" props="text-align:left; dom-dir:ltr">        &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;Menu One&lt;/a&gt;</p>
<p style="Normal" xid="45">					</p>
<p style="Normal" xid="46" props="text-align:left; dom-dir:ltr">        &lt;ul&gt;</p>
<p style="Normal" xid="47" props="text-align:left; dom-dir:ltr">        &lt;h4&gt;Subtitle Text&lt;/h4&gt;</p>
<p style="Normal" xid="48"><c></c></p>
<p style="Normal" xid="49" props="text-align:left; dom-dir:ltr">        &lt;?php</p>
<p style="Normal" xid="50"><c></c></p>
<p style="Normal" xid="51" props="text-align:left; dom-dir:ltr"> $text ='&lt;li&gt;&lt;a href=""&gt;Item One&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="52" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Two&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="53" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Three&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="54" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Four&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="55" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Five&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="56" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Six&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="57" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Seven&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="58" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Eight&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="59"> </p>
<p style="Normal" xid="60" props="text-align:left; dom-dir:ltr"> echo $text;</p>
<p style="Normal" xid="61">?&gt;</p>
<p style="Normal" xid="62"><c></c></p>
<p style="Normal" xid="63" props="text-align:left; dom-dir:ltr">           &lt;/ul&gt;&lt;/div&gt;</p>
<p style="Normal" xid="64"> </p>
<p style="Normal" xid="65" props="text-align:left; dom-dir:ltr">        &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;Menu Two&lt;/a&gt;</p>
<p style="Normal" xid="66">					</p>
<p style="Normal" xid="67" props="text-align:left; dom-dir:ltr">        &lt;ul&gt;</p>
<p style="Normal" xid="68" props="text-align:left; dom-dir:ltr">        &lt;h4&gt;Subtitle Text&lt;/h4&gt;</p>
<p style="Normal" xid="69"><c></c></p>
<p style="Normal" xid="70" props="text-align:left; dom-dir:ltr">        &lt;?php</p>
<p style="Normal" xid="71"><c></c></p>
<p style="Normal" xid="72" props="text-align:left; dom-dir:ltr"> $text ='&lt;li&gt;&lt;a href=""&gt;Item One&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="73" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Two&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="74" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Three&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="75" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Four&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="76" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Five&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="77" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Six&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="78" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Seven&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="79" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Eight&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="80"> </p>
<p style="Normal" xid="81" props="text-align:left; dom-dir:ltr"> echo $text;</p>
<p style="Normal" xid="82">?&gt;</p>
<p style="Normal" xid="83"><c></c></p>
<p style="Normal" xid="84" props="text-align:left; dom-dir:ltr">           &lt;/ul&gt;&lt;/div&gt;</p>
<p style="Normal" xid="85">           </p>
<p style="Normal" xid="86" props="text-align:left; dom-dir:ltr">        &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;Menu Three&lt;/a&gt;</p>
<p style="Normal" xid="87">					</p>
<p style="Normal" xid="88" props="text-align:left; dom-dir:ltr">        &lt;ul&gt;</p>
<p style="Normal" xid="89" props="text-align:left; dom-dir:ltr">        &lt;h4&gt;Subtitle Text&lt;/h4&gt;</p>
<p style="Normal" xid="90"><c></c></p>
<p style="Normal" xid="91" props="text-align:left; dom-dir:ltr">        &lt;?php</p>
<p style="Normal" xid="92"><c></c></p>
<p style="Normal" xid="93" props="text-align:left; dom-dir:ltr"> $text ='&lt;li&gt;&lt;a href=""&gt;Item One&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="94" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Two&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="95" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Three&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="96" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Four&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="97" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Five&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="98" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Six&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="99" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Seven&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="100" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Eight&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="101"> </p>
<p style="Normal" xid="102" props="text-align:left; dom-dir:ltr"> echo $text;</p>
<p style="Normal" xid="103">?&gt;</p>
<p style="Normal" xid="104">	</p>
<p style="Normal" xid="105"><c></c></p>
<p style="Normal" xid="106" props="text-align:left; dom-dir:ltr">           &lt;/ul&gt;&lt;/div&gt;</p>
<p style="Normal" xid="107" props="text-align:left; dom-dir:ltr">                &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;Menu Four&lt;/a&gt;</p>
<p style="Normal" xid="108">					</p>
<p style="Normal" xid="109" props="text-align:left; dom-dir:ltr">        &lt;ul&gt;</p>
<p style="Normal" xid="110" props="text-align:left; dom-dir:ltr">        &lt;h4&gt;Subtitle Text&lt;/h4&gt;</p>
<p style="Normal" xid="111"><c></c></p>
<p style="Normal" xid="112" props="text-align:left; dom-dir:ltr">        &lt;?php</p>
<p style="Normal" xid="113"><c></c></p>
<p style="Normal" xid="114" props="text-align:left; dom-dir:ltr"> $text ='&lt;li&gt;&lt;a href=""&gt;Item One&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="115" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Two&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="116" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Three&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="117" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Four&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="118" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Five&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="119" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Six&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="120" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Seven&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="121" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Eight&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="122"> </p>
<p style="Normal" xid="123" props="text-align:left; dom-dir:ltr"> echo $text;</p>
<p style="Normal" xid="124">?&gt;</p>
<p style="Normal" xid="125" props="text-align:left; dom-dir:ltr">           &lt;/ul&gt;&lt;/div&gt;</p>
<p style="Normal" xid="126">           </p>
<p style="Normal" xid="127" props="text-align:left; dom-dir:ltr">        &lt;li&gt;&lt;div&gt;&lt;a href="&lt;?php echo $CFG-&gt;wwwroot.'/' ?&gt;"&gt;Menu Five&lt;/a&gt;</p>
<p style="Normal" xid="128">					</p>
<p style="Normal" xid="129" props="text-align:left; dom-dir:ltr">        &lt;ul&gt;</p>
<p style="Normal" xid="130" props="text-align:left; dom-dir:ltr">        &lt;h4&gt;Subtitle Text&lt;/h4&gt;</p>
<p style="Normal" xid="131"><c></c></p>
<p style="Normal" xid="132" props="text-align:left; dom-dir:ltr">        &lt;?php</p>
<p style="Normal" xid="133"><c></c></p>
<p style="Normal" xid="134" props="text-align:left; dom-dir:ltr"> $text ='&lt;li&gt;&lt;a href=""&gt;Item One&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="135" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Two&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="136" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Three&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="137" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Four&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="138" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Five&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="139" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Six&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="140" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Seven&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="141" props="text-align:left; dom-dir:ltr"> $text .='&lt;li&gt;&lt;a href=""&gt;Item Eight&lt;/a&gt;&lt;/li&gt;';</p>
<p style="Normal" xid="142"> </p>
<p style="Normal" xid="143" props="text-align:left; dom-dir:ltr"> echo $text;</p>
<p style="Normal" xid="144">?&gt; 			</p>
<p style="Normal" xid="145"><c></c></p>
<p style="Normal" xid="146" props="text-align:left; dom-dir:ltr">           &lt;/ul&gt;&lt;/div&gt;</p>
</section>
</abiword>
