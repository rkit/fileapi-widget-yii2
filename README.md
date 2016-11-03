# FileApi widget for Yii2

Wrapper for [FileAPI](https://github.com/mailru/FileAPI)

## Installation

```
composer require rkit/fileapi-widget-yii2
```

## Usage

> See FileApi docs https://github.com/RubaXa/jquery.fileapi/

```php
use rkit\fileapi\Widget as FileApi;
…
<?= $form->field($model, $attribute, ['template' => "{label}\n{error}\n{input}\n{hint}"])
    ->widget(FileApi::className(), [
        'template' => '@app/path/to/template',
        'callbacks' => [
            'select' => new JsExpression('function (evt, ui) {}'),
            'filecomplete' => [new JsExpression('function (evt, ui) {}'),
        ]],
        'settings' => [
            'url' => yii\helpers\Url::toRoute('/url/to/upload'),
            'imageSize' => ['minWidth' => 1000],
            'accept' => 'image/*',
            'duplicate' => true
        ]
    ]);
?>
```

## Theme for Bootstrap 3

See in [assets/css/style.css](assets/css/style.css)
