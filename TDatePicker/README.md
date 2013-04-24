# TDatePicker - datepicker widget for yii CGridView
===========

# Screenshots

![Datepicker](https://github.com/madmis/yii-widgets/blob/master/TDatePicker/screenshots/4cf484e95f7c44c249f38bb28749da50.jpg?raw=true)

![Datepicker](https://github.com/madmis/yii-widgets/blob/master/TDatePicker/screenshots/8572eb857b487f48054c03817b8c5711.jpg?raw=true)

![Datepicker](https://github.com/madmis/yii-widgets/blob/master/TDatePicker/screenshots/4cf484e95f7c44c249f38bb28749da50.jpg?raw=true)

# Example:

CGridView column

```php
array(
    'name' => 'created',
    'value' => 'Yii::app()->format->formatDate($data->created)',
    'type' => 'raw',
    'htmlOptions' => array(),
    'filter' => $this->widget('application.widgets.TDatePicker.TDatePicker', array(
        'model' => $model,
        'attribute' => 'created',
        'htmlOptions' => array('id' => 'created'),
    ), true)
),
```
CGridView property

```php
'afterAjaxUpdate' => TDatePicker::gridAfterAjax(array('created', 'last_visit')),
```