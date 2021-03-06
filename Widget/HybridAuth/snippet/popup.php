<div class="ip ipWidget-HybridAuth">
    <div id="ipsWidgetHybridAuthPopup" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo __('Social login widget settings', 'ipAdmin') ?></h4>
                </div>

                <div class="modal-body">
                    <div class="_note bg-warning"><?php
                        echo __('To setup HybridAuth plugin, check the <code>Readme.md</code> file. To setup Facebook, Google and GitHub OAuth ID and secret, go to <code>Menu/Plugins/HybridAuth</code> panel.', 'HybridAuth', false);
                        ?></div>
                    <?php echo $form->render(); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'ipAdmin') ?></button>
                    <button type="button" class="btn btn-primary ipsConfirm"><?php echo __('Confirm', 'ipAdmin') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
