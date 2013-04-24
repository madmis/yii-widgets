<?php
/**
 * TDatePicker widget class
 *
 * Available options (http://www.malot.fr/bootstrap-datetimepicker/):
 *     format:
 *         String. Default: 'mm/dd/yyyy'. The date format, combination of h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy.
 *     weekStart:
 *         Integer. Default: 0. Day of the week start. 0 (Sunday) to 6 (Saturday).
 *     startDate:
 *         Date. Default: Beginning of time. The earliest date that may be selected; all earlier dates will be disabled.
 *    endDate
 *        Date. Default: End of time. The latest date that may be selected; all later dates will be disabled.
 *    daysOfWeekDisabled
 *         String, Array. Default: '', []. Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday).
 *         Multiple values should be comma-separated. Example: disable weekends: '0,6' or [0,6].
 *     autoclose
 *         Boolean. Default: false.
 *         Whether or not to close the datetimepicker immediately when a date is selected.
 *    startView
 *        Number, String. Default: 2, 'month'. The view that the datetimepicker should show when it is opened.
 *         Accepts values of :
 *             0 or 'hour' for the hour view
 *             1 or 'day' for the day view
 *             2 or 'month' for month view (the default)
 *             3 or 'year' for the 12-month overview
 *             4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
 *    minView
 *        Number, String. Default: 0, 'hour'. The lowest view that the datetimepicker should show.
 *    maxView
 *         Number, String. Default: 4, 'decade'. The highest view that the datetimepicker should show.
 *  todayBtn
 *         Boolean, "linked". Default: false. If true or "linked", displays a "Today" button at the bottom of the datetimepicker
 *         to select the current date. If true, the "Today" button will only move the current date into view; if "linked", the current date will also be selected.
 *    todayHighlight
 *         Boolean. Default: false. If true, highlights the current date.
 *    keyboardNavigation
 *         Boolean. Default: true. Whether or not to allow date navigation by arrow keys.
 *    language
 *         String. Default: 'en'. The two-letter code of the language to use for month and day names.
 *         These will also be used as the input's value (and subsequently sent to the server in the case of form submissions).
 *         Currently ships with English ('en'), German ('de'), Brazilian ('br'), and Spanish ('es') translations, but others can be added (see I18N below).
 *         If an unknown language code is given, English will be used.
 *    forceParse
 *         Boolean. Default: true. Whether or not to force parsing of the input value when the picker is closed.
 *         That is, when an invalid date is left in the input field by the user, the picker will forcibly parse that value,
 *         and set the input's value to the new, valid date, conforming to the given format.
 *    minuteStep
 *         Number. Default: 5. The increment used to build the hour view. A preset is created for each minuteStep minutes.
 *    pickerPosition
 *         String. Default: 'bottom-right' (other value supported : 'bottom-left').
 *         This option is currently only available in the component implementation. With it you can place the picker just under the input field.
 */
class TDatePicker extends CInputWidget {
	/**
	 * @var array the options for the Datetimepicker JavaScript plugin.
	 */
	public $options = array();

	/**
	 * @var array container options for datepicker field.
	 */
	public $container = array(
		'before' => null,
		'beforeClass' => null,
		'after' => null,
	);

	private $__cssFile = null;
	private $__jsFile = null;
	private $__localeFile = null;

	/**
	 * Initializes the widget.
	 */
	public function init() {
		parent::init();
		$this->htmlOptions['type'] = 'text';
		$this->htmlOptions['autocomplete'] = 'off';

		if (!isset($this->options['language'])) {
			$this->options['language'] = Yii::app()->language;
		}
		if (!isset($this->options['format'])) {
			$this->options['format'] = Yii::app()->format->clientDateFormat;
		}
		if (!isset($this->options['autoclose'])) {
			$this->options['autoclose'] = true;
		}
		if (!isset($this->options['minView'])) {
			$this->options['minView'] = 2;
		}

		if (empty($this->container['before'])) {
			$this->container['before'] = 'div';
		}
		if (empty($this->container['beforeClass'])) {
			$this->container['beforeClass'] = 'input-append date input-small';
		}
		if (empty($this->container['after'])) {
			$this->container['after'] = CHtml::openTag('span', array('class'=>'add-on'))
				. CHtml::openTag('i', array('class'=>'icon-calendar'))
				. CHtml::closeTag('span')
				. CHtml::closeTag('i')
				. CHtml::closeTag('div');
		}

		$this->__registerAsset();
	}

