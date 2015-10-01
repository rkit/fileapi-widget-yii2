# FileApi widget for Yii2

Wrapper for [FileAPI](https://github.com/mailru/FileAPI)

## Installation

```
composer require rkit/fileapi-widget-yii2
```

## Usage

```php
use rkit\fileapi\Widget as FileApi;
…
<?= $form->field($model, $attribute, ['template' => "{label}\n{error}\n{input}\n{hint}"])
    ->widget(FileApi::className(), [
        'template' => '@app/path/to/template',
        'callbacks' => [
            'select' => new JsExpression('function (evt, ui) {
               …
            }'),
            'filecomplete' => [new JsExpression('function (evt, uiEvt) {
               if (uiEvt.result.error) {
                  …
               } else {
                  …
               }
            }'),
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

### Basic usage with [FileMananger](https://github.com/rkit/filemanager-yii2)

1. **Form**

   ```php
   use rkit\fileapi\Widget as FileApi;
   …
   <?= $form->field($model, $attribute, ['template' => "{label}\n{error}\n{input}\n{hint}"])
       ->widget(FileApi::className(), [
           'template' => '@app/path/to/template',
           'callbacks' => [
               'select' => new JsExpression('function (evt, ui) {
                  var ufile = ui.files[0],
                  $el = $(this);

                  if (ui && ui.other.length && ui.other[0].errors) {
                    alert("'.Yii::t('app', 'Incorrect file format').': " + ui.other[0].name);
                  }
               }'),
               'filecomplete' => [new JsExpression('function (evt, uiEvt) {
                  if (uiEvt.result.error) {
                    alert(uiEvt.result.error);
                  } else {
                    $(".field-' . Html::getInputId($model, $attribute) . '").find(".help-block").empty();
                    $(".field-' . Html::getInputId($model, $attribute) . '").removeClass("has-error");
                    $(this).find("input[type=\"hidden\"]").val(uiEvt.result.id);
                    $(this).find(".fileapi-preview-wrapper").html("<img src=" + uiEvt.result.path + ">");
                    $(this).find(".fileapi-browse-text").text("' . Yii::t('app', 'Uploaded') . '");
                  }
               }'),
           ]],
           'settings' => [
               'url' => yii\helpers\Url::toRoute([$attribute . '-upload']),
               'imageSize' => $model->getFileRules($attribute)['imageSize'],
               'accept' => implode(',', $model->getFileRules($attribute)['mimeTypes']),
               'duplicate' => true
           ]
       ])
       ->hint($model->getFileRulesDescription($attribute), [
           'class' => 'fileapi-rules'
       ]);
   ?>
   ```

2. **Template**

   ```php
   <div id="<?= $selector; ?>" class="fileapi">
     <div class="btn btn-default js-fileapi-wrapper">
       <div class="fileapi-browse" data-fileapi="active.hide">
         <span class="glyphicon glyphicon-picture"></span>
         <span class="fileapi-browse-text">
           <?= $value ? Yii::t('app', 'Uploaded') : Yii::t('app', 'Upload') ?>
         </span>
         <span data-fileapi="name"></span>
         <input type="file" name="<?= $inputName ?>">
       </div>
       <div class="fileapi-progress" data-fileapi="active.show">
         <div class="progress progress-striped">
           <div class="fileapi-progress-bar progress-bar progress-bar-info" data-fileapi="progress"
           role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
         </div>
       </div>
     </div><br>
     <?php if ($preview === true) : ?>
     <a href="#" class="fileapi-preview">
       <span data-fileapi="delete" class="fileapi-preview-delete">
         <span class="glyphicon glyphicon-trash"></span>
       </span>
       <span class="fileapi-preview-wrapper">
         <?php if (!empty($value)):?>
         <img src="<?= $value ?>">
         <?php endif?>
       </span>
     </a>

     <?php $this->registerJs("
     $(document).on('click', '#$selector [data-fileapi=\"delete\"]', function(evt) {
       evt.preventDefault();
       var file = $(this).closest('#$selector');
       file.fileapi('clear');
       file.find('input[type=\"hidden\"]').val('');
       file.find('.fileapi-preview-wrapper').empty();
       file.find('.fileapi-browse-text').text('" . Yii::t('app', 'Upload') . "');
     })"); ?>
     <?php endif; ?>

     <?= $input ?>

   </div>
   ```

### Gallery with [FileMananger](https://github.com/rkit/filemanager-yii2)

1. **Form**

   ```php
   use rkit\fileapi\Widget as FileApi;
   …

   <?= $form->field($model, $attribute, ['template' => "{error}\n{input}\n{hint}"])
       ->widget(FileApi::className(), [
           'template' => '@app/path/to/template',
           'preview' => false,
           'callbacks' => [
               'select' => new JsExpression('function (evt, ui) {
                  if (ui && ui.other.length && ui.other[0].errors) {
                    alert("'.Yii::t('app', 'Incorrect file format').': " + ui.other.map(function(v) { return v.name; }));
                  }
               }'),
               'filecomplete' => new JsExpression('function (evt, uiEvt) {
                  if (uiEvt.result.error) {
                    alert(uiEvt.result.error);
                  } else {
                    $(".field-' . Html::getInputId($model, $attribute) . '").find(".help-block").empty();
                    $(".field-' . Html::getInputId($model, $attribute) . '").removeClass("has-error");
                    $(this).find(".fileapi-files").append(uiEvt.result);
                  }
               }'),
           ],
           'settings' => [
               'url' => yii\helpers\Url::toRoute([$attribute . '-upload']),
               'imageSize' => $model->getFileRules($attribute)['imageSize'],
               'multiple' => true,
               'duplicate' => true
           ]
       ])
       ->hint($model->getFileRulesDescription($attribute), [
           'class' => 'fileapi-rules'
       ]
   ); ?>
   ```

2. **Template**

   ```php
   use yii\helpers\Html;
   …

   <div id="<?= $selector; ?>" class="fileapi">
     <div class="btn btn-default btn-small fileapi-fileapi-wrapper">
       <div class="fileapi-browse" data-fileapi="active.hide">
         <span class="glyphicon glyphicon-picture"></span>
         <span><?= Yii::t('app', 'Upload')?></span>
         <input type="file" name="<?= $inputName ?>">
       </div>
     </div>
     <ul class="fileapi-files">
     <?php $files = $model->getFiles($attribute); foreach ($files as $file):?>
       <?= $this->render('gallery-item', [
               'file' => $file,
               'model' => $model,
               'attribute' => $attribute
       ]); ?>
     <?php endforeach?>
     </ul>
     <?= Html::hiddenInput(Html::getInputName($model, $attribute) . '[]', null, ['id' => Html::getInputId($model, $attribute)]) ?>
   </div>
   ```

3. **Item**

   ```php
   <li>
     <a href="<?= $file->path()?>" target="_blank"><img src="<?= $model->thumb('gallery', '80x80', $file->path())?>"></a>
     <a class="btn btn-lg"><span class="glyphicon glyphicon-remove remove-item" data-remove-item="li"></span></a>
     <?= Html::textInput(Html::getInputName($model, $attribute) . '[files][' . $file->id .']', $file->title, [
         'class' => 'form-control',
     ])?>
   </li>
   ```
