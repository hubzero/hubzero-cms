<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Fields\Radio;
//use Hubzero\Html\Builder\Behavior;
//use Document;
//use stdClass;
//use Route;
use Components\Groups\Models\Orm\Field;
use Lang;
use Hubzero\Html\Builder\Behavior;
use App;

/**
 * Supports a scaled selection field
 */
class Goals extends Radio
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'goals';

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="goals ' . (string) $this->element['class'] . '"' : ' class="goals"';

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . ' data-name="' . $this->fieldname. '">';

		$skills = null;

		if (isset($this->element['option_other']) && $this->element['option_other'])
		{
			include_once dirname(__DIR__) . '/field.php';

			$skills = Field::all()
				->whereEquals('type', 'scale')
				->order('ordering', 'asc')
				->rows();
		}

		$html[] = '
		<script id="new-strategy-row-' . $this->fieldname . '" type="text/x-handlebars-template">
			<div class="goal-strategy-row grid" id="new-strategy-{{index}}">
				<div class="col span7">
					<input type="text" id="' . $this->id . '-{{index}}-strategy-{{index2}}-completed" name="' . $this->name . '[{{index}}][strategy][{{index2}}][content]" placeholder="' . Lang::txt('Short, one line sentence') . '" value="{{content}}" />
				</div>
				<div class="col span3">
					<input type="text" id="' . $this->id . '-{{index}}-strategy-{{index2}}-completed" name="' . $this->name . '[{{index}}][strategy][{{index2}}][completed]" class="datepicker" placeholder="' . Lang::txt('YYYY-MM-DD') . '" value="{{completed}}" />
				</div>
				<div class="col span1">
					<input type="checkbox" id="' . $this->id . '-{{index}}-strategy-{{index2}}-badge" name="' . $this->name . '[{{index}}][strategy][{{index2}}][badge]" value="1" />
					<input type="hidden" id="' . $this->id . '-{{index}}-strategy-{{index2}}-id" name="' . $this->name . '[{{index}}][strategy][{{index2}}][id]" value="" />
				</div>
				<div class="col span1 omega">
					<a class="goals-strategy-remove delete icon-trash tooltips" href="#" title="' . Lang::txt('Remove strategy') . '">' . Lang::txt('Remove strategy') . '</a>
				</div>
			</div>
		</script>

		<script id="new-goal-row-' . $this->fieldname . '" type="text/x-handlebars-template">
			<div class="goals-field-wrap" id="' . $this->id . '-{{index}}">
				<div class="input-wrap">
					<label for="' . $this->id . '-{{index}}-goal">' . Lang::txt('Goal') . '</label>
					<input type="text" id="' . $this->id . '-{{index}}-goal" name="' . $this->name . '[{{index}}][goal]" placeholder="' . Lang::txt('Goal') . '" value="" />
					<input type="hidden" id="' . $this->id . '-{{index}}-id" name="' . $this->name . '[{{index}}][id]" value="" />
				</div>';
		if ($skills)
		{
			$html[] = '<fieldset>';
			$html[] = '<legend>' . Lang::txt('Skills Needed') . '</legend>';
			$html[] = '<div class="skills-wrap">';
			$j = 0;
			foreach ($skills as $skill)
			{
				$html[] = '<div class="input-wrap">';
					$html[] = '<input class="option" type="checkbox" id="' . $this->id . '{{index}}-skill-' . $j . '" name="' . $this->name . '[{{index}}][skill][' . $j . ']" value="' . $skill->get('id') . '" />';
					$html[] = '<label class="option" for="' . $this->id . '{{index}}-skill-' . $j . '">' . $skill->get('label') . '</label>';
				$html[] = '</div>';
				$j++;
			}
			$html[] = '</div>';
			$html[] = '<div class="input-wrap">';
			$html[] = '<label for="' . $this->id . '-{{index}}-skills_needed">' . Lang::txt('Extra skills') . '</label>';
			$html[] = '<textarea id="' . $this->id . '-{{index}}-skills_needed" name="' . $this->name . '[{{index}}][skills_needed]" placeholder="' . Lang::txt('Enter any skills not listed above') . '" cols="35" rows="3"></textarea>';
			$html[] = '</div>';
			$html[] = '</fieldset>';
		}
		$html[] = '
				<fieldset class="goals-strategies" data-index="1">
					<legend>Strategies</legend>
					<div class="goal-strategy-row grid">
						<div class="col span7">
							<label for="' . $this->id . '-{{index}}-strategy-0-content">' . Lang::txt('Strategy') . '</label>
							<input type="text" id="' . $this->id . '-{{index}}-strategy-0-content" name="' . $this->name . '[{{index}}][strategy][0][content]" placeholder="' . Lang::txt('Short, one line sentence') . '" value="" />
						</div>
						<div class="col span3">
							<label for="' . $this->id . '-{{index}}-strategy-0-completed">' . Lang::txt('Completed') . '</label>
							<input type="text" id="' . $this->id . '-{{index}}-strategy-0-completed" name="' . $this->name . '[{{index}}][strategy][0][completed]" class="datepicker" placeholder="' . Lang::txt('YYYY-MM-DD') . '" value="" />
						</div>
						<div class="col span1">
							<label for="' . $this->id . '-{{index}}-strategy-0-badge">' . Lang::txt('Badge') . '</label>
							<input type="checkbox" id="' . $this->id . '-{{index}}-strategy-0-badge" name="' . $this->name . '[{{index}}][strategy][0][badge]" value="1" />
							<input type="hidden" id="' . $this->id . '-{{index}}-strategy-0-id" name="' . $this->name . '[{{index}}][strategy][0][id]" value="" />
						</div>
						<div class="col span1 omega">
							<a class="goals-strategy-add add icon-add tooltips" data-index="{{index}}" href="#" title="' . Lang::txt('Add strategy') . '">' . Lang::txt('Add strategy') . '</a>
						</div>
					</div>
				</fieldset>
				<p class="btn-wrap"><a class="goals-remove icon-trash delete tooltips" data-index="{{index}}" href="#" title="' . Lang::txt('Remove goal') . '">' . Lang::txt('Remove goal') . '</a></p>
			</div>
		</script>
		';

		$values = $this->value;
		$values = is_array($values) ? $values : array($values);

		if (empty($values))
		{
			$values[] = array(
				'goal' => '',
				'skills_needed' => '',
				'skills_level' => '',
				'id' => 0
			);
		}

		// Build the radio field output.
		foreach ($values as $i => $value)
		{
			if (is_string($value))
			{
				$value = json_decode((string)$value, true);
			}

			if (!$value || json_last_error() !== JSON_ERROR_NONE)
			{
				$value = array();
				$value['goal'] = '';
				$value['skills_needed'] = '';
				$value['skills_level'] = '';
				$value['id'] = 0;
			}

			$html[] = '<div class="goals-field-wrap">';
				$html[] = '<div class="input-wrap">';
					$html[] = '<label for="' . $this->id . $i . '-goal">' . Lang::txt('Goal') . '</label>';
					$html[] = '<input type="text" id="' . $this->id . $i . '-goal" name="' . $this->name . '[' . $i . '][goal]" placeholder="Goal" value="' . htmlspecialchars($value['goal'], ENT_COMPAT, 'UTF-8') . '" />';
					$html[] = '<input type="hidden" id="' . $this->id . $i . '-id" name="' . $this->name . '[' . $i . '][id]" value="' . $value['id'] . '" />';
				$html[] = '</div>';
			if ($skills)
			{
				if (isset($value['skills_level']) && $value['skills_level'])
				{
					$value['skills_level'] = json_decode($value['skills_level'], true);
				}
				else
				{
					$value['skills_level'] = array();
				}
				$html[] = '<fieldset>';
				$html[] = '<legend>' . Lang::txt('Skills Needed') . '</legend>';
				$html[] = '<div class="skills-wrap">';
				$j = 0;
				foreach ($skills as $skill)
				{
					$checked = '';
					if (in_array($skill->get('id'), $value['skills_level']))
					{
						$checked = 'checked="checked"';
					}
					$html[] = '<div class="input-wrap">';
						$html[] = '<input class="option" type="checkbox" id="' . $this->id . $i . '-skill-' . $j . '" name="' . $this->name . '[' . $i . '][skill][' . $j . ']" ' . $checked . ' value="' . $skill->get('id') . '" />';
						$html[] = '<label class="option" for="' . $this->id . $i . '-skill-' . $j . '">' . $skill->get('label') . '</label>';
					$html[] = '</div>';
					$j++;
				}
				$html[] = '</div>';
				$html[] = '<div class="input-wrap">';
				$html[] = '<label for="' . $this->id . $i . '-skills_needed">' . Lang::txt('Extra skills') . '</label>';
				$html[] = '<textarea id="' . $this->id . $i . '-skills_needed" name="' . $this->name . '[' . $i . '][skills_needed]" placeholder="' . Lang::txt('Enter any skills not listed above') . '" cols="35" rows="3">' . htmlspecialchars($value['skills_needed'], ENT_COMPAT, 'UTF-8') . '</textarea>';
				$html[] = '</div>';
				$html[] = '</fieldset>';
			}

				if (!isset($value['strategy']) || !is_array($value['strategy']))
				{
					$value['strategy'] = array();
				}
				if (empty($value['strategy']))
				{
					$value['strategy'][] = array(
						'content'   => '',
						'completed' => '',
						'badge'     => 0,
						'id'        => 0
					);
				}
				$html[] = '<fieldset class="goals-strategies" data-index="' . count($value['strategy']) . '">';
					$html[] = '<legend>Strategies</legend>';
					foreach ($value['strategy'] as $z => $strategy)
					{
						$html[] = '<div class="grid goal-strategy-row">';
							$html[] = '<div class="col span7">';
							if ($z <= 0)
							{
								$html[] = '<label for="' . $this->id . $i . '-strategy-' . $z . '-content">' . Lang::txt('Strategy') . '</label>';
							}
								$html[] = '<input type="text" id="' . $this->id . $i . '-strategy-' . $z . '-content" name="' . $this->name . '[' . $i . '][strategy][' . $z . '][content]" placeholder="Short, one line sentence" value="' . htmlspecialchars($strategy['content'], ENT_COMPAT, 'UTF-8') . '" />';
							$html[] = '</div>';
							$html[] = '<div class="col span3">';
							if ($z <= 0)
							{
								$html[] = '<label for="' . $this->id . $i . '-strategy-' . $z . '-completed">' . Lang::txt('Date Completed') . '</label>';
							}
								$html[] = '<input type="text" id="' . $this->id . $i . '-strategy-' . $z . '-completed" name="' . $this->name . '[' . $i . '][strategy][' . $z . '][completed]" class="datepicker" placeholder="YYYY-MM-DD" value="' . htmlspecialchars($strategy['completed'], ENT_COMPAT, 'UTF-8') . '" />';
							$html[] = '</div>';
							$html[] = '<div class="col span1">';
							if ($z <= 0)
							{
								$html[] = '<label for="' . $this->id . $i . '-strategy-' . $z . '-badge">' . Lang::txt('Badge') . '</label>';
							}
								$html[] = '<input type="checkbox" id="' . $this->id . $i . '-strategy-' . $z . '-badge" name="' . $this->name . '[' . $i . '][strategy][' . $z . '][badge]" value="1" ' . ($strategy['badge'] ? ' checked="checked"' : '') . ' />';
								$html[] = '<input type="hidden" id="' . $this->id . $i . '-strategy-' . $z . '-id" name="' . $this->name . '[' . $i . '][strategy][' . $z . '][id]" value="' . $strategy['id'] . '" />';
							$html[] = '</div>';
							$html[] = '<div class="col span1 omega">';
							if ($z > 0)
							{
								$html[] = '<a class="goals-strategy-remove delete icon-trash tooltips" href="#" title="' . Lang::txt('Remove strategy') . '">' . Lang::txt('Remove strategy') . '</a>';
							}
							$html[] = '</div>';
						$html[] = '</div>';
					}
				$html[] = '</fieldset>';
			if ($i > 0)
			{
				$html[] = '<p class="btn-wrap"><a class="goals-remove icon-trash delete tooltips" data-index="' . $i . '" href="#" title="' . Lang::txt('Remove goal') . '">' . Lang::txt('Remove goal') . '</a></p>';
			}
			$html[] = '</div>';
		}

		// End the radio field output.
		$html[] = '</fieldset>';

		Behavior::framework(true);

		App::get('document')->addScript(str_replace(PATH_ROOT, '', PATH_CORE) . '/assets/js/handlebars.js');
		App::get('document')->addScript(str_replace(PATH_ROOT, '', dirname(dirname(__DIR__))) . '/site/assets/js/goals.js?v=' . filemtime(dirname(dirname(__DIR__)) . '/site/assets/js/goals.js'));

		return implode($html);
	}

	/**
	 * Render the supplied value
	 *
	 * @param   string  $value
	 * @return  string
	 */
	public function getValue($careerplan_id)
	{
		return null;
	}

	/**
	 * Render the supplied value
	 *
	 * @param   string  $value
	 * @return  string
	 */
	public function renderValue($value)
	{
		$html = array();

		if (is_array($value))
		{
			foreach ($value as $goal)
			{
				$html[] = '<div class="goal">';
				$html[] = '<span class="goal-title">' . $goal['goal'] . '</span>';
				if ((isset($goal['skills_level']) && $goal['skills_level'])
				 || (isset($goal['skills_needed']) && $goal['skills_needed']))
				{
					$html[] = '<div class="goal-skills">';
					$html[] = '<p><strong>Skills needed:</strong></p>';
					$html[] = '<ul>';
				}
				if (isset($goal['skills_level']) && $goal['skills_level'])
				{
					$skl = json_decode($goal['skills_level'], true);

					include_once dirname(__DIR__) . '/field.php';

					$skills = Field::all()
						->whereEquals('type', 'scale')
						->whereIn('id', $skl)
						->order('fieldset_id', 'asc')
						->order('ordering', 'asc')
						->rows();

					foreach ($skills as $skill)
					{
						$html[] = '<li><span class="goal-skill">' . $skill->get('label') . '</span></li>';
					}
				}
				if (isset($goal['skills_needed']) && $goal['skills_needed'])
				{
					$html[] = '<li><span class="goal-skill">' . $goal['skills_needed'] . '</span></li>';
				}
				if ((isset($goal['skills_level']) && $goal['skills_level'])
				 || (isset($goal['skills_needed']) && $goal['skills_needed']))
				{
					$html[] = '</ul>';
					$html[] = '</div>';
				}
				if (isset($goal['strategy']) && is_array($goal['strategy']))
				{
					$html[] = '<div class="goal-strategies">';
					$html[] = '<p><strong>Strategy:</strong></p>';
					$html[] = '<ol>';
					foreach ($goal['strategy'] as $strategy)
					{
						$html[] = '<li><span class="goal-strategy">' . $strategy['content'] . '</span></li>';
					}
					$html[] = '</ol>';
					$html[] = '</div>';
				}
				$html[] = '</div>';
			}
		}

		if (empty($html))
		{
			$html[] = '<p>' . Lang::txt('(none)') . '</p>';
		}

		$value = implode("\n", $html);

		return $value;
	}
}
