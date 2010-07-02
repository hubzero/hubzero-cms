<?php

	$Material=array("Document","Photo","Video","Table","Map","Illustration","Other");

  $TemplateMData = array(
    0=>array("TsunamiSiteConfiguration","configDescription"),
    1=>array("TsunamiSiteConfiguration","configTopography"),
    2=>array("TsunamiSiteConfiguration","configBathymetry"),
    3=>array("TsunamiSiteConfiguration","configVisuals"),

    4=>array("TsunamiSocialScienceData","bkg"),
    5=>array("TsunamiSocialScienceData","impact"),
    6=>array("TsunamiSocialScienceData","comm"),
    7=>array("TsunamiSocialScienceData","iresponse"),
    8=>array("TsunamiSocialScienceData","cresponse"),
    9=>array("TsunamiSocialScienceData","oresponse"),
    10=>array("TsunamiSocialScienceData","damage"),

    11=>array("TsunamiHydrodynamicData","runup"),
    12=>array("TsunamiHydrodynamicData","inundation"),
    13=>array("TsunamiHydrodynamicData","tidegauge"),
    14=>array("TsunamiHydrodynamicData","flow"),
    15=>array("TsunamiHydrodynamicData","wave"),
    16=>array("TsunamiHydrodynamicData","econdition"),

    17=>array("TsunamiSeismicData","local"),
    18=>array("TsunamiSeismicData","mia"),
    19=>array("TsunamiSeismicData","measures"),

    20=>array("TsunamiGeologicalData","fault"),
    21=>array("TsunamiGeologicalData","displacement"),
    22=>array("TsunamiGeologicalData","tdcbm"),
    23=>array("TsunamiGeologicalData","gmchanges"),
    24=>array("TsunamiGeologicalData","eil"),
    25=>array("TsunamiGeologicalData","smsl"),
    26=>array("TsunamiGeologicalData","paleo"),

    27=>array("TsunamiEngineeringData","event"),
    28=>array("TsunamiEngineeringData","structure"),
    29=>array("TsunamiEngineeringData","lifeline"),
    30=>array("TsunamiEngineeringData","geotech"),
    31=>array("TsunamiEngineeringData","hm"),

    32=>array("TsunamiBiologicalData","flora"),
    33=>array("TsunamiBiologicalData","fauna"),
    34=>array("TsunamiBiologicalData","marineBiology"));



	$Category=array(
		1=>array("General Site Configuration","TsunamiSiteConfiguration"),
		2=>array("Social Sciences Data","TsunamiSocialScienceData"),
		3=>array("Hydrodynamic Data","TsunamiHydrodynamicData"),
		4=>array("Seismic Data","TsunamiSeismicData"),
		5=>array("Geological Data","TsunamiGeologicalData"),
		6=>array("Engineering Data","TsunamiEngineeringData"),
		7=>array("Biological Data","TsunamiBiologicalData")
	);

	$subCategory = array (
		1=>array("Description","configDescription","CONFIG_DESCRIPTION"),
		2=>array("Topography","configTopography","CONFIG_TOPOGRAPHY"),
		3=>array("Bathymetry","configBathymetry","CONFIG_BATHYMETRY"),
		4=>array("Maps, Sketches, and Other Visuals","configVisuals","CONFIG_VISUALS"),

		5=>array("Background Information","bkg","BKG"),
		6=>array("Human Impact","impact","IMPACT"),
		7=>array("Communication","comm","COMM"),
		8=>array("Individual Response","iresponse","IRESPONSE"),
		9=>array("Community Response","cresponse","CRESPONSE"),
		10=>array("Organizational Response","oresponse","ORESPONSE"),
		11=>array("Damage & Loss","damage","DAMAGE"),

		12=>array("Run-up Heights","runup","RUNUP"),
		13=>array("Extent of Inundation","inundation","INUNDATION"),
		14=>array("Tide-Gauge Data","tidegauge","TIDEGAUGE"),
		15=>array("Flow","flow","FLOW"),
		16=>array("Wave Structure","wave","WAVE"),
		17=>array("Conditions at Time of Tsunami","econdition","ECONDITION"),

		18=>array("Local Seismographs","local","LOCAL"),
		19=>array("Macroscopic Intensity Assessment","mia","MIA"),
		20=>array("Post-Event Measurements","measures","MEASURES"),

		21=>array("Surface Fault","fault","FAULT"),
		22=>array("Tectonic Displacement","displacement","DISPLACEMENT"),
		23=>array("Tsunami Deposits & Clast/Boulder Movement","tdcbm","TDCBM"),
		24=>array("Geomorphological Changes","gmchanges","GMCHANGES"),
		25=>array("Earthquake Induced Liquefaction","eil","EIL"),
		26=>array("Submarine & Subaerial Landslides","smsl","SMSL"),
		27=>array("Paleo-Tsunami Data","paleo","PALEO"),

		28=>array("Event Data","event","EVENT"),
		29=>array("Structural Damage","structure","STRUCTURE"),
		30=>array("Lifeline Damage","lifeline","LIFELINE"),
		31=>array("Geotechnical Damage","geotech","GEOTECH"),
		32=>array("Pre-event Hazards and Mitigation","hm","HM"),

		33=>array("Flora","flora","FLORA"),
		34=>array("Fauna","fauna","FAUNA"),
		35=>array("Marine Biology","marineBiology","MARINE_BIOLOGY")
	);

	$attributes = array (
		1=>array(1=>array(array()
					  ),
					2=>array(array()
                 ),
					3=>array(array()
                 ),
					4=>array(array()
                 )
					),
		2=>array(5=>array(
								array("bkgCensus","Census Data"),
								array("bkgTransportSystems","Transportation Systems"),
								array("bkgTouristStats","Tourist Statistics"),
								array("bkgLanguageIssues","Language Issues")
							  ),
					6=>array(
								array("impactNumDead","# Dead"),
								array("impactNumInjured","# Injured"),
								array("impactNumMissing","# Missing"),
								array("impactNumHomeless","# Homeless"),
								array("impactNumFamSep","# Families Separated")
							  ),
					7=>array(
								array("commWarnSys","Warning Systems"),
								array("commInfoFromG","Information from Government or Media")
							  ),
					8=>array(
								array("iresponsePrep","Preparedness"),
								array("iresponseWarnings","Warnings"),
								array("iresponseRecovery","Recovery"),
								array("iresponseMitigation","Mitigation"),
								array("iresponseIntervw","Interviews/Reports about (or with) Individuals")
							  ),
					9=>array(
								array("cresponsePrep","Preparedness"),
								array("cresponseWarning","Warnings"),
								array("cresponseRecovery","Recovery"),
								array("cresponseMitigation","Mitigation"),
								array("cresponseIntervw","Interviews/Reports about (or with) Community Officials")
							  ),
					10=>array(
								array("oresponseGrelief","Government Relief Response"),
								array("oresponseNGORelief","Non-governmental Relief Response"),
								array("oresponsePrep","Preparedness"),
								array("oresponseRecovery","Recovery"),
								array("oresponseMitigation","Mitigation"),
								array("oresponseDisease","Disease Prevention"),
								array("oresponseIntervw","Interviews/Reports about (or with) Public Agencies")
							  ),
					11=>array(
								array("damageCostEst","Cost Estimates"),
								array("damageIndustry","Industries Affected"),
								array("damageType","Types of Damage")
							  )
					),
		3=>array(12=>array(
								array("runupSource","Source of Measurement"),
								array("runupHeight","Measured Height above Terrain"),
								array("runupPoRLoc","Point-of-Reference Location"),
								array("runupPoRHeight","Measured Height using Point-of-Reference"),
								array("runupTidalAdj","Runup Height Adjusted for Tide"),
								array("runupAdjMethod","Tidal Level Adjustment Method"),
								array("runupQuality","Evaluation of Quality")
							  ),
					13=>array(
								array("inundationSource","Source of Measurement"),
								array("inundationDist","Measured Distance"),
								array("inundationQuality","Evaluation of Quality")
							  ),
					14=>array(
								array("tidegaugeSource","Data Source"),
								array("tidegaugeType","Tide Gauge Type")
							  ),
					15=>array(
								array("flowSource","Source of Measurement"),
								array("flowDirection","Direction"),
								array("flowSpeed","Speed")
							  ),
					16=>array(
								array("waveSource","Source of Estimate"),
								array("waveNumber","# Waves"),
								array("waveArrivalTimes","Arrival Time(s)"),
								array("waveForm","Wave Form"),
								array("waveHeight","Wave Height"),
								array("wavePeriod","Wave Period"),
								array("waveTimeToNorm","Time When Sea Returned to Normal")
							  ),
					17=>array(
								array("conditionSource","Source of Estimate"),
								array("conditionWind","Wind Conditions"),
								array("conditionSea","Sea Conditions"),
								array("conditionWeather","Weather Conditions")
					    	  )
					),
		4=>array(18=>array(
								array("localType","Seismograph Type"),
								array("localDataSources","Data Sources")
							  ),
					19=>array(
								array("miaSource","Source of Measurements")
							  ),
					20=>array(
								array("measuresTypes","Types"),
								array("measuresSiteConfig","Site Configuration")
							  )
					),
		5=>array(21=>array(
								array("faultType","Type"),
								array("faultStrikeMeasure","Strike Measurement"),
								array("faultOffset","Offset Measurement"),
								array("faultGeomorphic","Geomorphic Expression"),
								array("faultPaleo","Paleoseismic Fault"),
							  ),
					22=>array(
								array("displacementUplift","Uplift"),
								array("displacementSubsidence","Subsidence"),
							  ),
					23=>array(
								array("tdcbmElevation","Elevation"),
								array("tdcbmDistInland","Distance Inland"),
								array("tdcbmScale","Scale or Dimension"),
								array("tdcbmSpatialVar","Spatial Variation"),
								array("tdcbmCharacteristics","Characteristics"),
							  ),
					24=>array(
								array("gmchangesScour","Scour"),
								array("gmchangesDeposit","Deposition"),
								array("gmchangesBedMod","Bedrock Modification"),
							  ),
					25=>array(
								array("eilElevation","Elevation"),
								array("eilDistinland","Distance Inland"),
								array("eilCharacteristics","Characteristics"),
							  ),
					26=>array(
								array("sslScars","Scars"),
								array("sslDeposits","Deposits"),
								array("sslCoefficientOfFriction","Coefficient of Friction"),
							  ),
					27=>array(
								array("paleoElevation","Elevation"),
								array("paleoDistInland","Distance Inland"),
								array("paleoScale","Scale or Dimension"),
								array("paleoSpatialVar","Spatial Variation"),
								array("paleoCharacteristics","Characteristics"),
								array("paleoOutcrops","Outcrops"),
								array("paleoSedPeels","Sediment Peels"),
								array("paleoCoreSamples","Core Samples"),
							  )
					),
		6=>array(28=>array(
								array("eventSensorData","Sensor Data"),
								array("eventVideo","Video Data"),
							  ),
					29=>array(
								array("structureType","Structure Type"),
								array("structureDamageDescription","Description of Damage"),
								array("structureDesign","Design Drawings/Documents"),
								array("structureYear","Year Designed"),
								array("structureSeismicDesign","Seismic Design Criteria"),
								array("structureVulAssessment","Vulnerability Assessment"),
							  ),
					30=>array(
								array("lifelineType","Lifeline Type"),
								array("lifelineDamageDescription","Description of Damage"),
								array("lifelineDesign","Design Drawings/Documents"),
								array("lifelineYear","Year Designed"),
								array("lifelineSeismicDesign","Seismic Design Criteria"),
								array("lifelineVulAssessment","Vulnerability Assessment"),
							  ),
					31=>array(
								array("geotechSiteChar","Site Characterization"),
								array("geotechSoilChar","Soil Characterization"),
								array("geotechDamageDescr","Description of Damage"),
								array("geotechVulAssessment","Vulnerability Assessment"),
							  ),
					32=>array(
								array("hmHazardAssessment","Hazard Assessment"),
								array("hmHazardMaps","Hazard Maps"),
								array("hmFaultMaps","Fault Maps"),
								array("hmEvacPlanMaps","Evacuation Planning Runup Maps"),
								array("hmShelterLocations","Shelter Locations"),
							  )
				),
		7=>array(33=>array(array()
					  			),
					34=>array(array()
                 			),
					35=>array(array()
								)
				)
	);

?>
