<?php

use yii\web\View;

/**
 *
 * User: yuantong
 * Date: 2023/1/31
 * Email: <yt.vertigo0927@gmail.com>
 */

/* @var $this View */
$this->registerAssetBundle(\frontend\assets\SweetAlertAsset::class);
$js = <<<JS
    $('#temp').on('click', function() {
        let timerInterval
        Swal.fire({
          title: 'Auto close alert!',
          html: 'I will close in <b></b> milliseconds.',
          timer: 2000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
              b.textContent = Swal.getTimerLeft()
            }, 100)
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
          }
        })
    })
JS;

$this->registerJs($js);
?>

<div class="d-grid gap-2">
    <button class="btn btn-primary" type="button" id="temp">Click</button>
</div>