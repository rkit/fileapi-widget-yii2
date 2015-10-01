# FileApi widget for Yii2

Wrapper for [FileAPI](https://github.com/mailru/FileAPI)

## Installation

```
composer require rkit/fileapi-widget-yii2
```

## Usage

### Basic usage

1. Form

   ```php
   use rkit\fileapi\Widget as FileApi;
   …

   $form->field($model, $attribute, ['template' => "{label}\n{error}\n{input}\n{hint}"])
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
               'duplicate' => true
           ]
       ])
   );
   ```

2. Template

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

### Gallery
