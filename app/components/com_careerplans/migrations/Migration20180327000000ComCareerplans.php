<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for installing career plan default questions
 **/
class Migration20180327000000ComCareerplans extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__careerplans_fieldsets'))
		{
			$query = "SELECT COUNT(*) FROM `#__careerplans_fieldsets`;";
			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$query = "INSERT INTO `#__careerplans_fieldsets` (`id`, `name`, `label`, `description`, `ordering`, `created`, `created_by`, `modified`, `modified_by`)
				VALUES
					(1,'introduction','Introduction',NULL,1,'2018-01-24 16:23:03',1001,NULL,0),
					(2,'researchskills','Skills Assessment: Research',NULL,2,'2018-01-24 16:23:03',1001,NULL,0),
					(3,'responsibleconduct','Skills Assessment: Responsible Conduct of Research',NULL,3,'2018-01-24 16:23:03',1001,NULL,0),
					(4,'communication','Skills Assessment: Communication',NULL,4,'2018-01-24 16:23:03',1001,NULL,0),
					(5,'professionalism','Skills Assessment: Professionalism',NULL,5,'2018-01-24 16:23:03',1001,NULL,0),
					(6,'management','Skills Assessment: Management & Leadership Skills',NULL,6,'2018-01-24 16:23:03',1001,NULL,0),
					(7,'planning','Skills Assessment: Career Planning',NULL,7,'2018-01-24 16:23:03',1001,NULL,0),
					(8,'values','Values Assessment',NULL,8,'2018-01-24 16:23:03',1001,NULL,0),
					(9,'goals','Goals',NULL,9,'2018-01-24 16:23:03',1001,NULL,0);";

				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__careerplans_fields'))
		{
			$query = "SELECT COUNT(*) FROM `#__careerplans_fields`;";
			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$query = "INSERT INTO `#__careerplans_fields` (`id`, `fieldset_id`, `type`, `name`, `label`, `placeholder`, `description`, `ordering`, `access`, `option_other`, `option_blank`, `required`, `readonly`, `disabled`, `min`, `max`, `rows`, `cols`, `default_value`, `created`, `created_by`, `modified`, `modified_by`, `parent_option`, `validate`)
				VALUES
					(1,1,'paragraph','intro','Intro',NULL,'<p>To achieve success in your career, you need more than an academic degree. There are technical competencies that will be directly relevant to a career in science, such as experimental design, data collection, and data analysis. There are also other professional competencies that are necessary and are transferable to other fields of work.  These include time management, planning, communication, and collaboration.  An Individual Development Plan maps out your strengths and weaknesses and allows you to identify what support you may need to work towards your goals.</p><p>Typically the Individual Development Plan (IDP) is based on the assumption that you can articulate a long term career goal on a horizon of 15 years or longer. At this stage of your career, you may not be ready for that.  In this exercise, we invite you to think about your short term career goals and how the SPUR Fellowship program may help you attain those goals.</p><p>You may begin this exercise before you arrive at your research site.  The first part of this exercise involves a self-assessment of your skills and values. This is to help you reflect on your strengths and weaknesses and illuminate what is important to you.</p>\r',1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(2,2,'scale','technicalskills','Technical skills related to my specific research area',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(3,2,'scale','experimentaldesign','Experimental design',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(4,2,'paragraph','instructions','Instructions',NULL,'Assess your Proficiency in these areas on a scale of 1 = Deficient to 5 = Highly Proficient',1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(5,2,'scale','statisticalanalysis','Statistical analysis',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(6,2,'scale','interpretationofdata','Interpretation of data',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(7,2,'scale','creativityinnovativethinking','Creativity/innovative thinking',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(8,2,'scale','navigatingpeerreview','Navigating the peer review process',NULL,NULL,7,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(9,3,'scale','recordkeeping','Careful recordkeeping practices',NULL,NULL,1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(10,3,'scale','dataownership','Understanding of data ownership/sharing issues',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(11,3,'scale','responsibleauthorship','Demonstrating responsible authorship and publication practices',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(12,3,'scale','responsibleconduct','Demonstrating responsible conduct in animal research',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(13,3,'scale','identifymisconduct','Can identify and address research misconduct',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(14,3,'scale','identifyconflictofinterest','Can identify and manage conflict of interest',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(15,4,'scale','basicwriting','Basic writing and editing',NULL,NULL,1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(16,4,'scale','writingpublications','Writing scientific publications',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(17,4,'scale','writinggrantproposals','Writing grant proposals',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(18,4,'scale','writingfornonscientists','Writing for nonscientists',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(19,4,'scale','speakingclearly','Speaking clearly and effectively',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(20,4,'scale','presentingresearchtoscientists','Presenting research to scientists',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(21,4,'scale','presentingresearchtononscientists','Presenting to nonscientists',NULL,NULL,7,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(22,4,'scale','teachinginaclassroom','Teaching in a classroom setting',NULL,NULL,8,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(23,4,'scale','trainingandmentoring','Training and mentoring individuals',NULL,NULL,9,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(24,4,'scale','seekingadvice','Seeking advice from advisors and mentors',NULL,NULL,10,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(25,4,'scale','negotiatingdifficultconversations','Negotiating difficult conversations',NULL,NULL,11,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(26,5,'scale','workplaceetiquette','Demonstrating workplace etiquette',NULL,NULL,1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(27,5,'scale','complyingwithrules','Complying with rules and regulations',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(28,5,'scale','upholdingcommitments','Upholding commitments and meeting deadlines',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(29,5,'scale','maintainingpositiverelationships','Maintaining positive relationships with colleagues',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(30,5,'scale','contributingtodisciplines','Contributing to discipline (e.g. member of professional society)',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(31,5,'scale','contributingtoinstitution','Contributing to institution (e.g. participate on committees)',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(32,6,'scale','providingguidance','Providing instruction and guidance',NULL,NULL,1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(33,6,'scale','providingfeedback','Providing constructive feedback',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(34,6,'scale','dealingwithconflict','Dealing with conflict',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(35,6,'scale','planningprojects','Planning and organizing projects',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(36,6,'scale','timemanagement','Time management',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(37,6,'scale','managingbudgets','Developing/managing budgets',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(38,6,'scale','managingresources','Managing data and resources',NULL,NULL,7,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(39,6,'scale','delegatingresponsibilities','Delegating responsibilities',NULL,NULL,8,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(40,6,'scale','leadingothers','Leading and motivating others',NULL,NULL,9,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(41,6,'scale','creatingvision','Creating vision and goals',NULL,NULL,10,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(42,6,'scale','servingasarolemodel','Serving as a role model',NULL,NULL,11,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(43,7,'scale','maintainprofessionalnetwork','How to maintain a professional network',NULL,NULL,1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(44,7,'scale','identifycareeroptions','How to identify career options',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(45,7,'scale','prepareapplicationmaterials','How to prepare application materials',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(46,7,'scale','interviewing','How to interview',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(47,7,'scale','negotiating','How to negotiate',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(48,8,'paragraph','whatoutcomesdoiwant','Outcomes',NULL,'<p>“What outcomes or rewards do I want from my work”?</p><p>Assess your Proficiency in these areas on a scale of <strong>1 = Deficient</strong> to <strong>5 = Highly Proficient</strong></p>',1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(49,8,'scale','helpsociety','Help Society: contribute to betterment of world',NULL,NULL,2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(50,8,'scale','helpothers','Help Others: be involved with directly helping individuals or small groups',NULL,NULL,3,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(51,8,'scale','peopleconduct','People Contact: have day-to-day contact with clients or colleagues',NULL,NULL,4,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(52,8,'scale','teamwork','Teamwork: work in collaboration with others as part of a team',NULL,NULL,5,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(53,8,'scale','friendships','Friendships: Develop close personal relationships with people at work',NULL,NULL,6,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(54,8,'scale','congenialatmosphere','Congenial Atmosphere:  work with friendly colleagues',NULL,NULL,7,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(55,8,'scale','competition','Competition: engage in activities that test my abilities/achievements against others’ abilities/achievements',NULL,NULL,8,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(56,8,'scale','makedecisions','Make Decisions:  have authority to decide courses of action, policies, etc.',NULL,NULL,9,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(57,8,'scale','fastpace','Fast Pace:  work in a busy atmosphere with frequent deadlines',NULL,NULL,10,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(58,8,'scale','supervision','Supervision:  be directly responsible for work done by others',NULL,NULL,11,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(59,8,'scale','influencepeople','Influence People:  be in a position to change attitudes or opinions of other people',NULL,NULL,12,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(60,8,'scale','workalone','Work Alone:  work on projects by myself, with little contact with others',NULL,NULL,13,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(61,8,'scale','independence','Independence:  work with little direction from others',NULL,NULL,14,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(62,8,'scale','intellectualchallenge','Intellectual Challenge:  perform work that is intellectually stimulating',NULL,NULL,15,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(63,8,'scale','workonfrontiersofknowledge','Work on Frontiers of Knowledge:  engage in the pursuit of knowledge or generating new ideas',NULL,NULL,16,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(64,8,'scale','expertstatus','Expert Status:  be acknowledged as an expert in a given field',NULL,NULL,17,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(65,8,'scale','creativity','Creativity:  originate and develop new ideas',NULL,NULL,18,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(66,8,'scale','aesthetics','Aesthetics:  appreciate the beauty of things and ideas that I work with',NULL,NULL,19,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(67,8,'scale','predictability','Predictability:  have job duties that are similar day-to-day',NULL,NULL,20,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(68,8,'scale','variety','Variety:  have job duties that change frequently',NULL,NULL,21,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(69,9,'goals','longtermgoals','What are your long-term goals (15 years or more)?',NULL,'Picture yourself 15 years from now. What do you see yourself involved in? Draw inspiration from your values assessment. List one or two goals that match what is important to you. ',1,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(70,9,'goals','intermediategoals','What are your intermediate career goals (5-10 years)?',NULL,'List two or three goals five to ten years from now.  You may include both professional and social goals.  Be as specific as you can.',2,0,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL),
					(71,9,'goals','shorttermgoals','What are your short-term career goals (next 5 years)?',NULL,'List three to five goals for the next five years. These should be specific and attainable goals. You may include both professional and social goals.',3,0,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL,'2018-01-24 16:23:03',1001,NULL,0,0,NULL);";

				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__careerplans_fields'))
		{
			$query = "DELETE FROM `#__careerplans_fields`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__careerplans_fieldsets'))
		{
			$query = "DELETE FROM `#__careerplans_fieldsets`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
