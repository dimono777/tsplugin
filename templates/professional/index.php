<div class="professional-request-message">
    <?php if (TSInit::$app->getVar('requestInProcess')) { ?>

        <div class="professional-request-message-success">
            <p class="title"><?php echo TS_Functions::__('Thank you for applying for a professional account.');?></p>
            <p><?php echo TS_Functions::__('Please note that we have sent you an email with further steps that must to be completed in order to finalize the application. Please feel free to contact your Account Manager or our Support Team, if you have any questions.');?></p>
        </div>
    
    <?php } elseif (TSInit::$app->getVar('docsReviewInProcess')) { ?>

        <div class="professional-request-message-success">
            <p class="title"><?php echo TS_Functions::__('Thank you for applying for a professional account.');?></p>
            <p><?php echo TS_Functions::__('We kindly ask for your patience while your application is being verified. Please feel free to contact your Account Manager or our Support Team, if you have any questions.');?></p>
        </div>
    
    <?php } elseif (TSInit::$app->getVar('rejected')) { ?>

        <div class="professional-request-message-error">
            <p><?php echo TS_Functions::__('Please note that your application for a professional account has been rejected as you do not possess sufficient knowledge and/or experience in order to be treated as a professional client. Please feel free to contact our support, if you have any questions.');?></p>
        </div>
    
    <?php } elseif (TSInit::$app->getVar('alreadyProfessional')) { ?>

        <div class="professional-request-message-success">
            <p><?php echo TS_Functions::__('You had been already confirmed to the Professional Level.');?></p>
        </div>
    
    <?php } ?>
</div>