	/**
	 * Runs the widget.
	 */
	public function run() {
		parent::run();
		list($name, $id) = $this->resolveNameID();

		if (isset($this->htmlOptions['id'])) {
			unset($this->htmlOptions['id']);
		}

		$this->__prepareValue();
		$result = CHtml::openTag($this->container['before'], array('class'=>$this->container['beforeClass'], 'id'=>$id));
		if ($this->hasModel()) {
			$result .= CHtml::activeTextField($this->model, $this->attribute, $this->htmlOptions);
		} else {
			$result .= CHtml::textField($name, $this->value, $this->htmlOptions);
		}
		$result .= $this->container['after'];
		echo $result;

		$this->registerClientScript($id);
	}

	private function __prepareValue() {
		if ($this->hasModel()) {
			$attribute = $this->attribute;
			if (!empty($this->model->$attribute)) {
				if ($this->options['format'] == Yii::app()->format->clientDateFormat) {
					$this->model->$attribute = Yii::app()->format->formatDate($this->model->$attribute);
				} else {
					$this->model->$attribute = Yii::app()->format->formatDatetime($this->model->$attribute);
				}
			}
		} else {
			if (!empty($this->value)) {
				if ($this->options['format'] == Yii::app()->format->clientDateFormat) {
					$this->value = Yii::app()->format->formatDate($this->value);
				} else {
					$this->value = Yii::app()->format->formatDatetime($this->value);
				}
			}
		}
	}

	private function __registerAsset() {
		$this->__cssFile = Yii::app()->getAssetManager()->publish(
			dirname(__FILE__) . '/assets/css/datetimepicker.css'
		);
		$this->__jsFile = Yii::app()->getAssetManager()->publish(
			dirname(__FILE__) . '/assets/js/bootstrap-datetimepicker.js'
		);
		$this->__localeFile = Yii::app()->getAssetManager()->publish(
			dirname(__FILE__) . '/assets/js/locales/bootstrap-datetimepicker.' . Yii::app()->language . '.js'
		);
	}

	/**
	 * Registers required client script for bootstrap datepicker. It is not used through bootstrap->registerPlugin
	 * in order to attach events if any
	 */
	public function registerClientScript($id) {
		Yii::app()->clientScript->registerCssFile($this->__cssFile);
		Yii::app()->clientScript->registerScriptFile($this->__jsFile, CClientScript::POS_END);
		Yii::app()->clientScript->registerScriptFile($this->__localeFile, CClientScript::POS_END);
		$options = !empty($this->options) ? CJavaScript::encode($this->options) : '';

		ob_start();
		echo "jQuery('#{$id}').datetimepicker({$options})";
//		foreach ($this->events as $event => $handler)
//			echo ".on('{$event}', " . CJavaScript::encode($handler) . ")";

		Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $this->getId(), ob_get_clean() . ';');
	}

	public static function gridAfterAjax($ids, $options = null) {
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		if (!isset($options['language'])) {
			$options['language'] = Yii::app()->language;
		}
		if (!isset($options['format'])) {
			$options['format'] = Yii::app()->format->clientDateFormat;
		}
		if (!isset($options['autoclose'])) {
				$options['autoclose'] = true;
		}
		if (!isset($options['minView'])) {
			$options['minView'] = 2;
		}
		$options = CJavaScript::encode($options);
		foreach ($ids as &$id) {
			$id = '#' . $id;
		}

		return "function() { jQuery('" . implode(',', $ids) . "').datetimepicker({$options}); }";
	}
}
