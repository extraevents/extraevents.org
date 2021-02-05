<?php if ($data->message) { ?>
    <div data-form-message 
         class='form_message_wrapper <?= $data->message_error ? 'form_message_wrapper_error' : '' ?>'>
        <div>
            <?= t('message.' . $data->message) ?>
        </div>    
        <div class='form_message_close'>
            <a data-form-message-hide href='#'>X</a>
        </div>
    </div>
<?php } ?>